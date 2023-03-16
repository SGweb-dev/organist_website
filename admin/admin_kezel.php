<?php
    include '../csatol/kapcsolat.php';
    session_start();
    if (!isset($_SESSION['admin'])) header("Location: ../index.php");
    
    $sql = "SELECT * FROM admin";
    $result = $conn->query($sql);
    //$uzen = "";
    
    if ( isset($_POST['azon']) && isset($_POST['jelszo']) ){
        $azon = $_POST['azon'];
        $jelszo = md5($_POST['jelszo']);
        $sql_admin = "SELECT * FROM admin WHERE azon = '$azon'";
        $keres = $conn->query($sql_admin);
        if ($keres->num_rows > 0){
            $uzen = "Ez az admin már szerepel az adatbázisban.";
        }
        else {
            $sql_rogzit = "INSERT INTO admin (azon, jelszo) VALUES ('$azon', '$jelszo')";
            $admin_rogzit = $conn->query($sql_rogzit);
            if ($admin_rogzit) $uzen = "Az admin rögzítése sikeres.";
            else $uzen = "Nem sikerült az admin rögzítése.";
        }
    }
    
    if (isset($_POST['admin'])){
        $admin_azon = $_POST['admin'];
    }
    
    if (isset($_POST['jelszo_uj'])){
        $jelszo_uj = md5($_POST['jelszo_uj']);
        $azon = $_POST['azon_modosit'];
        $sql_mod = "UPDATE admin SET jelszo = '$jelszo_uj' WHERE azon = '$azon'";
        $result_mod = $conn -> query($sql_mod);
        if ($result_mod) $uzen = "Az admin jelszó módosítva.";
        else $uzen = "Az admin jelszavát nem sikerült módosítani.";
    }
    
    if (isset($_POST['azon_torol'])){
        $azon = $_POST['azon_torol'];
        $sql_torol = "DELETE FROM admin WHERE azon = '$azon'";
        $result_torol = $conn -> query($sql_torol);
        if ($result_torol) $uzen = "Az admin törölve.";
        else $uzen = "Az admin törlése sikertelen.";
    }
?>

<!DOCTYPE html>
<html lang="hu">
    <head>
        <meta charset="UTF-8">
        <title>Adminisztráció</title>
    </head>
    <body>
<?php
    if (!isset($admin_azon)){
        $sql = "SELECT * FROM admin";
        $adminok = $conn -> query($sql);
?>        
        <h2>Admin felvétele</h2>
        <form action="<?php print $_SERVER['PHP_SELF']; ?>" method="post">
            <table>
                <tr>
                    <td>Azon: </td>
                    <td><input type="text" name="azon" size="20"></td>
                </tr>
                <tr>
                    <td>Jelszó: </td>
                    <td><input type="password" name="jelszo" size="20"></td>
                </tr>
                <tr>
                    <td colspan="2"><input type="submit" value="Mentés" name="ment"></td>
                </tr>
            </table>
        </form>
        <h2>Admin módosítása</h2>
        <p>Első lépésben ki kell választani az admint:</p>
        <form action="<?php print $_SERVER['PHP_SELF']; ?>" method="post">
            <select name="admin">
                <option>Admin kiválasztása</option>
<?php
        while ($admin = $adminok -> fetch_assoc()){
?>
                <option value="<?php echo $admin['azon'] ?>"><?php echo $admin['azon'] ?></option>
<?php                
        }    
?>                
            </select> &nbsp;
            <input type="submit" value="Kiválasztom">
        </form>
<?php
    }
    else {
?>
        <h2>Admin jelszó módosítása</h2>
        <form action="<?php print $_SERVER['PHP_SELF']; ?>" method="post">
            <p><?php echo $admin_azon ?></p>
            <p>Új jelszó: <input type="text" name="jelszo_uj" size="30"></p>
            <input type="hidden" name="azon_modosit" value="<?php echo $admin_azon ?>">
            <p><input type="submit" value="Módosítom"></p>
        </form>
        <h2>Admin törlése</h2>
        <form action="<?php print $_SERVER['PHP_SELF']; ?>" method="post">
            <p><?php echo $admin_azon ?></p>
            <input type="hidden" name="azon_torol" value="<?php echo $admin_azon ?>">
            <p><input type="submit" value="Törlöm"></p>
        </form>
<?php        
    }
?>                
        <p><?php if (isset($uzen)) echo $uzen ?></p>
        <p><a href="index.php">Vissza a főoldalra</a></p>
    </body>
</html>
