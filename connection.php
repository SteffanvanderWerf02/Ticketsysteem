<?php
session_start();
mysqli_report(MYSQLI_REPORT_STRICT);

$connection = "localhost";
$database = "bottomup";
$username = "root";
$password = "root";

try {
    // Create connection
    $db= new mysqli($connection,$username,$password,$database);
    // echo "You have connected to your database with MYSQLI";
}catch(Exception $e) {
    // error connection
    $error_message = $e->getMessage();
    echo $error_message;
    exit();
}

?>