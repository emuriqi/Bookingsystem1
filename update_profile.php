<?php 
include 'config.php';
session_start();

// Check if user_id is set in the session
if(isset($_SESSION['id'])){
    $user_id = $_SESSION['id'];

    if(isset($_POST['update_profile'])){
        $update_name = mysqli_real_escape_string($conn, $_POST['update_name']);
        $update_email = mysqli_real_escape_string($conn, $_POST['update_email']);

        mysqli_query($conn, "UPDATE `user_form` SET name = '$update_name', email = '$update_email' WHERE id = '$user_id'") or die('query failed');
    }
} else {
    // Handle the case where user_id is not set in the session
    echo "Session id is not set.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>

    <!-- custom css file link  -->
   <link rel="stylesheet" href="css/update_profile.css">
</head>
<body>
   <div class="update_profile">

   <?php
      $fetch = array(); // Initialize $fetch as an empty array
      $select = mysqli_query($conn, "SELECT * FROM `user_form` WHERE id = '$user_id'")
      or die('query failed');
      if(mysqli_num_rows($select) > 0){
        $fetch = mysqli_fetch_assoc($select);
      }
   ?>

    <form action="update_profile.php" method="post" enctype="multipart/form-data">
        <?php
        if($fetch['image'] == ''){
            echo '<img src="images/default-avatar.png">';
         }else {
            echo '<img src="uploaded_img/'.$fetch['image'].'">';
         }
        ?>

         <div class="flex">
         <div class="inputbox">
         <span>username:</span>
         <input type="text" name="update_name" value="<?php echo $fetch['name']?>" class="box">
         <span>your email :</span>
         <input type="email" name="update_email" value="<?php echo $fetch['email']?>" class="box">
         <span>update your pic:</span>
         <input type="file" name="update_image"  class="box" accept="image/jpg, image/jpeg, image/png">
    </div>
         <div class="inputbox">
         <input type="hidden" name="old_name" value="<?php echo $fetch['password']?>">
         <span>old password :</span>
         <input type="password" name="update_pass" placeholder="enter previous password" class="box">
         <span>new password :</span>
         <input type="password" name="new_pass" placeholder="enter new password" class="box">
         <span>confirm password :</span>
         <input type="password" name="confirm_pass" placeholder="confirm new password" class="box">
     </div>
     </div>
         <input type="submit" value="update profile" name="update_profile">
         <a href="home.php" class="delete-btn">go back</a>
     </form>
   </div>
</body>
</html>

