<?php
    include 'csatol/kapcsolat.php';
    
?>

<!DOCTYPE html>
<html lang="hu">
    <head>
        <meta charset="UTF-8">
        <title>Összetett képgaléria</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <!-- enyém -->
        <link rel="stylesheet" href="style/style_galeria.css" />
        <link rel="icon" type="image/x-icon" href="imgboot/favicon.ico" />
    
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
   
   
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
      </head>
    <body>

   
    <?php
    $kat_sql = "SELECT * FROM kategoria";
    $kat_keres = $conn -> query($kat_sql);
    if ($kat_keres -> num_rows > 0){
    
        if ( isset($_GET['sorszam']) && isset($_GET['kategoria']) ){  //ha kattintottak egy kisképre 
            $kep_sorszam = $_GET['sorszam'];
            $kategoria_id = $_GET['kategoria'];
            
            //kép adatai az adatbázisból:
            $kep_sql = "SELECT * FROM kepek WHERE sorszam = '$kep_sorszam' AND kategoria_id = '$kategoria_id'";
            $kep_keres = $conn -> query($kep_sql);
            $egyKep = $kep_keres -> fetch_assoc();
            $kep_neve = $egyKep['neve'];
            $kep_kategoria_id = $egyKep['kategoria_id'];
            
            //max sorszám keresése:
            $sql = "SELECT MAX(sorszam) FROM kepek WHERE kategoria_id = '$kep_kategoria_id'";
            $result = $conn -> query($sql);
            $tomb = $result -> fetch_array();
            $sorszamMax = $tomb[0];
            
            //kategória lekérdezése
            $kategoria_sql = "SELECT * FROM kategoria WHERE id = '$kep_kategoria_id'";
            $kategoria_keres = $conn -> query($kategoria_sql);
            $egyKat = $kategoria_keres -> fetch_assoc();
            $kategoria_neve = $egyKat['neve'];

            $kep_teljes = "kepek/" . $kategoria_neve . "/" . $kep_neve;
            
           
             
?>
    
            <div id="nagykep">
                <table>
                    <tr>
                        <td class="lapoz">&nbsp;</td>
                        <td class="cim"><?php echo $kategoria_neve . " : " . $kep_sorszam . "/" . $sorszamMax ?></td>
                        <td class="lapoz cim"><a href="<?php print $_SERVER['PHP_SELF']; ?>">X</a></td>
                    </tr>
                    <tr>
                        <td class="cim">
<?php
    if ($kep_sorszam > 1){
        $ujSorszam = $kep_sorszam - 1;
    }
    else {
        $ujSorszam = $sorszamMax;
    }
?>
                        <a href="<?php print $_SERVER['PHP_SELF']; ?>?sorszam=<?php echo $ujSorszam ?>&kategoria=<?php echo $kep_kategoria_id ?>">&laquo;</a>                            
                        </td>
                        <td id=kepnagydoboz>
                           <img src="<?php echo $kep_teljes ?>"> 
                        </td>
                        <td class="cim">
<?php
    if ($kep_sorszam < $sorszamMax){
        $ujSorszam = $kep_sorszam + 1;
    }
    else {
        $ujSorszam = 1;
    }
?>
                        <a href="<?php print $_SERVER['PHP_SELF']; ?>?sorszam=<?php echo $ujSorszam ?>&kategoria=<?php echo $kep_kategoria_id ?>">&raquo;</a>                            
                        </td>
                    </tr>
                </table>
            </div>

<?php             
             
        }
        //else {
            while ($egyKat = $kat_keres->fetch_assoc()){
            
?>
            <div class="row" id="kiskepek">
                <div class="col">
                    <h3><?php echo $egyKat['neve'] ?></h3>
<?php
                $kat_id = $egyKat['id'];
                $kat_neve = $egyKat['neve'];
                $kepek_sql = "SELECT * FROM kepek WHERE kategoria_id = '$kat_id' ORDER BY sorszam";
                $kepek_keres = $conn ->query($kepek_sql);
                if ($kepek_keres -> num_rows > 0){
                    while ($kep = $kepek_keres -> fetch_assoc()){
                        $eleres = "kepek/" . $kat_neve . "/" . $kep['neve'];
                        $kepId = $kep['id'];
                        $kep_sorszam = $kep['sorszam'];
?>
                    <a href="<?php print $_SERVER['PHP_SELF']; ?>?sorszam=<?php echo $kep_sorszam ?>&kategoria=<?php echo $kat_id ?>"><img src="<?php echo $eleres ?>"></a>
<?php                    
                    } //kepek while ciklus vége
                } //igaz ág vége
                else {
 ?>
            
                    <p>Ebbe a kategóriába még nem töltöttek fel képeket.</p>
                
 <?php
                }  //else ág vége
 ?>
                    <p class="leiras"><?php echo $egyKat['leiras'] ?></p>
                </div>
            </div>         
 <?php           
            } //kategóriák while ciklus vége
        //}   //ha nincs GET['kepId'] ág vége 
        
    }
    else { //ha nincs még kategória az adatbázisban
?>
            <div class="row">
                <div class="col">
                    A galéria feltöltés alatt.
                </div>
            </div>
<?php            
    } // else ág vége
    
?>            
        </div>
    </body>
</html>
