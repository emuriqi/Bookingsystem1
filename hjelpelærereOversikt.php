<?php
session_start();
include 'config.php'; 
include 'navbar/navbarUser.php';
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

<div class="container">
    <?php
    $query = "SELECT id, name, email, user_type, image, about_me, availability FROM user_form WHERE user_type='admin'";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $helpers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($helpers as $helper) {
        echo '<div class="helper-overview">';
            echo '<img src="' . $helper['image'].' alt="Helper Image>';
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
