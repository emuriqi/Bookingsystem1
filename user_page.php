<?php
// Inkluderer konfigurasjonsfilen, som kan inneholde databasetilkoblingsdetaljer og andre innstillinger.
include 'config.php';
include 'language_setup.php';

// Starter en ny eller fortsetter en eksisterende sesjon.

// Sjekker om brukeren er logget inn. Hvis ikke, blir brukeren omdirigert til innloggingssiden.
if(!isset($_SESSION['user_name'])){
   header('location:login_form.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <!-- Standard metainnstillinger for tegnsett, kompatibilitet og visningsstørrelse. -->
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">

   <!-- Tittelen på websiden. -->
   <title>user page</title>

   <!-- Lenke til en ekstern CSS-fil for å style siden. -->
   <link rel="stylesheet" href="css/style.css">
</head>
<body>
<!-- En navigasjonsbar som inneholder en logo, og lenker for å oppdatere profilen og logge ut. -->
<header class="navbar">
    <div class="navbar-container">
        <a href="user_page.php" class="navbar-logo">UiA</a>
        <ul class="navbar-menu">
            <li class="navbar-menu-item"><a href="updateU_profile.php">Oppdater profil</a></li>
            <li class="navbar-menu-item"><a href="logout.php">Logg ut</a></li>
        </ul>
    </div>
</header>   

<!-- Hovedinnholdet på siden, som inkluderer en hilsen til brukeren og en knapp for å booke tid. -->
<div class="container">
   <div class="content">
      <h1>Hei:</h1>
      <h1>Velkommen <span><?php echo $_SESSION['user_name'] ?></span></h1>
      <!-- PHP-koden ovenfor trekker brukernavnet fra sesjonen og viser det på siden. -->
      <p>Logget inn som student</p>

      <a href="hjelpelærereoversikt.php" class="btn">Hjelpelærere</a>
      <a href="oversiktUser.php" class="btn">Dine Timer</a>
      <a href="calendar.php" class="btn">Book Time</a>
      <a href="chatpage.php" class="btn">Chat</a>
      <!-- Lenken ovenfor skal sannsynligvis lede til en side hvor brukeren kan booke tid, men merkelig nok fører den til innloggingssiden. -->
   </div>
</div>
</body>
</html>
