<?php
// Inkluderer konfigurasjonsfilen og starter sesjonen.
include 'language_setup.php';
include 'config.php';
session_start();
$id = $_SESSION['id'];  // Henter brukerens ID fra sesjonen.

// Sjekker om brukeren har sendt inn oppdateringsformularet.
if(isset($_POST['update_profile'])){
   // Saniterer og lagrer brukerens inndata.
   // Oppdaterer brukerens navn og e-post i databasen.
   $about_me = isset($_POST['about_me']) ? mysqli_real_escape_string($conn, $_POST['about_me']) : '';
   $availability = isset($_POST['availability']) ? mysqli_real_escape_string($conn, $_POST['availability']) : '';
   
   // Update the database with the new textarea data, if they are not empty.
   if($about_me !== '' || $availability !== ''){
       mysqli_query($conn, "UPDATE `user_form` SET about_me = '$about_me', availability = '$availability' WHERE id = '$id'") or die('query failed');
   }

   $update_name = mysqli_real_escape_string($conn, $_POST['update_name']);
   $update_email = mysqli_real_escape_string($conn, $_POST['update_email']);
   mysqli_query($conn, "UPDATE `user_form` SET name = '$update_name', email = '$update_email' WHERE id = '$id'") or die('query failed');

   // Håndterer oppdatering av brukerens passord.
   $old_pass = $_POST['old_pass'];
   $update_pass = mysqli_real_escape_string($conn, md5($_POST['update_pass']));
   $new_pass = mysqli_real_escape_string($conn, md5($_POST['new_pass']));
   $confirm_pass = mysqli_real_escape_string($conn, md5($_POST['confirm_pass']));
   if(!empty($update_pass) || !empty($new_pass) || !empty($confirm_pass)){
      if($update_pass != $old_pass){
         $message[] = 'old password not matched!';
      }elseif($new_pass != $confirm_pass){
         $message[] = 'confirm password not matched!';
      }else{
         mysqli_query($conn, "UPDATE `user_form` SET password = '$confirm_pass' WHERE id = '$id'") or die('query failed');
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
         $image_update_query = mysqli_query($conn, "UPDATE `user_form` SET image = '$update_image' WHERE id = '$id'") or die('query failed');
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
        <a href="home.php" class="navbar-logo">logo</a>
        <ul class="navbar-menu">
            <li class="navbar-menu-item"><a href="update_profile.php">Oppdater profil</a></li>
            <li class="navbar-menu-item"><a href="logout.php">Log ut</a></li>
        </ul>
    </div>
</header>  
<div class="update-profile">
   <!-- PHP for å hente brukerens nåværende data -->
   <?php
      $select = mysqli_query($conn, "SELECT * FROM `user_form` WHERE id = '$id'") or die('query failed');
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
            <span>username :</span>
            <input type="text" name="update_name" value="<?php echo $fetch['name']; ?>" class="box">
            <span>your email :</span>
            <input type="email" name="update_email" value="<?php echo $fetch['email']; ?>" class="box">
            <span>update your pic :</span>
            <input type="file" name="update_image" accept="image/jpg, image/jpeg, image/png" class="box">
            <span for="about_me">Skriv om deg selv og erfaring :</span>
            <textarea id="about_me" name="about_me" rows="10" cols="50"><?php echo isset($fetch['about_me']) ? $fetch['about_me'] : ''; ?></textarea>
         </div>
         <div class="inputBox">
            <!-- Felt for å endre passord -->
            <input type="hidden" name="old_pass" value="<?php echo $fetch['password']; ?>">
            <span>old password :</span>
            <input type="password" name="update_pass" placeholder="enter previous password" class="box">
            <span>new password :</span>
            <input type="password" name="new_pass" placeholder="enter new password" class="box">
            <span>confirm password :</span>
            <input type="password" name="confirm_pass" placeholder="confirm new password" class="box">
            <span for="availability">Hva veilder du i og når du er ledig :</span>
            <textarea id="availability" name="availability" rows="10" cols="50"><?php echo isset($fetch['availability']) ? $fetch['availability'] : ''; ?></textarea>
         </div>
      </div>
      <input type="submit" value="update profile" name="update_profile" class="btn">
<a href="admin_page.php" class="delete-btn">go back</a>
   </form>
</div>
</body>
</html>

