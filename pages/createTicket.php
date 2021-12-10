<?php
    include_once("../connection.php");

    if (isset($_POST['sendNewTicket'])) {
        if (isset($_SESSION['accountType'])) {
            $title = filter_input(INPUT_POST, 'createTicketTitle', FILTER_SANITIZE_SPECIAL_CHARS);
            $category = filter_input(INPUT_POST, 'createTicketCategory', FILTER_SANITIZE_SPECIAL_CHARS);
            $description = filter_input(INPUT_POST, 'createTicketDescription', FILTER_SANITIZE_SPECIAL_CHARS);
            $result = filter_input(INPUT_POST, 'createTicketResult', FILTER_SANITIZE_SPECIAL_CHARS);

            if ($_SESSION['accountType'] == 0) {
                $frequency = "unknown";
            } else {
                $frequency = filter_input(INPUT_POST, 'createTicketFrequency', FILTER_SANITIZE_SPECIAL_CHARS);
            }

            $priority = 0;
            $sub_category = "unknown";
            $status = 1;

            if (isset($title)) {
                if (isset($category)) {
                    if (isset($description)) {
                        if (isset($result)) {
                            if (isset($frequency) && $frequency == "unknown" || $frequency == "none" || $frequency == "day" || $frequency == "week" || $frequency == "month" || $frequency == "year") {
                                $sql = "INSERT INTO issue
                                            (
                                                company_id,
                                                priority,
                                                category,
                                                sub_category,
                                                title,
                                                `description`,
                                                result,
                                                `created at`,
                                                `closed at`,
                                                frequency,
                                                status_timestamp,
                                                `status`    
                                            ) VALUES (
                                                ?,
                                                ?,
                                                ?,
                                                ?,
                                                ?,
                                                ?,
                                                ?,
                                                NOW(),
                                                NULL,
                                                ?,
                                                NOW(),
                                                ?
                                            )
                                            ";
                        
                                $stmt = mysqli_prepare($db, $sql) or die(mysqli_error($db));
                                mysqli_stmt_bind_param($stmt, 'iisssssss', $_SESSION['companyId'], $priority, $category, $sub_category, $title, $description, $result, $frequency, $status);
                                mysqli_stmt_execute($stmt) or die(mysqli_error($db));
                                mysqli_stmt_close($stmt);
                            } else {
                                echo "de keuze bij de herhaalbaarheid valt niet onder de gegeven opties";
                            }
                        } else {
                            echo "het gewenste resultaat is niet ingevuld";
                        }
                    } else {
                        echo "de omschrijving van de ticket is niet ingevuld";
                    }
                } else {
                    echo "de category is niet ingevuld";
                }
            } else {
                echo "de titel van de ticket is niet ingevuld";
            }
        }
    }
?>
<!DOCTYPE HTML>
<html lang="nl">
    <head>
        <?php include_once("../components/head.html"); ?>
        <title>Bottom up - ticket toevoegen</title>
    </head>
    <body>
        <?php include_once("../components/header.html"); ?>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h1>Ticket Toevoegen</h1>
                </div>
                <div class="col-lg-12">
                    <form method="post" action="" enctype="multipart/form-data">
                        <div class="row">
                            <?php 
                                //if the account type in the session is set
                                if (isset($_SESSION['accountType'])) {
                            ?>
                                    <div class="col-lg-12">
                                        <label for="createTicketTitle">Titel</label>
                                        <input type="text" id="createTicketTitle" class="form-control-ticket-page" name="createTicketTitle" placeholder="text" />
                                    </div>
                                    <div class="col-lg-12">
                                        <label for="createTicketCategory">Categorie</label>
                                        <input type="text" id="createTicketCategory" class="form-control-ticket-page" name="createTicketCategory" placeholder="text" />
                                    </div>
                                    <div class="col-lg-12">
                                        <label for="createTicketDescription">Omschrijving</label>
                                        <textarea id="createTicketDescription" class="createDescriptionArea" name="createTicketDescription"></textarea>
                                    </div>
                                    <div class="col-lg-12">
                                        <label for="createTicketResult">Gewenst resultaat</label>
                                        <textarea id="createTicketResult" class="createDescriptionArea" name="createTicketResult"></textarea>
                                    </div>
                                    <div class="col-lg-12">
                                        <label for="createTicketFile">Bestand</label>
                                        <input type="file" id="createTicketFile" class="form-control-ticket-page" name="createTicketFile" />
                                    </div>
                            <?php
                                //if the authentication type is set to bussiness then show 
                                //the html below and if it is not set to bussiness then don't show
                                if ($_SESSION['accountType'] >= 1) {
                            ?>
                                    <div class="col-lg-12">
                                        <label for="createTicketFrequency">Ticket herhalen</label>
                                        <select id="createTicketFrequency" class="createTicketFrequency" name="createTicketFrequency">
                                            <option value="none">geen</option>
                                            <option value="day">dagelijks</option>
                                            <option value="week">weekelijks</option>
                                            <option value="month">maandelijks</option>
                                            <option value="year">jaarlijks</option>
                                        </select>
                                    </div>
                            <?php
                                }
                            ?>
                                    <div class="col-lg-12">
                                        <input type="submit" name="sendNewTicket" value="versturen" class="sendNewTicket" />
                                    </div>
                            <?php
                                }
                            ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php include_once("../components/footer.php"); ?>
    </body>
</html>