<?php
function build_calendar($month, $year, $hjelpelærere_id = 0) {
    $mysqli = new mysqli('localhost', 'root', '', 'user_db');
    
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    // Getting teacher assistants
    $hjelpelærereOptions = "";
    $stmt = $mysqli->prepare("SELECT * FROM hjelpelærere");
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $selected = $hjelpelærere_id == $row['hjelpelærere_id'] ? "selected" : "";
            $hjelpelærereOptions .= "<option value='{$row['hjelpelærere_id']}' $selected>{$row['name']}</option>";
        }
        $stmt->close();
    }

    // Calendar building logic
    $daysOfWeek = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
    $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
    $numberDays = date('t', $firstDayOfMonth);
    $dateComponents = getdate($firstDayOfMonth);
    $monthName = $dateComponents['month'];
    $dayOfWeek = $dateComponents['wday'];
    
    $calendar = "<table class='table table-bordered'>";
    $calendar .= "<center><h2>$monthName $year</h2></center>";
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
        
        // Here's the condition to check for past dates
        if ($date < date('Y-m-d')) {
            $calendar .= "<td><h4>$currentDay</h4><button class='btn btn-danger btn-xs'>N/A</button></td>";
        } else {
            $calendar .= "<td><h4>$currentDay</h4><a href='book.php?date=$date&hjelpelærere_id=$hjelpelærere_id' class='btn btn-success btn-xs'>Book</a></td>";
        }
        
        $currentDay++;
        $dayOfWeek++;
    }
    if ($dayOfWeek != 7) { 
        $remainingDays = 7 - $dayOfWeek;
        for ($l=0; $l < $remainingDays; $l++) { 
            $calendar .= "<td></td>"; 
        }
    }

    $calendar .= "</tr>";
    $calendar .= "</table>";

    $calendar .= "<form method='get'>";
    $calendar .= "<label for='hjelpelærere_id'>Choose Teacher Assistant:</label>";
    $calendar .= "<select id='hjelpelærere_id' name='hjelpelærere_id' onchange='this.form.submit()'>";
    $calendar .= $hjelpelærereOptions;
    $calendar .= "</select>";
    $calendar .= "<input type='hidden' name='month' value='{$month}' />";
    $calendar .= "<input type='hidden' name='year' value='{$year}' />";
    $calendar .= "</form>";

    return $calendar;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/calendar.css">

    <title>Calendar Booking System</title>
    <!-- Insert your CSS and JavaScript links here -->
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <?php
                $dateComponents = getdate();
                $month = isset($_GET['month']) ? $_GET['month'] : $dateComponents['mon'];
                $year = isset($_GET['year']) ? $_GET['year'] : $dateComponents['year'];
                $hjelpelærere_id = isset($_GET['hjelpelærere_id']) ? $_GET['hjelpelærere_id'] : 0;

                echo build_calendar($month, $year, $hjelpelærere_id);
                ?>

                <!-- Navigation Buttons -->
                <a class="btn btn-primary" href="?month=<?php echo date('m', mktime(0, 0, 0, $month - 1, 1, $year)); ?>&year=<?php echo date('Y', mktime(0, 0, 0, $month - 1, 1, $year)); ?>">Previus Month</a>
                <a class="btn btn-primary" href="?month=<?php echo date("m"); ?>&year=<?php echo date("Y"); ?>">Current Month</a>
                <a class="btn btn-primary" href="?month=<?php echo date("m", mktime(0, 0, 0, $month + 1, 1, $year)); ?>&year=<?php echo date("Y", mktime(0, 0, 0, $month + 1, 1, $year)); ?>">Next Month</a>
            </div>
        </div>
    </div>
</body>
</html>