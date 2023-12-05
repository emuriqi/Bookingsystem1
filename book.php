<?php
session_start();
include 'emailValid.php'; 


if(!isset($_SESSION['admin_name']) && !isset($_SESSION['user_name'])){
    header('location:login_form.php');
    exit(); 
 }
 
//Variabler for dato, hjelpelærerID, meldingsvariabel for tilbakemeldinger og array for bestilte tidspunkter.
$date = "";
$hjelpelærere_id = "";
$msg = "";
$bookings = array();

$mysqli = new mysqli('localhost', 'root', '', 'user_db');

//Dersom tilbokbling mislykkes, avsluttes skriptet og det blir gitt feilmelding.
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

//Sjekker om det er sendt med dato og hjelpelærerensID i URL.
if (isset($_GET['date']) && isset($_GET['hjelpelærere_id'])) {
    $date = $_GET['date'];
    $hjelpelærere_id = $_GET['hjelpelærere_id'];

    //Forbereder en SQL-spørring ved bruk av prepare-metoden som binder parameterne til spørringen.
    $stmt = $mysqli->prepare("SELECT * FROM bookings WHERE date = ? AND hjelpelærere_id = ?");
    $stmt->bind_param('si', $date, $hjelpelærere_id);

    //Dersom spørringen blir vellykket hentes resultatene, og lest ved hjelp av while-løkken.
    if ($stmt->execute()) {
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $bookings[] = $row['timeslot'];
        }

        $stmt->close();
    }
}

//Sjekker om skjemaet er sendt med et parameter som heter "submit". Deretter hentes data fra skjemaet. 
if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $timeslot = $_POST['timeslot'];
    $hjelpelærere_id = $_POST['hjelpelærere_id'];

    //Validering av inndata
    if (!is_valid_email($email)) {
        $msg = "<div class='alert alert-danger'>Ugyldig Email!.</div>";
    }
    elseif (!$hjelpelærere_id) {
        $msg = "<div class='alert alert-danger'>Velg en hjelpelærer</div>";
    } elseif (in_array($timeslot, $bookings)) {
        $msg = "<div class='alert alert-danger'>Denne er allerede booket</div>";

        //Dersom alt er riktig, forberedes SQL uttalelsen for å legge bestilling i databasen. Parametene bindes til sprringen ved hjelp av bind_param().
    } else {
        $stmt = $mysqli->prepare("INSERT INTO bookings (name, timeslot, email, date, hjelpelærere_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('ssssi', $name, $timeslot, $email, $date, $hjelpelærere_id);

        if ($stmt->execute()) {
            $msg = "<div class='alert alert-success'>Booking var vellyket</div>";
            $bookings[] = $timeslot;
        } else {
            if ($stmt->errno == 1452) {  
                $msg = "<div class='alert alert-danger'>Invalid Teacher Assistant ID</div>";
            } else {
                $msg = "<div class='alert alert-danger'>Booking Failed: " . $stmt->error . "</div>";
            }
        }

        $stmt->close();
    }
}

$duration = 60;
$cleanup = 0;
$start = "09:00";
$end = "15:00";

function timeslots($duration, $cleanup, $start, $end) {
    $start = new DateTime($start);
    $end = new DateTime($end);
    $interval = new DateInterval("PT" . $duration . "M");
    $cleanupInterval = new DateInterval("PT" . $cleanup . "M");
    $slots = array();

    for ($intStart = $start; $intStart < $end; $intStart->add($interval)->add($cleanupInterval)) {
        $endPeriod = clone $intStart;
        $endPeriod->add($interval);

        if ($endPeriod > $end) {
            break;
        }

        $slots[] = $intStart->format("H:iA") . "-" . $endPeriod->format("H:iA");
    }

    return $slots;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/book.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Book for date</title>
</head>
<body>
<header class="navbar">
    <div class="navbar-container">
        <a href=".php" class="navbar-logo">logo</a>
        <ul class="navbar-menu">
            <li class="navbar-menu-item"><a href="update_profile.php">Oppdater profil</a></li>
            <li class="navbar-menu-item"><a href="logout.php">Log ut</a></li>
        </ul>
    </div>
</header>  
    <div class="container">
        <h1 class="text-center">Book for denne datoen: <?php echo date('d/m/Y', strtotime($date)); ?></h1>
        <div class="row">
            <div class="col-md-12">
                <?php echo $msg; ?>
            </div>
            <?php
            $timeslots = timeslots($duration, $cleanup, $start, $end);
            foreach ($timeslots as $ts) {
                ?>
                <div class="col-md-2">
                    <div class="form-group">
                        <?php if (in_array($ts, $bookings)) { ?>
                            <button class="btn btn-danger"><?php echo $ts; ?></button>
                        <?php } else { ?>
                            <button class="btn btn-success book" data-timeslot="<?php echo $ts; ?>"><?php echo $ts; ?></button>
                        <?php } ?>
                    </div>
                </div>
            <?php
            }
            ?>
        </div>
    </div>
 
    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Book: <span id="slot"></span></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form action="" method="post">
                                <div class="form-group">
                                    <label for="timeslot">Tidspunkt</label>
                                    <input required type="text" readonly name="timeslot" id="timeslot" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="name">Navn</label>
                                    <input required type="text" name="name" id="name" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input required type="text" name="email" id="email" class="form-control">
                                </div>
                                <input type="hidden" name="hjelpelærere_id" value="<?php echo $hjelpelærere_id; ?>">
                                <div class="form-group pull-right">
                                    <button class="btn btn-primary" type="submit" name="submit">Lagre</button>
                                </div>
                                <div class="container">
                                <a href="calendar.php" class="btn btn-primary">Tilbake til kalender</a>
                                </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(".book").click(function () {
            var timeslot = $(this).attr('data-timeslot');
            $("#slot").html(timeslot);
            $("#timeslot").val(timeslot);
            $("#myModal").modal('show');
        });
    </script>
</body>
</html>