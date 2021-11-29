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
                            <form action="<?=$_SERVER['PHP_SELF']?>" method="POST">
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
                                        <input id="login-email" type="text" placeholder="Gebruikersnaam"  name="username" class="form-control">
                                    </div>
                                    <div class="col-lg-12 mb-1">
                                        <label for="login-password">
                                            Wachtwoord
                                        </label>
                                    </div>
                                    <div class="col-lg-12 mb-1">
                                        <input id="login-password" type="password" placeholder="Wachtwoord"  name="password " class="form-control">
                                    </div>
                                    <div class="col-lg-12 mb-1">
                                        <a href="./forgot-password.php">Wachtwoord vergeten?</a>
                                        <button type="submit" name="login" class="btn btn-primary ml-2 mt-3 float-right">
                                            <span class="material-icons align-middle">lock</span>
                                            Login
                                        </button>
                                        <a href="./register.php" class="btn btn-primary mt-3 float-right">
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