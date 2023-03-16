<?php
    include '../csatol/kapcsolat.php';
    session_start();
    
    if ( isset($_POST['azon']) && isset($_POST['jelszo']) ){
        $azon = $_POST['azon'];
        $jelszo = md5($_POST['jelszo']);
        $sql_admin = "SELECT * FROM admin WHERE azon = '$azon' AND jelszo = '$jelszo'";
        $keres = $conn->query($sql_admin);
        if ($keres->num_rows > 0){
            $_SESSION['admin'] = $azon;
        }
    }
    
    if (isset($_GET['kijelent'])){
        session_destroy();
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <h1>Admin oldal</h1>
<?php
    if(isset($_SESSION['admin'])){
?>
        <h2>Admin funkciók</h2>
        <p><a href="kepFeltolt.php">Képek feltöltése</a></p>
        <p><a href="kategoriaModosit.php">Kategóriák módosítása</a></p>
        <p><a href="kep_modosit.php">Képek módosítása</a></p>
        <p><a href="admin_kezel.php">Adminok kezelése</a></p>
        <p><a href="<?php print $_SERVER['PHP_SELF']; ?>?kijelent=1">Kijelentkezés</a></p>
        <p><a href="../index.php">Vissza a honlap főoldalára</a></p>
<?php                
    }
    else{
?>
        <h2>Bejelentkezés</h2>
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
                    <td colspan="2"><input type="submit" value="Bejelentkezés" name="be"></td>
                </tr>
            </table>
        </form>
<?php
    }
            
        ?>
    </body>
</html>
