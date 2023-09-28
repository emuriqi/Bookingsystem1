<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/oversikt.css">
    <title>Oversikt</title>
</head>
<body>
<header class="navbar">
    <div class="navbar-container">
        <a href="home.php" class="navbar-logo">logo</a>
        <ul class="navbar-menu">
            <li class="navbar-menu-item"><a href="home.php">Hjem</a></li>
            <li class="navbar-menu-item"><a href="edit.php">Change Profile</a></li>
            <li class="navbar-menu-item"><a href="index.php">Logout</a></li>
        </ul>
    </div>
</header>

    <h1 class="overskrift">Oversikt</h1>
    <table class="table">
        <thead>
            <tr>
                <th>Dine fremtidige timer</th>
            </tr>
        </thead>
        <tbody>
           
        <tr>
                   
                    <td>Din neste time</td>
                    <td>
                    <a class='btn btn-primary btn-sm' href='rediger-innkjop.php?id=$id'>Endre</a>
                    <a class='btn btn-primary btn-sm' href='delete-innkjop.php?id=$id'>Slett</a>
                </td>
                </tr>
        </tbody>
    </table>
</div>

</body>
</html>
