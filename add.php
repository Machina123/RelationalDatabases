<?php
    include("database.inc.php");
    $db = mysqli_connect($dbHostname, $dbUsername, $dbPassword, $dbDatabase)
    or die("Blad polaczenia z baza danych!");
    $db->set_charset("utf-8");
    if(!isset($_POST["mode"])) {
        die("Wykonano nieprawidlowe zapytanie!");
    }

    $mode = $_POST["mode"];
    $message="";
    $badquery = false;
    $queryerror = "";

    if($mode=="star") {
        $starname=$db->real_escape_string($_POST["name"]);
        $query = "SELECT star_id FROM tm_stars WHERE star_name='$starname'";
        $result = $db->query($query);
        if($result->num_rows) {
            $badquery=true;
            $queryerror = "Taka gwiazda juz istnieje!";
        }
        if(!$badquery) {
            $query = "INSERT INTO tm_stars(star_name) VALUES (?)";
            $stmt = $db->prepare($query);
            $stmt->bind_param("s", $starname);
            $stmt->execute();
        }
    } elseif($mode=="venue") {
        $venuename=$db->real_escape_string($_POST["name"]);
        $query = "SELECT venue_id FROM tm_venues WHERE venue_name='$venuename'";
        $result = $db->query($query);
        if($result->num_rows) {
            $badquery=true;
            $queryerror = "Arena o takiej nazwie juz istnieje!";
        }
        if(!$badquery) {
            $query = "INSERT INTO tm_venues(venue_name, venue_address, venue_capacity) VALUES (?,?,?)";
            $stmt = $db->prepare($query);
            $stmt->bind_param("ssi", $venuename, $venueaddr, $venuecap);
            $venueaddr=$db->real_escape_string($_POST["address"]);
            $venuecap=$_POST["cap"];
            $stmt->execute();
        }
    } elseif($mode=="event") {
        $venueid=$_POST["venue"];
        $starid=$_POST["star"];
        $availtix=$_POST["tickets"];
        $tixprice=$_POST["price"];
        $eventdate=$db->real_escape_string(str_replace("T", " ", $_POST["date"]));
        $query = "SELECT event_id FROM tm_events WHERE (venue_id=$venueid AND event_date='$eventdate') OR (star_id=$starid AND event_date='$eventdate')";
        $result = $db->query($query);
        if($result->num_rows) {
            $badquery=true;
            $queryerror = "Arena lub gwiazda są w tym czasie zajęte!";
        }
        $query = "SELECT venue_capacity FROM tm_venues WHERE venue_id=$venueid";
        $result = $db->query($query);
        if($result->num_rows) {
            $row = $result->fetch_assoc();
            if(intval($availtix) > intval($row["venue_capacity"])) {
                $badquery=true;
                $queryerror = "Nie mozna sprzedać więcej biletów niz miejsc!";
            }
        }
        if(!$badquery) {
            $query = "INSERT INTO tm_events(venue_id, star_id, available_tickets, ticket_price, event_date) VALUES (?,?,?,?,?)";
            $stmt = $db->prepare($query);
            $stmt->bind_param("iiids", $venueid, $starid, $availtix, $tixprice, $eventdate);
            $stmt->execute();
        }
    }
    if($db->insert_id) $message="Dane zapisano pomyślnie";
    elseif($db->errno) $message="Wystąpił błąd: " . $db->error();
    elseif($badquery) $message="Błąd zapytania: " . $queryerror;

    $db->close();

    header("Location: admin.php?message=".urlencode($message));
?>