<?php 
session_start();
include 'config.php'; 
if(!isset($_SESSION['admin_name']) && !isset($_SESSION['user_name'])){
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
        <a href="admin_page.php" class="navbar-logo">UiA</a>
        <ul class="navbar-menu">
            <li class="navbar-menu-item"><a href="admin_page.php">Hjem</a></li>
            <li class="navbar-menu-item"><a href="update_profile.php">Endre profil</a></li>
            <li class="navbar-menu-item"><a href="logout.php">Logg ut</a></li>
        </ul>
    </div>
</header>

<div class="container my-5">
    <h1>Oversikt</h1>
    <a href="calendar.php "class="btn btn-primary" href="home.php" role="button">Book timer</a> <br>

    <br> 
    <h2 class="overskrift">Dine timer</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Klokkeslett</th>
                <th>Email</th>
                <th>Dato</th>
                <th>Handling</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            
            $sql = "SELECT * FROM bookings";
            $result = $conn->query($sql); 

            if (!$result) {
                die("Invalid query: " . $conn->error);
            }

            while ($row = $result->fetch_assoc()) {
                $id = $row['id'];
                $name = $row['name'];
                $timeslot = $row['timeslot'];
                $email = $row['email'];
                $date = $row['date'];
                $hjelpelærere_id = $row['hjelpelærere_id'];
               

                echo "
                <tr>
                    <td>$name</td>
                    <td>$timeslot</td>
                    <td>$email</td>
                    <td>$date</td>
                    <td>
                    <a class='btn btn-primary btn-sm' href='redigerBooking.php?id=$id'>Edit</a>
                    <a class='btn btn-primary btn-sm' href='deleteBooking.php?id=$id'>Delete</a>
                </td>
                </tr>
                ";
            }
            ?>
        </tbody>
    </table>