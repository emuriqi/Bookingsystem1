<?php
include 'language_setup.php';
include 'config.php';

$error = []; // Initialiser feil-array

if (isset($_POST['submit'])) {
    // Rens input
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
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
        $error[] = 'E-postadressen er i feil format!';
    } else {
        // Prepared statement for å sjekke e-post
        $select = $conn->prepare("SELECT * FROM user_form WHERE email = ?");
        $select->bind_param("s", $email);
        $select->execute();
        $result = $select->get_result();

        if ($result->num_rows > 0) {
            $error[] = 'En bruker med denne e-postadressen eksisterer allerede.';
        } else {
            if ($user_type !== 'admin' && $user_type !== 'user') {
                $error[] = 'Ugyldig brukertype valgt.';
            } else {
                // Prepared statement for å sette inn ny bruker
                $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);
                $insert = $conn->prepare("INSERT INTO user_form(name, email, password, user_type) VALUES(?, ?, ?, ?)");
                $insert->bind_param("ssss", $name, $email, $hashed_pass, $user_type);
                $insert->execute();

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
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Form</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
   
<div class="form-container">
    <form action="" method="post">
        <h3><?php echo $lang['register_now']; ?></h3>

        <?php if (!empty($error)) { ?>
            <div class="error-container">
                <?php foreach ($error as $error_message) { ?>
                    <p class="error-msg"><?php echo $error_message; ?></p>
                <?php } ?>
            </div>
        <?php } ?>

        <input type="text" name="name" placeholder="<?php echo $lang['enter_name']; ?>">
        <input type="text" name="email" placeholder="<?php echo $lang['enter_email']; ?>">
        <input type="password" name="password" placeholder="<?php echo $lang['enter_password']; ?>">
        <input type="password" name="cpassword" placeholder="<?php echo $lang['confirm_password']; ?>">
        <select name="user_type">
            <option value="user"><?php echo $lang['user']; ?></option>
            <option value="admin"><?php echo $lang['admin']; ?></option>
        </select>
        <input type="submit" name="submit" value="<?php echo $lang['register_now']; ?>" class="form-btn">
        <p><?php echo $lang['account_exists']; ?> <a href="login_form.php"><?php echo $lang['login_now']; ?></a></p>
    </form>
</div>

</body>
</html>
