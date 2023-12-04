<?php
// Start sesjonen hvis den allerede ikke er startet
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'no'; 
}

include "lang." . $_SESSION['lang'] . ".php";
?>
