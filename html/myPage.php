<?php
include_once 'functions.php';
include 'header.php';



if ($_SESSION['user_role'] == 'customer') {
    
    include 'myNewsletter.php';
    include 'subscribers.php';
} elseif ($_SESSION['user_role'] == 'subscriber') {
    echo "Välkommen till mina sidor";
    include 'mySubscriptions.php';
    include 'theNewsletter.php';
    
}



?>