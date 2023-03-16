<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
            include '../csatol/kapcsolat.php';
            $sql = "SELECT MAX(id) FROM kepek";
            $result = $conn -> query($sql);
            $tomb = $result -> fetch_array();
            if ($tomb[0] != null){
                echo $tomb[0];
            }
            else {
                echo 'a tábla üres';
            }
            
            $sql = "SELECT kepek.id, kepek.neve, kepek.sorszam, kategoria.id, kategoria.neve FROM kepek INNER JOIN kategoria ON kepek.kategoria_id = kategoria.id";
            $result = $conn -> query($sql);
            while ($sor = $result -> fetch_assoc()){
                var_dump($sor);
            }
        ?>
    </body>
</html>
