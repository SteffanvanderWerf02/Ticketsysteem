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
            `status` 
    FROM    issue
    WHERE   issue_id = ?
           ";

    $stmt = mysqli_prepare($db, $sql) or die(mysqli_error($db));
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt) or die(mysqli_error($db));
    mysqli_stmt_bind_result($stmt, $issue_id, $priority, $category, $title, $description, $created_at, $frequency, $status);
    mysqli_stmt_fetch($stmt);

            mysqli_stmt_execute($stmt) or die(mysqli_error($db));
            mysqli_stmt_bind_result($stmt, $issue_id, $priority, $category, $title, $description, $created_at, $frequency, $status);
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


    

?>
    <!DOCTYPE html>
    <html>

    <head>
        <?php include_once("../components/head.html") ?>
        <title>ticket detail</title>
    </head>

    <body>
        <!-- Header include -->
        <?php include_once("../components/header.html") ?>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-12">
                            <h2>Id <?php echo "#" . $issue_id; ?> | <?php echo $title; ?> | <?php echo $created_at; ?> </h2>
                            <h4><?php echo $description; ?></h4>
                            <p class="ticket_date"><?php echo $created_at; ?></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <table class="t_detail_table">
                                <tbody class="t_detail_tbody">
                                    <tr>
                                        <td class="text-left td-left">Status:</td>
                                        <td class="td-right text-right"><?php echo statusCheck($status); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-left td-left">Prioriteit:</td>
                                        <td class="td-right text-right"><?php echo priorityCheck($priority); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-left td-left">Categorie:</td>
                                        <td class="td-right text-right"><?php echo $category; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-left td-left">Aangemaakt door:</td>
                                        <td class="td-right text-right"><?php echo $name; ?></td>
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
                                    <div class="col-lg-12 message-view">
                                        <p>Klaas van Tongen</p>
                                        <p class="title-messages">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod</p>
                                    </div>
                                    <div class="col-lg-12 message-view">
                                        <p>Klaas van Tongen</p>
                                        <p class="title-messages">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod</p>
                                    </div>
                                </div>
                                <div class="col-lg-12 ticket-form">
                                    <form method="post" action="">
                                        <input type="radio" id="c_action" name="action_point" value="klant" checked />
                                        <label for="c_action">Actie klant</label>
                                        <input type="radio" id="b_action" name="action_point" value="bottom-up" />
                                        <label for="b_action">Actie Bottom up</label>
                                        <textarea class="t_area" placeholder="Uw bericht"></textarea>
                                        <label for="customFile">Bestand</label>
                                        <div class="custom-file">
                                            <input type="file" name="b_file" title="Kies uw profielfoto" class="custom-file-input" id="customFile">
                                            <label class="custom-file-label" for="customFile">Kies Bestand</label>
                                        </div>
                                        <input type="submit" class="upload_message" name="upload_message" value="Gesloten" />
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