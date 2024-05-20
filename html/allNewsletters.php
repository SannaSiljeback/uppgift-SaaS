<?php
include_once 'functions.php';
$mysqli = connectToDatabase();

if (basename($_SERVER['PHP_SELF']) != 'index.php') {
    include 'header.php';
}

$query = "SELECT * FROM newsletters";
$result = $mysqli->query($query);

echo "<h2>Alla nyhetsbrev</h2>";
echo "<ul>";
while ($row = $result->fetch_assoc()) {
    if ($row['title'] === '' && $row['description'] === '') {
        continue;
    }

    echo "<li>";
    echo $row['title'];
    echo " <a href='#' onclick='showDescription(" . $row['id'] . ")'>Läs mer</a>";
    echo "<div id='description-" . $row['id'] . "' style='display:none;'>" . $row['description'] . "</div>";
    echo "</li>";
}
echo "</ul>";

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