<?php
include_once 'functions.php';
include 'header.php';

//FRÅGA: ska denna också visa owner av nyhetsbrevet?


// Placera kodsnutten för att kontrollera sessionsvariabler här
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "<pre>";
    var_dump($_SESSION);
    echo "</pre>";
} else {
    echo "Sessionen är inte igång.";
}

// Hämta alla tillgängliga nyhetsbrev från databasen
$newsletters = getAllNewsletters();


// Visa alla nyhetsbrev med knappar bredvid dem
echo "<ul>";
foreach ($newsletters as $newsletter) {
    echo "<li>";
    echo "<h3>{$newsletter['title']}</h3>";
    echo "<p>{$newsletter['description']}</p>";

    // Kontrollera om användaren är inloggad
    if (is_signed_in()) {
        $is_subscriber = checkSubscriberStatus($_SESSION['user_id'], $newsletter['id']);

        // Visa lämplig knapp baserat på användarens prenumerationsstatus
        if ($is_subscriber) {
            // Visa avprenumerationsknapp om användaren är prenumerant
?>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <input type="hidden" name="newsletter_id" value="<?php echo $newsletter['id']; ?>">
                <button type="submit" name="action" value="unsubscribe">Avsluta prenumeration</button>
            </form>
        <?php
        } else {
            // Visa prenumerationsknapp om användaren inte är prenumerant
        ?>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <input type="hidden" name="newsletter_id" value="<?php echo $newsletter['id']; ?>">
                <button type="submit" name="action" value="subscribe">Prenumerera</button>
            </form>
        <?php
        }
    } else {
        // Visa inloggningsknapp om användaren är utloggad
        ?>
        <a href="login.php">Logga in för att prenumerera</a>
<?php
    }

    echo "</li>";
}
echo "</ul>";







// function connectToDatabase() {
//     $mysqli = new mysqli("db", "root", "notSecureChangeMe", "uppgift2");
//     if ($mysqli->connect_error) {
//         error_log("Connection failed: " . $mysqli->connect_error);
//         return false;
//     }
//     return $mysqli;
// }

// Funktion för att hämta alla nyhetsbrev från databasen
function getAllNewsletters()
{
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

// // Funktion för att hämta nyhetsbrev baserat på id
// function getNewsletterById($newsletter_id) {
//     // Anslut till databasen
//     $mysqli = connectToDatabase();

//     // Kontrollera anslutningen
//     if ($mysqli->connect_error) {
//         die("Connection failed: " . $mysqli->connect_error);
//     }

//     // Förbered en SQL-fråga för att hämta nyhetsbrevet med det angivna id
//     $query = "SELECT id, title, description FROM newsletters WHERE id = ?";
//     $stmt = $mysqli->prepare($query);

//     // Om förberedelsen misslyckas, avsluta med ett felmeddelande
//     if (!$stmt) {
//         die("Prepare failed: " . $mysqli->error);
//     }

//     // Binda parametern och utför SQL-frågan
//     $stmt->bind_param("i", $newsletter_id);
//     $stmt->execute();
//     $result = $stmt->get_result();

//     // Hämta raden från resultatet om det finns någon
//     if ($result->num_rows == 1) {
//         $newsletter = $result->fetch_assoc();
//         return $newsletter;
//     } else {
//         return null; // Om nyhetsbrevet inte hittas, returnera null
//     }

//     // Stäng anslutningen och frigör resurser
//     $stmt->close();
//     $mysqli->close();
// }

// Funktion för att kontrollera om användaren är prenumerant
function checkSubscriberStatus($user_id, $newsletter_id)
{
    // Anslut till databasen
    $mysqli = connectToDatabase();

    // Kontrollera anslutningen
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    // Förbered en SQL-fråga för att kontrollera om användaren är prenumerant av nyhetsbrevet
    $query = "SELECT * FROM subscriptions WHERE user_id = ? AND newsletter_id = ?";
    $stmt = $mysqli->prepare($query);

    // Om förberedelsen misslyckas, avsluta med ett felmeddelande
    if (!$stmt) {
        die("Prepare failed: " . $mysqli->error);
    }

    // Binda parametrarna och utför SQL-frågan
    $stmt->bind_param("ii", $user_id, $newsletter_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Om användaren är prenumerant, returnera true, annars false
    if ($result->num_rows > 0) {
        return true;
    } else {
        return false;
    }

    // Stäng anslutningen och frigör resurser
    $stmt->close();
    $mysqli->close();
}

?>





<?php include 'footer.php'; ?>