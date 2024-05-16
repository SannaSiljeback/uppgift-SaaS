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

// 2. Hämta nyhetsbrev som ägs av en specifik användare
$user_id = $_SESSION['user_id'] ?? ''; // Antag att du har använt sessioner för att lagra användar-ID
$query = "SELECT * FROM newsletters WHERE owner = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// 3. Visa nyhetsbrevens information på sidan
echo "<h2>Nyhetsbrev som jag äger</h2>";
echo "<ul>";

if ($result->num_rows > 0) { // Kontrollera om det finns resultat från SQL-frågan
    while ($row = $result->fetch_assoc()) {
        echo "<li>" . $row['title'] . "</li>";
    }
}

echo "</ul>";

// Stäng anslutningen till databasen
$stmt->close();
$mysqli->close();

include 'footer.php';
?>