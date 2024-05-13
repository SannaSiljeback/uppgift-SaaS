<?php
include 'header.php';

// Anslut till databasen
$mysqli = new mysqli("db", "root", "notSecureChangeMe", "uppgift2");

// Kontrollera anslutningen
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    exit();
}

// Hämta data från databasen
$query = "SELECT * FROM newsletters";
$result = $mysqli->query($query);

// Bearbeta och visa data
echo "<h2>Alla nyhetsbrev</h2>";
echo "<ul>";
while ($row = $result->fetch_assoc()) {
    echo "<li>" . $row['title'] . "</li>";
}
echo "</ul>";

// Stäng anslutningen till databasen
$mysqli->close();

include 'footer.php';
?>