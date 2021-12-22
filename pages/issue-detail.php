<?php
include_once("../config.php");
include_once("../connection.php");
include_once("../components/functions.php");

if ($id = filter_input(INPUT_GET, "id", FILTER_SANITIZE_NUMBER_INT)) {
    $sql = "
    SELECT  issue_id,
            priority,
            category,
            title,
            `description`,
            `created_at`,
            frequency,
            `status`,
            issue_action 
    FROM    issue
    WHERE   issue_id = ?
           ";

    $stmt = mysqli_prepare($db, $sql) or die(mysqli_error($db));
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt) or die(mysqli_error($db));
    mysqli_stmt_bind_result($stmt, $issue_id, $priority, $category, $title, $description, $created_at, $frequency, $status, $issue_action);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    $sql = "SELECT  `name`
            FROM    user
            WHERE   `user_id` = ?
           ";

    $stmt = mysqli_prepare($db, $sql) or die(mysqli_error($db));

    mysqli_stmt_bind_param($stmt, "i", $_SESSION['userId']);
    mysqli_stmt_execute($stmt) or die(mysqli_error($db));
    mysqli_stmt_bind_result($stmt, $name);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if (isset($_POST['upload_message'])) {
        if (!empty($_POST['issue_message']) && $issue_message = filter_input(INPUT_POST, 'issue_message', FILTER_SANITIZE_SPECIAL_CHARS)) {
            if (isset($_POST['action_point']) && $action_point = filter_input(INPUT_POST, 'action_point', FILTER_SANITIZE_NUMBER_INT)) {

                if (checkIfFile("b_file")) {
                    if (checkFileSize("b_file")) {
                        if (checkFileType("b_file", $acceptedFileTypes)) {
                            if (makeFolder($id, "../assets/issueFiles/")) {
                                if (!checkFileExist("../assets/issueFiles/" . $id . "/", $_FILES["b_file"]["name"])) {
                                    uploadMessage($db, $_SESSION['userId'], $issue_message, $id);
                                    $messageId = mysqli_insert_id($db);
                                    uploadActionIssue($db, $id, $action_point);
                                    if ($issue_action != $action_point) {
                                        insertStatus($db, $_SESSION['userId'], $id, $action_point);
                                    }
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
                    $messageId = mysqli_insert_id($db);
                    uploadActionIssue($db, $id, $action_point);
                    if ($issue_action != $action_point) {
                        insertStatus($db, $_SESSION['userId'], $id, $action_point);
                    }
                }
            } else {
                echo "<div class='alert alert-danger'>De optie van actie Bottom Up of Actie Klant is niet ingevuld</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Het bericht is niet ingevuld</div>";
        }
    }

?>
    <!DOCTYPE html>
    <html>

    <head>
        <?php include_once("../components/head.html") ?>
        <title>ticket detail</title>
    </head>

    <body>
        <!-- Header include -->
        <?php include_once("../components/header.php") ?>
        <div id="content" class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-12">
                            <h2>Id <?= "#" . $issue_id; ?> | <?= $title; ?> | <?= $created_at; ?> </h2>
                            <h4><?= $description; ?></h4>
                            <p class="ticket_date"><?= $created_at; ?></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <table class="t_detail_table">
                                <tbody class="t_detail_tbody">
                                    <tr>
                                        <td class="text-left td-left">Status:</td>
                                        <td class="td-right text-right"><?= statusCheck($status); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-left td-left">Prioriteit:</td>
                                        <td class="td-right text-right"><?= priorityCheck($priority); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-left td-left">Categorie:</td>
                                        <td class="td-right text-right"><?= $category; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-left td-left">Aangemaakt door:</td>
                                        <td class="td-right text-right"><?= $name; ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <div class="form-container">
                                    <div class="col-lg-12 message-view">
                                        <p class="title-messages">Bericht</p>
                                    </div>
                                    <?= getMessage($db, $id); ?>
                                </div>
                                <div class="col-lg-12 ticket-form">
                                    <form method="post" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>?id=<?= $id ?>" enctype="multipart/form-data">
                                        <input type='radio' id='c_action' name='action_point' value='2' <?= (issueActionCheck(getActionIssue($db, $id)) == "bottomup") ? 'checked="checked"' : ''; ?> />
                                        <label for='c_action'>Actie Bottom up</label>
                                        <input type='radio' id='b_action' name='action_point' value='1' <?= (issueActionCheck(getActionIssue($db, $id)) == "klant") ? 'checked="checked"' : ''; ?> />
                                        <label for='b_action'>Actie klant</label>
                                        <textarea class="t_area" name="issue_message" placeholder="Uw bericht"></textarea>
                                        <label for="customFile">Bestand</label>
                                        <div class="custom-file">
                                            <input type="file" name="b_file" title="Kies uw profielfoto" class="custom-file-input" id="customFile">
                                            <label class="custom-file-label" for="customFile">Kies Bestand</label>
                                        </div>
                                        <input type="submit" class="upload_message" name="upload_message" value="Versturen" />
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer include -->

    <?php
} ?>
    <?php include_once("../components/footer.php") ?>
    </body>

    </html>