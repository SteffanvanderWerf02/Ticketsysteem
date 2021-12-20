<?php
session_start();

define('ROOT_PATH', dirname(__DIR__) . '/');
define("MAIL_HEADERS", "MIME-Version: 1.0" . "\r\n" . "Content-type:text/html;charset=UTF-8" . "\r\n");

$acceptedFileTypes = ["image/jpg", "image/jpeg", "image/png", "image/gif", "application/pdf"];

mysqli_report(MYSQLI_REPORT_STRICT);

$connection = CONNECTION;
$database = DATABASE;
$username = USERNAME;
$password = PASSWORD;

try {
    // Create connection
    $db = new mysqli($connection, $username, $password, $database);
    // echo "You have connected to your database with MYSQLI";
} catch (Exception $e) {
    // error connection
    $error_message = $e->getMessage();
    echo $error_message;
    exit();
}
