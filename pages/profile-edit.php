<?php
include_once("../config.php");
include_once("../connection.php");
?>

<!DOCTYPE html>
<html lang="nl">

<head>
    <?php include_once("../components/head.html") ?>
    <title>Bottom up - Profiel</title>
</head>

<body>
    <!--Header include -->
    <?php include_once("../components/header.html") ?>
    <div id="content" class="container">
        <div class="row">
            <div class="col-lg-12">
                <h1>Gebruikers Informatie</h1>
            </div>
            <div class="col-lg-12 mb-3">
                <a class="d-block mb-1" href=""><span class="material-icons align-middle">add</span>Nieuwe gebruiker aanmaken</a>
                <?php
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
                }else {
                    $amount = 0;
                }
                ?>
                <a class="d-block" href="./companyApplication.php"><span class="badge badge-pill badge-primary"><?=$amount?></span> Nieuwe Bedrijfaanvragen</a>
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
                
                //1. ga na of er op submit is gedrukt
                //2. filter inputs
                //3. bind de ingevulde gegevens
                //4. update de database
                

if(isset($_POST['submit'])){
    if (isset($_POST['username']) && $_POST['username'] = filter_input(INPUT_POST, "username", FILTER_SANITIZE_SPECIAL_CHARS)){
       if(isset($_POST['city']) && $_POST['city'] = filter_input(INPUT_POST, "city", FILTER_SANITIZE_SPECIAL_CHARS)){
          if(isset($_POST['postal']) && $_POST['postal'] = filter_input(INPUT_POST, "postal", FILTER_SANITIZE_SPECIAL_CHARS)){
               if(isset($_POST['street']) && $_POST['street'] = filter_input(INPUT_POST, "street", FILTER_SANITIZE_SPECIAL_CHARS)){
                 if(isset($_POST['housenumber']) && $_POST['housenumber'] = filter_input(INPUT_POST, "housenumber", FILTER_SANITIZE_SPECIAL_CHARS)){
                    if(isset($_POST['phonenumber']) && $_POST['phonenumber'] = filter_input(INPUT_POST, "phonenumber", FILTER_SANITIZE_SPECIAL_CHARS)){
                        if(isset($_POST['email']) && $_POST['email'] = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL)){
                                $stmt = mysqli_prepare($db, "
                                UPDATE  user
                                SET     name = ?,
                                        city = ?,
                                        postcalcode = ?,
                                        streetname = ?,
                                        house_numbr = ?,
                                        phone_number = ?,
                                        mail_adres = ?
                                ") or die(mysqli_error($db));
                                mysqli_stmt_bind_param($stmt, "ssssiss", $_POST['username'], $_POST['city'], $_POST['postal'], $_POST['street'], $_POST['housenumber'], $_POST['phonenumber'], $_POST['email']);
                                mysqli_stmt_execute($stmt) or mysqli_error($db);
                            } else{
                                echo "Voer een geldig e-mail adres in";
                            }   
                       } else{
                            echo "Voer uw telefoonnummer in";
                        }   
                    } else{
                        echo "Voer uw huisnummer in";
                    }     
                } else{
                    echo "Voer uw sraatnaam in";
                }    
            } else{
                echo "voer uw postcode in";
            }       
        } else{
            echo "Voer uw woonplaats in";
        }           
    } else{
        echo "voer uw gebruikersnaam in";
    }    
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
                            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
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
                            <label for="username">Email</label>
                            <input type="email" id="email" value="<?=$email?>" name="email" class="form-control" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <label for="username">Gebruikersnaam</label>
                            <input type="text" id="username" value="<?=$name?>" name="username" class="form-control" readonly required>
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
                            <input type="text" id="housenumber" value="<?= $houseNumber ?>" name="housenumber" class="form-control" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <label for="phonenumber">Telefoonnummer</label>
                            <input type="text" id="phonenumber" value="<?= $phoneNumber ?>" name="phonenumber" class="form-control" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <label for="kvk_number">KVK nummer</label>
                            <input type="text" id="kvk_number" name="kvk_nummer" class="form-control" required>
                        </div>
                    </div>
              
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <input type="submit" value="Opslaan" name="submit" class="btn btn-primary">
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