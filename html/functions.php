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
    if (isset($_SESSION['is_signed_in']) && $_SESSION['is_signed_in']) {
        return true;
    } else {
        return false;
    }
}



//funktion för att se en viss roll, fungerar denna? när ska den användas?
function require_role($role)
{
    // Kontrollera om användaren är inloggad och har rätt roll
    if (is_signed_in() && user_has_role($role)) {
        // Användaren har rätt roll, inget behov av omdirigering
        return;
    } else {
        // Användaren har inte rätt roll, gör en redirect till /no-access.php
        header("Location: /no-access.php");
        exit;
    }
}

//connect to database
function connectToDatabase()
{
    $mysqli = new mysqli("db", "root", "notSecureChangeMe", "uppgift2");
    if ($mysqli->connect_error) {
        error_log("Connection failed: " . $mysqli->connect_error);
        return false;
    }
    return $mysqli;
}
