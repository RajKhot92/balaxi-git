<?php
    // $dbServerName = "162.241.148.36";
    // $dbUsername = "kluger3j_balaxi";
    // $dbPassword = "ed5{Ty7K!AIW";
    // $dbName = "kluger3j_balaxi";
    
    // $interval = 330;

    // // create connection
    // $conn = new mysqli($dbServerName, $dbUsername, $dbPassword, $dbName);

    // // check connection
    // if ($conn->connect_error) {
    //     die("Connection failed: " . $conn->connect_error);
    // }

    $dbServerName = "localhost";
    $dbUsername = "root";
    $dbPassword = "";
    $dbName = "balaxi";
    $interval = 330;

    // create connection
    $conn = new mysqli($dbServerName, $dbUsername, $dbPassword, $dbName);

    // check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
?>