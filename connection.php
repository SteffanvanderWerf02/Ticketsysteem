<?php
session_start();

define('ROOT_PATH', dirname(__DIR__) . '/');
define("MAIL_HEADERS", "MIME-Version: 1.0" . "\r\n" . "Content-type:text/html;charset=UTF-8" . "\r\n");

$acceptedFileTypes = ["image/jpg", "image/jpeg", "image/png", "image/gif", "application/pdf"];
$acceptedFileTypesPP = ["image/jpg", "image/jpeg", "image/png", "image/gif"];
mysqli_report(MYSQLI_REPORT_STRICT);


try {
    // Create connection
    $db = new mysqli(CONNECTION, USERNAME,PASSWORD,DATABASE);
     //echo "You have connected to your database with MYSQLI";
} catch (Exception $e) {
    // error connection
    $error_message = $e->getMessage();
    echo $error_message;
    exit();
}
