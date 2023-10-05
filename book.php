<?php
// Initialize variables
$date = "";
$msg = "";
$bookings = array(); // Initialiser bookings som en tom array

$mysqli = new mysqli('localhost', 'root', '', 'user_db');

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

if (isset($_GET['date'])) {
    $date = $_GET['date'];
    $stmt = $mysqli->prepare("SELECT * FROM bookings WHERE date = ?");
    $stmt->bind_param('s', $date);

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

    // Sjekk om tidspunktet allerede er booket
    if (in_array($timeslot, $bookings)) {
        $msg = "<div class='alert alert-danger'>Already Booked</div>";
    } else {
        $stmt = $mysqli->prepare("INSERT INTO bookings (name, timeslot, email, date) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('ssss', $name, $timeslot, $email, $date);

        if ($stmt->execute()) {
            $msg = "<div class='alert alert-success'>Booking Successful</div>";
            $bookings[] = $timeslot;
        } else {
            $msg = "<div class='alert alert-danger'>Booking Failed</div>";
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

