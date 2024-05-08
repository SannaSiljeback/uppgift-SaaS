<?php
include_once 'functions.php';
include 'header.php';

if (isset($_SESSION['user_id'])) {
    echo '<a href="logout.php">Logga ut</a>';
}

include 'footer.php';
?>