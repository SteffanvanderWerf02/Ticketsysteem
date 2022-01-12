<?php
include_once("../config.php");
include_once("../components/functions.php");
include_once("../connection.php");
?>
<!DOCTYPE html>
<html lang="nl" class="h-100">

<head>
    <?php include_once("../components/head.html") ?>
    <title>Bottom up - 403</title>
</head>
<body class="h-100 bg-front">
    <div class="container h-100">
        <div class="row h-100">
            <div class="col-lg-12 d-flex h-100">
                <div class="login-container mx-auto">
                    <div class="card">
                        <div class="card-body text-center">
                            <a href="../index.php"><img class="img-403" src="../error/error_img/stop-maar-stop.gif" alt="403"></a>
                            <h1 class="error-title">403</h1>
                            <h3>Forbidden</h3>
                            <p>U heeft geen toegang deze server</p>
                            <a  class="dec-underline" href="../index.php"><span class="material-icons align-middle">login</span> Klik hier om in te loggen</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>