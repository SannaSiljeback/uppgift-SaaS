<?php
include_once 'functions.php';
// include 'header.php';

// Kontrollera användarens roll
if ($_SESSION['user_role'] != 'subscriber') {
    // Användaren har inte rätt behörighet, omdirigera till no-access-sidan
    header("Location: noAccess.php");
    exit;
}

// Kontrollera om användaren är inloggad
if (!is_signed_in()) {
    echo "Du måste vara inloggad för att se dina prenumerationer.";
    include 'footer.php';
    exit;
}

$mysqli = connectToDatabase();

// Hämta data från databasen
$query = "SELECT newsletters.* FROM newsletters 
          JOIN subscriptions ON newsletters.id = subscriptions.newsletter_id 
          WHERE subscriptions.user_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

// Bearbeta och visa data
echo "<h2>Mina prenumerationer</h2>";
echo "<ul>";
while ($row = $result->fetch_assoc()) {
    echo "<li>" . $row['title'] . "</li>";
}
echo "</ul>";

// Stäng anslutningen till databasen
$mysqli->close();

// include 'footer.php';
?>