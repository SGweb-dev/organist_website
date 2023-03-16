<?php
    include '../csatol/kapcsolat.php';
    session_start();
    if (!isset($_SESSION['admin'])) header("Location: ../index.php");
    
    //függvény a képek sorszámának cseréjére
    function csere($mit, $mire, $kat_id, $conn){
        $sql_mit = "SELECT id FROM kepek WHERE sorszam = '$mit' AND kategoria_id = '$kat_id'";
        $result_mit = $conn -> query($sql_mit);
        $mit_tomb = $result_mit -> fetch_array();
        $mit_id = $mit_tomb[0];
        $sql_mire = "SELECT id FROM kepek WHERE sorszam = '$mire' AND kategoria_id = '$kat_id'";
        $result_mire = $conn -> query($sql_mire);
        $mire_tomb = $result_mire -> fetch_array();
        $mire_id = $mire_tomb[0];
        
        $sql1 = "UPDATE kepek SET sorszam = '$mire' WHERE id = '$mit_id'";
        $result1 = $conn ->query($sql1);
        $sql2 = "UPDATE kepek SET sorszam = '$mit' WHERE id = '$mire_id'";
        $result2 = $conn -> query($sql2);
    }
    
    if (isset($_GET['sorsz'])){
        $kep_sorszam = $_GET['sorsz'];
        $kategoria_id = $_GET['kat_id'];
        $fel = $_GET['fel'];
        if ($fel == 1){
            csere($kep_sorszam, $kep_sorszam-1, $kategoria_id, $conn);
        }
        else {
            csere($kep_sorszam, $kep_sorszam+1, $kategoria_id, $conn);
        }
        
    }
    
    if (isset($_GET['torol'])){
        $torolKepek = $_GET['torol'];
        $max_sorszam = $_GET['max_sorszam'];
        foreach ($torolKepek as $egyTorol){  //sorszám átírás ide!!!
            $sql = "DELETE FROM kepek WHERE id = '$egyTorol'";
            $result = $conn -> query($sql);
        }
    }
    
    if (isset($_GET['kat_id'])){
        $kat_id = $_GET['kat_id'];
        $sql = "SELECT * FROM kepek WHERE kategoria_id = '$kat_id' ORDER BY sorszam";
        $kepek = $conn -> query($sql);
        $sql = "SELECT * FROM kategoria WHERE id = '$kat_id'";
        $result = $conn -> query($sql);
        $kategoria = $result -> fetch_assoc();
        $utso_sql = "SELECT MAX(sorszam) FROM kepek WHERE kategoria_id = '$kat_id'";
        $utso_keres = $conn -> query($utso_sql);
        $utso_sorszam = $utso_keres -> fetch_array();
        $max_sorszam = $utso_sorszam[0];
    }
    
?>

<!DOCTYPE html>
<html lang="hu">
    <head>
        <meta charset="UTF-8">
        <title>Képek módosítása</title>
        <link href="../stilus/admin.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <h1>Képek módosítása</h1>
        <?php
            if (isset($kepek)){
                
?>
        <form action="<?php print $_SERVER['PHP_SELF']; ?>" method="get" id="kepModosit">
            <table>
                <tr>
                    <th colspan="3"><?php echo $kategoria['neve'] ?></th>
                    <th class="torles">törlés</th>
                </tr>
<?php
                while ($kep = $kepek -> fetch_assoc()){
                    $forras = "../kepek/". $kategoria['neve'] . "/" . $kep['neve'];
?>
                <tr>
                    <td class="nyilak">
<?php
                if ($kep['sorszam'] > 1 ){
?>
                        <a href="<?php print $_SERVER['PHP_SELF']; ?>?sorsz=<?php echo $kep['sorszam'] ?>&kat_id=<?php echo $kat_id ?>&fel=1">&DoubleUpArrow;</a>   
<?php
                }
                else {
                    echo '&nbsp;';
                }
?>                        
                    </td>
                    <td class="kisKep">
                        <img src="<?php echo $forras ?>">
                    </td>
                    <td class="nyilak">
<?php
                if ($kep['sorszam'] < $max_sorszam ){
?>
                        <a href="<?php print $_SERVER['PHP_SELF']; ?>?sorsz=<?php echo $kep['sorszam'] ?>&kat_id=<?php echo $kat_id ?>&fel=0">&DoubleDownArrow;</a>
<?php                        
                }
                else {
                    echo '&nbsp;';
                }
?>                        
                    </td>
                    <td class="torles">
                        <input type="checkbox" name="torol[]" value="<?php echo $kep['id'] ?>">
                        <?php echo '<br>' . $kep['sorszam'] ?>
                    </td>
                </tr>
<?php                
                }
?>                
                <tr>
                    <td colspan="3"></td>
                
                    <td>
                        <input type="hidden" name="kat_id" value="<?php echo $kat_id ?>">
                        <input type="hidden" name="max_sorszam" value="<?php echo $max_sorszam ?>">
                        <input type="submit" value="Törlés">
                    </td>
                </tr>
            </table>
        </form>
<?php        
            }
            else {
                $sql = "SELECT * FROM kategoria";
                $kategoriak = $conn -> query($sql);
?>
        <p>Először a kategóriát kell kiválasztani:</p>
        <form action="<?php print $_SERVER['PHP_SELF']; ?>" method="get">
            <select name="kat_id">
                <option>kategória kiválasztása</option>
<?php
                while ($egyKat = $kategoriak -> fetch_assoc()){
?>
                <option value="<?php echo $egyKat['id'] ?>"><?php echo $egyKat['neve'] ?></option>
<?php                
                }
?>
            </select>
            <input type="submit" value="Kiválasztom">
        </form> 
<?php                
            }
        ?>
        <p><a href="index.php">Vissza a főoldalra</a></p>
    </body>
</html>