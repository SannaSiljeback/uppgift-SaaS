<?php
ob_start(); // Starta outputbuffring
include_once 'functions.php';
include 'header.php';

// Om formuläret har postats
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Hämta användaruppgifter från formuläret
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Kontrollera användarens existens och lösenord i databasen
    $user = verifyLogin($username, $password);
    if ($user) {
        // Sätt session för inloggad användare och roll
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role']; // Sätt användarens roll i sessionen

        // Omdirigera till olika sidor baserat på användarens roll
        if ($_SESSION['user_role'] == 'subscriber') {
            header("Location: theNewsletter.php");
        } else if ($_SESSION['user_role'] == 'customer') {
            header("Location: subscribers.php");
        }
        exit;
    } else {
        // Om användarnamn eller lösenord är felaktigt, visa felmeddelande
        $error_message = "Felaktigt användarnamn eller lösenord.";
    }
}

// Placera kodsnutten för att kontrollera sessionsvariabler här
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "<pre>";
    var_dump($_SESSION);
    echo "</pre>";
} else {
    echo "Sessionen är inte igång.";
}

// Funktion för att hämta användarroller från databasen baserat på användarnamn
function getUserRoles($userId)
{
    // Anslut till databasen (anpassa anslutningsparametrarna efter din server)
    $mysqli = new mysqli("db", "root", "notSecureChangeMe", "uppgift2");

    // Kontrollera anslutningen
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli->connect_error;
        exit();
    }

    // Förbered och utför en SQL-fråga för att hämta användarrollerna baserat på användarnamn
    $query = "SELECT role FROM users WHERE id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    // Skapa en array för att lagra användarrollerna
    $roles = array();

    // Loopa igenom resultatet och lägg till varje roll i arrayen
    while ($row = $result->fetch_assoc()) {
        $roles[] = $row['role'];
    }

    // Stäng anslutningen till databasen
    $stmt->close();
    $mysqli->close();

    // Returnera arrayen med användarroller
    return $roles;
}

// Funktion för att verifiera inloggning mot databasen
function verifyLogin($username, $password)
{
    // Anslut till databasen (anpassa anslutningsparametrarna efter din server)
    $mysqli = new mysqli("db", "root", "notSecureChangeMe", "uppgift2");

    // Kontrollera anslutningen
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli->connect_error;
        exit();
    }

    // Förbered och utför en SQL-fråga för att kontrollera användaruppgifterna
    $query = "SELECT * FROM users WHERE email = ? AND password = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    // Kontrollera om det finns en matchande rad i resultatet
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        return array('id' => $user['id'], 'role' => $user['role']); // Returnera användarens id och roll
    } else {
        return false; // Användaren finns inte eller lösenordet är felaktigt
    }

    // Stäng anslutningen till databasen
    $stmt->close();
    $mysqli->close();
}

?>

<!DOCTYPE html>
<html lang="sv">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inloggning</title>
</head>

<body>

    <h2>Inloggning</h2>

    <?php if (isset($error_message)) { ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php } ?>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div>
            <label for="username">Användarnamn:</label>
            <input type="text" id="username" name="username">
        </div>
        <div>
            <label for="password">Lösenord:</label>
            <input type="password" id="password" name="password">
        </div>
        <div>
            <button type="submit">Logga in</button>
        </div>
    </form>

</body>

</html>

<?php
include 'footer.php';
?>

<?php
ob_end_flush(); // Skicka buffrad output till webbläsaren
?>