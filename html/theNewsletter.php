<?php
include_once 'functions.php';
include 'header.php';

// Hämta alla tillgängliga nyhetsbrev från databasen
$newsletters = getAllNewsletters();

// Kontrollera om formuläret har skickats och ett nyhetsbrev har valts
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['newsletter_id'])) {
    $selected_newsletter_id = $_POST['newsletter_id'];

    // Hämta titel och beskrivning för det valda nyhetsbrevet
    $selected_newsletter = getNewsletterById($selected_newsletter_id);

    if ($selected_newsletter) {
        // Visa titel och beskrivning för det valda nyhetsbrevet
        echo "<h2>{$selected_newsletter['title']}</h2>";
        echo "<p>{$selected_newsletter['description']}</p>";
    } else {
        echo "<p>Nyhetsbrevet kunde inte hittas.</p>";
    }
}

function connectToDatabase() {
    $mysqli = new mysqli("db", "root", "notSecureChangeMe", "uppgift2");
    if ($mysqli->connect_error) {
        error_log("Connection failed: " . $mysqli->connect_error);
        return false;
    }
    return $mysqli;
}

// Funktion för att hämta alla nyhetsbrev från databasen
function getAllNewsletters() {
    // Anslut till databasen
    $mysqli = connectToDatabase();

    // Kontrollera anslutningen
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    // Förbered en SQL-fråga för att hämta alla nyhetsbrev
    $query = "SELECT id, title, description FROM newsletters";
    $result = $mysqli->query($query);

    // Skapa en array för att lagra alla nyhetsbrev
    $newsletters = array();

    // Hämta varje rad från resultatet och lägg till i arrayen
    while ($row = $result->fetch_assoc()) {
        $newsletters[] = $row;
    }

    // Stäng anslutningen och frigör resurser
    $result->close();
    $mysqli->close();

    return $newsletters;
}

// Funktion för att hämta nyhetsbrev baserat på id
function getNewsletterById($newsletter_id) {
    // Anslut till databasen
    $mysqli = connectToDatabase();

    // Kontrollera anslutningen
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    // Förbered en SQL-fråga för att hämta nyhetsbrevet med det angivna id
    $query = "SELECT id, title, description FROM newsletters WHERE id = ?";
    $stmt = $mysqli->prepare($query);

    // Om förberedelsen misslyckas, avsluta med ett felmeddelande
    if (!$stmt) {
        die("Prepare failed: " . $mysqli->error);
    }

    // Binda parametern och utför SQL-frågan
    $stmt->bind_param("i", $newsletter_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Hämta raden från resultatet om det finns någon
    if ($result->num_rows == 1) {
        $newsletter = $result->fetch_assoc();
        return $newsletter;
    } else {
        return null; // Om nyhetsbrevet inte hittas, returnera null
    }

    // Stäng anslutningen och frigör resurser
    $stmt->close();
    $mysqli->close();
}


?>




<!-- Skapa HTML-formulär för att välja nyhetsbrev -->
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <label for="newsletter">Välj ett nyhetsbrev:</label>
    <select name="newsletter_id" id="newsletter">
        <?php foreach ($newsletters as $newsletter): ?>
            <option value="<?php echo $newsletter['id']; ?>"><?php echo $newsletter['title']; ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit">Visa nyhetsbrev</button>
</form>

<?php include 'footer.php'; ?>