<?php
include_once("./config.php");
include_once("./components/functions.php");
include_once("./connection.php");


// Delete all the session data when the logout button is pressed
if (isset($_POST['logout'])) {
    session_destroy();
    ?>
    <div class="alert alert-success">
            U bent succesvol uitgelogd.
    </div>
    <?php
}
// Checking if the username and password are correct & filtering
if (isset($_POST['login'])) {
    if (isset($_POST['username']) && $username = filter_input(INPUT_POST, "username", FILTER_DEFAULT)) {
        if (isset($_POST['password']) && $password = filter_input(INPUT_POST, "password", FILTER_DEFAULT)) {
            $stmt = mysqli_prepare($db, "
                SELECT  user_id,
                        name,
                        auth_id,
                        company_id,
                        hash_password,
                        status
                FROM    user
                WHERE name = ?
            ") or die(mysqli_error($db));
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt) or die(mysqli_error($db));
            mysqli_stmt_store_result($stmt) or die(mysqli_error($db));
            mysqli_stmt_bind_result($stmt, $userId, $username, $authId, $companyId, $hash_password, $status);
            mysqli_stmt_fetch($stmt);
            if (mysqli_stmt_num_rows($stmt) > 0) {
                mysqli_stmt_close($stmt);
                if (password_verify($password, $hash_password)) {
                    if ($companyId == NULL) {
                        $_SESSION["loggedIn"] = true;
                        $_SESSION["userId"] = $userId;
                        $_SESSION["accountType"] = $authId; // 0 = Private Account 
                        $_SESSION["companyId"] = NULL;
                        header("Location: ./pages/ticket_overview.php");                     
                    } else if ($companyId == 1) {
                        $stmt = mysqli_prepare($db, "
                            SELECT  user.status
                            FROM user
                            WHERE user_id = ?
                        ") or die(mysqli_error($db));
                        mysqli_stmt_bind_param($stmt, "i", $userId);
                        mysqli_stmt_execute($stmt) or die(mysqli_error($db));
                        mysqli_stmt_store_result($stmt) or die(mysqli_error($db));
                        mysqli_stmt_bind_result($stmt, $customerStatus);
                        mysqli_stmt_fetch($stmt);
                        if (mysqli_stmt_num_rows($stmt) > 0) {
                            if ($customerStatus == 1) {
                                mysqli_stmt_close($stmt);
                                $_SESSION["loggedIn"] = true;
                                $_SESSION["userId"] = $userId;
                                $_SESSION["accountType"] = $authId; // 2 = Bottom Up User
                                $_SESSION["companyId"] = $companyId;

                                header("Location: ./pages/ticket_overview.php");  
                                
                            } else {
                                echo "<div class='alert alert-danger'>De gebruikersnaam of het wachtwoord is niet correct of uw account is nog niet actief</div>";
                            }
                        }
                    } else {
                        $stmt = mysqli_prepare($db, "
                            SELECT  user.status,
                                    company.status
                            FROM    user
                            INNER JOIN company
                            ON company.company_id = user.company_id
                            WHERE user.user_id = ?
                        ") or die(mysqli_error($db));
                        mysqli_stmt_bind_param($stmt, "i", $userId);
                        mysqli_stmt_execute($stmt) or die(mysqli_error($db));
                        mysqli_stmt_store_result($stmt) or die(mysqli_error($db));
                        mysqli_stmt_bind_result($stmt, $customerStatus, $companyStatus);
                        mysqli_stmt_fetch($stmt);
                        if (mysqli_stmt_num_rows($stmt) > 0) {
                            if ($customerStatus == 1 && $companyStatus == 1) {
                                mysqli_stmt_close($stmt);
                                $_SESSION["loggedIn"] = true;
                                $_SESSION["userId"] = $userId;
                                $_SESSION["accountType"] = $authId; // 1 = Corporate Account
                                $_SESSION["companyId"] = $companyId;

                                header("Location: ./pages/ticket_overview.php");
                            } else {
                                echo "<div class='alert alert-danger'>De gebruikersnaam of het wachtwoord is niet correct of uw account is nog niet actief</div>";
                            }
                        } else {
                            echo "<div class='alert alert-danger'>De gebruikersnaam of het wachtwoord is niet correct of uw account is nog niet actief</div>";
                        }
                    }
                } else {
                    echo "<div class='alert alert-danger'>De gebruikersnaam of het wachtwoord is niet correct of uw account is nog niet actief</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>De gebruikersnaam of het wachtwoord is niet correct of uw account is nog niet actief</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Uw wachtwoord is niet ingevuld</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Uw gebruikersnaam is niet ingevuld</div>";
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
    <title>Bottom up - Inloggen</title>
</head>

<body class="h-100 bg-front">
    <div class="container h-100">
        <div class="row h-100">
            <div class="col-lg-12 d-flex h-100">
                <div class="login-container mx-auto">
                    <div class="card">
                        <div class="card-body">
                            <form action="<?=htmlspecialchars($_SERVER['PHP_SELF'])?>" method="POST">
                                <div class="col-lg-12 text-center mt-3 mb-3 ">
                                    <img src="./assets/img/logo/newLogo.svg" style="height:150px;" alt="logo">
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 mb-1">
                                        <label for="login-email">
                                            Gebruikersnaam
                                        </label>
                                    </div>
                                    <div class="col-lg-12 mb-1">
                                        <input id="login-email" type="text" placeholder="Gebruikersnaam" name="username" class="form-control">
                                    </div>
                                    <div class="col-lg-12 mb-1">
                                        <label for="login-password">
                                            Wachtwoord
                                        </label>
                                    </div>
                                    <div class="col-lg-12 mb-1">
                                        <input id="login-password" type="password" placeholder="Wachtwoord" name="password" class="form-control">
                                    </div>
                                    <div class="col-lg-12 mb-1">
                                        <a href="./pages/password_forget.php">Wachtwoord vergeten?</a>
                                        <button type="submit" name="login" class="btn btn-primary ml-2 mt-3 float-right">
                                            <span class="material-icons align-middle">lock</span>
                                            Login
                                        </button>
                                        <a href="./register.php" class="btn btn-primary mt-3 float-right">
                                            <span class="material-icons align-middle">add</span>
                                            Registeren
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>