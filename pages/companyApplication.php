<?php
include_once("../connection.php");
// adding basic functions
require_once("../components/functions.php");
?>
<!DOCTYPE html>
<html lang="nl">

<head>
    <?php include_once("../components/head.html"); ?>
    <title>Bottom up - Bedrijf aanvragen</title>
</head>

<body>
    <!--Header include -->
    <?php include_once("../components/header.html");
    if (isset($_POST['approve']) && $id = filter_input(INPUT_POST, "approve", FILTER_VALIDATE_INT)) {
        $stmt = mysqli_prepare($db, "
            SELECT  email_adres,
                    name
            FROM    company
            WHERE   company_id = ?
        ") or die(mysqli_error($db));
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt) or die(mysqli_error($db));
        mysqli_stmt_store_result($stmt) or die(mysqli_error($db));
        mysqli_stmt_bind_result($stmt, $email, $name);

        if (mysqli_stmt_num_rows($stmt) > 0) { //check if there is an company with that Id
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);

            // updates the status from company to active
            $stmt = mysqli_prepare($db, "
                UPDATE  company
                SET     status = 1
                WHERE   company_id = ?
            ") or die(mysqli_error($db));
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt) or die(mysqli_error($db));
            mysqli_stmt_close($stmt);

            // send mail to company that account is activated
            mail(
                $email,
                "Zakelijk bedrijf geactiveerd",
                "<h1>Geachte dhr/mevr {$name},</h1>
                <p>Wij willen u melden dat uw zakelijke bedrijfs account is gecontroleerd en goed gekeurd.</p>
                <br>
                <p>Met vriendelijke groet,</p>
                <p>Bottom up</p>
                ",
                MAIL_HEADERS
            );
        }
        $stmt = mysqli_prepare($db, "
            SELECT  email_adres,
                    name
            FROM    user
            WHERE   company_id = ?
        ") or die(mysqli_error($db));
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt) or die(mysqli_error($db));
        mysqli_stmt_store_result($stmt) or die(mysqli_error($db));
        mysqli_stmt_bind_result($stmt, $userEmail, $username);

        //check if there is an user with that Id
        if (mysqli_stmt_num_rows($stmt) > 0) { 
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);

            // updates the status from user acount(s) from the activated company
            $stmt = mysqli_prepare($db, "
                UPDATE  user
                SET     status = 1
                WHERE   company_id = ?
            ") or die(mysqli_error($db));
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt) or die(mysqli_error($db));
            mysqli_stmt_close($stmt);

            // send mail to user that account is activated
            mail(
                $userEmail,
                "Uw account is geactiveerd",
                "<h1>Geachte dhr/mevr {$username},</h1>
                <p>Wij willen u melden dat uw account is geactiveerd.</p>
                <p>U kunt nu inloggen met uw inlog gegevens.</p>
                <br>
                <p>Met vriendelijke groet,</p>
                <p>Bottom up</p>
                ",
                MAIL_HEADERS
            );
    ?>
            <div class="alert alert-success">
                Het bedrijf en zijn gebruiker(s) zijn succesvol op actief gezet.
            </div>
        <?php
        }
    }

    if (isset($_POST['denied']) && $id = filter_input(INPUT_POST, "denied", FILTER_VALIDATE_INT)) {

        $stmt = mysqli_prepare($db, "
            SELECT  email_adres,
                    name
            FROM    company
            WHERE   company_id = ?
        ") or die(mysqli_error($db));
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt) or die(mysqli_error($db));
        mysqli_stmt_store_result($stmt) or die(mysqli_error($db));
        mysqli_stmt_bind_result($stmt, $email, $name);

        //check if there is an company with that Id
        if (mysqli_stmt_num_rows($stmt) > 0) { 
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);

            // Delete the denied company
            $stmt = mysqli_prepare($db, "
                DELETE FROM company
                WHERE company_id = ?
            ") or die(mysqli_error($db));
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt) or die(mysqli_error($db));
            mysqli_stmt_close($stmt);

             // send mail to company that account is deleted and not activated
             mail(
                $email,
                "Zakelijk bedrijf afgekeurd",
                "<h1>Geachte dhr/mevr {$name},</h1>
                <p>Wij willen u melden dat uw zakelijke bedrijfsaccount is gecontroleerd en is afgekeurd.</p>
                <p>Dit kan komen door de volgende redenen.</p>
                <ul>
                    <li>Geen correcte KVK</li>
                    <li>Geen correcte adres gegevens</li>
                </ul>
                <p>Wij verzoeken u om u opnieuw te registeren.</p>
                <br>
                <p>Met vriendelijke groet,</p>
                <p>Bottom up</p>
                ",
                MAIL_HEADERS
            );
        }

        $stmt = mysqli_prepare($db, "
            SELECT  email_adres,
                    name
            FROM    user
            WHERE   company_id = ?
        ") or die(mysqli_error($db));
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt) or die(mysqli_error($db));
        mysqli_stmt_store_result($stmt) or die(mysqli_error($db));
        mysqli_stmt_bind_result($stmt, $userEmail, $username);
        
        //check if there is an user with that Id
        if (mysqli_stmt_num_rows($stmt) > 0) { 
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
            
            // updates the status from user acount(s) from the activated company
            $stmt = mysqli_prepare($db, "
                DELETE FROM user
                WHERE company_id = ?
            ") or die(mysqli_error($db));
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt) or die(mysqli_error($db));
            mysqli_stmt_close($stmt);

            // send mail to user that account is not activated and deleted
            mail(
                $userEmail,
                "Uw account is niet goedgekeurd",
                "<h1>Geachte dhr/mevr {$username},</h1>
                <p>Wij willen u melden dat uw zakelijke account niet is goedgekeurd.</p>
                <p>Dit is omdat u gegevens van u bedrijf niet kloppen.</p>
                <p>Wij verzoeken u om u opnieuw te registeren.</p>
                <br>
                <p>Met vriendelijke groet,</p>
                <p>Bottom up</p>
                ",
                MAIL_HEADERS
            );
        ?>
            <div class="alert alert-success">
                Het bedrijf en zijn gebruiker(s) zijn succesvol verwijderd.
            </div>
    <?php
        }
    }
    ?>

    <div class="container" id="content">
        <div class="row">
            <div class="col-lg-12">
                <h1>Bedrijf aanvragen</h1>
            </div>
            <div class="col-lg-12">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Bedrijfsnaam</th>
                            <th>Kvk nummer</th>
                            <th>Postcode</th>
                            <th class="text-center"><span class="material-icons align-middle red">clear</span></th>
                            <th class="text-center"><span class="material-icons align-middle green">done</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = mysqli_prepare($db, "
                            SELECT  company_id,
                                    name,
                                    kvk,
                                    postalcode                                    
                            FROM    company
                            WHERE   status = 0
                        ") or die(mysqli_error($db));
                        mysqli_stmt_execute($stmt) or die(mysqli_error($db));
                        mysqli_stmt_store_result($stmt) or die(mysqli_error($db));
                        mysqli_stmt_bind_result($stmt, $id, $name, $kvk, $postalcode);
                        if (mysqli_stmt_num_rows($stmt) > 0) {
                            while (mysqli_stmt_fetch($stmt)) {
                        ?>
                                <tr>
                                    <td><?= $name ?></td>
                                    <td><?= $kvk ?></td>
                                    <td><?= $postalcode ?></td>
                                    <td class="text-center">
                                        <form action="<?= $_SERVER["PHP_SELF"] ?>" method="POST">
                                            <button class="btn btn-denied" onclick="return confirm('Weet u zeker of dat u dit bedrijf wil afkeuren')" name="denied" value="<?= $id ?>" type="submit">Afkeuren</button>
                                        </form>
                                    </td>
                                    <td class="text-center">
                                        <form action="<?= $_SERVER["PHP_SELF"] ?>" method="POST">
                                            <button class="btn btn-primary" onclick="return confirm('Weet u zeker of dat u dit bedrijf wil goedkeuren')" name="approve" value="<?= $id ?>" type="submit">Goedkeuren</button>
                                        </form>
                                    </td>
                                </tr>
                        <?php
                            }
                        } else {
                            echo "
                                <tr>
                                    <td colspan='5'>Er zijn geen nieuwe aanvragen</td>
                                </tr>
                            ";
                        }
                        mysqli_stmt_close($stmt);
                        ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Footer include -->
    <?php include_once("../components/footer.php"); ?>
</body>

</html>