<?php
include_once ("../config.php");
// Adding basic functions
include_once("../components/functions.php");
include_once ("../connection.php");
?>
<!DOCTYPE html>
<html lang="nl" class="h-100">
<head>
    <?php include_once("../components/head.html")?>
    <title>Bottom up - Overzicht</title>
</head>
<body class="h-100">
    <!--Header include -->
    <?php include_once("../components/header.php")?>
    <div id="content" class="container h-100">
        <div class="row h-100">
            <div class="col-lg-4 my-auto">
                <div class="card square mx-auto justify-content-center">
                    <a href="./create_issue.php?issueType=Ticket" class="fill-div">Ticket aanvraag</a>
                </div>
            </div>
            <div class="col-lg-4 my-auto">
                <div class="card square mx-auto justify-content-center">
                    <a href="./create_issue.php?issueType=Dienst/service" class="fill-div">Dienst/Service aanvraag</a>
                </div>
            </div>
            <div class="col-lg-4 my-auto">
                <div class="card square mx-auto justify-content-center">
                    <a href="./create_issue.php?issueType=Product" class="fill-div">Product aanvraag</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer include -->
    <?php include_once("../components/footer.php")?>
</body>
</html>