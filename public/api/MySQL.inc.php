<?php
    $hostname = "localhost:3306";
    $database = "Website_execrise";
    $username = "Website";
    $password = "6y805maCvrfZS65p";
    
    $conn = isset($conn) ? $conn : mysqli_connect($hostname, $username, $password, $database) or die("Failed: " . mysqli_connect_error());
?>