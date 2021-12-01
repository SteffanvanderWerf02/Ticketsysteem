<?php
include_once("connection.php");
if (isset($_POST['register'])) {
    if (isset($_POST['account']) && $acountType = filter_input(INPUT_POST, "account", FILTER_SANITIZE_NUMBER_INT)) {
        if ($acountType == 1) {
            if (isset($_POST['companyName'])) {
                $companyName = filter_input(INPUT_POST, "companyName", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                if (isset($_POST['kvkNumber'])) {
                    $kvk = filter_input(INPUT_POST, "kvkNumber", FILTER_SANITIZE_NUMBER_INT);
                } else {
                    echo "U kvk nummer bevat letters inplaats van cijfers";
                }
            } else {
                echo "De naam van u bedrijf bevat speciale tekens. Deze zijn niet toegestaan.";
            }
        }
    }

    if (isset($_POST['email']) && $email = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL)) {
        if (isset($_POST['username']) && $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_FULL_SPECIAL_CHARS)) {
            if (isset($_POST['password']) && $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_FULL_SPECIAL_CHARS)) {
                if (isset($_POST['postalcode']) && $postalcode = filter_input(INPUT_POST, "postalcode", FILTER_SANITIZE_FULL_SPECIAL_CHARS)) {
                    if (isset($_POST['city']) && $city = filter_input(INPUT_POST, "city", FILTER_SANITIZE_FULL_SPECIAL_CHARS)) {
                        if (isset($_POST['streetname']) && $streetname = filter_input(INPUT_POST, "streetname", FILTER_SANITIZE_FULL_SPECIAL_CHARS)) {
                            if (isset($_POST['housenumber']) && $housenumber = filter_input(INPUT_POST, "housenumber", FILTER_SANITIZE_FULL_SPECIAL_CHARS)) {
                                if (isset($_POST['phonenumber']) && $phonenumber = filter_input(INPUT_POST, "phonenumber", FILTER_SANITIZE_FULL_SPECIAL_CHARS)) {

                                    // Password hash
                                    $hash_password = password_hash($password, PASSWORD_DEFAULT);

                                    $stmt = mysqli_prepare($db, "
                                        SELECT 1
                                        FROM customer
                                        WHERE name = ?
                                    ") or die(mysqli_error($db));
                                    mysqli_stmt_bind_param($stmt, "s", $username);
                                    mysqli_stmt_execute($stmt) or die(mysqli_error($db));
                                    mysqli_stmt_store_result($stmt) or die(mysqli_error($db));
                                    if (mysqli_stmt_num_rows($stmt) == 0) {
                                        mysqli_stmt_close($stmt);
                                        $stmt = mysqli_prepare($db, "
                                            SELECT 1
                                            FROM customer
                                            WHERE email_adres = ?
                                        ") or die(mysqli_error($db));
                                        mysqli_stmt_bind_param($stmt, "s", $email);
                                        mysqli_stmt_execute($stmt) or die(mysqli_error($db));
                                        mysqli_stmt_store_result($stmt) or die(mysqli_error($db));

                                        if (mysqli_stmt_num_rows($stmt) == 0) {
                                            mysqli_stmt_close($stmt);
                                            if ($acountType == 1) {
                                                $stmt = mysqli_prepare($db, "
                                                    SELECT 1
                                                    FROM company
                                                    WHERE name = ?
                                                ") or die(mysqli_error($db));

                                                mysqli_stmt_bind_param($stmt, "s", $companyName);
                                                mysqli_stmt_execute($stmt) or die(mysqli_error($db));
                                                mysqli_stmt_store_result($stmt);
                                                if (mysqli_stmt_num_rows($stmt) == 0) {
                                                    mysqli_stmt_close($stmt);
                                                    // added company
                                                    $stmt = mysqli_prepare($db, "
                                                        INSERT
                                                        INTO company (
                                                            name,
                                                            postalcode,
                                                            house_number,
                                                            phone_number,
                                                            status,
                                                            kvk
                                                        )
                                                        VALUES 
                                                        (
                                                            ?,
                                                            ?,
                                                            ?,
                                                            ?,
                                                            0,
                                                            ?
                                                        )
                                                    ") or die(mysqli_error($db));
                                                    mysqli_stmt_bind_param($stmt, "ssssi", $companyName, $postalcode, $housenumber, $phonenumber, $kvk);
                                                    mysqli_stmt_execute($stmt) or die(mysqli_error($db));
                                                    mysqli_stmt_store_result($stmt) or die(mysqli_error($db));
                                                    mysqli_stmt_close($stmt);

                                                    // added user
                                                    $companyStmt = mysqli_prepare($db, "
                                                        SELECT id
                                                        FROM company
                                                        WHERE name = ?
                                                    ") or die(mysqli_error($db));
                                                    mysqli_stmt_bind_param($companyStmt, "s", $companyName);
                                                    mysqli_stmt_execute($companyStmt) or die(mysqli_error($db));
                                                    mysqli_stmt_bind_result($companyStmt, $companyId);
                                                    mysqli_stmt_fetch($companyStmt) or die(mysqli_error($db));
                                                    mysqli_stmt_close($companyStmt);
                                                    $stmt = mysqli_prepare($db, "
                                                        INSERT
                                                        INTO customer (
                                                            company_id,
                                                            name,
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
                                                            0
                                                        )
                                                    ") or die(mysqli_error($db));
                                                    mysqli_stmt_bind_param($stmt, "issssss", $companyId, $companyName, $postalcode, $housenumber, $phonenumber, $email, $hash_password);
                                                    mysqli_stmt_execute($stmt) or die(mysqli_error($db));
                                                    mysqli_stmt_close($stmt);
                                                    
                                                    
                                                    echo "U heeft een Zakelijk account aangemaakt deze word gecontroleerd";
                                                } else {
                                                    echo "Dit bedrijf bestaat al";
                                                }
                                            } else {
                                                $stmt = mysqli_prepare($db, "
                                                    INSERT
                                                    INTO customer (
                                                        company_id,
                                                        name,
                                                        postalcode,
                                                        house_number,
                                                        phone_number,
                                                        email_adres,
                                                        hash_password,
                                                        status
            
                                                    )
                                                    VALUES 
                                                    (
                                                        NULL,
                                                        ?,
                                                        ?,
                                                        ?,
                                                        ?,
                                                        ?,
                                                        ?,
                                                        1
                                                    )
                                                ") or die(mysqli_error($db));
                                                mysqli_stmt_bind_param($stmt, "ssssss", $username, $postalcode, $housenumber, $phonenumber, $email, $hash_password);
                                                mysqli_stmt_execute($stmt) or die(mysqli_error($db));
                                                mysqli_stmt_close($stmt);

                                                echo "U heeft een particulier account aangemaakt. U kunt inloggen";
                                            }
                                        } else {
                                            echo "Dit email adres bestaat al";
                                        }
                                    } else {
                                        echo "Deze naam bestaat al";
                                    }
                                } else {
                                    echo "Uw telefoonnummer bevat speciale tekens die niet zijn toegestaan.";
                                }
                            } else {
                                echo "Uw huisnummer bevat speciale tekens die niet zijn toegestaan.";
                            }
                        } else {
                            echo "Uw straatnaam bevat speciale tekens die niet zijn toegestaan.";
                        }
                    } else {
                        echo "Uw woonplaats bevat speciale tekens die niet zijn toegestaan.";
                    }
                } else {
                    echo "Uw postcode bevat speciale tekens die niet zijn toegestaan.";
                }
            } else {
                echo "Uw wachtwoord bevat speciale tekens die niet zijn toegestaan.";
            }
        } else {
            echo "Uw username bevat speciale tekens die niet zijn toegestaan.";
        }
    } else {
        echo "Uw emailadres bevat speciale tekens die niet zijn toegestaan.";
    }
}
?>
<!DOCTYPE html>
<html lang="nl" class="h-100">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="apple-touch-icon" sizes="180x180" href="./assets/img/favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="./assets/img/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="./assets/img/favicons/favicon-16x16.png">
    <link rel="manifest" href="./assets/img/favicons/site.webmanifest">
    <link rel="mask-icon" href="./assets/img/favicons/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">
    <link rel="shortcut icon" href="./assets/img/favicons/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,700&display=swap" rel="stylesheet">
    <title>Bottom up - Register</title>
</head>

<body class="h-100 bg-front">
    <div class="container h-100">
        <div class="row h-100">
            <div class="col-lg-12 d-flex h-100">
                <div class="login-container mx-auto">
                    <div class="card">
                        <div class="card-body">
                            <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
                                <div class="col-lg-12 text-center">
                                    <a href="./index.php"><img src="./assets/img/logo/newLogo.svg" style="height:150px;" alt="logo"></a>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-12">
                                        <label for="acount">
                                            Acount soort:
                                        </label>
                                    </div>
                                    <div class="col-lg-12">
                                        <label><input type="radio" <?= (isset($acountType) && $acountType == 0) ? "checked" : ""; ?> checked name="account" onclick="switchForm(this);" value="0" id="acount">Particulier</label>
                                        <label><input type="radio" <?= (isset($acountType) && $acountType == 1) ? "checked" : ""; ?> name="account" onclick="switchForm(this);" value="1" id="business">Zakelijk</label>
                                    </div>
                                </div>
                                <div class="row mb-2 buisness <?= (!isset($acountType)) ? "d-none" : ""; ?> <?= (isset($acountType) && $acountType == 0) ? "d-none" : ""; ?>">
                                    <div class="col-lg-12">
                                        <label for="companyName">
                                            Bedrijfsnaam
                                        </label>
                                    </div>
                                    <div class="col-lg-12">
                                        <input class="form-control" type="text" name="companyName" id="companyName">
                                    </div>
                                </div>
                                <div class="row mb-2 buisness <?= (!isset($acountType)) ? "d-none" : ""; ?>  <?= (isset($acountType) && $acountType == 0) ? "d-none" : ""; ?>">
                                    <div class="col-lg-12">
                                        <label for="kvkNumber">
                                            Kvk nummer
                                        </label>
                                    </div>
                                    <div class="col-lg-12">
                                        <input class="form-control" type="number" name="kvkNumber" id="kvkNumber">
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-12">
                                        <label for="email">
                                            E-mail
                                        </label>
                                    </div>
                                    <div class="col-lg-12">
                                        <input class="form-control" type="email" name="email" id="email">
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-12">
                                        <label for="username">
                                            Gebruikersnaam
                                        </label>
                                    </div>
                                    <div class="col-lg-12">
                                        <input class="form-control" type="text" name="username" id="username">
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-12">
                                        <label for="password">
                                            Wachtwoord
                                        </label>
                                    </div>
                                    <div class="col-lg-12">
                                        <input class="form-control" type="password" name="password" id="password">
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-12">
                                        <label for="phonenumber">
                                            Telefoon nummer
                                        </label>
                                    </div>
                                    <div class="col-lg-12">
                                        <input class="form-control" type="number" name="phonenumber" id="phonenumber">
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-6">
                                        <label for="postalcode">
                                            Postcode
                                        </label>
                                        <input class="form-control" type="text" name="postalcode" id="postalcode">
                                    </div>
                                    <div class="col-lg-6">
                                        <label for="city">
                                            Woonplaats
                                        </label>
                                        <input class="form-control" type="text" name="city" id="city">
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-6">
                                        <label for="streetname">
                                            Straatnaam
                                        </label>
                                        <input class="form-control" type="text" name="streetname" id="streetname">
                                    </div>
                                    <div class="col-lg-6">
                                        <label for="housenumber">
                                            Huisnummer + toevoeging
                                        </label>
                                        <input class="form-control" type="text" name="housenumber" id="housenumber">
                                    </div>
                                </div>
                                <div class="row mb-2 text-right">
                                    <div class="col-lg-12">
                                        <input class="btn btn-primary" value="Registeren" type="submit" name="register">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function switchForm(radio) {
            let elements = document.getElementsByClassName('buisness');
            if (radio.value == 0) {
                for (let index = 0; index < elements.length; index++) {
                    elements[index].classList.add('d-none');
                }
            } else {
                for (let index = 0; index < elements.length; index++) {
                    elements[index].classList.remove('d-none');
                }
            }
        }
    </script>
</body>

</html>