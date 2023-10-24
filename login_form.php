<?php

@include 'config.php';  // Inkluderer konfigurasjonsfilen som inneholder databasetilkoblingsinnstillinger eller annen konfigurasjon.

session_start();  // Starter sesjonen slik at brukerdata kan lagres i sesjonsvariabler etter vellykket innlogging.

if(isset($_POST['submit'])){  // Sjekker om skjemaet er sendt.

   // Saniterer og lagrer brukerinput.
   $name = mysqli_real_escape_string($conn, $_POST['name']); 
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $pass = md5($_POST['password']);  // MD5 for passordhashing (ikke anbefalt pga sikkerhetsproblemer).
   $cpass = md5($_POST['cpassword']);  
   $user_type = $_POST['user_type'];

   // SQL-spørring for å sjekke om brukeren med gitt e-post og passord eksisterer i databasen.
   $select = "SELECT * FROM user_form WHERE email = '$email' && password = '$pass'";
   $result = mysqli_query($conn, $select);

   if(mysqli_num_rows($result) > 0){  // Hvis en bruker er funnet.

      $row = mysqli_fetch_array($result);  // Henter brukerdata.
      $_SESSION['id'] = $row['id'];  // Lagrer brukerens ID i en sesjonsvariabel.

      // Sjekker brukerens type og omdirigerer til riktig side.
      if($row['user_type'] == 'admin'){
         $_SESSION['admin_name'] = $row['name'];  
         header('location:admin_page.php');
      }elseif($row['user_type'] == 'user'){
         $_SESSION['user_name'] = $row['name'];
         header('location:user_page.php');
      }
     
   } else{
      $error[] = 'incorrect email or password!';  // Legger til en feilmelding hvis ingen bruker er funnet.
   }
};

?>
<!DOCTYPE html>
<html lang="en">
<head>
   <!-- Standard metadata og tilkobling til eksternt CSS-stilark. -->
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>login form</title>
   <link rel="stylesheet" href="css/style.css">
</head>
<body>
   
<div class="form-container">
   <form action="" method="post">
      <h3>login now</h3>
      <?php
      // Viser eventuelle feilmeldinger til brukeren.
      if(isset($error)){
         foreach($error as $error){
            echo '<span class="error-msg">'.$error.'</span>';
         };
      };
      ?>
      <!-- Inputfelt for e-post og passord, samt en innsendingsknapp. -->
      <input type="email" name="email" required placeholder="enter your email">
      <input type="password" name="password" required placeholder="enter your password">
      <input type="submit" name="submit" value="login now" class="form-btn">
      <!-- Lenke for brukere som ikke har en konto, til å registrere seg. -->
      <p>don't have an account? <a href="register_form.php">register now</a></p>
   </form>
</div>

</body>
</html>
