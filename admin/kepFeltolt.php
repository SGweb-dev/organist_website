<?php
    include '../csatol/kapcsolat.php';
    session_start();
    
    if (!isset($_SESSION['admin'])) header("Location: ../index.php");

    if (isset($_POST['regi_kategoria'])){
        $kat_id = $_POST['regi_kategoria'];
       
        $kat_sql = "SELECT * FROM kategoria WHERE id = '$kat_id'";
        $kat_keres = $conn->query($kat_sql);
        $kategoria = $kat_keres->fetch_assoc();

        $_SESSION['kategoria_neve'] = $kategoria['neve'];
        $_SESSION['kategoria_id'] = $kat_id;
    } 
    
    if (isset($_POST['uj_kategoria'])) {
        $kat_neve = $_POST['ujKat'];
        $kat_leiras = $_POST['leiras'];

        $konyvtar = "../kepek/" . $kat_neve;
        $konyvtar = mb_convert_encoding($konyvtar, "ISO-8859-2", "UTF-8");
        if (!mkdir($konyvtar, 0777, true)) {
            $uzenet = "Hiba történt a fájl feltöltésekor.";
        }
        else {
            $kat_beszur = "INSERT INTO kategoria (neve, leiras) VALUES ('$kat_neve', '$kat_leiras')";
            $keres_kat = $conn->query($kat_beszur);
            if ($keres_kat) {
                $kat_id = $conn->insert_id;  //az utoljára beszúrt sor id-jét mondja meg
                $uzenet = "A kategória létrehozása sikeres.";
                $_SESSION['kategoria_neve'] = $kat_neve;
                $_SESSION['kategoria_id'] = $kat_id;
            }
            else {
                $uzenet = "A kategória adatbázisba mentése sikertelen.";
                rmdir($konyvtar);  //ha nem sikerült felvenni az adatbázisba, akkor törli a könyvtárat
            }
        }

    }
    
    if (isset($_POST['feltolt'])){
        $target= "../kepek/" . $_SESSION['kategoria_neve'] . "/"; //célmappa
        $target = mb_convert_encoding($target, "ISO-8859-2", "UTF-8");
        $file_name = $_FILES['file']['name']; //a célfájlt nevezze el a $_FILES superglobal változóban lévo fájlnévre (a fájl eredeti nevére)
        $file_name = mb_convert_encoding($file_name, "ISO-8859-2", "UTF-8");
        
        $tmp_dir = $_FILES['file']['tmp_name']; //az ideiglenes mappa helyét a $tmp_dir változóban tároljuk

        if(!preg_match('/(gif|jpe?g|png)$/i', $file_name)) //ha a fájlnak ($file_name-nek) a kiterjesztése nem gif, jpg/jpeg, png, akkor...
        {
            $uzenet = "Rossz fajltipus!"; //hibaüzenet
        }
        else {
            $kat_id = $_SESSION['kategoria_id'];
            $sql = "SELECT * FROM kepek WHERE neve = '$file_name' and kategoria_id = '$kat_id'";
            $result = $conn->query($sql);
            if ( $result -> num_rows > 0) {
                $uzenet = "Ezzel a fáljnévvel már van kép a könyvtárban";
            }
            else {
                $feltolt = move_uploaded_file($tmp_dir, $target . $file_name); //az ideiglenes mappából átteszi a fájlt a végleges mappába (a $target . $file_name összeilleszti a két stringet, így uploads/fajlnev-et kapunk)
                if ($feltolt){
                    $kat_id = $_SESSION['kategoria_id'];
                    
                    $utso_sql = "SELECT MAX(sorszam) FROM kepek WHERE kategoria_id = '$kat_id'";
                    $utso_keres = $conn -> query($utso_sql);
                    $utso_sorszam = $utso_keres -> fetch_array();
                    if ($utso_sorszam[0] != null){
                        $sorszam = $utso_sorszam[0] + 1;
                    }
                    else {
                        $sorszam = 1;
                    }
                    
                    $sql = "INSERT INTO kepek (neve, kategoria_id, sorszam) VALUES ('$file_name', '$kat_id', '$sorszam')";
                    $result = $conn->query($sql);
                    if ($result){
                        $uzenet = "A kép feltöltve a könyvtárba.";
                    }
                    else {
                        $uzenet = "A kép feltöltve, de az adatbázisba töltés sikertelen. Próbáld újra. ide még kell valami - ki kellene törölni a képet a könyvtárból.";
                    }
                }
                else {
                    $uzenet = "A képfájl feltöltése nem sikerült.";
                    unset($file_name);
                }
            }
        }    
    }
    
    if (isset($_GET['katValt'])){
        unset($_SESSION['kategoria_neve']);
        unset($_SESSION['kategoria_id']);
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Képek feltöltése</title>
    </head>
    <body>
        <h1>Képek feltöltése</h1>
<?php
    if (isset($_SESSION['kategoria_id'])){
?>
        <h2>Kép feltöltése a kategóriához</h2>
        <h3><?php echo $_SESSION['kategoria_neve'] ?></h3>
        <form method="post" action="<?php print $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
            <input type="hidden" name="MAX_FILE_SIZE" value="2000000"> <!--a feltöltött file maximális mérete 1mb-->
            <input id="file" type="file" name="file">
            <input type="submit" name="feltolt" value="Feltöltöm">
        </form>
        
<?php
    }
    else {
        $kategoria_sql = "SELECT * FROM kategoria";
        $keres_kategoria = $conn->query($kategoria_sql);
        
        
?>
        <h2>Kategória kiválasztása</h2>
        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
<?php 
        if ($keres_kategoria->num_rows > 0){
?>
            <p>
                <select name="regi_kategoria">
                    <option value="0">válassz kategóriát...</option>
<?php
                while ($egyKat = $keres_kategoria->fetch_assoc()){
?>
                    <option value="<?php echo $egyKat['id'] ?>"><?php echo $egyKat['neve'] ?></option>
<?php
                }
?>                
                </select>
            </p>
            <p>
                <input type="submit" value="Kiválasztom">
            </p>
        </form>
<?php
        }
?>
        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">    
            <p>
                Új kategória felvétele: <input type="text" name="ujKat" size="25">
            </p>
            <p>
                Leírás: <textarea name="leiras" rows="5" cols="50"></textarea>
            </p>
            <p>
                <input type="submit" value="Kategória mentése" name="uj_kategoria">
            </p>
        </form>
<?php
    }
?>
        <p><?php if (isset($uzenet)) echo $uzenet ?></p>
        <p>
            <a href="<?php print $_SERVER['PHP_SELF']; ?>?katValt=1">Másik kategória kiválasztása</a> 
        </p>
        <p>
            <a href="index.php">Vissza a főoldalra</a>
        </p>
    </body>
</html>
