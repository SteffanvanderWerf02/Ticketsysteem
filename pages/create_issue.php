<?php
include_once("../config.php");
// Adding basic functions
include_once("../components/functions.php");
include_once("../connection.php");

// Checking if the values have been set & filtering them
$issueType = filter_input(INPUT_GET, 'issueType', FILTER_SANITIZE_SPECIAL_CHARS);
if (isset($_POST['sendNewIssue'])) {
    if (isset($_SESSION['accountType'])) {
        $title = filter_input(INPUT_POST, 'createIssueTitle', FILTER_SANITIZE_SPECIAL_CHARS);
        $subCategory = filter_input(INPUT_POST, 'createIssueCategory', FILTER_SANITIZE_SPECIAL_CHARS);
        $description = filter_input(INPUT_POST, 'createIssueDescription', FILTER_SANITIZE_SPECIAL_CHARS);
        $result = filter_input(INPUT_POST, 'createIssueResult', FILTER_SANITIZE_SPECIAL_CHARS);

        // Setting the frequency of an issue
        if ($_SESSION['accountType'] == 0 || $issueType == "Ticket" ||  $issueType == "Product") {
            $frequency = "N.V.T";
        } else {
            $frequency = filter_input(INPUT_POST, 'createIssueFrequency', FILTER_SANITIZE_SPECIAL_CHARS);
        }

        $priority = 1;
        $status = 1;
        
        // Checking if the values have been set & inserting them into the database if this was a success
        if (isset($title)) {
            if (isset($issueType)) {
                if (isset($description)) {
                    if (isset($result)) {
                        if ($frequency == "N.V.T" || $frequency == "Dagelijks" || $frequency == "Wekelijks" || $frequency == "Maandelijks" || $frequency == "Jaarlijks") {
                            $sql = "
                                INSERT 
                                INTO issue
                                (
                                    `user_id`,
                                    company_id,
                                    priority,
                                    category,
                                    sub_category,
                                    title,
                                    `description`,
                                    result,
                                    `created_at`,
                                    `closed_at`,
                                    frequency,
                                    status_timestamp,
                                    `status`    
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
                            $lastIssueId = getLastId($db);
                            mysqli_stmt_close($stmt);

                            // Checking if the file meets the requirements
                            if (checkIfFile("issueFile")) {
                                if (checkFileSize("issueFile")) {
                                    if (checkFileType("issueFile", $acceptedFileTypes)) {
                                        if (makeFolder($lastIssueId, "../assets/issueFiles/")) {
                                            if (!checkFileExist("../assets/issueFiles/" . $lastIssueId . "/", $_FILES["issueFile"]["name"])) {
                                                if (uploadFile($db, "issueFile", "issue", "appendex_url", "issue_id", $lastIssueId, "../assets/issueFiles/" . $lastIssueId . "/")) {
                                                    echo "<div class='alert alert-success'>Uw issue is verzonden</div>";
                                                } else {
                                                    echo "<div class='alert alert-danger'>Uw bestand is niet toegevoegd, probeer het opnieuw</div>";
                                                    
                                                    // Deleting the issue when the file doesn't meet the requirements
                                                    deleteIssue($db, $lastIssueId);
                                                }
                                            } else {
                                                echo "<div class='alert alert-danger'>U heeft deze bijlagen al toegevoegd</div>";
                                                
                                                // Deleting the issue when the file doesn't meet the requirements
                                                deleteIssue($db, $lastIssueId);
                                            }
                                        }
                                    } else {
                                        echo "<div class='alert alert-danger'>Uw geüploade bestand type wordt niet geaccepteerd. Er worden alleen pdf's, jpg's, jpeg's, png's, en gif's geaccepteerd</div>";
                                        
                                        // Deleting the issue when the file doesn't meet the requirements
                                        deleteIssue($db, $lastIssueId);
                                    }
                                } else {
                                    echo "<div class='alert alert-danger'>Uw geüploade bestand is te groot</div>";

                                    // Deleting the issue when the file doesn't meet the requirements
                                    deleteIssue($db, $lastIssueId);
                                }
                            } else {
                                echo "<div class='alert alert-success'>Uw issue is verzonden</div>";
                            }
                        } else {
                            echo "<div class='alert alert-danger'>De keuze bij de herhaalbaarheid valt niet onder de gegeven opties</div>";
                        }
                    } else {
                        echo "<div class='alert alert-danger'>Het gewenste resultaat is niet ingevuld</div>";
                    }
                } else {
                    echo "<div class='alert alert-danger'>De omschrijving van de ticket is niet ingevuld</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>De category is niet ingevuld</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>De titel van de ticket is niet ingevuld</div>";
        }
    }
}
?>
<!DOCTYPE HTML>
<html lang="nl">

<head>
    <?php include_once("../components/head.html"); ?>
    <title>Bottom up - Issue toevoegen</title>
</head>

<body>
    <?php include_once("../components/header.php"); ?>
    <div id="content" class="container">
        <div class="row">
            <div class="col-lg-12">
                <h1><?php echo ucFirst($issueType) . " "; ?> Aanvraag</h1>
            </div>
            <div class="col-lg-12">
                <form method="post" action="" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-lg-12">
                            <?php
                            if (isset($issueType) && isset($_SESSION['accountType'])) {
                            ?>
                                <div class="row mb-3">
                                    <div class="col-lg-6">
                                        <label for="createIssueTitle">Titel</label>
                                        <input type="text" id="createIssueTitle" class="form-control" name="createIssueTitle" />
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-lg-6">
                                    <label for="createIssueCategory">Subcategorie</label>
                                    <select id="createIssueCategory" name="createIssueCategory" class="form-control">
                                    <?= getCatOptions(str_replace(' ', '', $issueType))?>  
                                    </select>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-lg-6">
                                        <label for="createIssueDescription">Omschrijving</label>
                                        <textarea id="createIssueDescription" class="form-control" name="createIssueDescription"></textarea>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-lg-6">
                                        <label for="createIssueResult">Gewenst resultaat</label>
                                        <textarea id="createIssueResult" class="form-control" name="createIssueResult"></textarea>
                                    </div>
                                </div>
                                <?php
                                if ($_SESSION['accountType'] >= 1 && $issueType == "Dienst/service") {
                                ?>
                                    <div class="row mb-3">
                                        <div class="col-lg-6">
                                            <label for="createIssueFrequency">Ticket herhalen</label>
                                            <select id="createIssueFrequency" class="form-control" name="createIssueFrequency">
                                                <option value="N.V.T">N.V.T</option>
                                                <option value="Dagelijks">Dagelijks</option>
                                                <option value="Wekelijks">Wekelijks</option>
                                                <option value="Maandelijks">Maandelijks</option>
                                                <option value="Jaarlijks">Jaarlijks</option>
                                            </select>
                                        </div>
                                    </div>
                                <?php
                                }
                                ?>
                                <div class="row mb-3">
                                    <div class="col-lg-6">
                                        <label for="createIssueFile" class="pointer">Bijlagen (Optioneel)</label>
                                        <div class="custom-file">
                                            <input type="file" name="issueFile" title="Kies uw bijlagen" name="createIssueFile" class="custom-file-input" id="createIssueFile">
                                            <label class="custom-file-label" for="createIssueFile">Kies Bestand</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-lg-6">
                                        <input type="submit" name="sendNewIssue" value="Versturen" class="sendNewIssue" />
                                    </div>
                                </div>
                            <?php
                            } elseif ($issueType != "dienst/service" || $issueType != "Product" || $issueType != "ticket") {
                                die("<div class='col-lg-12'>
                                        <div class='alert alert-danger'>
                                            Het gegeven issue type is nog niet gespecificeerd, ga terug naar de vorige pagina
                                        </div>
                                    </div>
                                ");
                            }
                            ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php include_once("../components/footer.php"); ?>
</body>

</html>