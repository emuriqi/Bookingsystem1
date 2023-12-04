<?php 
session_start();
include 'config.php'; 

// Sjekker hvis brukeren ikke er logget på, da blir brukeren omdirigert til login-siden
if(!isset($_SESSION['user_name'])){
    header('location:login_form.php');
    exit();
}

// Bruker 'user_id' som sesjons variabelen til bruker's ID.
if(isset($_SESSION['user_id'])){
    $user_id = $_SESSION['user_id'];
} else {
    // Hvis brukerID ikke er satt, omdirigeres til login eller gir en error
    header('location:login_form.php');
    exit();
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
        <a href="user_page.php" class="navbar-logo">UiA</a>
        <ul class="navbar-menu">
            <li class="navbar-menu-item"><a href="user_page.php">Hjem</a></li>
            <li class="navbar-menu-item"><a href="updateU_profile.php">Endre Profil</a></li>
            <li class="navbar-menu-item"><a href="logout.php">Logg Ut</a></li>
        </ul>
    </div>
</header>

<div class="container my-5">
    <h1>Oversikt</h1>
    <a href="calendar.php" class="btn btn-primary" role="button">Book timer</a> <br>

    <br> 
    <h2 class="overskrift">Dine timer</h2>
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
            // Spørring som kun velger bestillingene for den påloggende brukeren
            $sql = "SELECT bookings.* FROM bookings 
        JOIN user_form ON bookings.email = user_form.email 
        WHERE user_form.user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
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
                        <a class='btn btn-primary btn-sm' href='redigerUserBooking.php?id=" . htmlspecialchars($id) . "'>Rediger</a>
                        <a class='btn btn-danger btn-sm' href='deleteUserBooking.php?id=" . htmlspecialchars($id) . "'>Slett</a>
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
