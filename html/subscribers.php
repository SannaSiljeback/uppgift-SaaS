<?php
include_once 'functions.php';

if ($_SESSION['user_role'] != 'customer') {
    header("Location: noAccess.php");
    exit;
}

if (!is_signed_in()) {
    echo "Du måste vara inloggad för att se dina prenumeranter.";
    include 'footer.php';
    exit;
}

$mysqli = connectToDatabase();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

$sqlGetNewsletter = "SELECT id FROM newsletters WHERE owner = ?";
$stmtGetNewsletter = $mysqli->prepare($sqlGetNewsletter);

if (!$stmtGetNewsletter) {
    die("Prepare failed: " . $mysqli->error);
}
$stmtGetNewsletter->bind_param("i", $_SESSION['user_id']);
$stmtGetNewsletter->execute();
$resultGetNewsletter = $stmtGetNewsletter->get_result();

if ($resultGetNewsletter->num_rows > 0) {
    $newsletter = $resultGetNewsletter->fetch_assoc();
    $newsletter_id = $newsletter['id'];

    $sqlGetSubscribers = "SELECT u.email, u.role 
                          FROM users u 
                          JOIN subscriptions us ON u.id = us.user_id 
                          WHERE us.newsletter_id = ?";
    $stmtGetSubscribers = $mysqli->prepare($sqlGetSubscribers);
    if (!$stmtGetSubscribers) {
        die("Prepare failed: " . $mysqli->error);
    }
    $stmtGetSubscribers->bind_param("i", $newsletter_id);
    $stmtGetSubscribers->execute();
    $resultGetSubscribers = $stmtGetSubscribers->get_result();

    echo "<h2>Mina prenumeranter</h2>";
    echo "<ul>";
    while ($row = $resultGetSubscribers->fetch_assoc()) {
        echo "<li>" . $row['email'] . "</li>";
    }
    echo "</ul>";
} else {
    echo "Inga prenumeranter hittades för den inloggade användaren.";
}
}

$mysqli->close();

include 'footer.php';
