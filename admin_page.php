<?php
session_start();

include 'language_setup.php';
@include 'config.php';  // Inkluderer konfigurasjonsfilen som sannsynligvis inneholder databasetilkoblingsinnstillinger

 // Starter en ny sesjon eller fortsetter den eksisterende

// Hvis det ikke er satt noen sesjonsvariabel for admin_name, blir brukeren omdirigert til innloggingssiden
if(!isset($_SESSION['admin_name'])){
   header('location:login_form.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>admin page</title>

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
<header class="navbar">
    <div class="navbar-container">
        <a href="home.php" class="navbar-logo">logo</a>
        <ul class="navbar-menu">
            <li class="navbar-menu-item"><a href="update_profile.php"> <?php echo $lang['update_profile']; ?>
            </a></li>
            <li class="navbar-menu-item"><a href="logout.php"><?php echo $lang['logout']; ?></a></li>
        </ul>
    </div>
</header>   

<div class="container">

   <div class="content">
      <h1><?php echo $lang['hello']; ?>:</h1>
      <h3><?php echo $lang['welcome']; ?>: <span><?php echo $_SESSION['admin_name'] ?></span></h3>
      <p><?php echo $lang['logged_as_ta']; ?></p>
      <a href="oversikt.php" class="btn"><?php echo $lang['your_appointments']; ?></a>
      <a href="calendar.php" class="btn"><?php echo $lang['set_availability']; ?></a>
      <a href="chatpage.php" class="btn"><?php echo $lang['chat']; ?></a>
     
   </div>
</div>

<div>
<a href="?lang=no">Norsk</a> | <a href="?lang=en">English</a>
</div>

</body>
</html>