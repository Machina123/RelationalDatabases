<?php
    include("database.inc.php");
    $db = mysqli_connect($dbHostname, $dbUsername, $dbPassword, $dbDatabase)
    or die("Blad polaczenia z baza danych!");
    $db->set_charset("utf-8");
?>
<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Admin - TicketManager</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" />
        <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"></script>
    </head>
    <body>

        <div class="modal fade" id="modal-add-venue" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form method="post" action="add.php">
                        <div class="modal-header">
                            <h5 class="modal-title">Dodaj arenę</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="name">Nazwa areny:</label>
                                <input class="form-control" type="text" name="name" id="name">
                            </div>
                            <div class="form-group">
                                <label for="name">Adres areny:</label>
                                <input class="form-control" type="text" name="address" id="address">
                            </div>
                            <div class="form-group">
                                <label for="cap">Pojemność maksymalna:</label>
                                <input class="form-control" type="number" name="cap" id="cap">
                            </div>
                            <input type="hidden" name="mode" value="venue" />
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Anuluj</button>
                            <button type="submit" class="btn btn-success">Dodaj</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal-add-star" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form method="post" action="add.php">
                        <div class="modal-header">
                            <h5 class="modal-title">Dodaj "gwiazdę"</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="name">Nazwa "gwiazdy":</label>
                                <input class="form-control" type="text" name="name" id="name">
                            </div>
                            <input type="hidden" name="mode" value="star" />
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Anuluj</button>
                            <button type="submit" class="btn btn-success">Dodaj</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal-remove-star" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form method="post" action="remove.php">
                        <div class="modal-header">
                            <h5 class="modal-title">Usuń "gwiazdę"</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                        <?php
                            $query = "SELECT * FROM tm_stars";
                            $result = $db->query($query);
                            if($result->num_rows) {
                                echo <<<EOT
                                <div class="form-group">
                                    <label for="star">Wybierz gwiazdę do usunięcia:</label>
                                    <select class="form-control" name="star">
EOT;
                                while($row = $result->fetch_assoc()) {
                                    echo <<<EOT
                                    <option value="{$row["star_id"]}">{$row["star_name"]}</option>
EOT;
                                }
                                echo <<<EOT
                                    </select>
                                </div>
EOT;
                            } else {
                                $disable_star_remove=true;
                                echo <<<EOT
                                <div class="alert alert-danger">
                                    Brak gwiazd w bazie danych!
                                </div>
EOT;
                            }
                            ?>
                            <input type="hidden" name="mode" value="star" />
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Anuluj</button>
                            <?php if(!$disable_star_remove): ?>
                            <button type="submit" class="btn btn-success">Usuń</button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal-remove-event" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form method="post" action="remove.php">
                        <div class="modal-header">
                            <h5 class="modal-title">Usuń wydarzenie</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                        <?php
                            $query = "SELECT star_name, venue_name, event_id, event_date FROM tm_events JOIN tm_venues ON tm_venues.venue_id=tm_events.venue_id JOIN tm_stars ON tm_events.star_id=tm_stars.star_id";
                            $result = $db->query($query);
                            if($result->num_rows) {
                                echo <<<EOT
                                <div class="form-group">
                                    <label for="event">Wybierz wydarzenie do usunięcia:</label>
                                    <select class="form-control" name="event">
EOT;
                                while($row = $result->fetch_assoc()) {
                                    echo <<<EOT
                                    <option value="{$row["event_id"]}">{$row["star_name"]} @ {$row["venue_name"]} ({$row["event_date"]})</option>
EOT;
                                }
                                echo <<<EOT
                                    </select>
                                </div>
                                <p class="text-muted">Uwaga: usunięcie wydarzenia spowoduje usunięcie wszystkich biletów z nim powiązanych!</p>
EOT;
                            } else {
                                $disable_event_remove = true;
                                echo <<<EOT
                                <div class="alert alert-danger">
                                    Brak wydarzeń w bazie danych!
                                </div>
EOT;
                            }
                            ?>
                            <input type="hidden" name="mode" value="event" />
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Anuluj</button>
                            <?php if(!$disable_event_remove): ?>
                            <button type="submit" class="btn btn-success">Usuń</button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal-remove-venue" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form method="post" action="remove.php">
                        <div class="modal-header">
                            <h5 class="modal-title">Usuń arenę</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <?php
                                $query = "SELECT * FROM tm_venues";
                                $result = $db->query($query);
                                if($result->num_rows) {
                                    echo <<<EOT
                                    <div class="form-group">
                                        <label for="venue">Wybierz arenę do usunięcia:</label>
                                        <select class="form-control" name="venue">
EOT;
                                    while($row = $result->fetch_assoc()) {
                                        echo <<<EOT
                                        <option value="{$row["venue_id"]}">{$row["venue_name"]} (poj. {$row["venue_capacity"]})</option>
EOT;
                                    }
                                    echo "</select></div>";
                                } else {
                                    $disable_remove_venue = false;
                                    echo <<<EOT
                                    <div class="alert alert-danger">
                                        Brak aren w bazie danych!
                                    </div>
EOT;
                                }
                            ?>
                            <input type="hidden" name="mode" value="venue" />
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Anuluj</button>
                            <?php if(!$disable_remove_venue): ?>
                            <button type="submit" class="btn btn-success">Usuń</button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="row">
                <div class="col-12">
                <?php
                    if(isset($_GET["message"])) {
                        echo <<<EOT
                        <div class="alert alert-primary w-100">
                            {$_GET["message"]}
                        </div>
EOT;
                    }
                ?> 
                <h1 class="text-center">TicketManager</h1>
                <h3 class="text-center text-muted">Panel administracyjny</h3>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-6">
                    <h1 class="text-center">Szybkie akcje</h1>
                    <button class="btn btn-primary w-100 my-1" data-toggle="modal" data-target="#modal-add-venue">Dodaj nową arenę</button>
                    <button class="btn btn-primary w-100 my-1" data-toggle="modal" data-target="#modal-add-star">Dodaj nową "gwiazdę"</button>
                    <button class="btn btn-danger w-100 my-1" data-toggle="modal" data-target="#modal-remove-venue">Usuń arenę</button>
                    <button class="btn btn-danger w-100 my-1" data-toggle="modal" data-target="#modal-remove-star">Usuń gwiazdę</button>
                    <button class="btn btn-danger w-100 my-1" data-toggle="modal" data-target="#modal-remove-event">Usuń wydarzenie</button>
                    <hr>
                    <h1 class="text-center">Dodawanie wydarzenia</h1>
                    <form method="post" action="add.php">
                        <input type="hidden" name="mode" value="event" />
                    <?php
                        $enable_adding = true;
                        $query = "SELECT * FROM tm_stars";
                        $result = $db->query($query);
                        if($result->num_rows) {
                            echo <<<EOT
                            <div class="form-group">
                                <label for="star">Wybierz "gwiazdę":</label>
                                <select class="form-control" name="star">
EOT;
                            while($row = $result->fetch_assoc()) {
                                echo <<<EOT
                                <option value="{$row["star_id"]}">{$row["star_name"]}</option>
EOT;
                            }
                            echo "</select></div>";
                        } else {
                            $enable_adding = false;
                            echo <<<EOT
                            <div class="alert alert-danger">
                                Brak gwiazd w bazie danych!
                            </div>
EOT;
                        }

                        $query = "SELECT * FROM tm_venues";
                        $result = $db->query($query);
                        if($result->num_rows) {
                            echo <<<EOT
                            <div class="form-group">
                                <label for="venue">Wybierz arenę:</label>
                                <select class="form-control" name="venue">
EOT;
                            while($row = $result->fetch_assoc()) {
                                echo <<<EOT
                                <option value="{$row["venue_id"]}">{$row["venue_name"]} (poj. {$row["venue_capacity"]})</option>
EOT;
                            }
                            echo "</select></div>";
                        } else {
                            $enable_adding = false;
                            echo <<<EOT
                            <div class="alert alert-danger">
                                Brak aren w bazie danych!
                            </div>
EOT;
                        }

                        if($enable_adding) {
                            echo <<<EOT
                            <div class="form-group">
                                <label for="tickets">Wielkość puli biletów:</label>                           
                                <input type="number" name="tickets" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label for="date">Data i godzina wydarzenia:</label>                           
                                <input type="datetime-local" name="date" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label for="price">Cena biletów:</label>                           
                                <input type="number" name="price" step="0.01" class="form-control" />
                            </div>
                            <button type="submit" class="btn btn-success w-100">Dodaj wydarzenie</button>
EOT;
                        }
                    ?>
                    </form>
                </div>
                <div class="col-12 col-md-6">
                    <h1 class="text-center">Podgląd wydarzeń</h1>
                    <form action="events.php" method="post">
                        <input type="hidden" name="filter" value="star">
                        <div class="form-group">
                            <?php
                            $query = "SELECT * FROM tm_stars";
                            $result = $db->query($query);
                            if($result->num_rows) {
                                echo <<<EOT
                                <div class="form-group">
                                    <label for="star">Według gwiazdy:</label>
                                    <select class="form-control" name="star">
EOT;
                                while($row = $result->fetch_assoc()) {
                                    echo <<<EOT
                                    <option value="{$row["star_id"]}">{$row["star_name"]}</option>
EOT;
                                }
                                echo <<<EOT
                                    </select>
                                </div>
                                <button type="submit" class="w-100 btn btn-primary">Wyświetl</button>
EOT;
                            } else {
                                echo <<<EOT
                                <div class="alert alert-danger">
                                    Brak gwiazd w bazie danych!
                                </div>
EOT;
                            }
                            ?>
                        </div>
                    </form>
                    <form action="events.php" method="post">
                        <input type="hidden" name="filter" value="venue">
                        <div class="form-group">
                            <?php
                            $query = "SELECT * FROM tm_venues";
                            $result = $db->query($query);
                            if($result->num_rows) {
                                echo <<<EOT
                                <div class="form-group">
                                    <label for="venue">Według areny:</label>
                                    <select class="form-control" name="venue">
EOT;
                                while($row = $result->fetch_assoc()) {
                                    echo <<<EOT
                                    <option value="{$row["venue_id"]}">{$row["venue_name"]}</option>
EOT;
                                }
                                echo <<<EOT
                                    </select>
                                </div>
                                <button type="submit" class="w-100 btn btn-primary">Wyświetl</button>
EOT;
                            } else {
                                echo <<<EOT
                                <div class="alert alert-danger">
                                    Brak aren w bazie danych!
                                </div>
EOT;
                            }
                            ?>
                        </div>
                    </form>
                    <hr>
                    <h1 class="text-center">Podgląd biletów</h1>
                    <form action="tickets.php" method="post">
                        <input type="hidden" name="filter" value="event">
                        <div class="form-group">
                            <?php
                            $query = "SELECT star_name, venue_name, event_id, event_date FROM tm_events JOIN tm_venues ON tm_venues.venue_id=tm_events.venue_id JOIN tm_stars ON tm_events.star_id=tm_stars.star_id";
                            $result = $db->query($query);
                            if($result->num_rows) {
                                echo <<<EOT
                                <div class="form-group">
                                    <label for="event">Według wydarzenia:</label>
                                    <select class="form-control" name="event">
EOT;
                                while($row = $result->fetch_assoc()) {
                                    echo <<<EOT
                                    <option value="{$row["event_id"]}">{$row["star_name"]} @ {$row["venue_name"]} ({$row["event_date"]})</option>
EOT;
                                }
                                echo <<<EOT
                                    </select>
                                </div>
                                <button type="submit" class="w-100 btn btn-primary">Wyświetl</button>
EOT;
                            } else {
                                echo <<<EOT
                                <div class="alert alert-danger">
                                    Brak wydarzeń w bazie danych!
                                </div>
EOT;
                            }
                            ?>
                        </div>
                    </form>
                    <form action="tickets.php" method="post">
                        <input type="hidden" name="filter" value="customer">
                        <div class="form-group">
                            <?php
                            $query = "SELECT * FROM tm_customers";
                            $result = $db->query($query);
                            if($result->num_rows) {
                                echo <<<EOT
                                <div class="form-group">
                                    <label for="venue">Według użytkownika:</label>
                                    <select class="form-control" name="customer">
EOT;
                                while($row = $result->fetch_assoc()) {
                                    echo <<<EOT
                                    <option value="{$row["customer_id"]}">{$row["customer_name"]} (tel.: {$row["customer_phoneno"]})</option>
EOT;
                                }
                                echo <<<EOT
                                    </select>
                                </div>
                                <button type="submit" class="w-100 btn btn-primary">Wyświetl</button>
EOT;
                            } else {
                                echo <<<EOT
                                <div class="alert alert-danger">
                                    Brak użytkowników w bazie danych!
                                </div>
EOT;
                            }
                            ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>
<?php
    $db->close();