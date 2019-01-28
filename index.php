<?php
    include("database.inc.php");
    
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>TicketManager</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" />
        <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"></script>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-12"><h1 class="text-center">TicketManager</h1></div>
                <div class="col-12">
                    <p class="text-center">
                    <?php
                    $db = mysqli_connect($dbHostname, $dbUsername, $dbPassword, $dbDatabase);
                    if($db->connect_errno) echo "Błąd połączenia z bazą danych: " . $db->connect_error();
                    else echo "Połączono z bazą danych";
                    $db->close();
                    ?>
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-6"><a href="shop.php" class="btn btn-primary w-100">Sklep</a></div>
                <div class="col-12 col-md-6"><a href="admin.php" class="btn btn-primary w-100">Panel administracyjny</a></div>
            </div>
        </div>
    </body>
</html>