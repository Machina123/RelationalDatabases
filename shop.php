<?php
    include("database.inc.php");
    $db = mysqli_connect($dbHostname, $dbUsername, $dbPassword, $dbDatabase)
    or die("Blad polaczenia z baza danych!");

    if(isset($_POST["action"])) {
        if($_POST["action"] == "register") {
            $query = "INSERT INTO tm_customers (customer_name, customer_phoneno) VALUES(?,?)";
            $stmt = $db->prepare($query);
            $stmt->bind_param("ss", $name, $phoneno);
            $name = $_POST["name"];
            $phoneno = $_POST["phone"];
            $stmt->execute();
        } 
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Sklep - TicketManager</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" />
        <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"></script>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1 class="text-center">Sklep - TicketManager</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-6">
                    <h2 class="text-center">Zaloguj się</h2><br>
                    <?php
                        $query = "SELECT * FROM tm_customers";
                        $result = $db->query($query);
                        if($result->num_rows) {
                            echo "<form method=\"post\">";
                            echo "<input type=\"hidden\" name=\"action\" value=\"login\"/>";
                            echo "<select class=\"form-control\" id=\"customer_id\" name=\"customer_id\">";
                            while($res = $result->fetch_assoc()) {
                                echo '<option value="'.$res['customer_id'] . '">ID:'. $res['customer_id'] . " - " . $res['customer_name'] . "</option>";
                            }
                            echo "</select>";
                            echo "<button class=\"btn btn-primary w-100 my-3\" type=\"submit\">Zaloguj się</button>";
                            echo "</form>";
                        } else {
                            echo <<<EOT
                        <div class="alert alert-danger">Aktualnie nie ma w bazie żadnego użytkownika</div>
EOT;
                        }
                    ?>
                    <hr>
                    <?php if(!isset($_POST["customer_id"])): ?>
                    <form method="post">
                        <input type="hidden" name="action" value="register">
                        <h2 class="text-center">Zarejestruj się</h2>
                        <div class="form-group">
                            <label for="name">Imię i nazwisko</label>
                            <input type="text" name="name" id="name" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="phone">Numer telefonu</label>
                            <input type="tel" name="phone" id="phone" class="form-control">
                        </div>
                        <button type="submit" class="w-100 btn btn-primary">Zarejestruj się</button>
                    </form>
                    <?php else: ?>
                    <?php
                        $query = "SELECT * FROM tm_customers WHERE customer_id=" . $_POST["customer_id"];
                        $res = $db->query($query);
                        if($result->num_rows) {
                            $row = $res->fetch_assoc();
                            echo "<div class=\"alert alert-primary my-3\">Witaj, <strong>" . $row["customer_name"] . "</strong></div>";
                            echo <<<EOT
                                <form method="post" action="tickets.php">
                                    <input type="hidden" name="filter" value="customer" />
                                    <input type="hidden" name="customer" value="{$_POST["customer_id"]}"/>
                                    <button type="submit" class="btn btn-primary w-100">Moje bilety</button>
                                </form>
                                <form method="post" action="buy.php">
                                    <input type="hidden" name="customer" value="{$_POST["customer_id"]}">
                                    <button type="submit" class="btn btn-primary w-100 my-3">Kup bilety</button>
                                </form>
EOT;
                        } else {
                            echo "<div class=\"alert alert-danger\">Nieprawidłowy użytkownik!</div>";
                        }
                    ?>
                    <?php endif; ?>
                </div>
                <div class="col-12 col-md-6">
                    <h2 class="text-center">Najbliższe wydarzenia</h2>
                    <?php
                        $query = "SELECT tm_stars.star_name, tm_venues.venue_name, tm_venues.venue_address, tm_events.available_tickets, tm_events.event_date FROM tm_events JOIN tm_stars ON tm_events.star_id=tm_stars.star_id JOIN tm_venues ON tm_venues.venue_id=tm_events.venue_id WHERE tm_events.event_date >=\"". date("Y-m-d") ."\" ORDER BY tm_events.event_date ASC LIMIT 5";
                        $result = $db->query($query);
                        if($db->errno) {
                            echo $db->error();
                        } else {
                            if($result->num_rows) {
                                while($res = $result->fetch_assoc()) {
                                    echo <<<EOT
                                    <div class="card w-100 mx-3">
                                        <div class="card-body">
                                            <h5 class="card-title">{$res["star_name"]}</h5>
                                            <h6 class="card-subtitle text-muted mb-2">{$res["venue_name"]}</h6>
                                            <p class="card-text">
                                                Adres areny: {$res["venue_address"]}<br>
                                                Data wydarzenia: {$res["event_date"]}<br>
                                                Dostępnych biletów: {$res["available_tickets"]}
                                            </p>
                                        </div>
                                    </div>
EOT;
                                }
                            } else {
                                echo "<div class=\"alert alert-primary\">Brak wydarzeń w bazie, wszystkie trwają lub zostały zakończone</div>";
                            }
                        }
                    ?>
                </div>
            </div>
        </div>
    </body>
</html>
<?php
    $db->close();