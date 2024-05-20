<?php
ob_start();
include_once 'functions.php';
include 'header.php';

unset($_SESSION['user_id']);
unset($_SESSION['user_roles']);
header("Location: index.php");
exit;


include 'footer.php';
?>

<?php
ob_end_flush();
?>