<?php
include_once 'functions.php';
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Din Webbplats</title>
    <!-- Länka till CSS-filer, JavaScript-filer, osv. här -->
</head>
<body>

<header>
    <!-- Navigationsmeny -->
<nav>
    <?php
    // Bestäm användarens roll baserat på inloggningstillstånd
    if (is_signed_in()) {
        $user_role = user_has_role('subscriber'); // Kontrollera om användaren har rollen 'subscriber'
        ?>
        <ul>
            <li><a href="allNewsletters.php">Alla nyhetsbrev</a></li>
            <?php if ($user_role) { // Om användaren är en prenumerant ?>
                <li><a href="mySubscriptions.php">Mina prenumerationer</a></li>
            <?php } ?>
            <li><a href="logout.php">Logga ut</a></li>
        </ul>
        <?php
    } else {
        // Visa inloggning och registreringsknappar om användaren är utloggad
        ?>
        <ul>
            <li><a href="allNewsletters.php">Alla nyhetsbrev</a></li>
            <li><a href="login.php">Logga in</a></li>
            <li><a href="createAccount.php">Skapa konto</a></li>
        </ul>
        <?php
    }
    ?>
</nav>
</header>
