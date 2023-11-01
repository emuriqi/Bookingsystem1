<?php
session_start();
include 'emailValid.php'; 

if(!isset($_SESSION['admin_name']) && !isset($_SESSION['user_name'])){
    header('location:login_form.php');
    exit(); // make sure no further processing is done after the redirect
 }
 

$date = "";
$hjelpelærere_id = "";
$msg = "";
$bookings = array();

$mysqli = new mysqli('localhost', 'root', '', 'user_db');

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

if (isset($_GET['date']) && isset($_GET['hjelpelærere_id'])) {
    $date = $_GET['date'];
    $hjelpelærere_id = $_GET['hjelpelærere_id'];

    $stmt = $mysqli->prepare("SELECT * FROM bookings WHERE date = ? AND hjelpelærere_id = ?");
    $stmt->bind_param('si', $date, $hjelpelærere_id);

    if ($stmt->execute()) {
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $bookings[] = $row['timeslot'];
        }

        $stmt->close();
    }
}

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $timeslot = $_POST['timeslot'];
    $hjelpelærere_id = $_POST['hjelpelærer_id'];

    if (!is_valid_email($email)) {
        $msg = "<div class='alert alert-danger'>Ugyldig Email!.</div>";
    }
    elseif (!$hjelpelærere_id) {
        $msg = "<div class='alert alert-danger'>Teacher Assistant ID is missing</div>";
    } elseif (in_array($timeslot, $bookings)) {
        $msg = "<div class='alert alert-danger'>Already Booked</div>";
    } else {
        $stmt = $mysqli->prepare("INSERT INTO bookings (name, timeslot, email, date, hjelpelærere_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('ssssi', $name, $timeslot, $email, $date, $hjelpelærere_id);

        if ($stmt->execute()) {
            $msg = "<div class='alert alert-success'>Booking Successful</div>";
            $bookings[] = $timeslot;
        } else {
            if ($stmt->errno == 1452) {  // Error code for foreign key constraint fail
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
        <a href="home.php" class="navbar-logo">logo</a>
        <ul class="navbar-menu">
            <li class="navbar-menu-item"><a href="update_profile.php">Oppdater profil</a></li>
            <li class="navbar-menu-item"><a href="logout.php">Log ut</a></li>
        </ul>
    </div>
</header>  
    <div class="container">
        <h1 class="text-center">Book for date: <?php echo date('d/m/Y', strtotime($date)); ?></h1>
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
    <!-- Modal -->
    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Booking: <span id="slot"></span></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form action="" method="post">
                                <div class="form-group">
                                    <label for="timeslot">Timeslot</label>
                                    <input required type="text" readonly name="timeslot" id="timeslot" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input required type="text" name="name" id="name" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input required type="text" name="email" id="email" class="form-control">
                                </div>
                                <input type="hidden" name="hjelpelærer_id" value="<?php echo $hjelpelærere_id; ?>">
                                <div class="form-group pull-right">
                                    <button class="btn btn-primary" type="submit" name="submit">Submit</button>
                                </div>
                                <div class="container">
                                <a href="calender.php" class="btn btn-primary">Tilbake til kalender</a>
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