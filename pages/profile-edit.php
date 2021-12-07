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
            <div class="col-lg-12 mb-3">
                <a href=""><span class="material-icons align-middle">add</span>Nieuwe gebruiker aanmaken</a>
            </div>
            <div class="col-lg-6">
                <?php
                $stmt = mysqli_prepare($db, "
                    SELECT  name, 
                            city,
		                    postalcode,
                            streetname,
                            house_number,
                            phone_number,
                            email_adres
                    FROM 	user
                    WHERE 	user_id = ?
                ") or die(mysqli_error($db));
                mysqli_stmt_bind_param($stmt, "i", $_SESSION["userId"]);
                mysqli_stmt_execute($stmt) or die(mysqli_error($db));
                mysqli_stmt_store_result($stmt) or die(mysqli_error($db));
                if (mysqli_stmt_num_rows($stmt) > 0) {
                    mysqli_stmt_bind_result($stmt, $name, $city, $postalcode, $streetname, $houseNumber, $phoneNumber, $email);
                    mysqli_stmt_fetch($stmt);
                    mysqli_stmt_close($stmt);
                } else {
                    echo "Er zijn geen gebruikers gegevens beschikbaar";
                }


                ?>
                <form method="POST">
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <label for="customFile" class="pointer">Profiel foto</label>
                            <div class="custom-file">
                                <input type="file" title="Kies uw profielfoto" class="custom-file-input" id="customFile">
                                <label class="custom-file-label" for="customFile">Kies Bestand</label>
                            </div>
                            <script>
                                $(".custom-file-input").on("change", function() {
                                    var fileName = $(this).val().split("\\").pop();
                                    $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
                                });
                            </script>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <label for="username">Gebruikersnaam</label>
                            <input type="text" id="username" value="<?= $name ?>" name="" class="form-control" readonly required>
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
                            <input type="text" id="postal" value="<?= $postalcode ?>" name="" class="form-control">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-6">
                            <label for="city">Woonplaats</label>
                            <input type="text" id="city" value="<?= $city ?>" name="" class="form-control">
                        </div>
                        <div class="col-lg-6">
                            <label for="street">Straatnaam + huisnummer</label>
                            <input type="text" id="street" value="<?= $streetname . " " . $houseNumber ?>" name="" class="form-control">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <label for="kvk_number">KVK nummer</label>
                            <input type="text" id="form-kvk_number" name="" class="form-control">
                        </div>
                    </div>
                    <div class="row mb-3">
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