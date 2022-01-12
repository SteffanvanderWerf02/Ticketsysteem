<?php
include_once("../config.php");
// Adding basic functions
include_once("../components/functions.php");
include_once("../connection.php");


// Checking if values have been set & filtering them
if (isset($_POST['submit'])) {
    if (isset($_POST['auth']) && $auth = filter_input(INPUT_POST, "auth", FILTER_SANITIZE_NUMBER_INT)) {
        if (isset($_POST['email']) && $email = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL)) {
            if (isset($_POST['username']) && $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_FULL_SPECIAL_CHARS)) {
                if (isset($_POST['password']) && $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_FULL_SPECIAL_CHARS)) {
                    if (isset($_POST['postalcode']) && $postalcode = filter_input(INPUT_POST, "postalcode", FILTER_SANITIZE_FULL_SPECIAL_CHARS)) {
                        if (isset($_POST['city']) && $city = filter_input(INPUT_POST, "city", FILTER_SANITIZE_FULL_SPECIAL_CHARS)) {
                            if (isset($_POST['streetname']) && $streetname = filter_input(INPUT_POST, "streetname", FILTER_SANITIZE_FULL_SPECIAL_CHARS)) {
                                if (isset($_POST['housenumber']) && $housenumber = filter_input(INPUT_POST, "housenumber", FILTER_SANITIZE_FULL_SPECIAL_CHARS)) {
                                    if (isset($_POST['phonenumber']) && $phonenumber = filter_input(INPUT_POST, "phonenumber", FILTER_SANITIZE_FULL_SPECIAL_CHARS)) {
                                        $stmt = mysqli_prepare($db, "
                                            SELECT 1
                                            FROM user
                                            WHERE name = ?
                                            AND email_adres = ?
                                        ") or die(mysqli_error($db));
                                        mysqli_stmt_bind_param($stmt, "ss", $username, $email);
                                        mysqli_stmt_execute($stmt) or die(mysqli_error($db));
                                        mysqli_stmt_store_result($stmt) or die(mysqli_error($db));
                                        if (mysqli_stmt_num_rows($stmt) == 0) {
                                            mysqli_stmt_close($stmt);

                                            // Password hash
                                            $hash_password = password_hash($password, PASSWORD_DEFAULT);
                                            $stmt = mysqli_prepare($db, "
                                                    INSERT
                                                    INTO user (
                                                        company_id,
                                                        auth_id,
                                                        name,
                                                        city,
                                                        streetname,
                                                        postalcode,
                                                        house_number,
                                                        phone_number,
                                                        email_adres,
                                                        hash_password,
                                                        status
                                                    )
                                                    VALUES 
                                                    (
                                                        ?,
                                                        ?,
                                                        ?,
                                                        ?,
                                                        ?,
                                                        ?,
                                                        ?,
                                                        ?,
                                                        ?,
                                                        ?,
                                                        1
                                                    )
                                                ") or die(mysqli_error($db));
                                            mysqli_stmt_bind_param($stmt, "iissssssss", $_SESSION['companyId'], $auth, $username, $city, $streetname, $postalcode, $housenumber, $phonenumber, $email, $hash_password);
                                            mysqli_stmt_execute($stmt) or die(mysqli_error($db));
                                            mysqli_stmt_close($stmt);
                                            $lastUserId = getLastId($db);
                                        } else {
                                            echo "<div class='alert alert-danger'>Deze naam of dit e-mailadres bestaat al.</div>";
                                        }
                                    } else {
                                        echo "<div class='alert alert-danger'>Uw telefoonnummer bevat speciale tekens die niet zijn toegestaan.</div>";
                                    }
                                } else {
                                    echo "<div class='alert alert-danger'>Uw huisnummer bevat speciale tekens die niet zijn toegestaan.</div>";
                                }
                            } else {
                                echo "<div class='alert alert-danger'>Uw straatnaam bevat speciale tekens die niet zijn toegestaan.</div>";
                            }
                        } else {
                            echo "<div class='alert alert-danger'>Uw woonplaats bevat speciale tekens die niet zijn toegestaan.</div>";
                        }
                    } else {
                        echo "<div class='alert alert-danger'>Uw postcode bevat speciale tekens die niet zijn toegestaan.</div>";
                    }
                } else {
                    echo "<div class='alert alert-danger'>Uw wachtwoord bevat speciale tekens die niet zijn toegestaan.</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>Uw username bevat speciale tekens die niet zijn toegestaan.</div>";
            }
        } else {
            echo "<div class='alert alert-danger'> Uw e-mailadres bevat speciale tekens die niet zijn toegestaan.</div>";
        }
    } else {
        echo "<div class='alert alert-danger'> U heeft de rechten niet correct ingevuld.</div>";
    }
    // Checking if a file has been uploaded
    if (checkIfFile("pfp")) {
        if (checkFileSize("pfp")) {
            if (checkFileType("pfp", $acceptedFileTypesPP)) {
                if (makeFolder($lastUserId, "../assets/img/pfpic/")) {
                    if (!checkFileExist("../assets/img/pfpic/" . $lastUserId . "/", $_FILES["pfp"]["name"])) {
                        if (deleteFile("../assets/img/pfpic/" . $lastUserId . "/")) {
                            // For Mac
                            if (OS) {
                                if (uploadFile($db, "pfp", "user", "profilepicture", "user_id", $lastUserId, "/assets/img/pfpic/" . $lastUserId . "/")) {
                                    echo "<div class='alert alert-success'>Uw profielfoto is succesvol geüpload</div>";
                                } else {
                                    echo "<div class='alert alert-danger'>Uw profielfoto is niet toegevoegd, probeer het opnieuw</div>";
                                }
                            } else {
                                // For Windows
                                if (uploadFile($db, "pfp", "user", "profilepicture", "user_id", $lastUserId, "../assets/img/pfpic/" . $lastUserId . "/")) {
                                    echo "<div class='alert alert-success'>Uw profielfoto is succesvol geüpload</div>";
                                } else {
                                    echo "<div class='alert alert-danger'>Uw profielfoto is niet toegevoegd, probeer het opnieuw</div>";
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
            <div class="col-lg-12">
                <?php
                ?>
                <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST" enctype="multipart/form-data">
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <label for="customFile" class="pointer">Profiel foto</label>
                            <div class="custom-file">
                                <input type="file" title="Kies uw profielfoto" name="pfp" class="custom-file-input" id="customFile">
                                <label class="custom-file-label" for="customFile">Kies Bestand</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <label for="auth">Rechten</label>
                            <select class="form-control" name="auth" id="auth">
                                <?php
                                if ($_SESSION['accountType'] == 3) {
                                    echo "<option value='3'>Beheer</option>";
                                    echo "<option value='4'>Issuemaster</option>";
                                } elseif ($_SESSION['accountType'] == 4) {
                                    echo "<option value='4'>Issuemaster</option>";
                                } else {
                                    echo "<option value='2'>Zakelijk</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <label for="username">Email</label>
                            <input type="email" id="username" value="" name="email" class="form-control" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <label for="username">Gebruikersnaam</label>
                            <input type="text" id="username" value="" name="username" class="form-control" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <label for="password">Wachtwoord</label>
                            <input type="password" id="password" name="password" class="form-control">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <label for="phonenumber">
                                Telefoon nummer
                            </label>
                        </div>
                        <div class="col-lg-12">
                            <input class="form-control" type="number" name="phonenumber" id="phonenumber" control-id="ControlID-8">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-6">
                            <label for="postal">Postcode</label>
                            <input type="text" id="postal" value="" name="postalcode" class="form-control" required>
                        </div>
                        <div class="col-lg-6">
                            <label for="city">Woonplaats</label>
                            <input type="text" id="city" value="" name="city" class="form-control" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-6">
                            <label for="street">Straatnaam</label>
                            <input type="text" id="street" value="" name="streetname" class="form-control" required>
                        </div>
                        <div class="col-lg-6">
                            <label for="housenumber">Huisnummer</label>
                            <input type="text" id="housenumber" value="" name="housenumber" class="form-control" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <input type="submit" name="submit" value="Opslaan" class="btn btn-primary">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Footer include -->
    <?php include_once("../components/footer.php") ?>
</body>

</html>