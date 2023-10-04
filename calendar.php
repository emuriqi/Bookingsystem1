<?php
function build_calendar($month, $year) {

    $mysqli = new mysqli('localhost', 'root', '','user_db');
    $stmt = $mysqli->prepare("select * from bookings where MONTH(date) = ? AND YEAR(date)=?");
    $stmt->bind_param('ss',$month,$year);
    $bookings = array();
    if($stmt->execute()){
      $result = $stmt->get_result();
      if($result->num_rows>0){
        while($row = $result->fetch_assoc()){
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
        }else {
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
    <link rel="stylesheet" href="css/calendar.css"> <!-- Husk å inkludere CSS-stilen din -->
    <title>Calendar</title>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <?php
                // Hent dagens dato
                $dateComponents = getdate();
                $month = $dateComponents['mon'];
                $year = $dateComponents['year'];

                if (isset($_GET['month']) && isset($_GET['year'])) {
                    $month = $_GET['month'];
                    $year = $_GET['year'];
                }

                echo build_calendar($month, $year);
                ?>

                    <a class="btn btn-primary" href="?month=<?php echo date("m", mktime(0, 0, 0, $month - 1, 1, $year)); ?>&year=<?php echo date('Y', mktime(0, 0, 0, $month - 1, 1, $year)); ?>">Previous Month</a>
                    <a class="btn btn-primary" href="?month=<?php echo date("m"); ?>&year=<?php echo date('Y'); ?>">Current Month</a>
                    <a class="btn btn-primary" href="?month=<?php echo date("m", mktime(0, 0, 0, $month + 1, 1, $year)); ?>&year=<?php echo date('Y', mktime(0, 0, 0, $month + 1, 1, $year)); ?>">Next Month</a>
                    
            </div>
        </div>
    </div>
</body>
</html>