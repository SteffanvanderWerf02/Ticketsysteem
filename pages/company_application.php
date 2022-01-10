<?php
include_once("../config.php");
// Adding basic functions
require_once("../components/functions.php");
include_once("../connection.php");

?>
<!DOCTYPE html>
<html lang="nl">

<head>
    <?php include_once("../components/head.html"); ?>
    <title>Bottom up - Bedrijf aanvragen</title>
</head>

<body>
    <!--Header include -->
    <?php include_once("../components/header.php");
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

        // Checking if there is a company with that Id
        if (mysqli_stmt_num_rows($stmt) > 0) { 
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);

            // Updating the status from company to active
            $stmt = mysqli_prepare($db, "
                UPDATE  company
                SET     status = 1
                WHERE   company_id = ?
            ") or die(mysqli_error($db));
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt) or die(mysqli_error($db));
            mysqli_stmt_close($stmt);

            // Sending a mail to the company that the account has been activated
            mail(
                $email,
                "Zakelijk bedrijf geactiveerd",
                "<h1>Geachte dhr/mevr {$name},</h1>
                <p>Wij willen u melden dat uw zakelijke bedrijfsaccount is gecontroleerd en goedgekeurd.</p>
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

        // Checking if there is an user with that Id
        if (mysqli_stmt_num_rows($stmt) > 0) { 
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);

            // Updating the status from user acount(s) from the registered company
            $stmt = mysqli_prepare($db, "
                UPDATE  user
                SET     status = 1
                WHERE   company_id = ?
            ") or die(mysqli_error($db));
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt) or die(mysqli_error($db));
            mysqli_stmt_close($stmt);

            // Sending a mail to the user that the account has been activated
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

        // Checking if there is a company with that Id
        if (mysqli_stmt_num_rows($stmt) > 0) { 
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);

            // Deleting the denied company
            $stmt = mysqli_prepare($db, "
                DELETE FROM company
                WHERE company_id = ?
            ") or die(mysqli_error($db));
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt) or die(mysqli_error($db));
            mysqli_stmt_close($stmt);

             // Sending a mail to the company that the account has been deleted and not activated
             mail(
                $email,
                "Zakelijk bedrijf afgekeurd",
                "<h1>Geachte dhr/mevr {$name},</h1>
                <p>Wij willen u melden dat uw zakelijke bedrijfsaccount is gecontroleerd en is afgekeurd.</p>
                <p>Dit kan komen door de volgende redenen.</p>
                <ul>
                    <li>Geen correct KVK nummer</li>
                    <li>Geen correcte adresgegevens</li>
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
        
        // Checking if there is an user with that Id
        if (mysqli_stmt_num_rows($stmt) > 0) { 
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
            
            // Updating the status from user acount(s) from the registered company
            $stmt = mysqli_prepare($db, "
                DELETE FROM user
                WHERE company_id = ?
            ") or die(mysqli_error($db));
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt) or die(mysqli_error($db));
            mysqli_stmt_close($stmt);

            // Sending a mail to the user that the account has not been activated and has been deleted
            mail(
                $userEmail,
                "Uw account is niet goedgekeurd",
                "<h1>Geachte dhr/mevr {$username},</h1>
                <p>Wij willen u melden dat uw zakelijke account niet is goedgekeurd.</p>
                <p>Dit komt doordat de gegevens van uw bedrijf niet kloppen.</p>
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
                <table cellspacing="0" cellpadding="0" class="table">
                    <thead>
                        <tr>
                            <th>Bedrijfsnaam</th>
                            <th>Kvk nummer</th>
                            <th>Woonplaats</th>
                            <th>Postcode</th>
                            <th>Straatnaam + huisnummer</th>
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
                                    postalcode,
                                    streetname,
                                    city,
                                    house_number                                    
                            FROM    company
                            WHERE   status = 0
                        ") or die(mysqli_error($db));
                        mysqli_stmt_execute($stmt) or die(mysqli_error($db));
                        mysqli_stmt_store_result($stmt) or die(mysqli_error($db));
                        mysqli_stmt_bind_result($stmt, $id, $name, $kvk, $postalcode, $streetname, $city, $housenumber);
                        if (mysqli_stmt_num_rows($stmt) > 0) {
                            while (mysqli_stmt_fetch($stmt)) {
                        ?>
                                <tr>
                                    <td><?= $name ?></td>
                                    <td><?= $kvk ?></td>
                                    <td><?= $city ?></td>
                                    <td><?= $postalcode ?></td>
                                    <td><?= $streetname . " " . $housenumber ?></td>
                                    <td class="text-center">
                                        <form action="<?=htmlspecialchars($_SERVER['PHP_SELF'])?>" method="POST">
                                            <button class="btn btn-denied" onclick="return confirm('Weet u zeker dat u dit bedrijf wilt afkeuren')" name="denied" value="<?= $id ?>" type="submit">Afkeuren</button>
                                        </form>
                                    </td>
                                    <td class="text-center">
                                        <form action="<?=htmlspecialchars($_SERVER['PHP_SELF'])?>" method="POST">
                                            <button class="btn btn-primary" onclick="return confirm('Weet u zeker dat u dit bedrijf wilt goedkeuren')" name="approve" value="<?= $id ?>" type="submit">Goedkeuren</button>
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