<?php
include_once 'functions.php';

if ($_SESSION['user_role'] != 'subscriber') {
    header("Location: noAccess.php");
    exit;
}

if (!is_signed_in()) {
    echo "Du måste vara inloggad för att se dina prenumerationer.";
    include 'footer.php';
    exit;
}

$mysqli = connectToDatabase();

$query = "SELECT newsletters.* FROM newsletters 
          JOIN subscriptions ON newsletters.id = subscriptions.newsletter_id 
          WHERE subscriptions.user_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

echo "<h2>Mina prenumerationer</h2>";
echo "<ul>";
while ($row = $result->fetch_assoc()) {
    echo "<li>" . $row['title'] . "</li>";
}
echo "</ul>";

$mysqli->close();
?>