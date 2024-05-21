<?php
include_once 'functions.php';
$mysqli = connectToDatabase();

if (basename($_SERVER['PHP_SELF']) != 'index.php') {
    include 'header.php';
}
?>
<style>
    .wrapper {
        margin: 0 auto;
        width: 80%;
        text-align: center;
        margin-top: 15px; 
    }

    .wrapper ul {
        list-style-type: none;
        padding: 0;
    }
</style>
<div class="wrapper">
    <?php
    $query = "SELECT * FROM newsletters";
    $result = $mysqli->query($query);

    echo "<h2>Alla nyhetsbrev</h2>";
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        if ($row['title'] === '' && $row['description'] === '') {
            continue;
        }

        echo "<li style='margin:7px;'>";
        echo $row['title'];
        echo " <a href='#' onclick='showDescription(" . $row['id'] . ")' style='color:#2F7561;'>Läs mer</a>";
        echo "<div id='description-" . $row['id'] . "' style='display:none;'>" . $row['description'] . "</div>";
        echo "</li>";
    }
    echo "</ul>";

    $mysqli->close();
    ?>
</div>

<footer>
    <?php include 'footer.php'; ?>
</footer>


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