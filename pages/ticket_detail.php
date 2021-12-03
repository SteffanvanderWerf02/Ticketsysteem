<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" type="text/css" href="assets/css/style.css" />
        <link rel="stylesheet" type="text/css" href="assets/css/ticket_detail.css" />
        <title>ticket detail</title>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-12">
                            <h2>Id  #1 | Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed | 17-02-2021 </h2>
                            <h4>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed</h4>
                            <p class="ticket_date">1-12-2021</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <table>
                                <tbody>
                                    <tr>
                                        <td class="text-left td-left">Status:</td>
                                        <td class="td-right text-right">In behandeling</td>
                                    </tr>
                                    <tr>
                                        <td class="text-left td-left">Prioriteit:</td>
                                        <td class="td-right text-right">Laag</td>
                                    </tr>
                                    <tr>
                                        <td class="text-left td-left">Categorie:</td>
                                        <td class="td-right text-right">Watervoorzieningen</td>
                                    </tr>
                                    <tr>
                                        <td class="text-left td-left">Aangemaakt door:</td>
                                        <td class="td-right text-right">Klaas van Tuigen</td>
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
                                        <input type="radio" id="c_action" name="action_point" value="klant" />
                                        <label for="c_action">Actie klant</label>
                                        <input type="radio" id="b_action" name="action_point" value="bottom-up" />
                                        <label for="b_action">Actie Bottom up</label>
                                        <textarea class="t_area" placeholder="Uw bericht"></textarea>
                                        <label for="b_file">Bestand</label>
                                        <input type="text" id="b_file" name="b_file" />
                                        <input type="submit" class="upload_message" name="upload_message" value="Gesloten" />
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>