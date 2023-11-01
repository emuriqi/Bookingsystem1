<?php

session_start();
include 'config.php';


$id = "";
$date = "";
$timeslot = "";
$email = "";



$duration = 60;
$cleanup = 0;
$start = "09:00";
$end = "15:00";

function timeslots($duration, $cleanup, $start, $end)
{
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


if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (!isset($_GET["id"])) {
        header("location: oversikt.php");
        exit;
    }

    $id = $_GET["id"];

    $stmt = $conn->prepare("SELECT * FROM bookings WHERE id=?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result || $result->num_rows == 0) {
        header("Location: oversikt.php");
        exit;
    }

    $row = $result->fetch_assoc();
    $id = $row['id'];
    $date = $row['date'];
    $timeslot = $row['timeslot'];
    $email = $row["email"];

    $stmt = $conn->prepare("SELECT * FROM bookings WHERE id=?");
$stmt->bind_param("s", $id);

    
    $stmt->execute();
    $bookedTimeslots = $stmt->get_result();
    $bookings = array();

while ($booking = $bookedTimeslots->fetch_assoc()) {
    $bookings[] = $booking['timeslot'];
}
} else {
    $id = $_POST["id"];
    $date = $_POST["date"];
    $timeslot = $_POST["timeslot"];
    $email = $_POST["email"];

    do {
        if (empty($id) || empty($date) || empty($timeslot) || empty($email)) {
            $errorMessage = "Alle feltene må fylles ut";
            break;
        }
      
        $stmt = $conn->prepare("UPDATE bookings SET Date=?, Timeslot=?, email=? WHERE id=?");
        $stmt->bind_param("ssss", $date, $timeslot, $email, $id);
        $result = $stmt->execute();

      
        if(!$result) {
            $errorMessage = "Feil ved oppdatering av informasjon: " . $conn->error;
            break;
        }
     
        $successMessage = "Oppdatering vellykket";
        header("Location: oversikt.php");
        exit;
    } while (false);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="css/redigerBooking.css">
    <title>Omsetning</title>
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

    <h1 class="overskrift">Dato</h1>
    <div class="container my-5">
 
        <form method="post" action="">
            <input type="hidden" name="id" value="<?php echo $id ?>">
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Dato</label>
                <div class="col-sm-6">
                    <input type="date" class="form-control" name="date" value="<?php echo $date ?>">
                </div>
            </div>
            <div class="row mb-3">
    <?php
    $timeslots = timeslots($duration, $cleanup, $start, $end);
    foreach ($timeslots as $ts) {
    ?>
    <div class="col-md-2">
        <div class="form-group">
            <?php if (in_array($ts, $bookings)) { ?>
                <button type="button" class="btn btn-danger" disabled><?php echo $ts; ?></button>
            <?php } else { ?>
                <button type="button" class="btn btn-success select-timeslot" data-timeslot="<?php echo $ts; ?>"><?php echo $ts; ?></button>
            <?php } ?>
        </div>
    </div>
    <?php
    }
    ?>
    <input type="hidden" name="timeslot" id="selected-timeslot" value="<?php echo $timeslot ?>">
</div>
            
        
               

            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">E-post</label>
                <div class="col-sm-6">
                    <input type="email" class="form-control" name="email" value="<?php echo $email ?>">
                </div>
            </div>

            <div class="row mb-3">
                <button class="btn btn-outline-primary" type="submit">Lagre</button>
            </div>

            <div class="row mb-3">
                <a class="btn btn-outline-primary" href="oversikt.php">Avbryt</a>
            </div>
        </form>
        
        
    <script>
    $(".select-timeslot").click(function () {
        var timeslot = $(this).attr('data-timeslot');
        $("#selected-timeslot").val(timeslot);
        
        // Optionally, change the visual indication of the selected timeslot:
        $(".select-timeslot").removeClass('btn-primary').addClass('btn-success');
        $(this).removeClass('btn-success').addClass('btn-primary');
    });
</script>

</body>

</html>