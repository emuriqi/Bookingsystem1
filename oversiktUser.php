<?php 
session_start();
include 'config.php'; 
if(!isset($_SESSION['user_name'])){
    header('location:login_form.php');
    exit();
}

// Assuming the user's ID is stored in a session variable called 'id'.
if(isset($_SESSION['id'])){
    $id = $_SESSION['id'];
} else {
    // If user ID is not set, redirect to login or give an error
    header('location:login_form.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/oversikt.css">
    <title>Oversikt</title>
</head>
<body>
<header class="navbar">
    <div class="navbar-container">
        <a href="home.php" class="navbar-logo">Logo</a>
        <ul class="navbar-menu">
            <li class="navbar-menu-item"><a href="home.php">Hjem</a></li>
            <li class="navbar-menu-item"><a href="edit.php">Endre Profil</a></li>
            <li class="navbar-menu-item"><a href="index.php">Logg Ut</a></li>
        </ul>
    </div>
</header>

<div class="container my-5">
    <h2>Oversikt</h2>
    <a href="calendar.php" class="btn btn-primary" role="button">Book timer</a>

    <br> 
    <h1 class="overskrift">Dine timer</h1>
    <table class="table">
        <thead>
            <tr>
                <th>Navn</th>
                <th>Klokkeslett</th>
                <th>E-post</th>
                <th>Dato</th>
                <th>Handling</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            // Query that selects only the bookings for the logged-in user
            $sql = "SELECT * FROM bookings WHERE id = $id";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();

            if (!$result) {
                die("Invalid query: " . $conn->error);
            }

            while ($row = $result->fetch_assoc()) {
                $id = $row['id'];
                $name = $row['name'];
                $timeslot = $row['timeslot'];
                $email = $row['email'];
                $date = $row['date'];

                echo "
                <tr>
                    <td>" . htmlspecialchars($name) . "</td>
                    <td>" . htmlspecialchars($timeslot) . "</td>
                    <td>" . htmlspecialchars($email) . "</td>
                    <td>" . htmlspecialchars($date) . "</td>
                    <td>
                        <a class='btn btn-primary btn-sm' href='redigerBooking.php?id=" . htmlspecialchars($id) . "'>Rediger</a>
                        <a class='btn btn-danger btn-sm' href='deleteBooking.php?id=" . htmlspecialchars($id) . "'>Slett</a>
                    </td>
                </tr>
                ";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>
