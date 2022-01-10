<?php include_once("../config.php");
// Adding basic functions
include_once("../components/functions.php");
include_once("../connection.php");

// Checking if there are any search requests to filter on
if (isset($_GET['filter'])) {
    $filter = filter_input(INPUT_GET, "filter", FILTER_SANITIZE_NUMBER_INT);
} else {
    $filter = NULL;
}

// Filtering the tickets by the values from the search request
if (isset($_GET['submit'])) {
    $ticket_id = filter_input(INPUT_GET, "ticket_id", FILTER_SANITIZE_SPECIAL_CHARS);
    $ticket_title = filter_input(INPUT_GET, "ticket_title", FILTER_SANITIZE_SPECIAL_CHARS);
} else {
    $ticket_id = NULL;
    $ticket_title = NULL;
}
?>
<!DOCTYPE html>
<html lang="nl">

<head>
    <title>Bottom up - Overzicht</title>
    <?php include_once("../components/head.html"); ?>
</head>

<body>
    <!-- Header include -->
    <?php include_once("../components/header.php") ?>
    <div id="content" class="container">
        <h1>Dienst/Service overzicht</h1>
        <div class="row mb-3">
            <div class="col-lg-12">
                <form action="<?= $_SERVER["PHP_SELF"] ?>" method="GET">
                    <label>Dienst/Service id
                        <input type="text" name="ticket_id" class="rounded form-control w-25 d-inline" value="<?= (isset($_GET['ticket_id']) && $id = filter_input(INPUT_GET, 'ticket_id', FILTER_SANITIZE_NUMBER_INT)) ? $id : "" ?>">
                    </label>
                    <label class="ml-2">Titel
                        <input type="text" name="ticket_title" class="rounded form-control w-25 d-inline" value="<?= (isset($_GET['ticket_title']) && $title = filter_input(INPUT_GET, 'ticket_title', FILTER_SANITIZE_SPECIAL_CHARS)) ? $title : "" ?>">
                    </label>
                    <button class="btn btn-primary" name="submit" type="submit"><span class="material-icons align-middle">search</span> Zoeken</button>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-1">
                <div class="row mb-2">
                    <div class="col-lg-12">
                        <h4 class="mt-0">Status filteren:</h4>
                    </div>
                    <div class="col-lg-12">
                        <form action="<?= $_SERVER["PHP_SELF"] ?>" method="GET">
                            <button class="btn btn-standard btn-primary" name="filter" value="" type="submit">Alles</button>
                        </form>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-lg-12">
                        <form action="<?= $_SERVER["PHP_SELF"] ?>" method="GET">
                            <button class="btn btn-standard btn-primary" name="filter" value="1" type="submit">Nieuw</button>
                        </form>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-lg-12">
                        <form action="<?= $_SERVER["PHP_SELF"] ?>" method="GET">
                            <button class="btn btn-standard btn-primary" name="filter" value="2" type="submit">Bezig</button>
                        </form>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-lg-12">
                        <form action="<?= $_SERVER["PHP_SELF"] ?>" method="GET">
                            <button class="btn btn-standard btn-primary" name="filter" value="3" type="submit">On hold</button>
                        </form>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-lg-12">
                        <form action="<?= $_SERVER["PHP_SELF"] ?>" method="GET">
                            <button class="btn btn-standard btn-primary" name="filter" value="4" type="submit">Gesloten</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-11">
                <?php echo getIssueOverview($db, $_SESSION['companyId'], $_SESSION['userId'], "dienst/service", $filter, $ticket_id, $ticket_title); ?>
                <script>
                    // When clicked on the object with the class action, the user will be redirected to the ticket detail page
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