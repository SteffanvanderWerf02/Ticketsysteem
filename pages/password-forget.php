<?php
include_once("../connection.php");
// adding basic functions
require_once("../components/functions.php");


if (isset($_POST['passwordConfirm'])) {
    if (isset($_POST['email_adres']) && $email = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL)) {
        if (isset($_POST['password']) && $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS)) {
            if (isset($_POST['repeat-password']) && $repeatPassword = filter_input(INPUT_POST, "repeat-password", FILTER_SANITIZE_SPECIAL_CHARS)) {
                
            }else{
                echo "Uw heeft het veld herhaal wachtwoord niet correct ingevuld";
            }
        }else{
            echo "Uw heeft het veld wachtwoord niet correct ingevuld";
        }
    }else{
        echo "Uw heeft het veld email adres niet correct ingevuld";
    }
}

if (isset($_POST['generateToken'])) {
    if (isset($_POST['email']) && $email = filter_input(INPUT_POST, "email_adres", FILTER_VALIDATE_EMAIL)) {

        $stmt = mysqli_prepare($db, "
            SELECT  user_id,
                    name
            FROM user
            WHERE email_adres = ?
        ") or die(mysqli_error($db));
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt) or die(mysqli_error($db));
        mysqli_stmt_store_result($stmt) or die(mysqli_error($db));

        if (mysqli_stmt_num_rows($stmt) > 0) {
            mysqli_stmt_bind_result($stmt, $userId, $name);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
            
            //Generate a random string.
            $token = openssl_random_pseudo_bytes(16);
            
            //Convert the binary data into hexadecimal representation.
            $token = bin2hex($token);
            $t = time() + 3600 * 24;
            $expireDate = date("Y-m-d h:m:s", $t);
            
            // update token into Database
            $stmt = mysqli_prepare($db, "
            UPDATE user
            SET passwordForget_token = ?,
                token_expireDate = ?
            WHERE user_id = ?
            ") or die(mysqli_error($db));
            mysqli_stmt_bind_param($stmt, "ssi", $token, $expireDate, $userId);
            mysqli_stmt_execute($stmt) or die(mysqli_error($db));
            mysqli_stmt_close($stmt);

            // send mail to user to change password
            mail(
                $email,
                "Wachtwoord veranderen",
                "<h1>Geachte dhr/mevr {$name},</h1>
                <p>U kunt u wachtwoord veranderen met de volgende linkje</p>
                <p>http://localhost/" . PROJECT_PATH . "pages/password-forget.php?token={$token}</p>
                <br>
                <p>Met vriendelijke groet,</p>
                <p>Bottom up</p>
                ",
                MAIL_HEADERS
            );
        } else {
            echo "Deze gebruiker niet bekent.";
        }
    }else {
        echo "U heeft het veld email adres niet ingevuld";
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
                                if (!isset($_GET['token'])) {
                                ?>
                                    <div class="row">
                                        <div class="col-lg-12 mb-1">
                                            <label for="login-email">
                                                Vul hier u gekoppelde email adres in
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
                                } else {
                                ?>
                                    <div class="row">
                                        <div class="col-lg-12 mb-1">
                                            <label for="email">
                                                Email
                                            </label>
                                        </div>
                                        <div class="col-lg-12 mb-1">
                                            <input id="email" type="email" placeholder="Wachtwoord" name="email" class="form-control">
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