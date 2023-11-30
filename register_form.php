<?php
include 'language_setup.php';
include 'config.php';


$error = []; // Initialiser feil-array

if (isset($_POST['submit'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = $_POST['password'];
    $cpass = $_POST['cpassword'];
    $user_type = $_POST['user_type']; // Henter brukertype fra POST-data


    // Passordvalidering
    $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/';
    if (!preg_match($pattern, $pass)) {
        $error[] = 'Passordet må inneholde minst en stor bokstav, ett tall, og være minst 8 tegn langt.';
    } elseif ($pass !== $cpass) {
        $error[] = 'Passordene stemmer ikke overens.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error[] = 'E-postadressen er skrevet på feil format!';
    } else {
        // Prepared statement for å sjekke e-post
        $select = $conn->prepare("SELECT * FROM user_form WHERE email = ?");
        $select->bind_param("s", $email);
        $select->execute();
        $result = $select->get_result();

   

    if (!($uppercase && $lowercase && $number && strlen($password) >= 8)) {
        $error[] = 'Passordet må ha minst 8 tegn og inkludere minst en stor bokstav og et tall';
    }

    // Sjekker om en bruker allerede eksisterer med samme e-post.
    $select = "SELECT * FROM user_form WHERE email = ?";
    $stmt = mysqli_prepare($conn, $select);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if(mysqli_stmt_num_rows($stmt) > 0){
        $error[] = 'Bruker finnes allerede!';
    } else {
        $select = "SELECT * FROM user_form WHERE email = '$email'";
        $result = mysqli_query($conn, $select);
        if (mysqli_num_rows($result) > 0) {
            $error[] = 'En bruker med denne e-postadressen eksisterer allerede.';
        } else {
            if ($user_type !== 'admin' && $user_type !== 'user') {
                $error[] = 'Ugyldig brukertype valgt.';
            } else {
                $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);
                $insert = "INSERT INTO user_form(name, email, password, user_type) VALUES('$name','$email','$hashed_pass','$user_type')";
                mysqli_query($conn, $insert);
                header('location:login_form.php');
                exit();
            }
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
   <title>register form</title>
   <link rel="stylesheet" href="css/style.css">  <!--Lenker til det eksterne CSS-stilarket.-->
</head>
<body>
   
<div class="form-container">

   <form action="" method="post">
      <h3><?php echo $lang['register_now']; ?></h3>
      <?php
      // Viser eventuelle feilmeldinger eller suksessmelding.
      if(isset($error)){
         foreach($error as $error){
            echo '<span class="error-msg">'.$error.'</span>';
         };
      } elseif (isset($success_message)) {
         echo '<span class="success-msg">'.$success_message.'</span>';
      }
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
      <input type="submit" name="submit" value="registrer nå" class="form-btn">
      <!-- Lenke til innloggingssiden for eksisterende brukere -->
      <p><?php echo $lang['account_exists']; ?> <a href="login_form.php"><?php echo $lang['login_now']; ?></a></p>
   </form>

</div>

</body>
</html>

