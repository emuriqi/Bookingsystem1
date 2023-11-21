<?php
session_start();
include 'config.php'; 

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hjelpelærere Overikt</title>
    <link rel="stylesheet" href="css/hjelpelærereOversikt.css">
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


<div class="container">
    <?php
    $query = "SELECT id, name, email, user_type, image, about_me, availability FROM user_form WHERE user_type='admin'";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $helpers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($helpers as $helper) {
        echo '<div class="helper-overview">';
        echo '<img src="uploaded_img/' . $helper['image'] . '" alt="Helper Image">';
            echo '<div class="helper-overview-details">';
                echo '<div class="helper-overview-title">' . $helper['name'] . '</div>';
                echo '<div class="helper-overview-content">Email: ' . $helper['email'] . '</div>';
                echo '<div class="helper-overview-content">About Me: ' . $helper['about_me'] . '</div>';
                echo '<div class="helper-overview-content">Availability: ' . $helper['availability'] . '</div>';
            echo '</div>';
        echo '</div>';
    }
    ?>
</div>

</body>
</html>
