<?php 
    include("database.inc.php");
    $db = mysqli_connect($dbHostname, $dbUsername, $dbPassword, $dbDatabase)
    or die("Blad polaczenia z baza danych!");
    $db->set_charset("utf-8");
    if(!isset($_POST["customer"])) die("Wykonano nieprawidlowe zapytanie");
    
    $query = "SELECT event_id, available_tickets FROM tm_events WHERE event_id=".$_POST["event"];
    $result = $db->query($query);
    $row = $result->fetch_assoc();
    $tickets_before=$row["available_tickets"];
    if(intval($_POST["amount"]) > $row["available_tickets"]) echo "Brak wystarczającej ilości biletów. Przekierowanie nastąpi za 5 sekund";
    else {
        $ticket_amount = intval($_POST["amount"]);
        for($i = 0; $i < $ticket_amount; $i++) {
            $query = "INSERT INTO tm_tickets (event_id, customer_id, ticket_checksum) VALUES (?,?,?)";
            $stmt = $db->prepare($query);
            $stmt->bind_param("iis", $eid, $cid, $chksum);
            $eid = $_POST["event"];
            $cid = $_POST["customer"];
            $chksum = substr(uniqid("TM", true), 0, 20);
            $stmt->execute();
        }

        $query = "UPDATE tm_events SET available_tickets=" . (intval($tickets_before)-$ticket_amount) . " WHERE event_id=".$_POST["event"];
        $result = $db->query($query);
        if($db->errno) echo "Błąd SQL: " . $db->error . ". Przekierowanie nastąpi za 5 sekund";
        else echo "Zapytanie wykonane prawidłowo, sprawdź zakładkę Moje bilety. Przekierowanie nastąpi za 5 sekund";
    }
    header("Refresh: 5; url=shop.php");
    ?>