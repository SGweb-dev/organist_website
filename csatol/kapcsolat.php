<?php

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "galeria";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    $conn->query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
    
?>