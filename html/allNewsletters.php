<?php



// Anslut till databasen
$mysqli = new mysqli("db", "root", "notSecureChangeMe", "uppgift2");

// Kontrollera anslutningen
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    exit();
}

if (basename($_SERVER['PHP_SELF']) != 'index.php') {
    include 'header.php';
}



// Hämta data från databasen
$query = "SELECT * FROM newsletters";
$result = $mysqli->query($query);

// Bearbeta och visa data
echo "<h2>Alla nyhetsbrev</h2>";
echo "<ul>";
while ($row = $result->fetch_assoc()) {
    // Kontrollera om både titeln och beskrivningen är tomma strängar
    if ($row['title'] === '' && $row['description'] === '') {
        continue; // Hoppa över den aktuella iterationen och gå vidare till nästa rad
    }

    echo "<li>";
    echo $row['title'];
    echo " <a href='#' onclick='showDescription(" . $row['id'] . ")'>Läs mer</a>"; // Lägg till en länk med onclick för att visa beskrivningen
    echo "<div id='description-" . $row['id'] . "' style='display:none;'>" . $row['description'] . "</div>"; // Gömd beskrivning
    echo "</li>";
}
echo "</ul>";

// Stäng anslutningen till databasen
$mysqli->close();

include 'footer.php';
?>

<script>
function showDescription(id) {
    var descriptionDiv = document.getElementById('description-' + id);
    if (descriptionDiv.style.display === 'none') {
        descriptionDiv.style.display = 'block';
    } else {
        descriptionDiv.style.display = 'none';
    }
}
</script>