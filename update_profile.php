<?php

include 'language_setup.php';
include 'config.php';

if(!isset($_SESSION['admin_name'])){
   header('location:login_form.php');
}

$user_id = $_SESSION['user_id'];  // Henter brukerens ID fra sesjonen.

$fetch = array();
// Sjekker om brukeren har sendt inn oppdateringsformularet.
if(isset($_POST['update_profile'])){
   $select = mysqli_query($conn, "SELECT * FROM `user_form` WHERE user_id = '$user_id'") or die('query failed');
   if(mysqli_num_rows($select) > 0){
      $fetch = mysqli_fetch_assoc($select);
   } else {
      // Håndter tilfeller der ingen rader blir returnert
      echo "Ingen brukerdata funnet.";
      exit;
   }

   // Saniterer og lagrer brukerens inndata.
   // Oppdaterer brukerens navn og e-post i databasen.
   $about_me = isset($_POST['about_me']) ? mysqli_real_escape_string($conn, $_POST['about_me']) : '';
   $availability = isset($_POST['availability']) ? mysqli_real_escape_string($conn, $_POST['availability']) : '';
   
   // Oppdatere databasen med nye tekstområdedata, hvis de ikke er tomme.
   if($about_me !== '' || $availability !== ''){
       mysqli_query($conn, "UPDATE `user_form` SET about_me = '$about_me', availability = '$availability' WHERE user_id = '$user_id'") or die('query failed');
   }

   $update_name = mysqli_real_escape_string($conn, $_POST['update_name']);
   $update_email = mysqli_real_escape_string($conn, $_POST['update_email']);
   mysqli_query($conn, "UPDATE `user_form` SET name = '$update_name', email = '$update_email' WHERE user_id = '$user_id'") or die('query failed');

   // Håndterer oppdatering av brukerens passord.
   $old_pass = $_POST['old_pass']; // Dette bør være passordet i klar tekst som brukeren oppgir
$stored_pass = $fetch['password']; // Dette er det hashede passordet hentet fra databasen

$update_pass = $_POST['update_pass']; // Det nye passordet i klar tekst
$new_pass = $_POST['new_pass'];
$confirm_pass = $_POST['confirm_pass'];

if (!empty($update_pass) || !empty($new_pass) || !empty($confirm_pass)) {
    if (!password_verify($update_pass, $stored_pass)) {
        $message[] = 'old password not matched!';
    } elseif ($new_pass != $confirm_pass) {
        $message[] = 'confirm password not matched!';
    } else {
        $hashed_new_pass = password_hash($new_pass, PASSWORD_DEFAULT);
        mysqli_query($conn, "UPDATE `user_form` SET password = '$hashed_new_pass' WHERE user_id = '$user_id'") or die('query failed');
        $message[] = 'password updated successfully!';
    }
}
   // Håndterer oppdatering av brukerens profilbilde.
   $update_image = $_FILES['update_image']['name'];
   $update_image_size = $_FILES['update_image']['size'];
   $update_image_tmp_name = $_FILES['update_image']['tmp_name'];
   $update_image_folder = 'uploaded_img/'.$update_image;
   if(!empty($update_image)){
      if($update_image_size > 2000000){
         $message[] = 'image is too large';
      }else{
         // Oppdaterer bildeinformasjonen i databasen og flytter bildet til en angitt mappe.
         $image_update_query = mysqli_query($conn, "UPDATE `user_form` SET image = '$update_image' WHERE user_id = '$user_id'") or die('query failed');
         if($image_update_query){
            move_uploaded_file($update_image_tmp_name, $update_image_folder);
         }
         $message[] = 'image updated succssfully!';
      }
   }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <!-- Metadata og stilark for siden -->
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>update profile</title>
   <link rel="stylesheet" href="css/updateProfile.css">
</head>

<body>
<header class="navbar">
    <!-- Navigasjonsbar med lenker -->
    <div class="navbar-container">
        <a href="admin_page.php" class="navbar-logo">UiA</a>
        <ul class="navbar-menu">
            <li class="navbar-menu-item"><a href="update_profile.php">Oppdater profil</a></li>
            <li class="navbar-menu-item"><a href="logout.php">Log ut</a></li>
        </ul>
    </div>
</header>  
<div class="update-profile">
   <!-- PHP for å hente brukerens nåværende data -->
   <?php
      $select = mysqli_query($conn, "SELECT * FROM `user_form` WHERE user_id = '$user_id'") or die('query failed');
      if(mysqli_num_rows($select) > 0){
         $fetch = mysqli_fetch_assoc($select);
      }
   ?>

   <form action="" method="post" enctype="multipart/form-data">
      <!-- Viser brukerens nåværende profilbilde eller en standard avatar -->
      <?php
         if($fetch['image'] == ''){
            echo '<img src="images/default-avatar.png">';
         }else{
            echo '<img src="uploaded_img/'.$fetch['image'].'">';
         }
         // Viser meldinger (for eksempel feil eller bekreftelser)
         if(isset($message)){
            foreach($message as $message){
               echo '<div class="message">'.$message.'</div>';
            }
         }
      ?>
<div class="flex">
         <div class="inputBox">
            <!-- Felt for å endre brukernavn, e-post og profilbilde -->
            <span>Navn :</span>
            <input type="text" name="update_name" value="<?php echo $fetch['name']; ?>" class="box">
            <span>Email :</span>
            <input type="email" name="update_email" value="<?php echo $fetch['email']; ?>" class="box">
            <span>Oppdater profilbilde :</span>
            <input type="file" name="update_image" accept="image/jpg, image/jpeg, image/png" class="box">
            <span for="about_me">Skriv om deg selv og erfaring :</span>
            <textarea id="about_me" name="about_me" rows="10" cols="50"><?php echo isset($fetch['about_me']) ? $fetch['about_me'] : ''; ?></textarea>
         </div>
         <div class="inputBox">
            <!-- Felt for å endre passord -->
            <input type="hidden" name="old_pass" value="<?php echo $fetch['password']; ?>">
            <span>Gammelt passord :</span>
            <input type="password" name="update_pass" placeholder="enter previous password" class="box">
            <span>Nytt passord :</span>
            <input type="password" name="new_pass" placeholder="enter new password" class="box">
            <span>Bekreft passord :</span>
            <input type="password" name="confirm_pass" placeholder="confirm new password" class="box">
            <span for="availability">Hva veilder du i og når du er ledig :</span>
            <textarea id="availability" name="availability" rows="10" cols="50"><?php echo isset($fetch['availability']) ? $fetch['availability'] : ''; ?></textarea>
         </div>
      </div>
      <input type="submit" value="Oppdater profil" name="update_profile" class="btn">
<a href="admin_page.php" class="delete-btn">Tilbake</a>
   </form>
</div>
</body>
</html>

