<?php
session_start();

function user_has_role($role)
{
    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == $role) {
        return true;
    } else {
        return false;
    }
}


function is_signed_in()
{
    if (isset($_SESSION['user_id']) && $_SESSION['user_id']) {
        return true;
    } else {
        return false;
    }
}


function require_role($role)
{
    if (is_signed_in() && user_has_role($role)) {
        return;
    } else {
        header("Location: /no-access.php");
        exit;
    }
}


function connectToDatabase()
{
    $mysqli = new mysqli("db", "root", "notSecureChangeMe", "uppgift2");
    if ($mysqli->connect_error) {
        error_log("Connection failed: " . $mysqli->connect_error);
        return false;
    }
    return $mysqli;
}



