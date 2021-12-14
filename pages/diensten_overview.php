<?php include_once("../config.php");?>
<?php include_once("../connection.php");?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <title>Bottom up - Overzicht</title>
    <?php include_once("../components/head.html") ;
    var_dump($_SESSION);
    ?>
</head>

<body>
    <!-- Header include -->
    <?php include_once("../components/header.html") ?>
    <div id="content" class="container">
        <h1>Ticket overzicht</h1>
        <div class="row mb-3">
            <div class="col-lg-12">
                <form action="<?= $_SERVER["PHP_SELF"] ?>" method="GET">
                    <label>Ticket id
                        <input type="text" name="ticket_id" class="rounded form-control w-25 d-inline" name="ticket-id">
                    </label>
                    <label class="ml-2">Titel
                        <input type="text" name="ticket_title" class="rounded form-control w-25 d-inline" name="titel">
                    </label>
                    <button class="btn btn-primary" name="submit" type="submit"><span class="material-icons align-middle">search</span> Zoeken</button>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-2">
                <div class="row mb-2">
                    <div class="col-lg-12">
                        <h4 class="mt-0">Status filteren:</h4>
                    </div>
                    <div class="col-lg-12">
                        <form action="<?= $_SERVER["PHP_SELF"] ?>" method="get">
                            <button class="btn btn-primary" name="filter" value="new" type="submit">Nieuw</button>
                        </form>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-lg-12">
                        <form action="<?= $_SERVER["PHP_SELF"] ?>" method="get">
                            <button class="btn btn-primary" name="filter" value="transit" type="submit">In Behandeling</button>
                        </form>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-lg-12">
                        <form action="<?= $_SERVER["PHP_SELF"] ?>" method="get">
                            <button class="btn btn-primary" name="filter" value="onHold" type="submit">On hold</button>
                        </form>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-lg-12">
                        <form action="<?= $_SERVER["PHP_SELF"] ?>" method="get">
                            <button class="btn btn-primary" name="filter" value="closed" type="submit">Gesloten</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-10">
                <table cellspacing="0" cellpadding="0" class="table">
                    <thead>
                        <tr data-href="ticket_detail.php?id=1">
                            <th>id</th>
                            <th>Aanmaak datum</th>
                            <th>Titel</th>
                            <th>Bedrijf</th>
                            <th>Prioriteit</th>
                            <th>Status</th>
                            <th>Hoofdcategorie</th>
                            <th>Categorie</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $stmt = mysqli_prepare($db, 
                                    "SELECT issue_id, `created at`, title, company.name, priority, issue.`status`, category, sub_category 
                                     FROM issue
                                     JOIN company ON issue.company_id = company.company_id
                                     WHERE category = 'diensten/service'");
                            mysqli_stmt_execute($stmt) OR DIE(mysqli_error($db));
                            mysqli_stmt_store_result($stmt) OR DIE(mysqli_error($db));
                            mysqli_stmt_bind_result($stmt, $issueID, $createdAt, $title, $companyName, $priority, $status, $category, $subCategory);
                        ?>
                        <?php 
                        if(mysqli_stmt_num_rows($stmt) > 0) {
                            while(mysqli_stmt_fetch($stmt)) {
                                echo
                                "<tr class='action' data-href='ticket_detail.php?id={$issueID}'>
                                    <td>{$issueID}</td>
                                    <td>{$createdAt}</td>
                                    <td>{$title}</td>
                                    <td>{$companyName}</td>
                                    <td>{$priority}</td>
                                    <td>{$status}</td>
                                    <td>{$category}</td>
                                    <td>{$subCategory}</td>
                                </tr>";
                            }
                        } 
                        ?>                   
                    </tbody>
                </table>
                <script>
                    $(document).ready(function($) {
                        $(".action").click(function() {
                            window.location = $(this).data("href");
                        });
                    });
                </script>
            </div>
        </div>
    </div>
    <!-- Footer include -->
    <?php include_once("../components/footer.php") ?>
</body>

</html>