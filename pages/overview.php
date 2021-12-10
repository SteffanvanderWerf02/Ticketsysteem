<?php
include_once ("../connection.php")
?>
<!DOCTYPE html>
<html lang="nl" class="h-100">
<head>
    <?php include_once("../components/head.html")?>
    <title>Bottom up - Overzicht</title>
</head>
<body class="h-100">
    <!--Header include -->
    <?php include_once("../components/header.html")?>
    <div id="overviewContent" class="container h-100">
        <div class="row h-100">
            <div class="col-lg-4 my-auto">
                <div class="card square mx-auto justify-content-center">
                    <a href="#" class="fill-div">Ticket aanmaken</a>
                </div>
            </div>
            <div class="col-lg-4 my-auto">
                <div class="card square mx-auto justify-content-center">
                    <a href="#" class="fill-div">Dienst / Service aanvragen</a>
                </div>
            </div>
            <div class="col-lg-4 my-auto">
                <div class="card square mx-auto justify-content-center">
                    <a href="#" class="fill-div">Product aanvraag</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer include -->
    <?php include_once("../components/footer.php")?>
</body>
</html>