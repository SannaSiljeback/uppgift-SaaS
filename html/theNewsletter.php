<?php
include_once 'functions.php';
// include 'header.php';

// if (basename($_SERVER['PHP_SELF']) != 'index.php') {
//     include 'header.php';
//     // echo "<p><a href='index.php'>Gå tillbaka till startsidan</a></p>";
// }

// Hämta alla tillgängliga nyhetsbrev från databasen
$newsletters = getAllNewsletters();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_SESSION['user_id'])) {
        $action = $_POST['action'];
        $newsletter_id = $_POST['newsletter_id'];
        $user_id = $_SESSION['user_id']; // Antag att du har lagt till användarens ID i sessionen vid inloggning

        handleSubscription($user_id, $newsletter_id, $action);
    } else {
        echo "Användaren är inte inloggad.";
    }
}

// Visa alla nyhetsbrev med knappar bredvid dem
echo "<h2>Alla nyhetsbrev</h2>";
echo "<ul>";
foreach ($newsletters as $newsletter) {
    // Kontrollera om titeln eller beskrivningen är tom
    if (empty($newsletter['title']) || empty($newsletter['description'])) {
        continue; // Hoppa över denna iteration om nyhetsbrevet är tomt
    }

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


function handleSubscription($user_id, $newsletter_id, $action)
{
    // Anslut till databasen
    $mysqli = connectToDatabase();

    // Kontrollera anslutningen
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    // Kontrollera om användaren finns i users-tabellen
    if (userExists($mysqli, $user_id)) {
        // Förbered en SQL-fråga för att lägga till eller ta bort prenumeration
        if ($action == "subscribe") {
            $query = "INSERT INTO subscriptions (user_id, newsletter_id) VALUES (?,?)";
        } else { // action == "unsubscribe"
            $query = "DELETE FROM subscriptions WHERE user_id =? AND newsletter_id =?";
        }

        // Förbered SQL-frågan
        $stmt = $mysqli->prepare($query);

        // Om förberedelsen misslyckas, avsluta med ett felmeddelande
        if (!$stmt) {
            die("Prepare failed: " . $mysqli->error);
        }

        // Binda parametrarna och utför SQL-frågan
        $stmt->bind_param("ii", $user_id, $newsletter_id);
        $stmt->execute();

        // Stäng anslutningen och frigör resurser
        $stmt->close();
    } else {
        echo "Användaren finns inte i users-tabellen.";
    }

    $mysqli->close();
}

function userExists($mysqli, $user_id)
{
    // echo "User ID: " . $user_id . "<br>";

    // Förbered en SQL-fråga för att kontrollera om användaren finns
    $query = "SELECT id FROM users WHERE id = ?";
    $stmt = $mysqli->prepare($query);

    // Om förberedelsen misslyckas, skriv ut felmeddelandet och avsluta
    if (!$stmt) {
        die("Prepare failed: " . $mysqli->error);
    }

    // Binda parametrarna och utför SQL-frågan
    $stmt->bind_param("i", $user_id);
    if (!$stmt->execute()) {
        die("Query execution failed: " . $stmt->error);
    }

    $result = $stmt->get_result();

    // echo "SQL query: " . $query . "<br>";
    // echo "Result: " . var_dump($result->fetch_assoc()) . "<br>";

    // Returnera true om användaren finns, annars false
    if ($result->num_rows > 0) {
        return true;
    } else {
        return false;
    }

    // Stäng frågestället
    $stmt->close();
}

?>




<?php include 'footer.php'; ?>