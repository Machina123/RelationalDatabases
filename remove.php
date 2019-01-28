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
        $starid=$_POST["star"];
        $query = "SELECT event_id FROM tm_events WHERE star_id=$starid";
        $result = $db->query($query);
        if($result->num_rows > 0) {
            $badquery=true;
            $queryerror = "Istnieją wydarzenia przypisane do tej gwiazdy";
        }
        if(!$badquery) {
            $query = "DELETE FROM tm_stars WHERE star_id=$starid";
            $db->query($query);
        }
    } elseif($mode=="venue") {
        $venueid=$_POST["venue"];
        $query = "SELECT event_id FROM tm_events WHERE venue_id=$venueid";
        $result = $db->query($query);
        if($result->num_rows > 0) {
            $badquery=true;
            $queryerror = "Istnieją wydarzenia przypisane do tej areny";
        }
        if(!$badquery) {
            $query = "DELETE FROM tm_venues WHERE venue_id=$venueid";
            $db->query($query);
        }
    } elseif($mode=="event") {
        $eventid=$_POST["event"];
        $query = "DELETE FROM tm_tickets WHERE event_id=$eventid";
        $db->query($query);
        $query = "DELETE FROM tm_events WHERE event_id=$eventid";
        $db->query($query);
    }
    
    if($db->errno) $message="Wystąpił błąd: " . $db->error();
    elseif($badquery) $message="Błąd zapytania: " . $queryerror;
    else $message="Dane usunięto pomyślnie";

    header("Location: admin.php?message=".urlencode($message));
?>