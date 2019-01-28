<?php
    include("database.inc.php");
    $db = mysqli_connect($dbHostname, $dbUsername, $dbPassword, $dbDatabase)
    or die("Blad polaczenia z baza danych!");
    $db->set_charset("utf-8");
    if(!isset($_POST["customer"])) {
        die("Wykonano nieprawidlowe zapytanie");
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Bilety - TicketManager</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" />
        <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"></script>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1 class="text-center">Kup bilety</h1>
                    <form method="post" action="cart.php">
                    <?php
                            $query = "SELECT star_name, venue_name, event_id, event_date, ticket_price, available_tickets FROM tm_events JOIN tm_venues ON tm_venues.venue_id=tm_events.venue_id JOIN tm_stars ON tm_events.star_id=tm_stars.star_id";
                            $result = $db->query($query);
                            if($result->num_rows) {
                                echo <<<EOT
                                <div class="form-group">
                                    <label for="event">Wybierz wydarzenie:</label>
                                    <select class="form-control" name="event">
EOT;
                                while($row = $result->fetch_assoc()) {
                                    echo <<<EOT
                                    <option value="{$row["event_id"]}">{$row["star_name"]} @ {$row["venue_name"]} ({$row["event_date"]}) | Cena: {$row["ticket_price"]} PLN | Dostępnych: {$row["available_tickets"]}</option>
EOT;
                                }
                                echo <<<EOT
                                    </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="amount">Ilość biletów:</label>
                                        <input class="form-control" type="number" name="amount" min="1" step="1"/>
                                    </div>
                                    <input type="hidden" name="customer" value="{$_POST["customer"]}" />
                                    <button type="submit" class="btn btn-success w-100">Kup bilety</button>
                                </div>
EOT;
                            } else {
                                echo <<<EOT
                                <div class="alert alert-danger">
                                    Brak wydarzeń w bazie danych!
                                </div>
EOT;
                            }
                    ?>

                    </form>
                </div>
            </div>
        </div>
    </body>
</html>