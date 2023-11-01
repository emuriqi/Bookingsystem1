<?php
include 'language_setup.php';
include 'config.php';  // Inkluderer konfigurasjonsfilen som antakelig inneholder databasetilkoblingsinnstillingene.

if(isset($_POST['submit'])){  // Sjekker om skjemaet er sendt.

   // Saniterer og lagrer brukerens inndata.
   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $pass = md5($_POST['password']);  // Bruker MD5 for passordhashing, som ikke anbefales på grunn av sikkerhetsproblemer.
   $cpass = md5($_POST['cpassword']);  // Gjør det samme for bekreftelsespassordet.
   $user_type = $_POST['user_type'];  // Henter brukertypen.

   // Sjekker om en bruker allerede eksisterer med samme e-post og passord.
   $select = " SELECT * FROM user_form WHERE email = '$email' && password = '$pass' ";
   $result = mysqli_query($conn, $select);

   if(mysqli_num_rows($result) > 0){  // Hvis en bruker allerede eksisterer, opprettes en feilmelding.
      $error[] = 'user already exist!';
   }else{
      // Sjekker om passordet og bekreftelsespassordet matcher.
      if($pass != $cpass){
         $error[] = 'password not matched!';
      }else{
         // Innskudd av brukerens data i databasen og omdirigering til innloggingssiden.
         $insert = "INSERT INTO user_form(name, email, password, user_type) VALUES('$name','$email','$pass','$user_type')";
         mysqli_query($conn, $insert);
         header('location:login_form.php');
      }
   }
};

?>
<!DOCTYPE html>
<html lang="en">
<head>
   <!-- Metadata og stilark for siden -->
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>register form</title>
   <link rel="stylesheet" href="css/style.css">  <!--Lenker til det eksterne CSS-stilarket.-->
</head>
<body>
   
<div class="form-container">

   <form action="" method="post">
      <h3><?php echo $lang['register_now']; ?></h3>
      <?php
      // Viser eventuelle feilmeldinger.
      if(isset($error)){
         foreach($error as $error){
            echo '<span class="error-msg">'.$error.'</span>';
         };
      };
      ?>
      <!-- Inputfelter for navn, e-post, passord, bekreft passord og brukertype -->
      <input type="text" name="name" required placeholder="<?php echo $lang['enter_name']; ?>">
      <input type="email" name="email" required placeholder="<?php echo $lang['enter_email']; ?>">
      <input type="password" name="password" required placeholder="<?php echo $lang['enter_password']; ?>">
      <input type="password" name="cpassword" required placeholder="<?php echo $lang['confirm_password']; ?>">
      <select name="user_type">
         <option value="user"><?php echo $lang['user']; ?> </option>
         <option value="admin"><?php echo $lang['admin']; ?></option>
      </select>
      <!-- Registreringsknapp -->
      <input type="submit" name="submit" value="register now" class="form-btn">
      <!-- Lenke til innloggingssiden for eksisterende brukere -->
      <p><?php echo $lang['account_exists']; ?> <a href="login_form.php"><?php echo $lang['login_now']; ?></a></p>
   </form>

</div>

</body>
</html>
