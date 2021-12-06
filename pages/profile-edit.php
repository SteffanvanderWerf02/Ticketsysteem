<?php
include_once("../connection.php")
?>

<!DOCTYPE html>
<html lang="en" class="h-100">
<head>
    <?php include_once("../components/head.html") ?>
    <title>Bottom up - Profiel</title>
</head>
<body class="h-100">
<!--Header include -->
<?php include_once("../components/header.html") ?>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h1>Gebruikers Informatie</h1>
        </div>
        <div class="col-lg-12">
            <a href="">Nieuwe gebruiker aanmaken</a>
        </div>
        <div class="col-lg-12">
            <form method="POST">
                <div class="row mb-3">
                    <div class="col-lg-12">
                        <label for="profilepic">Profiel foto</label>
                        <input type="file" id="profilepic" name="">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-lg-12">
                        <label for="username">Gebruikersnaam</label>
                        <input type="text" id="username" name="" class="form-control">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-lg-12">
                        <label for="password">Wachtwoord</label>
                        <input type="text" id="password" name="" class="form-control">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-lg-12">
                        <label for="postal">Postcode</label>
                        <input type="text" id="postal" name="" class="form-control">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-lg-6">
                        <label for="city">Woonplaats</label>
                        <input type="text" id="city" name="" class="form-control">
                    </div>
                    <div class="col-lg-6">
                        <label for="street">Straatnaam + huisnummer</label>
                        <input type="text" id="street" name="" class="form-control">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-lg-12">
                        <label for="kvk_number">KVK nummer</label>
                        <input type="text" id="form-kvk_number" name="" class="form-control">
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <input type="submit" value="Opslaan" class="btn btn-primary">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Footer include -->
<?php include_once("../components/footer.html") ?>
</body>
</html>
