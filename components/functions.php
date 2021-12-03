<?php
define('ROOT_PATH', dirname(__DIR__) . '/');
define("MAIL_HEADERS", "MIME-Version: 1.0" . "\r\n"."Content-type:text/html;charset=UTF-8" . "\r\n");

function printArray($array)
{
    echo '<pre>';
    print_r($array);
    echo '</pre>';
}
?>