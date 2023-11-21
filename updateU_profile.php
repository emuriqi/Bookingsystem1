<?php
// Inkluderer konfigurasjonsfilen, som sannsynligvis inneholder databasetilkoblingsdetaljer.
session_start();
include 'config.php';
if(!isset($_SESSION['user_name'])){
   header('location:login_form.php');
}


// Henter bruker-ID fra sesjonen.
$id = $_SESSION['id'];

// Sjekker om brukeren har sendt inn skjemaet for å oppdatere profilen.
if(isset($_POST['update_profile'])){

   // Saniterer og henter inn inndata fra brukeren for å forhindre SQL-injeksjon.
   $update_name = mysqli_real_escape_string($conn, $_POST['update_name']);
   $update_email = mysqli_real_escape_string($conn, $_POST['update_email']);

   // Oppdaterer brukerens navn og e-post i databasen.
   mysqli_query($conn, "UPDATE `user_form` SET name = '$update_name', email = '$update_email' WHERE id = '$id'") or die('query failed');

   // Henter og behandler passorddata fra skjemaet.
   $old_pass = $_POST['old_pass'];
   $update_pass = mysqli_real_escape_string($conn, md5($_POST['update_pass']));
   $new_pass = mysqli_real_escape_string($conn, md5($_POST['new_pass']));
   $confirm_pass = mysqli_real_escape_string($conn, md5($_POST['confirm_pass']));

   // Sjekker om brukeren forsøker å endre passordet.
   if(!empty($update_pass) || !empty($new_pass) || !empty($confirm_pass)){
      // Validerer det gamle passordet og bekreftelsen av det nye passordet.
      if($update_pass != $old_pass){
         $message[] = 'old password not matched!';
      }elseif($new_pass != $confirm_pass){
         $message[] = 'confirm password not matched!';
      }else{
         // Oppdaterer passordet i databasen hvis valideringen er vellykket.
         mysqli_query($conn, "UPDATE `user_form` SET password = '$confirm_pass' WHERE id = '$id'") or die('query failed');
         $message[] = 'password updated successfully!';
      }
   }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <!-- Definerer metainnstillinger og inkluderer en ekstern CSS-fil for styling. -->
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>update profile</title>
   <link rel="stylesheet" href="css/updateUprofile.css">
</head>
<body>
<!-- Viser en navigasjonsbar med lenker for profiloppdatering og utlogging. -->
<header class="navbar">
    <div class="navbar-container">
        <a href="home.php" class="navbar-logo">logo</a>
        <ul class="navbar-menu">
            <li class="navbar-menu-item"><a href="updateU_profile.php">Oppdater profil</a></li>
            <li class="navbar-menu-item"><a href="logout.php">Log ut</a></li>
        </ul>
    </div>
</header>   

<!-- Viser et skjema der brukeren kan oppdatere sin profilinformasjon og passord. -->
<div class="update-profile">

   <?php
      // Henter brukerdata fra databasen basert på bruker-ID.
      $select = mysqli_query($conn, "SELECT * FROM `user_form` WHERE id = '$id'") or die('query failed');
      if(mysqli_num_rows($select) > 0){
         $fetch = mysqli_fetch_assoc($select);
      }
   ?>

   <form action="" method="post" enctype="multipart/form-data">
      <?php
         // Viser eventuelle meldinger relatert til profiloppdatering.
         if(isset($message)){
            foreach($message as $message){
               echo '<div class="message">'.$message.'</div>';
            }
         }
      ?>
      <!-- Skjemafelter for å oppdatere brukerinformasjon og passord. -->
      <div class="input-container">
            <div class="inputBox">
                <span>username :</span>
                <input type="text" name="update_name" value="<?php echo $fetch['name']; ?>" class="box">
                <span>your email :</span>
                <input type="email" name="update_email" value="<?php echo $fetch['email']; ?>" class="box">
            </div>
            <div class="inputBox">
                <input type="hidden" name="old_pass" value="<?php echo $fetch['password']; ?>">
                <span>old password :</span>
                <input type="password" name="update_pass" placeholder="enter previous password" class="box">
                <span>new password :</span>
                <input type="password" name="new_pass" placeholder="enter new password" class="box">
                <span>confirm password :</span>
                <input type="password" name="confirm_pass" placeholder="confirm new password" class="box">
            </div>
        </div>
        <!-- Knapper for å sende inn skjemaet eller gå tilbake til brukersiden. -->
        <div class="btn-container">
            <input type="submit" value="update profile" name="updateU_profile" class="btn">
            <a href="user_page.php" class="delete-btn">go back</a>
        </div>
   </form>
</body>
</html>
