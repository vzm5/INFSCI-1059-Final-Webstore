<?php
    //Connect to DB
    $servername = "";
    $server_username = "";
    $server_password = "";
    $dbname = "";

    $conn = new mysqli($servername, $server_username, $server_password, $dbname);
    if($conn->connect_error){
      die ("connection failed: " . $conn->connect_error);
    }
?>