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
        $user_is_subscriber = user_has_role('subscriber'); // Kontrollera om användaren har rollen 'subscriber'
        $user_is_customer = user_has_role('customer'); // Kontrollera om användaren har rollen 'customer'
        ?>
        <ul>
            <li><a href="index.php">Startsidan</a></li>
            <?php if ($user_is_subscriber) { // Om användaren är en prenumerant ?>
                <li><a href="myPage.php">Mina sidor</a></li>
            <?php } ?>
            <?php if ($user_is_customer) { // Om användaren är en kund ?>
                <li><a href="myPage.php">Mina sidor</a></li>
            <?php } ?>
            <li><a href="logout.php">Logga ut</a></li>
        </ul>
        <?php
    } else {
        // Visa inloggning och registreringsknappar om användaren är utloggad
        ?>
        <ul>
            <li><a href="index.php">Startsidan</a></li>
            <li><a href="login.php">Logga in</a></li>
            <li><a href="createAccount.php">Skapa konto</a></li>
        </ul>
        <?php
    }
    ?>
</nav>
</header>
