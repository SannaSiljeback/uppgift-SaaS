<?php
// include 'header.php';
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
echo "<h2>Mitt nyhetsbrev</h2>";

$updatedDescription = null;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    // Kontrollera om uppdateringsformuläret har skickats
    $newsletterId = $_POST['newsletter_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];

    // SQL-fråga för att uppdatera nyhetsbrevet
    $query = "UPDATE newsletters SET title = ?, description = ? WHERE id = ? AND owner = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("ssis", $title, $description, $newsletterId, $user_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "<p>Newsletter updated successfully.</p>";
        $updatedDescription = $description;
    } else {
        echo "<p>Failed to update newsletter. Please make sure you are the owner of the newsletter.</p>";
    }

    // Stänga statement
    $stmt->close();
}

if ($result->num_rows > 0) { // Kontrollera om det finns resultat från SQL-frågan
    while ($row = $result->fetch_assoc()) {
        echo "<h3>" . $row['title'] . "</h3>";
        echo "<form method='post' action=''>";
        echo "<input type='hidden' name='newsletter_id' value='" . $row['id'] . "'>";
        echo "<label for='title'>Title:</label>";
        echo "<input type='text' id='title' name='title' value='" . $row['title'] . "' required>";
        echo "<label for='description'>Description:</label>";
        echo "<textarea id='description' name='description' rows='4' cols='50'>" . ($updatedDescription ?? $row['description']) . "</textarea>";
        echo "<button type='submit' name='update'>Update</button>";
        echo "</form>";
    }
}

// Stäng anslutningen till databasen
$mysqli->close();

// include 'footer.php';
?>