<?php

@include 'config.php';  // Inkluderer konfigurasjonsfilen som sannsynligvis inneholder databasetilkoblingsinnstillinger

session_start();  // Starter en ny sesjon eller fortsetter den eksisterende

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
            <li class="navbar-menu-item"><a href="update_profile.php">Oppdater profil</a></li>
            <li class="navbar-menu-item"><a href="logout.php">Log ut</a></li>
        </ul>
    </div>
</header>   

<div class="container">

   <div class="content">
      <h1>Hei</h1>
      <h3>Velkommen <span><?php echo $_SESSION['admin_name'] ?></span></h3>
      <p>Logget inn som hjelpelærer</p>
      <a href="calendar.php" class="btn">Dine timer </a>
      <a href="set_availability.php" class="btn">Sett inn tiljenglighet</a>
      <a href="chatpage.php" class="btn">chat</a>
     
   </div>

</div>

</body>
</html>