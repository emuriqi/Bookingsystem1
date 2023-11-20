<?php
include 'language_setup.php';
include 'config.php';  // Inkluderer konfigurasjonsfilen som inneholder databasetilkoblingsinnstillinger.

session_start(); // Starter en ny session eller gjenopptar en eksisterende.

$error = []; // Initialiserer en array for å holde feilmeldinger.

if (isset($_POST['submit'])) {  // Sjekker om skjemaet er sendt.

    // Renser e-postadressen for å forhindre XSS.
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];  // Passordet som brukeren har innskrevet.

    // Forberedt uttalelse for å forhindre SQL-injeksjon.
    $select = $conn->prepare("SELECT * FROM user_form WHERE email = ?");
    $select->bind_param("s", $email);
    $select->execute();
    $result = $select->get_result();

    if ($result->num_rows > 0) {  // Hvis en bruker er funnet.
        $row = $result->fetch_assoc();  // Henter brukerdata.

        // Verifiser passordet
        if (password_verify($password, $row['password'])) {
            $_SESSION['id'] = $row['id'];  // Lagrer brukerens ID i en sesjonsvariabel.

            // Sjekker brukerens type og omdirigerer til riktig side.
            if ($row['user_type'] == 'admin') {
                $_SESSION['admin_name'] = $row['name'];
                header('location:admin_page.php');
                exit();
            } elseif ($row['user_type'] == 'user') {
                $_SESSION['user_name'] = $row['name'];
                header('location:user_page.php');
                exit();
            }
        } else {
            $error[] = 'Incorrect email or password!';  // Feilmelding hvis passordet ikke stemmer.
        }
    } else {
        $error[] = 'Incorrect email or password!';  // Feilmelding hvis ingen bruker er funnet.
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
   
<div class="form-container">
    <form action="" method="post">
        <h3><?php echo $lang['login_now']; ?></h3>
        <?php
        if (isset($error)) {
            foreach ($error as $error) {
                echo '<span class="error-msg">' . $error . '</span>';
            }
        }
        ?>
        <input type="email" name="email" required placeholder="<?php echo $lang['enter_email']; ?>">
        <input type="password" name="password" required placeholder="<?php echo $lang['enter_password']; ?>">
        <input type="submit" name="submit" value="Login Now" class="form-btn">
        <p><?php echo $lang['account_exists']; ?> <a href="register_form.php"><?php echo $lang['register_now']; ?></a></p>
    </form>
</div>

</body>
</html>
