<?php
    // if (session_status() == PHP_SESSION_NONE) {
    //     session_start(); 
    // }
    $host = 'localhost';
    $dbname = 'oes';
    $db_user = 'root';
    $db_password = '';

    $conn = new mysqli($host, $db_user, $db_password, $dbname);

    if (!$conn->connect_error) 
    {
        // echo "connect";
    }
    else{
        echo "error";
    }
?>
