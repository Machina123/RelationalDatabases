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
                    <h1 class="text-center">Przeglądanie wydarzeń</h1>
                    <?php 
                        if($_POST["filter"] == "star") {
                            $query = "SELECT star_name FROM tm_stars WHERE star_id=".$_POST["star"];
                            $result = $db->query($query);
                            if($result->num_rows) {
                                $row = $result->fetch_assoc();
                                echo <<<EOT
                                <h4 class="text-center text-muted">Gwiazda: {$row["star_name"]}</h4>
EOT;
                            }

                            $query = "SELECT venue_name, event_date, event_id, available_tickets FROM tm_events JOIN tm_venues ON tm_venues.venue_id=tm_events.venue_id WHERE star_id=".$_POST["star"];
                            $result = $db->query($query);
                            if($result->num_rows) {
                                while($row = $result->fetch_assoc()) {
                                    echo <<<EOT
                                    <div class="alert alert-dark">Wydarzenie #{$row["event_id"]} | Arena: {$row["venue_name"]} | Data: {$row["event_date"]} | Dostępne bilety: {$row["available_tickets"]}</div>
EOT;
                                }
                            }

                        } elseif($_POST["filter"] == "venue") {
                            $query = "SELECT venue_name FROM tm_venues WHERE venue_id=".$_POST["venue"];
                            $result = $db->query($query);
                            if($result->num_rows) {
                                $row = $result->fetch_assoc();
                                echo <<<EOT
                                <h4 class="text-center text-muted">Arena: {$row["venue_name"]}</h4>
EOT;
                            }
                            $query = $query = "SELECT star_name, event_date, event_id, available_tickets FROM tm_events JOIN tm_stars ON tm_stars.star_id=tm_events.star_id WHERE venue_id=".$_POST["venue"];
                            $result = $db->query($query);
                            if($result->num_rows) {
                                while($row = $result->fetch_assoc()) {
                                    echo <<<EOT
                                    <div class="alert alert-dark">Wydarzenie #{$row["event_id"]} | Gwiazda: {$row["star_name"]} | Data: {$row["event_date"]} | Dostępne bilety: {$row["available_tickets"]}</div>
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