<?php
include 'header.php';
include_once 'functions.php';

// 1. Anslut till databasen
$mysqli = new mysqli("db", "root", "notSecureChangeMe", "uppgift2");

// Kontrollera anslutningen
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    exit();
}

// 2. Hämta prenumerationer för en specifik användare
$user_id = $_SESSION['user_id'] ?? ''; // Antag att du har använt sessioner för att lagra användar-ID
$query = "SELECT * FROM subscriptions WHERE user_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// 3. Hämta nyhetsbrevens information och visa data på sidan
echo "<h2>Nyhetsbrev som du prenumererar på</h2>";
echo "<ul>";

$newsletter_stmt = null; // Definiera $newsletter_stmt utanför loopen och sätt det till null

if ($result->num_rows > 0) { // Kontrollera om det finns resultat från den första SQL-frågan
    while ($row = $result->fetch_assoc()) {
        $newsletter_id = $row['newsletter_id'];
        $newsletter_query = "SELECT * FROM newsletters WHERE id = ?";
        $newsletter_stmt = $mysqli->prepare($newsletter_query);
        $newsletter_stmt->bind_param("s", $newsletter_id);
        $newsletter_stmt->execute();
        $newsletter_result = $newsletter_stmt->get_result();
        $newsletter = $newsletter_result->fetch_assoc();
        echo "<li>" . $newsletter['title'] . "</li>";
    }
}

echo "</ul>";

// Stäng anslutningen till databasen
$stmt->close();

if ($newsletter_stmt !== null) { // Kontrollera om $newsletter_stmt är definierad innan du försöker stänga den
    $newsletter_stmt->close();
}

$mysqli->close();

include 'footer.php';
?>