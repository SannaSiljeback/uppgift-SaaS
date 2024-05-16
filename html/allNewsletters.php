<?php

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
    echo "<li>";
    echo $row['title'];
    echo " <a href='theNewsletter.php?id=" . $row['id'] . "'>Läs mer</a>"; // Lägg till en länk till varje nyhetsbrev
    echo "</li>";
}
echo "</ul>";

// Lägg till en länk till index.php om vi inte redan är där
if (basename($_SERVER['PHP_SELF']) != 'index.php') {
    echo "<p><a href='index.php'>Gå tillbaka till startsidan</a></p>";
}


// Stäng anslutningen till databasen
$mysqli->close();

include 'footer.php';
?>