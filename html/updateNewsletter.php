<?php
include_once 'functions.php';
include 'header.php';


//RADERA?!  


// Anslut till databasen
$mysqli = connectToDatabase();


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    // Kontrollera om uppdateringsformuläret har skickats
    $newsletterId = $_POST['newsletter_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];

    // SQL-fråga för att uppdatera nyhetsbrevet
    $query = "UPDATE newsletters SET title = ?, description = ? WHERE id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("ssi", $title, $description, $newsletterId);
    $stmt->execute();

    var_dump($stmt->error);

    echo "<p>Newsletter updated successfully.</p>";

    // Stänga statement
    $stmt->close();
}


// Kontrollera om formuläret har skickats för att välja nyhetsbrev att redigera
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['newsletter_id'])) {
    $newsletterId = $_POST['newsletter_id'];

    // SQL-fråga för att hämta titeln och beskrivningen för det valda nyhetsbrevet
    $query = "SELECT title, description FROM newsletters WHERE id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $newsletterId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // Visa formuläret för att redigera nyhetsbrevet
?>
    <h2>Edit Newsletter</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <input type="hidden" name="newsletter_id" value="<?php echo $newsletterId; ?>">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" value="<?php echo $row['title']; ?>" required>

        <label for="description">Description:</label>
        <textarea id="description" name="description" rows="4" cols="50"><?php echo $row['description']; ?></textarea>

        <button type="submit" name="update">Update</button>
    </form>
<?php
    // Stänga statement
    $stmt->close();
} 


// SQL-fråga för att hämta alla nyhetsbrev
$query = "SELECT id, title FROM newsletters";
$stmt = $mysqli->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$newsletters = $result->fetch_all(MYSQLI_ASSOC);

// Stänga statement
$stmt->close();

// Stänga anslutningen till databasen
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Newsletter</title>
</head>

<body>
    <h2>Edit Newsletter</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="newsletter">Choose a newsletter to edit:</label>
        <select id="newsletter" name="newsletter_id">
            <?php foreach ($newsletters as $newsletter) : ?>
                <option value="<?php echo $newsletter['id']; ?>"><?php echo $newsletter['title']; ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Edit</button>
    </form>

    <?php include 'footer.php'; ?>