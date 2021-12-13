<?php
    include_once("../connection.php");

    $issueType = filter_input(INPUT_GET, 'issueType', FILTER_SANITIZE_SPECIAL_CHARS);

    if (isset($_POST['sendNewTicket'])) {
        if (isset($_SESSION['accountType'])) {
            $title = filter_input(INPUT_POST, 'createIssueTitle', FILTER_SANITIZE_SPECIAL_CHARS);
            $subCategory = filter_input(INPUT_POST, 'createIssueCategory', FILTER_SANITIZE_SPECIAL_CHARS);
            $description = filter_input(INPUT_POST, 'createIssueDescription', FILTER_SANITIZE_SPECIAL_CHARS);
            $result = filter_input(INPUT_POST, 'createIssueResult', FILTER_SANITIZE_SPECIAL_CHARS);

            if ($_SESSION['accountType'] == 0) {
                $frequency = "unknown";
            } else {
                $frequency = filter_input(INPUT_POST, 'createIssueFrequency', FILTER_SANITIZE_SPECIAL_CHARS);
            }

            $priority = 0;
            $status = 1;

            if (isset($title)) {
                if (isset($category)) {
                    if (isset($description)) {
                        if (isset($result)) {
                            if (isset($frequency) && $frequency == "unknown" || $frequency == "none" || $frequency == "day" || $frequency == "week" || $frequency == "month" || $frequency == "year") {
                                $sql = "INSERT INTO issue
                                            (
                                                `user_id`,
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
                                                ?,
                                                NOW(),
                                                NULL,
                                                ?,
                                                NOW(),
                                                ?
                                            )
                                            ";
                        
                                $stmt = mysqli_prepare($db, $sql) or die(mysqli_error($db));
                                mysqli_stmt_bind_param($stmt, 'iiissssssi', $_SESSION['userId'], $_SESSION['companyId'], $priority, $issueType, $subCategory, $title, $description, $result, $frequency, $status);
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
                    <h1><?php echo ucFirst($issueType) . " "; ?>Toevoegen</h1>
                </div>
                <div class="col-lg-12">
                    <form method="post" action="" enctype="multipart/form-data">
                        <div class="row">
                            <?php
                                if (isset($issueType)) {
                                    if ($issueType == "ticket") { 
                                        //if the account type in the session is set
                                        if (isset($_SESSION['accountType'])) {
                            ?>
                                            <div class="col-lg-12">
                                                <label for="createIssueTitle">Titel</label>
                                                <input type="text" id="createIssueTitle" class="form-control-issue-page" name="createIssueTitle" placeholder="text" />
                                            </div>
                                            <div class="col-lg-12">
                                                <label for="createIssueCategory">sub-category</label>
                                                <select id="createIssueCategory" name="createIssueCategory" class="createIssueCategory">
                                                    <option value="klacht">klacht</option>
                                                    <option value="feedback">feedback</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-12">
                                                <label for="createIssueDescription">Omschrijving</label>
                                                <textarea id="createIssueDescription" class="createDescriptionArea" name="createIssueDescription"></textarea>
                                            </div>
                                            <div class="col-lg-12">
                                                <label for="createIssueResult">Gewenst resultaat</label>
                                                <textarea id="createIssueResult" class="createDescriptionArea" name="createIssueResult"></textarea>
                                            </div>
                                            <div class="col-lg-12">
                                                <label for="createIssueFile">Bestand</label>
                                                <input type="file" id="createIssueFile" class="form-control-issue-page" name="createIssueFile" />
                                            </div>
                            <?php
                                            //if the authentication type is set to bussiness then show 
                                            //the html below and if it is not set to bussiness then don't show
                                            if ($_SESSION['accountType'] >= 1) {
                            ?>
                                                <div class="col-lg-12">
                                                    <label for="createIssueFrequency">Ticket herhalen</label>
                                                    <select id="createIssueFrequency" class="createIssueFrequency" name="createIssueFrequency">
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
                                                <input type="submit" name="sendNewIssue" value="versturen" class="sendNewIssue" />
                                            </div>
                            <?php
                                        }
                                    } elseif ($issueType == "aanvraag") {
                                        if (isset($_SESSION['accountType'])) {
                            ?>
                                            <div class="col-lg-12">
                                                <label for="createIssueTitle">Titel</label>
                                                <input type="text" id="createIssueTitle" class="form-control-issue-page" name="createIssueTitle" placeholder="text" />
                                            </div>
                                            <div class="col-lg-12">
                                                <label for="createIssueCategory">sub-category</label>
                                                <select id="createIssueCategory" name="createIssueCategory" class="createIssueCategory">
                                                    <option value="schop">schop</option>
                                                    <option value="gereedschapskist">gereedschapskist</option>
                                                    <option value="graafmachine">graafmachine</option>
                                                    <option value="tracker">tracker</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-12">
                                                <label for="createIssueDescription">Omschrijving</label>
                                                <textarea id="createIssueDescription" class="createDescriptionArea" name="createIssueDescription"></textarea>
                                            </div>
                                            <div class="col-lg-12">
                                                <label for="createIssueResult">Gewenst resultaat</label>
                                                <textarea id="createIssueResult" class="createDescriptionArea" name="createIssueResult"></textarea>
                                            </div>
                                            <div class="col-lg-12">
                                                <label for="createIssueFile">Bestand</label>
                                                <input type="file" id="createIssueFile" class="form-control-issue-page" name="createIssueFile" />
                                            </div>

                            <?php
                                            //if the authentication type is set to bussiness then show 
                                            //the html below and if it is not set to bussiness then don't show
                                            if ($_SESSION['accountType'] >= 1) {
                            ?>

                                                <div class="col-lg-12">
                                                    <label for="createIssueFrequency">Ticket herhalen</label>
                                                    <select id="createIssueFrequency" class="createIssueFrequency" name="createIssueFrequency">
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
                                                <input type="submit" name="sendNewIssue" value="versturen" class="sendNewIssue" />
                                            </div>
                            <?php
                                        }
                                    } elseif ($issueType == "dienst/service") {
                                        if (isset($_SESSION['accountType'])) {
                            ?>
                                            <div class="col-lg-12">
                                                <label for="createIssueTitle">Titel</label>
                                                <input type="text" id="createIssueTitle" class="form-control-issue-page" name="createIssueTitle" placeholder="text" />
                                            </div>
                                            <div class="col-lg-12">
                                                <label for="createIssueCategory">sub-category</label>
                                                <select id="createIssueCategory" name="createIssueCategory" class="createIssueCategory">
                                                    <option value="watervoliere">watervoliere</option>
                                                    <option value="vogelhuis">vogelhuis</option>
                                                    <option value="grasmaaien">grasmaaien</option>
                                                    <option value="schuuronderhoud">schuuronderhoud</option>
                                                    <option value="tuinonderhoud">tuinonderhoud</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-12">
                                                <label for="createIssueDescription">Omschrijving</label>
                                                <textarea id="createIssueDescription" class="createDescriptionArea" name="createIssueDescription"></textarea>
                                            </div>
                                            <div class="col-lg-12">
                                                <label for="createIssueResult">Gewenst resultaat</label>
                                                <textarea id="createIssueResult" class="createDescriptionArea" name="createIssueResult"></textarea>
                                            </div>
                                            <div class="col-lg-12">
                                                <label for="createIssueFile">Bestand</label>
                                                <input type="file" id="createIssueFile" class="form-control-issue-page" name="createIssueFile" />
                                            </div>
                            <?php
                                            //if the authentication type is set to bussiness then show 
                                            //the html below and if it is not set to bussiness then don't show
                                            if ($_SESSION['accountType'] >= 1) {
                            ?>
                                                <div class="col-lg-12">
                                                    <label for="createIssueFrequency">Ticket herhalen</label>
                                                    <select id="createIssueFrequency" class="createIssueFrequency" name="createIssueFrequency">
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
                                                <input type="submit" name="sendNewIssue" value="versturen" class="sendNewIssue" />
                                            </div>
                            <?php
                                        }
                                    } elseif ($issueType != "dienst/service" || $issueType != "aanvraag" || $issueType != "ticket") {
                                        die("the given issue type doesn't exist, return to previous page");
                                    }
                                } else {
                                    die("the given issue type hasn't been specified yet, return to previous page");
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