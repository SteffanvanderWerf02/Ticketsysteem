<?php

session_start();

define('ROOT_PATH', dirname(__DIR__) . '/');
define("MAIL_HEADERS", "MIME-Version: 1.0" . "\r\n" . "Content-type:text/html;charset=UTF-8" . "\r\n");

$acceptedFileTypes = ["image/jpg", "image/jpeg", "image/png", "image/gif", "application/pdf"];
$acceptedFileTypesPP = ["image/jpg", "image/jpeg", "image/png", "image/gif"];

mysqli_report(MYSQLI_REPORT_STRICT);

$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
try {
    // Create connection
    $db = new mysqli(CONNECTION, USERNAME, PASSWORD, DATABASE);
    CheckAcces(isset($_SESSION['loggedIn']), $actual_link);
    
} catch (Exception $e) {
    // Error connection
    $error_message = $e->getMessage();
    echo $error_message;
    exit();
}
