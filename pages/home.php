<?php 
include_once("../config.php");
include_once("../connection.php");
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <?php include_once("../components/head.html")?>
    <title>Bottom up - voorpagina</title>
</head>
<body>
    <!--Header include -->
    <?php include_once("../components/header.html")?>
    <?php
    echo var_dump($_SESSION);
    ?>
    <!-- Footer include -->
    <?php include_once("../components/footer.php")?>
</body>
</html>