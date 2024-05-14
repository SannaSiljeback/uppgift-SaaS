<?php
include_once 'functions.php';
include 'header.php';

// Anslut till databasen
$mysqli = connectToDatabase();

// SQL-fråga för att hämta alla nyhetsbrev
$query = "SELECT id, title FROM newsletters";
$stmt = $mysqli->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$newsletters = $result->fetch_all(MYSQLI_ASSOC);

// Stänga statement
$stmt->close();

// Kontrollera om formuläret har skickats
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newsletterId = $_POST['newsletter_id'];

    // Återöppna anslutningen och statementet för att köra en ny SQL-fråga
    $stmt = $mysqli->prepare("SELECT title, description FROM newsletters WHERE id =?");
    $stmt->bind_param("i", $newsletterId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // Skicka titeln och beskrivningen till formuläret för redigering
  ?>
    <form method="post" action="update_newsletter.php">
        <input type="hidden" name="newsletter_id" value="<?php echo $newsletterId;?>">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" value="<?php echo $row['title'];?>" required>
        
        <label for="description">Description:</label>
        <textarea id="description" name="description" rows="4" cols="50"><?php echo $row['description'];?></textarea>
        
        <button type="submit">Update</button>
    </form>
<?php
    // Stänga statementet
    $stmt->close();
}

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
    <form method="post" action="">
        <label for="newsletter">Choose a newsletter to edit:</label>
        <select id="newsletter" name="newsletter_id">
            <?php foreach ($newsletters as $newsletter):?>
                <option value="<?php echo $newsletter['id'];?>"><?php echo $newsletter['title'];?></option>
            <?php endforeach;?>
        </select>
        <button type="submit">Edit</button>
    </form>
<?php include 'footer.php';?>