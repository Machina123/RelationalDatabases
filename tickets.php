<?php
    include("database.inc.php");
    $db = mysqli_connect($dbHostname, $dbUsername, $dbPassword, $dbDatabase)
    or die("Blad polaczenia z baza danych!");
    $db->set_charset("utf-8");
    if(!isset($_POST["filter"])) die("Wykonano nieprawidlowe zapytanie");
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
                    <h1 class="text-center">Przeglądanie biletów</h1>
                    <?php 
                        if($_POST["filter"] == "event") {
                            $query = "SELECT star_name, venue_name, event_date FROM tm_events JOIN tm_venues ON tm_venues.venue_id=tm_events.venue_id JOIN tm_stars ON tm_events.star_id=tm_stars.star_id WHERE event_id=".$_POST["event"];
                            $result = $db->query($query);
                            if($result->num_rows) {
                                $row = $result->fetch_assoc();
                                echo <<<EOT
                                <h4 class="text-center text-muted">Wydarzenie: {$row["star_name"]} @ {$row["venue_name"]} ({$row["event_date"]})</h4>
EOT;
                            }

                            $query = "SELECT customer_name, ticket_id, ticket_checksum FROM tm_tickets JOIN tm_customers ON tm_tickets.customer_id=tm_customers.customer_id WHERE event_id=".$_POST["event"];
                            $result = $db->query($query);
                            if($result->num_rows) {
                                while($row = $result->fetch_assoc()) {
                                    echo <<<EOT
                                    <div class="alert alert-dark">Bilet #{$row["ticket_id"]} | Klient: {$row["customer_name"]} |  Suma kontrolna: {$row["ticket_checksum"]}</div>
EOT;
                                }
                            }

                        } elseif($_POST["filter"] == "customer") {
                            $query = "SELECT customer_name, customer_phoneno FROM tm_customers WHERE customer_id=".$_POST["customer"];
                            $result = $db->query($query);
                            if($result->num_rows) {
                                $row = $result->fetch_assoc();
                                echo <<<EOT
                                <h4 class="text-center text-muted">Klient: {$row["customer_name"]} (tel.: {$row["customer_phoneno"]})</h4>
EOT;
                            }
                            $query = "SELECT star_name, venue_name, event_date, ticket_id, ticket_checksum FROM tm_tickets JOIN tm_events ON tm_events.event_id=tm_tickets.event_id JOIN tm_venues ON tm_venues.venue_id=tm_events.venue_id JOIN tm_stars ON tm_events.star_id=tm_stars.star_id WHERE customer_id=".$_POST["customer"];
                            $result = $db->query($query);
                            if($result->num_rows) {
                                while($row = $result->fetch_assoc()) {
                                    echo <<<EOT
                                    <div class="alert alert-dark">Bilet #{$row["ticket_id"]} | Wydarzenie: {$row["star_name"]} @ {$row["venue_name"]} ({$row["event_date"]}) | Suma kontrolna: {$row["ticket_checksum"]}</div>
EOT;
                                }
                            }
                        }
                    ?>
                </div>
            </div>
        </div>
    </body>
</html>