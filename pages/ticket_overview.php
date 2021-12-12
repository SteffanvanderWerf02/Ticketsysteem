<!DOCTYPE html>
<html lang="nl">

<head>
    <title>Bottom up - Overzicht</title>
    <?php include_once("../components/head.html") ?>
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
                            <th>Hooftcategorie</th>
                            <th>Categorie</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="action" data-href="ticket_detail.php?id=1">
                            <td>1</td>
                            <td>25/02/2021</td>
                            <td>Lorem ipsum</td>
                            <td>Schoen - Renner</td>
                            <td>Laag</td>
                            <td>Gesloten</td>
                            <td>ticket</td>
                            <td>Vragen</td>
                        </tr>
                        <tr class="action" data-href="ticket_detail.php?id=1">
                            <td>2</td>
                            <td>24/10/2021</td>
                            <td>Lorem ipsum</td>
                            <td>Hansen - Hettinger</td>
                            <td>Hoog</td>
                            <td>Open</td>
                            <td>ticket</td>
                            <td>Klacht</td>
                        </tr>
                        <tr class="action" data-href="ticket_detail.php?id=3">
                            <td>3</td>
                            <td>21/11/2021</td>
                            <td>Lorem ipsum</td>
                            <td>Wehner LLC</td>
                            <td>Gemiddeld</td>
                            <td>In Behandeling</td>
                            <td>ticket</td>
                            <td>Vraag</td>
                        </tr>
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