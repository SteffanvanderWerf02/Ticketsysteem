<!DOCTYPE html>
<html lang="nl">
<head>
    <title>Bottom up - Overzicht</title>
    <?php include_once("../components/head.html")?>
</head>
<body>
    <!-- Header include -->
    <?php include_once("../components/header.html")?>
    <div class="container">
        <h1>Tickets</h1>
        <div class="row">
            <div class="col-lg-12">
                <form action="welcome_get.php" method="get">
                        <h2>
                            Ticket id<input type="text" class="rounded" name="ticket-id">
                            Titel<input type="text" class="rounded" name="titel">
                            <div class="btn btn-primary">
                                <button class="btn btn-primary" name="submit" type="submit">Zoeken</button>
                            </div>
                        </h2>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-2">
                <div class="row mb-2">
                    <div class="col-lg-12">
                        <form action="<?= $_SERVER["PHP_SELF"] ?>" method="get">
                            <button class="btn btn-primary" name="submit" type="submit">Nieuw</button>
                        </form>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-lg-12">
                        <form action="<?= $_SERVER["PHP_SELF"] ?>" method="get">
                            <button class="btn btn-primary" name="submit" type="submit">In Behandeling</button>
                        </form>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-lg-12">
                        <form action="<?= $_SERVER["PHP_SELF"] ?>" method="get">
                            <button class="btn btn-primary" name="submit" type="submit">On hold</button>
                        </form>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-lg-12">
                        <form action="<?= $_SERVER["PHP_SELF"] ?>" method="get">
                            <button class="btn btn-primary" name="submit" type="submit">Gesloten</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-10">
                <table class="table">
                    <thead>
                        <tr>
                            <th>id</th>
                            <th>Aanmaak datum</th>
                            <th>Titel</th>
                            <th>Bedrijf</th>
                            <th>Prioriteit</th>
                            <th>Status</th>
                            <th>Categorie</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>25/02/2021</td>
                            <td>Lorem ipsum</td>
                            <td>Schoen - Renner</td>
                            <td>Laag</td>
                            <td>Gesloten</td>
                            <td>Vragen</td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>24/10/2021</td>
                            <td>Lorem ipsum</td>
                            <td>Hansen - Hettinger</td>
                            <td>Hoog</td>
                            <td>Open</td>
                            <td>Klacht</td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>21/11/2021</td>
                            <td>Lorem ipsum</td>
                            <td>Wehner LLC</td>
                            <td>Gemiddeld</td>
                            <td>In Behandeling</td>
                            <td>Vraag</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
     <!-- Footer include -->
     <?php include_once("../components/footer.php")?>
</body>
</html>