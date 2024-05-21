<?php
include_once 'functions.php';
?>

<!DOCTYPE html>
<html lang="sv">

<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <link rel="stylesheet" href="style.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Din Webbplats</title>
    <style>
        nav {
            background-color: #7CCBB3;
            padding: 10px;
            color: black;
        }
        nav ul {
            list-style-type: none;
            padding: 0;
            margin: 0; 
            display: flex;
            justify-content: space-around;
        }

        nav li {
            margin-right: 20px;
        }

        nav li:last-child {
            margin-right: 0; 
        }

        nav a {
            text-decoration: none;
            color: inherit;
        }

        nav a:hover {
            text-decoration: underline;
        }

        header {
            margin-bottom: 40px;
        }
    </style>
</head>

<body>

    <header>
        <nav>
            <?php
            if (is_signed_in()) {
                $user_is_subscriber = user_has_role('subscriber');
                $user_is_customer = user_has_role('customer');
            ?>
                <ul>
                    <li><a href="index.php">Startsidan</a></li>
                    <?php if ($user_is_subscriber) { 
                    ?>
                        <li><a href="myPage.php">Mina sidor</a></li>
                    <?php } ?>
                    <?php if ($user_is_customer) { 
                    ?>
                        <li><a href="myPage.php">Mina sidor</a></li>
                    <?php } ?>
                    <li><a href="logout.php">Logga ut</a></li>
                </ul>
            <?php
            } else {
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