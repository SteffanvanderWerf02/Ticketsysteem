<?php
include_once("../config.php");
// Adding basic functions
include_once("../components/functions.php");
include_once("../connection.php");

// Filtering the Id
if ($id = filter_input(INPUT_GET, "id", FILTER_SANITIZE_NUMBER_INT)) {
?>
    <!DOCTYPE html>
    <html>

    <head>
        <?php include_once("../components/head.html") ?>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css" />
        <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
    </head>
    <title>Bottom Up - Issue detail</title>
    </head>

    <body>
        <?php

        // Checking if the issue details have been changed
        if (isset($_POST['updateIssue'])) {
            if (isset($_POST['issueStatus']) && $issueStatus = filter_input(INPUT_POST, 'issueStatus', FILTER_SANITIZE_NUMBER_INT)) {
                if (isset($_POST['issuePir']) && $issuePir = filter_input(INPUT_POST, 'issuePir', FILTER_SANITIZE_NUMBER_INT)) {
                    if (isset($_POST['issueCat']) && $issueCat = filter_input(INPUT_POST, 'issueCat', FILTER_SANITIZE_FULL_SPECIAL_CHARS)) {
                        if (isset($_POST['issueSCat']) && $issueSCat = filter_input(INPUT_POST, 'issueSCat', FILTER_SANITIZE_FULL_SPECIAL_CHARS)) {
                            
                            // Updating the data if the issue details have been changed
                            issueStatusUpdate($db, $_SESSION['userId'], $id, $issueStatus);
                            notifyPriUser($db, $id, $issuePir);
                            $stmt = mysqli_prepare($db, " 
                                    UPDATE  issue 
                                    SET     priority = ?,
                                            `status` = ?,
                                            category = ?,
                                            sub_category = ?
                                    WHERE   issue_id = ?
                            ") or die(mysqli_error($db));
                            mysqli_stmt_bind_param($stmt, "iissi", $issuePir, $issueStatus, $issueCat, $issueSCat, $id) or die(mysqli_error($db));
                            mysqli_stmt_execute($stmt) or die(mysqli_error($db));
                            mysqli_stmt_close($stmt);
                            
                            // Closing the issue
                            if($issueStatus == 4) {
                                $stmt = mysqli_prepare($db, " 
                                        UPDATE  issue 
                                        SET     closed_at = NOW()
                                        WHERE   issue_id = ?
                                ") or die(mysqli_error($db));
                                mysqli_stmt_bind_param($stmt, "i", $id) or die(mysqli_error($db));
                                mysqli_stmt_execute($stmt) or die(mysqli_error($db));
                                mysqli_stmt_close($stmt);
                            } else {
                                $stmt = mysqli_prepare($db, " 
                                        UPDATE  issue 
                                        SET     closed_at = NULL
                                        WHERE   issue_id = ?
                                ") or die(mysqli_error($db));
                                mysqli_stmt_bind_param($stmt, "i", $id) or die(mysqli_error($db));
                                mysqli_stmt_execute($stmt) or die(mysqli_error($db));
                                mysqli_stmt_close($stmt);
                            }

                            //Updating the frequency of an issue & filtering
                            if ($issueCat == "Dienst/service" && $_SESSION['accountType'] == 3 || $issueCat == "Dienst/service" && $_SESSION['accountType'] == 4) {
                                if ($frequency = filter_input(INPUT_POST, 'issueFreq', FILTER_SANITIZE_FULL_SPECIAL_CHARS)) {
                                    $stmt = mysqli_prepare($db, " 
                                            UPDATE  issue 
                                            SET     frequency = ?
                                            WHERE   issue_id = ?
                                    ") or die(mysqli_error($db));
                                    mysqli_stmt_bind_param($stmt, "si", $frequency, $id) or die(mysqli_error($db));
                                    mysqli_stmt_execute($stmt) or die(mysqli_error($db));
                                    mysqli_stmt_close($stmt);
                                } else {
                                    echo "<div class='alert alert-danger'>Uw herhalingsniveau komt niet overeen met de opties</div>";
                                }
                            }
                            echo "<div class='alert alert-success'>Uw issue is succesvol aangepast</div>";
                        } else {
                            echo "<div class='alert alert-danger'>Uw sub-categorie komt niet overeen met de opties</div>";
                        }
                    } else {
                        echo "<div class='alert alert-danger'>Uw categorie komt niet overeen met de opties</div>";
                    }
                } else {
                    echo "<div class='alert alert-danger'>Uw prioriteitsniveau komt niet overeen met de opties</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>Uw status komt niet overeen met de opties</div>";
            }
        }

        $sql = "
            SELECT  issue.issue_id,
                    issue.priority,
                    issue.category,
                    issue.sub_category,
                    issue.title,
                    issue.`description`,
                    issue.`created_at`,
                    issue.frequency,
                    issue.`status`,
                    issue.appendex_url,
                    issue.issue_action,
                    issue.company_id,
                    company.name,
                    user.name,
                    issue.result
            FROM    issue
            INNER JOIN user
            ON issue.user_id = user.user_id
            LEFT JOIN   company
            ON  issue.company_id = company.company_id
            WHERE   issue_id = ?
        ";

        $stmt = mysqli_prepare($db, $sql) or die(mysqli_error($db));
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt) or die(mysqli_error($db));
        mysqli_stmt_bind_result($stmt, $issue_id, $priority, $category, $subCat, $title, $description, $created_at, $frequency, $status, $appendex, $issue_action, $companyId, $companyName, $name, $result);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        // Checking if the button has been pressed & filtering
        if (isset($_POST['upload_message'])) {
            if (!empty($_POST['issue_message']) && $issue_message = filter_input(INPUT_POST, 'issue_message', FILTER_SANITIZE_FULL_SPECIAL_CHARS)) {
                if (isset($_POST['action_point']) && $action_point = filter_input(INPUT_POST, 'action_point', FILTER_SANITIZE_NUMBER_INT)) {

                    // Checking if the file meets the requirements
                    if (checkIfFile("b_file")) {
                        if (checkFileSize("b_file")) {
                            if (checkFileType("b_file", $acceptedFileTypes)) {
                                if (makeFolder($id, "../assets/issueFiles/")) {

                                    // Checking if the file already exists
                                    if (!checkFileExist("../assets/issueFiles/" . $id . "/", $_FILES["b_file"]["name"])) {
                                        uploadMessage($db, $_SESSION['userId'], $issue_message, $id);
                                        $messageId = getLastId($db);
                                        uploadActionIssue($db, $id, $action_point);
                                        
                                        // Checking if the actions of the issue & the radio buttons are the same & updating this
                                        if ($issue_action != $action_point) {
                                            updateIssueAction($db, $_SESSION['userId'], $id, $action_point);
                                        }
                                        
                                        // Uploading the file
                                        if (uploadFile($db, "b_file", "message", "appendex_url", "message_id", $messageId, "../assets/issueFiles/" . $id . "/")) {
                                        } else {
                                            echo "<div class='alert alert-danger'>Uw bijlage is niet toegevoegd, probeer het opnieuw</div>";
                                        }
                                    } else {
                                        echo "<div class='alert alert-danger'>Uw bijlage bestaat al</div>";
                                    }
                                }
                            } else {
                                echo "<div class='alert alert-danger'>Uw geüploadde bestand type wordt niet geaccepteerd. Er worden alleen pdf's, jpg's, jpeg's, png's, en gif's geaccepteerd</div>";
                            }
                        } else {
                            echo "<div class='alert alert-danger'>Uw geüploadde bestand is te groot</div>";
                        }
                    } else {
                        uploadMessage($db, $_SESSION['userId'], $issue_message, $id);
                        $messageId = getLastId($db);
                        echo notifyAction($db, $id, $action_point, $messageId);
                        // Checking if the actions of the issue & the radio buttons are the same & updating this
                        if ($issue_action != $action_point) {
                            updateIssueAction($db, $_SESSION['userId'], $id, $action_point);
                        }
                        uploadActionIssue($db, $id, $action_point);
                    }
                } else {
                    echo "<div class='alert alert-danger'>De optie van actie Bottom Up of Actie Klant is niet ingevuld</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>Het bericht is niet ingevuld</div>";
            }
        }
        ?>
        <!-- Header include -->
        <?php include_once("../components/header.php"); ?>
        <div id="content" class="container">
            <div class="row">
                <div class="col-lg-12 my-auto">
                    <div class="row mt-2 mb-2">
                        <div class="col-lg-10">
                            <h2 class="m-0">Id #<?= $issue_id; ?> | <?= $title; ?> | <?= date("d-m-Y", strtotime($created_at)) ?> </h2>
                            <p><b>Omschrijving: </b><?= $description; ?></p>
                            <p><b>Gewenst Resultaat: </b><?= $result; ?></p>
                        </div>
                        <div class="col-lg-2 my-auto text-right">
                            <?php
                            // Adding a button to update the issue details after the edit button has been pressed
                            if (isset($_GET['edit']) && $_SESSION["accountType"] == 3 || isset($_GET['edit']) && $_SESSION['accountType'] == 4) {
                            ?>
                                <form id="editForm" class="d-inline" action="<?= htmlentities($_SERVER['PHP_SELF']) ?>?id=<?= $id ?>" method="POST">
                                    <button class="btn d-inline btn-primary" name="updateIssue" type="submit"><span class="material-icons align-middle">done</span></button>
                                </form>

                            <?php
                            }
                            // Checking if the account is a ticket administrator & make a ticket editable
                            if ($_SESSION["accountType"] == 3 || $_SESSION['accountType'] == 4) {
                            ?>
                                <a href="./issue_detail.php?id=<?= $id ?>&amp;edit=true" class="btn d-inline btn-primary"><span class="material-icons align-middle">edit</span></a>
                            <?php
                            }
                            ?>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">

                            <table class="t_detail_table mb-3">
                                <tbody class="t_detail_tbody">
                                    <tr>
                                        <td class="text-left td-left">Status:</td>
                                        <td class="td-right text-right">
                                            <?php
                                            // Editing the status of an issue
                                            if (isset($_GET['edit']) && $_SESSION["accountType"] == 3 ||isset($_GET['edit']) && $_SESSION['accountType'] == 4) {
                                            ?>
                                                <select name="issueStatus" class="form-control" form="editForm">
                                                    <option value="1" <?= ($status == 1 ? "selected" : "") ?>>Nieuw</option>
                                                    <option value="2" <?= ($status == 2 ? "selected" : "") ?>>In behandeling</option>
                                                    <option value="3" <?= ($status == 3 ? "selected" : "") ?>>On hold</option>
                                                    <option value="4" <?= ($status == 4 ? "selected" : "") ?>>Gesloten</option>
                                                </select>
                                            <?php
                                            } else {
                                            ?>
                                                <?= statusCheck($status); ?>
                                            <?php
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-left td-left">Prioriteit:</td>
                                        <td class="td-right text-right">
                                            <?php
                                            // Showing the priority options the editor can choose from
                                            if (isset($_GET['edit']) && $_SESSION["accountType"] == 3 ||isset($_GET['edit']) && $_SESSION['accountType'] == 4) {
                                            ?>
                                                <select name="issuePir" class="form-control" form="editForm">
                                                    <option value="1" <?= ($priority == 1 ? "selected" : "") ?>>Laag</option>
                                                    <option value="2" <?= ($priority == 2 ? "selected" : "") ?>>Gemiddeld</option>
                                                    <option value="3" <?= ($priority == 3 ? "selected" : "") ?>>Hoog</option>
                                                </select>
                                            <?php
                                            } else {
                                            ?>
                                                <?= priorityCheck($priority); ?>
                                            <?php
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <?php if ($category == "Dienst/service" && $_SESSION['accountType'] == 2 || $category == "Dienst/service" && $_SESSION['accountType'] == 3 || $category == "Dienst/service" && $_SESSION['accountType'] == 4) { ?>
                                        <tr>
                                            <td class="text-left td-left">Herhaling:</td>
                                            <td class="td-right text-right">
                                                <?php
                                                // Showing the frequencies the editor can choose from
                                                if (isset($_GET['edit']) && $_SESSION["accountType"] == 3 || isset($_GET['edit']) && $_SESSION['accountType'] == 4) {
                                                ?>
                                                    <select name="issueFreq" class="form-control" form="editForm">
                                                        <option value="N.V.T" <?= ($frequency == "N.V.T" ? "selected" : "") ?>>N.V.T</option>
                                                        <option value="Dagelijks" <?= ($frequency == "Dagelijks" ? "selected" : "") ?>>Dagelijks</option>
                                                        <option value="Wekelijks" <?= ($frequency == "Wekelijks" ? "selected" : "") ?>>Wekelijks</option>
                                                        <option value="Maandelijks" <?= ($frequency == "Maandelijks" ? "selected" : "") ?>>Maandelijks</option>
                                                        <option value="Jaarlijks" <?= ($frequency == "Jaarlijks" ? "selected" : "") ?>>Jaarlijks</option>
                                                    </select>
                                                <?php
                                                } else {
                                                ?>
                                                    <?= $frequency; ?>
                                                <?php
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    <tr>
                                        <td class="text-left td-left">Categorie:</td>
                                        <td class="td-right text-right">
                                            <?php
                                            // Showing the categories the editor can choose from
                                            if (isset($_GET['edit']) && $_SESSION["accountType"] == 3 || isset($_GET['edit']) && $_SESSION['accountType'] == 4) {
                                            ?>
                                                <select name="issueCat" class="form-control" form="editForm" id="isCat">
                                                    <option value="Dienst/service" <?= ($category == "Dienst/service" ? "selected" : "") ?>>Dienst / Service</option>
                                                    <option value="Ticket" <?= ($category == "Ticket" ? "selected" : "") ?>>Ticket</option>
                                                    <option value="Product" <?= ($category == "Product" ? "selected" : "") ?>>Product</option>
                                                </select>
                                            <?php
                                            } else {
                                            ?>
                                                <?= $category; ?>
                                            <?php
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-left td-left">Subcategorie:</td>
                                        <td class="td-right text-right">
                                            <?php
                                            // Showing the subcategories the editor can choose from
                                            if (isset($_GET['edit']) && $_SESSION["accountType"] == 3 || isset($_GET['edit']) && $_SESSION['accountType'] == 4) {
                                            ?>
                                                <select name="issueSCat" class="form-control" form="editForm">
                                                    <?= getCatOptions($category, $subCat) ?>
                                                </select>
                                            <?php
                                            } else {
                                            ?>
                                                <?= $subCat; ?>
                                            <?php
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-left td-left">Aangemaakt door:</td>
                                        <td class="td-right text-right">
                                            <?= $name; ?>
                                        </td>
                                    </tr>
                                    <?php
                                    // compare the company name if the Id is not NULL
                                    if ($companyId != NULL) {
                                    ?>
                                        <tr>
                                            <td class="text-left td-left">Bedrijf:</td>
                                            <td class="td-right text-right">
                                                <?= $companyName; ?>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    // compere a clickable link for the appendix if the variable is not NULL
                                    if ($appendex != NULL) {
                                    ?>
                                        <tr>
                                            <td class="text-left td-left">Bijlagen:</td>
                                            <td class="td-right text-right">
                                                <a target="_blank" href="<?= $appendex; ?>">Bekijk hier</a>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                            </form>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <div class="form-container">
                                    <div class="col-lg-12 message-view">
                                        <h2 class="header-messages text-white">Bericht</h2>
                                    </div>
                                    <?= getMessage($db, $id); ?>
                                </div>
                                <?php 
                                // Checking which side has to take the next action in an issue if the ticket is still opened
                                if ($status != 4) {
                                ?>
                                    <div class="col-lg-12 ticket-form">
                                        <form method="post" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>?id=<?= $id ?>" enctype="multipart/form-data">
                                            <input type='radio' id='c_action' name='action_point' value='2' <?= (issueActionCheck(getActionIssue($db, $id)) == "Bottom Up") ? 'checked="checked"' : ''; ?> />
                                            <label for='c_action'>Actie Bottom up</label>
                                            <input type='radio' id='b_action' name='action_point' value='1' <?= (issueActionCheck(getActionIssue($db, $id)) == "Klant") ? 'checked="checked"' : ''; ?> />
                                            <label for='b_action'>Actie klant</label>
                                            <textarea class="t_area" name="issue_message" placeholder="Uw bericht"></textarea>
                                            <label for="customFile">Bestand</label>
                                            <div class="custom-file">
                                                <input type="file" name="b_file" title="Kies uw profielfoto" class="custom-file-input" id="customFile">
                                                <label class="custom-file-label" for="customFile">Kies Bestand</label>
                                            </div>
                                            <input type="submit" class="upload_message pointer" name="upload_message" value="Versturen" />
                                        </form>
                                    </div>
                                <?php
                                } else {
                                ?>
                                    <div class="col-lg-12">
                                        <p><b>De issue is gesloten u kunt geen berichten meer plaatsen. Mocht u achteraf nog vragen hebben verzoeken wij u om een nieuwe issue aan te maken.</b></p>
                                    </div>
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            // On change isCat will submit editForm
            $(document).ready(function() {
                $("#isCat").on("change", function() {
                    let form = $("#editForm");
                    form.find("button[type=submit]").click();
                })
            })

            // All images with the data attribute 'data-fancybox="gallery"' will have the function carousel from Fancybox
            Fancybox.bind('[data-fancybox="gallery"]', {
                caption: function(fancybox, carousel, slide) {
                    return (
                        `${slide.index + 1} / ${carousel.slides.length} <br />` + slide.caption
                    );
                },
            });
        </script>
        <!-- Footer include -->

    <?php
} ?>
    <?php include_once("../components/footer.php") ?>
    </body>

    </html>