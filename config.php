<?php
//her kobler vi til databasen og sier hvor den skal kobles til
$conn = mysqli_connect('localhost','root','','user_db');
$pdo = new PDO('mysql:host=localhost;dbname=user_db', 'root', '');

?>