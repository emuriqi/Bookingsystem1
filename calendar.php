<?php
function build_calendar($month, $year, $hjelpelærer) {
    // Database connection
    $mysqli = new mysqli('localhost', 'root', '', 'user_db');


    // Query to retrieve hjelpelærere
    $stmt = $mysqli->prepare("SELECT * FROM hjelpelærere");
    $hjelpelærerOptions = "";
    $first_hjelpelærer = 0;
    $i = 0;


    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                if ($i == 0) {
                    $first_hjelpelærer = $row['id'];
                }
                $hjelpelærerOptions .= "<option value=" . $row['id'] . ">" . $row['name'] . "</option>";
                $i++;
            }
            $stmt->close();
        }
    }


    if ($hjelpelærer != 0) {
        $first_hjelpelærer = $hjelpelærer;
    }


    // Query to retrieve bookings
    $stmt = $mysqli->prepare("SELECT * FROM bookings WHERE MONTH(date) = ? AND YEAR(date) = ? AND hjelpelærer_id = ?");
    $stmt->bind_param('ssi', $month, $year, $first_hjelpelærer);
    $bookings = array();


    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $bookings[] = $row['date'];
            }
            $stmt->close();
        }
    }


    $daysOfWeek = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
    $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
    $numberDays = date('t', $firstDayOfMonth);
    $dateComponents = getdate($firstDayOfMonth);
    $monthName = $dateComponents['month'];
    $dayOfWeek = $dateComponents['wday'];
    $dateToday = date('Y-m-d');


    $calendar = "<table class='table table-bordered'>";
    $calendar .= "<center><h2>$monthName $year</h2>";


    $calendar .= "
    <form id='hjelpelærer_select_form'>
    <div class='row'>
     <div class='col-md-6 col-md-offset-3 form-group'>
     <label>Velg hjelpelærer</label>
        <select class='form-control' id='room_select' name='hjelpelærer'>
        " . $hjelpelærerOptions . "
        </select>
        <input type='hidden' name='month' value=" . $month . ">
        <input type='hidden' name='year' value=" . $year . ">
       
    </div>
    </div>
    </form>


    <table class='table table-bordered'>";
    $calendar .= "<tr>";


    foreach ($daysOfWeek as $day) {
        $calendar .= "<th class='header'>$day</th>";
    }


    $calendar .= "</tr><tr>";


    if ($dayOfWeek > 0) {
        for ($k = 0; $k < $dayOfWeek; $k++) {
            $calendar .= "<td></td>";
        }
    }


    $currentDay = 1;
    $month = str_pad($month, 2, "0", STR_PAD_LEFT);


    while ($currentDay <= $numberDays) {
        if ($dayOfWeek == 7) {
            $dayOfWeek = 0;
            $calendar .= "</tr><tr>";
        }


        $currentDayRel = str_pad($currentDay, 2, "0", STR_PAD_LEFT);
        $date = "$year-$month-$currentDayRel";
        $dayname = strtolower(date("D", strtotime($date)));
        $eventNum = 0;
        $today = $date == date('Y-m-d') ? "today" : "";


        if ($date < date('Y-m-d')) {
            $calendar .= "<td><h4>$currentDay</h4> <button class='btn btn-danger btn-xs'>N/A</button></td>";
        } else {
            $calendar .= "<td class='$today'><h4>$currentDay</h4> <a href='book.php?date=$date' class='btn btn-success btn-xs'>Book</a>";
        }


        $currentDay++;
        $dayOfWeek++;
    }


    if ($dayOfWeek != 7) {
        $remainingDays = 7 - $dayOfWeek;
        for ($i = 0; $i < $remainingDays; $i++) {
            $calendar .= "<td></td>";
        }
    }


    $calendar .= "</tr>";
    $calendar .= "</table>";


    return $calendar;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/calendar.css"> <!-- Make sure to include your CSS file -->
    <title>Calendar</title>
        <!-- ... Make sure to include your CSS file and other meta tags ... -->


       
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
        <div class="row">
            <div class="col-md-12">
                <?php
                // Get current date
                $dateComponents = getdate();
                $month = $dateComponents['mon'];
                $year = $dateComponents['year'];


                if (isset($_GET['month']) && isset($_GET['year'])) {
                    $month = $_GET['month'];
                    $year = $_GET['year'];
                }


                if (isset($_GET['hjelpelærer'])) {
                    $hjelpelærer = $_GET['hjelpelærer'];
                } else {
                    $hjelpelærer = 0;
                }
                echo build_calendar($month, $year, $hjelpelærer);
                ?>


                <a class="btn btn-primary" href="?month=<?php echo date("m", mktime(0, 0, 0, $month - 1, 1, $year)); ?>&year=<?php echo date('Y', mktime(0, 0, 0, $month - 1, 1, $year)); ?>">Previous Month</a>
                <a class="btn btn-primary" href="?month=<?php echo date("m"); ?>&year=<?php echo date('Y'); ?>">Current Month</a>
                <a class="btn btn-primary" href="?month=<?php echo date("m", mktime(0, 0, 0, $month + 1, 1, $year)); ?>&year=<?php echo date('Y', mktime(0, 0, 0, $month + 1, 1, $year)); ?>">Next Month</a>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function() {
            // Listen for changes on the #room_select element (not #hjelpelærer_select)
            $("#room_select").change(function() {
                // Submit the form with the id 'hjelpelærer_select_form'
                $("#hjelpelærer_select_form").submit();
            });


            // Set the selected option in the select element
            $("#room_select option[value='<?php echo $hjelpelærer; ?>']").attr('selected', 'selected');
        });
    </script>
</body>
</html>





