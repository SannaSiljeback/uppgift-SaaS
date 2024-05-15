<?php
ob_start(); // Starta outputbuffring
include_once 'functions.php';
include 'header.php';

// Töm sessionsvariabler för inloggning och roll
unset($_SESSION['user_id']);
unset($_SESSION['user_roles']);

// Omdirigera till utloggningssidan
header("Location: index.php");
exit;


include 'footer.php';
?>

<?php
ob_end_flush(); // Skicka buffrad output till webbläsaren
?>