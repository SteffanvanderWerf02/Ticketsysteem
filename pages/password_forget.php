<?php
include_once("../config.php");
// Adding basic functions
include_once("../components/functions.php");
include_once("../connection.php");

if (isset($_POST['passwordConfirm'])) {
    if (isset($_POST['email']) && $email = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL)) {
        if (isset($_POST['password']) && $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS)) {
            if (isset($_POST['repeat-password']) && $repeatPassword = filter_input(INPUT_POST, "repeat-password", FILTER_SANITIZE_SPECIAL_CHARS)) {
                if (isset($_POST['token']) && $token = filter_input(INPUT_POST, "token", FILTER_SANITIZE_SPECIAL_CHARS)) {
                    $stmt = mysqli_prepare($db, "
                        SELECT  token_expireDate
                        FROM    user
                        WHERE   passwordForget_token = ?
                                AND email_adres = ?
                    ") or die(mysqli_error($db));
                    mysqli_stmt_bind_param($stmt, "ss", $token, $email) or mysqli_error($db);
                    mysqli_stmt_execute($stmt) or mysqli_error($db);
                    mysqli_stmt_store_result($stmt);
                    if (mysqli_stmt_num_rows($stmt) > 0) {
                        mysqli_stmt_bind_result($stmt, $expireDate);
                        mysqli_stmt_fetch($stmt);
                        mysqli_stmt_close($stmt);
                        $date_now = date("Y-m-d h:i:s"); // This format is string comparable
                        if ($date_now < $expireDate) {
                            if ($password == $repeatPassword) {
                                $hashPassword = password_hash($password, PASSWORD_DEFAULT);
                                $stmt = mysqli_prepare($db, "
                                UPDATE  user
                                SET     hash_password = ?,
                                        passwordForget_token = NULL,
                                        token_expireDate = NULL
                                WHERE   passwordForget_token = ?
                            ") or die(mysqli_error($db));
                                mysqli_stmt_bind_param($stmt, "ss", $hashPassword, $token) or mysqli_error($db);
                                mysqli_stmt_execute($stmt) or mysqli_error($db);
                                mysqli_stmt_close($stmt);
                                echo "<div class='alert alert-success'>Uw nieuwe wachtwoord is succesvol aangepast</div>";
                            } else {
                                echo "<div class='alert alert-danger'>Uw wachtwoorden komen niet overeen</div>";
                            }
                        } else {
                            echo "<div class='alert alert-danger'>Uw token is niet correct of is verlopen</div>";
                        }
                    } else {
                        echo "<div class='alert alert-danger'>Uw token is niet correct of is verlopen</div>";
                    }
                } else {
                    echo "<div class='alert alert-danger'>Uw token is niet correct of is verlopen</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>U heeft het veld herhaal wachtwoord niet correct ingevuld</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>U heeft het veld wachtwoord niet correct ingevuld</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>U heeft het veld e-mailadres niet correct ingevuld</div>";
    }
}

// Checking if the e-mail address is valid
if (isset($_POST['generateToken'])) {
    if (isset($_POST['email_adres']) && $email = filter_input(INPUT_POST, "email_adres", FILTER_VALIDATE_EMAIL)) {
        $stmt = mysqli_prepare($db, "
            SELECT  user_id,
                    name
            FROM    user
            WHERE   email_adres = ?
        ") or die(mysqli_error($db));
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt) or die(mysqli_error($db));
        mysqli_stmt_store_result($stmt) or die(mysqli_error($db));

        if (mysqli_stmt_num_rows($stmt) > 0) {
            mysqli_stmt_bind_result($stmt, $userId, $name);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);

            //Generating a random string
            $token = openssl_random_pseudo_bytes(16);

            //Converting the binary data to hexadecimal representation
            $token = bin2hex($token);
            $t = time() + 3600 * 24;
            $expireDate = date("Y-m-d h:m:s", $t);

            // Updating the token in the Database
            $stmt = mysqli_prepare($db, "
            UPDATE user
            SET passwordForget_token = ?,
                token_expireDate = ?
            WHERE user_id = ?
            ") or die(mysqli_error($db));
            mysqli_stmt_bind_param($stmt, "ssi", $token, $expireDate, $userId);
            mysqli_stmt_execute($stmt) or die(mysqli_error($db));
            mysqli_stmt_close($stmt);

            // Sending a mail to the user to change their password
            mail(
                $email,
                "Wachtwoord veranderen",
                "<h1>Geachte dhr/mevr {$name},</h1>
                <p>U kunt u wachtwoord veranderen met de volgende link</p>
                <p>http://localhost/" . PROJECT_PATH . "pages/password_forget.php?token={$token}</p>
                <br>
                <p>Met vriendelijke groet,</p>
                <p>Bottom up</p>
                ",
                MAIL_HEADERS
            );
            echo "<div class='alert alert-success'>Uw wachtwoord vergeten token is succesvol naar uw mail verzonden</div>";
        } else {
            echo "<div class='alert alert-danger'>Deze gebruiker is niet bekend</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>U heeft het veld e-mailadres niet ingevuld</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="nl" class="h-100">

<head>
    <?php include_once("../components/head.html"); ?>
    <title>Bottom up - Wachtwoord veranderen</title>
</head>

<body class="h-100 bg-front">
    <div class="container h-100">
        <div class="row h-100">
            <div class="col-lg-12 d-flex h-100">
                <div class="login-container mx-auto">
                    <div class="card">
                        <div class="card-body">
                            <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
                                <div class="col-lg-12 text-center mt-3 mb-3 ">
                                    <a href="../index.php">
                                        <img src="../assets/img/logo/newLogo.svg" style="height:150px;" alt="logo">
                                    </a>
                                </div>
                                <?php
                                // Giving the option to request a password change
                                if (!isset($_GET['token'])) {
                                ?>
                                    <div class="row">
                                        <div class="col-lg-12 mb-1">
                                            <label for="login-email">
                                                Vul hier uw gekoppelde email adres in
                                            </label>
                                        </div>
                                        <div class="col-lg-12 mb-1">
                                            <input id="login-email" type="email" placeholder="E-mail" name="email_adres" class="form-control">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12 mb-1">
                                            <button type="submit" name="generateToken" class="btn btn-primary ml-2 mt-3 float-right">
                                                <span class="material-icons align-middle">lock</span>
                                                Vraag aan
                                            </button>
                                        </div>
                                    </div>
                                <?php
                                // Giving the option to change your password
                                } else {
                                ?>
                                    <input type="hidden" name="token" value="<?= $_GET["token"] ?>">
                                    <div class="row">
                                        <div class="col-lg-12 mb-1">
                                            <label for="email">
                                                Email
                                            </label>
                                        </div>
                                        <div class="col-lg-12 mb-1">
                                            <input id="email" type="email" placeholder="Email" name="email" class="form-control">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12 mb-1">
                                            <label for="password">
                                                Wachtwoord
                                            </label>
                                        </div>
                                        <div class="col-lg-12 mb-1">
                                            <input id="password" type="password" placeholder="Wachtwoord" name="password" class="form-control">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12 mb-1">
                                            <label for="repeat-password">
                                                Herhaal wachtwoord
                                            </label>
                                        </div>
                                        <div class="col-lg-12 mb-1">
                                            <input id="repeat-password" type="password" placeholder="herhaal wachtwoord" name="repeat-password" class="form-control">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12 mb-1">
                                            <button type="submit" name="passwordConfirm" class="btn btn-primary ml-2 mt-3 float-right">
                                                <span class="material-icons align-middle">lock</span>
                                                Opslaan
                                            </button>
                                        </div>
                                    </div>
                                
                                <?php
                                }
                                ?>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>