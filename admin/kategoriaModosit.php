<?php
    include '../csatol/kapcsolat.php';
    session_start();
    if (!isset($_SESSION['admin'])) header("Location: ../index.php");
    
    if (isset($_POST['kat_valaszt'])){
        $kat_id = $_POST['kat_valaszt'];
        $sql = "SELECT * FROM kategoria WHERE id = '$kat_id'";
        $result = $conn -> query($sql);
        $kategoria = $result -> fetch_assoc();
    }
    
    if (isset($_POST['kat_uj'])){
        $kat_id = $_POST['kat_regi'];
        $kat_uj_nev = $_POST['kat_uj']; //ezt írja be a admin
        $kat_regi_neve = $_POST['kat_regi_neve'];
        $sql = "UPDATE kategoria SET neve = '$kat_uj_nev' WHERE id = '$kat_id'";
        $result = $conn -> query($sql);
        if ($result) {
            $konyvtar = "../kepek/" . $kat_uj_nev . "/";
            $konyvtar = mb_convert_encoding($konyvtar, "ISO-8859-2", "UTF-8");
            $eredeti = "../kepek/" . $kat_regi_neve . "/";
            mkdir($konyvtar, 0777, true);
            $sql = "SELECT * FROM kepek WHERE kategoria_id = '$kat_id'";
            $result = $conn -> query($sql);
            if ($result -> num_rows > 0){
                while ($kep = $result -> fetch_assoc()){
                    $forras = $eredeti . $kep['neve'];
                    $cel = $konyvtar . $kep['neve'];
                    copy($forras, $cel);
                    unlink($forras);
                }
            }
            rmdir($eredeti);
            
        }
        else {
            $uzen = "Nem sikerült a kategória módosítása. Próbálja meg később.";
        }
    }
    
    if (isset($_POST['modosit'])){
        $kat_id = $_POST['kat_id'];
        $leiras_uj = $_POST['leiras_uj'];
        if ($_POST['aktualis_uj'] == 1 ){
            $aktualis_uj = true;
        }
        else {
            $aktualis_uj = false;
        }
        
        $sql = "UPDATE kategoria SET leiras = '$leiras_uj', aktualis = '$aktualis_uj' WHERE id = '$kat_id'";
        $result = $conn -> query($sql);
        if ($result) $uzen = "Sikerült a kategória adatait módosítani.";
    }
    
    if (isset($_POST['kat_torol'])){
        $kat_id = $_POST['kat_torol'];
        $kat_neve = $_POST['kat_neve'];
        $sql_kepek = "SELECT * FROM kepek WHERE kategoria_id = '$kat_id'";
        $result_kepek = $conn -> query($sql_kepek);
        if ($result_kepek -> num_rows > 0){            
            $sql_kepek_torol = "DELETE FROM kepek WHERE kategoria_id = '$kat_id'";
            $result = $conn -> query($sql_kepek_torol);
            $eleres = "../kepek/" . $kat_neve . "/";
            while ($kep = $result_kepek -> fetch_assoc()){
                $forras = $eleres . $kep['neve'];
                unlink($forras);
            }
        }
        rmdir($eleres);
        $sql_kat_torol = "DELETE FROM kategoria WHERE id = '$kat_id'";
        $result = $conn -> query($sql_kat_torol);
    }
?>

<html lang="hu">
    <head>
        <meta charset="UTF-8">
        <title>Képkategóriák módosítása</title>
    </head>
    <body>
        <h1>Képkategóriák módosítása</h1>
        <?php
            if (isset($kategoria)){
?>
        <h2><?php echo $kategoria['neve'] ?></h2>
        <h3>Kategória nevének módosítása</h3>
        <form action="<?php print $_SERVER['PHP_SELF']; ?>" method="post">
            <input type="text" name="kat_uj" size="30"> &nbsp; 
            <input type="hidden" name="kat_regi" value="<?php echo $kategoria['id'] ?>">
            <input type="hidden" name="kat_regi_neve" value="<?php echo $kategoria['neve'] ?>" >
            <input type="submit" value="Módosítom">
        </form>
        <h3>Kategória adatainak módosítása</h3>
        <form action="<?php print $_SERVER['PHP_SELF']; ?>" method="post">
            <table>
                <tr>
                    <td>Leírás: </td>
                    <td>
                        <textarea name="leiras_uj" cols="80" rows="10"><?php echo $kategoria['leiras'] ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td>Aktuális: </td>
                    <td>
<?php
                if ($kategoria['aktualis']){
?>
                        <input type="checkbox" checked name="aktualis_uj" value="1">
<?php                    
                }
                else {
?>
                        <input type="checkbox" name="aktualis_uj" value="1">
<?php                    
                }
?>                        
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="hidden" name="kat_id" value="<?php echo $kategoria['id'] ?>">
                        <input type="submit" name="modosit" value="Módosítom">
                    </td>
                </tr>
            </table>
        </form>
        <h3>Kategória törlése</h3>
        <form action="<?php print $_SERVER['PHP_SELF']; ?>" method="post">
            <input type="hidden" name="kat_torol" value="<?php echo $kategoria['id'] ?>">
            <input type="hidden" name="kat_neve" value="<?php echo $kategoria['neve'] ?>">
            <input type="submit" value="Törlöm">
        </form>
<?php        
            }
            else {
                $sql = "SELECT * FROM kategoria";
                $result = $conn -> query($sql);
                
?>
        <h2>Képkategória kiválasztása</h2>
        <p>Először ki kell választani a módosítandó kategóriát</p>
        <form action="<?php print $_SERVER['PHP_SELF']; ?>" method="post">
            <select  name="kat_valaszt">
                <option>Kategória kiválasztása</option>
<?php
                while ($egyKategoria = $result -> fetch_assoc()){
?>
                <option value="<?php echo $egyKategoria['id'] ?>"><?php echo $egyKategoria['neve'] ?></option>
<?php                
                }
?>                
            </select> &nbsp; 
            <input type="submit" value="Kiválasztom">
        </form>
<?php        
                
            }
        ?>
        <p><?php if (isset($uzen)) echo $uzen ?></p>
        <p><a href="index.php">Vissza a főoldalra</a></p>
    </body>
</html>
