<?php
include_once("../config.php");
include_once("../connection.php");
include_once("../components/functions.php");
$acceptedFileTypesPP = ["image/jpg", "image/jpeg", "image/png", "image/gif"];

if (isset($_POST["deletepfp"]) && $id = filter_input(INPUT_POST, "deletepfp", FILTER_SANITIZE_NUMBER_INT)) {
    $stmt = mysqli_prepare($db, "
        UPDATE  user
        SET     profilepicture = NULL 
        WHERE   user_id = ?       
    ") or die(mysqli_error($db));
    mysqli_stmt_bind_param($stmt, "i", $id);
    if (mysqli_stmt_execute($stmt) && deleteFile("../assets/img/pfpic/" . $_SESSION["userId"] . "/")) {
        echo "Uw profielfoto is succesvol verwijderd";
    } else {
        echo "Er is iets fout gegaan probeer het nog een keer";
    }
}

// User update
if (isset($_POST['userSubmit'])) {
    if (isset($_POST['username']) && $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_SPECIAL_CHARS)) {
        if (isset($_POST['city']) && $city = filter_input(INPUT_POST, "city", FILTER_SANITIZE_SPECIAL_CHARS)) {
            if (isset($_POST['postal']) && $postal = filter_input(INPUT_POST, "postal", FILTER_SANITIZE_SPECIAL_CHARS)) {
                if (isset($_POST['street']) && $street = filter_input(INPUT_POST, "street", FILTER_SANITIZE_SPECIAL_CHARS)) {
                    if (isset($_POST['housenumber']) && $housenumber = filter_input(INPUT_POST, "housenumber", FILTER_SANITIZE_NUMBER_INT)) {
                        if (isset($_POST['phonenumber']) && $phonenumber = filter_input(INPUT_POST, "phonenumber", FILTER_SANITIZE_SPECIAL_CHARS)) {
                            if (isset($_POST['email']) && $email = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL)) {
                                $stmt = mysqli_prepare($db, "
                                UPDATE  user
                                SET     name = ?,
                                        city = ?,
                                        postalcode = ?,
                                        streetname = ?,
                                        house_number = ?,
                                        phone_number = ?,
                                        email_adres = ?
                                WHERE   user_id = ?       
                                ") or die(mysqli_error($db));
                                mysqli_stmt_bind_param($stmt, "ssssissi", $username, $city, $postal, $street, $housenumber, $phonenumber, $email, $_SESSION["userId"]);
                                mysqli_stmt_execute($stmt) or mysqli_error($db);
                            } else {
                                echo "<div class='alert alert-danger>Voer een geldig e-mail adres in</div>";
                            }
                        } else {
                            echo "<div class='alert alert-danger>Voer uw telefoonnummer in</div>";
                        }
                    } else {
                        echo "<div class='alert alert-danger>Voer uw huisnummer in</div>";
                    }
                } else {
                    echo "<div class='alert alert-danger>Voer uw sraatnaam in</div>";
                }
            } else {
                echo "<div class='alert alert-danger>Voer uw postcode in</div>";
            }
        } else {
            echo "<div class='alert alert-danger>Voer uw woonplaats in</div>";
        }
    } else {
        echo "<div class='alert alert-danger>Voer uw gebruikersnaam in</div>";
    }

    if (isset($_POST['password']) && $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS)) {
        $stmt = mysqli_prepare($db, "
        SELECT hash_password
        FROM user
        WHERE user_id = ?") or die(mysqli_error($db));
        mysqli_stmt_bind_param($stmt, 'i', $_SESSION["userId"]);
        mysqli_stmt_execute($stmt) or die(mysqli_error($db));
        mysqli_stmt_store_result($stmt) or die(mysqli_error($db));
        mysqli_stmt_bind_result($stmt, $hashPassword);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        if (password_verify($password, $hashPassword) == false) {
            $stmt = mysqli_prepare($db, "
            UPDATE user
            SET hash_password = ?
            WHERE user_id = ?") or die(mysqli_error($db));
            $newPassword = password_hash($password, PASSWORD_DEFAULT);
            mysqli_stmt_bind_param($stmt, 'si', $newPassword, $_SESSION['userId']);
            mysqli_stmt_execute($stmt) or die(mysqli_error($db));
            mysqli_stmt_close($stmt);
            echo "<div class='alert alert-success'>Wachtwoord succesvol aangepast</div>";
        } else {
            echo "<div class='alert alert-danger'>Het nieuw ingevoerde wachtwoord kan niet gelijk zijn aan het oude wachtwoord</div>";
        }
    }
    if (checkIfFile("pfp")) {
        if (checkFileSize("pfp")) {
            if (checkFileType("pfp", $acceptedFileTypesPP)) {
                if (makeUserFolder($_SESSION["userId"])) {
                    if (!checkFileExist("../assets/img/pfpic/" . $_SESSION["userId"] . "/", $_FILES["pfp"]["name"])) {
                        if (deleteFile("../assets/img/pfpic/" . $_SESSION["userId"] . "/")) {
                            if (uploadFile($db, "pfp", "user", "profilepicture", "user_id", $_SESSION["userId"], "../assets/img/pfpic/" . $_SESSION["userId"] . "/")) {
                                echo "<div class='alert alert-danger'>Uw profielfoto is succesvol geüpload</div>";
                            } else {
                                echo "Uw profielfoto is niet toegevoegd, probeer het opnieuw";
                            }
                        }
                    } else {
                        echo "<div class='alert alert-danger'>Uw profielfoto bestaat al</div>";
                    }
                }
            } else {
                echo "<div class='alert alert-danger'>Uw geüploadde bestand type wordt niet geaccepteerd. Er worden alleen jpg's, jpeg's, png's, en gif's geaccepteerd</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Uw geüploadde bestand is te groot</div>";
        }
    }
}

// Company update
if (isset($_POST['companySubmit'])) {
    if (isset($_POST['city']) && $city = filter_input(INPUT_POST, "city", FILTER_SANITIZE_SPECIAL_CHARS)) {
        if (isset($_POST['postal']) && $postal = filter_input(INPUT_POST, "postal", FILTER_SANITIZE_SPECIAL_CHARS)) {
            if (isset($_POST['street']) && $street = filter_input(INPUT_POST, "street", FILTER_SANITIZE_SPECIAL_CHARS)) {
                if (isset($_POST['housenumber']) && $housenumber = filter_input(INPUT_POST, "housenumber", FILTER_SANITIZE_NUMBER_INT)) {
                    if (isset($_POST['phonenumber']) && $phonenumber = filter_input(INPUT_POST, "phonenumber", FILTER_SANITIZE_SPECIAL_CHARS)) {
                        if (isset($_POST['email']) && $email = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL)) {
                            $stmt = mysqli_prepare($db, "
                                    UPDATE  company
                                    SET     city = ?,
                                            postalcode = ?,
                                            streetname = ?,
                                            house_number = ?,
                                            phone_number = ?,
                                            email_adres = ?
                                    WHERE   company_id = ?       
                                    ") or die(mysqli_error($db));
                            mysqli_stmt_bind_param($stmt, "sssissi", $city, $postal, $street, $housenumber, $phonenumber, $email, $_SESSION["companyId"]);
                            mysqli_stmt_execute($stmt) or mysqli_error($db);
                        } else {
                            echo "<div class='alert alert-danger'>Voer een geldig e-mail adres in</div>";
                        }
                    } else {
                        echo "<div class='alert alert-danger'>Voer uw telefoonnummer in</div>";
                    }
                } else {
                    echo "<div class='alert alert-danger'>Voer uw huisnummer in</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>Voer uw straatnaam in</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Voer uw postcode in</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Voer uw woonplaats in</div>";
    }
}


?>

<!DOCTYPE html>
<html lang="nl">

<head>
    <?php include_once("../components/head.html") ?>
    <title>Bottom up - Profiel</title>
</head>

<body>
    <!--Header include -->
    <?php include_once("../components/header.php") ?>
    <div id="content" class="container">
        <div class="row">
            <div class="col-lg-12">
                <h1>Gebruikers Informatie</h1>
            </div>
            <div class="col-lg-12 mb-3">
                <?php
                if ($_SESSION["accountType"] == 3 || $_SESSION["accountType"] == 2) {
                ?>
                    <a class="d-block mb-1" href="./addInternUser.php"><span class="material-icons align-middle">add</span>Nieuwe
                        gebruiker aanmaken</a>
                <?php
                }
                if ($_SESSION["accountType"] == 3) {
                    $stmt = mysqli_prepare($db, "
                    SELECT  count(company_id) as amount
                    FROM 	company
                    WHERE 	status = 0
                ") or die(mysqli_error($db));
                    mysqli_stmt_execute($stmt) or die(mysqli_error($db));
                    mysqli_stmt_store_result($stmt) or die(mysqli_error($db));
                    if (mysqli_stmt_num_rows($stmt) > 0) {
                        mysqli_stmt_bind_result($stmt, $amount);
                        mysqli_stmt_fetch($stmt);
                        mysqli_stmt_close($stmt);
                    } else {
                        $amount = 0;
                    }
                ?>
                    <a class="d-block" href="./companyApplication.php"><span class="badge badge-pill badge-primary"><?= $amount ?></span> Nieuwe Bedrijfaanvragen</a>
                <?php
                }
                ?>
            </div>
            <div class="col-lg-12">
                <?php
                $stmt = mysqli_prepare($db, "
                    SELECT  name, 
                            city,
		                    postalcode,
                            profilepicture,
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
                    mysqli_stmt_bind_result($stmt, $name, $city, $postalcode, $profilePicture, $streetname, $houseNumber, $phoneNumber, $email);
                    mysqli_stmt_fetch($stmt);
                    mysqli_stmt_close($stmt);
                } else {
                    echo "<div class='alert alert-danger'>Er zijn geen gebruikers gegevens beschikbaar</div>";
                }


                ?>
                <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST" enctype="multipart/form-data">
                    <div class="row mb-2">
                        <div class="col-lg-12">
                            <label for="customFile" class="pointer">Profiel foto</label>
                            <div class="custom-file">
                                <input type="file" title="Kies uw profielfoto" name="pfp" class="custom-file-input" id="customFile">
                                <label class="custom-file-label" for="customFile">Kies Bestand</label>
                            </div>
                        </div>
                    </div>
                    <?php
                    if ($profilePicture != NULL) {
                    ?>
                        <div class="row mb-3">
                            <div class="col-lg-2">
                                <img src="<?= $profilePicture ?>" class="profileprev" alt="Profiel Foto">

                            </div>
                            <div class="col-lg-10 my-auto">
                                <button type="submit" class="btn btn-primary btn-small" name="deletepfp" onclick="return confirm('Weet u zeker dat u dit wilt verwijderen?')" value="<?= $_SESSION["userId"] ?>">
                                    Verwijderen
                                </button>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
            <div class="row mb-3">
                <div class="col-lg-12">
                    <label for="username">Gebruikersnaam</label>
                    <input type="text" id="username" value="<?= $name ?>" name="username" class="form-control" readonly required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-lg-12">
                    <label for="username">Email</label>
                    <input type="email" id="email" value="<?= $email ?>" name="email" class="form-control" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-lg-12">
                    <label for="password">Wijzig Wachtwoord</label>
                    <input type="password" id="password" name="password" class="form-control">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-lg-12">
                    <label for="postal">Postcode</label>
                    <input type="text" id="postal" value="<?= $postalcode ?>" name="postal" class="form-control" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-lg-12">
                    <label for="city">Woonplaats</label>
                    <input type="text" id="city" value="<?= $city ?>" name="city" class="form-control" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-lg-6">
                    <label for="street">Straatnaam</label>
                    <input type="text" id="street" value="<?= $streetname ?>" name="street" class="form-control" required>
                </div>
                <div class="col-lg-6">
                    <label for="housenumber">Huisnummer</label>
                    <input type="number" id="housenumber" value="<?= $houseNumber ?>" name="housenumber" class="form-control" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-lg-12">
                    <label for="phonenumber">Telefoonnummer</label>
                    <input type="tel" id="phonenumber" value="<?= $phoneNumber ?>" name="phonenumber" class="form-control" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-lg-12">
                    <input type="submit" value="Gebruikergegevens opslaan" name="userSubmit" class="btn btn-primary">
                </div>
            </div>
            </form>
        </div>

        <?php
        if ($_SESSION['companyId'] != NULL) {
        ?>
            <div class="col-lg-12">
                <?php
                $stmt = mysqli_prepare($db, "
                SELECT  name, 
                        city,
                        postalcode,
                        streetname,
                        house_number,
                        phone_number,
                        email_adres,
                        kvk
                FROM 	company
                WHERE 	company_id = ?
                ") or die(mysqli_error($db));
                mysqli_stmt_bind_param($stmt, "i", $_SESSION["companyId"]);
                mysqli_stmt_execute($stmt) or die(mysqli_error($db));
                mysqli_stmt_store_result($stmt) or die(mysqli_error($db));
                if (mysqli_stmt_num_rows($stmt) > 0) {
                    mysqli_stmt_bind_result($stmt, $name, $city, $postalcode, $streetname, $houseNumber, $phoneNumber, $email, $kvk);
                    mysqli_stmt_fetch($stmt);
                    mysqli_stmt_close($stmt);
                } else {
                    echo "<div class='alert alert-danger'>Er zijn geen gebruikers gegevens beschikbaar</div>";
                }


                ?>
                <!-- Company information -->
                <div class="row mb-3">
                    <div class="col-lg-12">
                        <h1>Bedrijfsinformatie</h1>
                    </div>
                </div>
                <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST">
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <label for="username">Bedrijfsnaam</label>
                            <input type="text" id="username" value="<?= $name ?>" name="username" class="form-control" readonly required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <label for="kvk_number">KVK nummer</label>
                            <input type="number" id="kvk" value="<?= $kvk ?>" name="kvk" class="form-control" readonly required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <label for="username">Email</label>
                            <input type="email" id="email" value="<?= $email ?>" name="email" class="form-control" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <label for="postal">Postcode</label>
                            <input type="text" id="postal" value="<?= $postalcode ?>" name="postal" class="form-control" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <label for="city">Woonplaats</label>
                            <input type="text" id="city" value="<?= $city ?>" name="city" class="form-control" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-6">
                            <label for="street">Straatnaam</label>
                            <input type="text" id="street" value="<?= $streetname ?>" name="street" class="form-control" required>
                        </div>
                        <div class="col-lg-6">
                            <label for="housenumber">Huisnummer</label>
                            <input type="number" id="housenumber" value="<?= $houseNumber ?>" name="housenumber" class="form-control" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <label for="phonenumber">Telefoonnummer</label>
                            <input type="tel" id="phonenumber" value="<?= $phoneNumber ?>" name="phonenumber" class="form-control" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <input type="submit" value="Bedrijfsgegevens opslaan" name="companySubmit" class="btn btn-primary">
                        </div>
                    </div>
                </form>
            </div>
        <?php
        }
        ?>
    </div>
    </div>
    </div>
    </div>


    <!-- Footer include -->
    <?php include_once("../components/footer.php") ?>
</body>

</html>