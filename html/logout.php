<?php
include_once 'functions.php';
include 'header.php';

// Töm sessionsvariabler för inloggning och roll
unset($_SESSION['user_id']);
unset($_SESSION['user_roles']);

// Omdirigera till utloggningssidan
header("Location: logoutMessage.php");
exit;


include 'footer.php';
?>