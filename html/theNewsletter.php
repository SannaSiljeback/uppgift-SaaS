<?php
include_once 'functions.php';
include 'header.php';


// Om formuläret har postats
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mailgun API-nyckel och domän
    $api_key = 'e81d762c845ce3f5c6f722e2102b1ab7-ed54d65c-2e9c08b4';
    $domain = 'sandbox92fa9355d2ba47daa8646868b9080ed6.mailgun.org';

    // Mottagarens e-postadress
    $recipient = $_POST['email'] ?? '';

    // Kontrollera om e-postadressen finns i användartabellen
    $email_exists = emailExistsInDatabase($recipient);

    if ($email_exists) {
        // Generera en slumpmässig kod
        $random_code = generateRandomCode(6); // Generera en 6-teckens slumpmässig kod

        // Spara den genererade koden i databasen
        saveResetCodeToDatabase($recipient, $random_code);

        // E-postens ämne och meddelande
        $subject = 'Din slumpmässiga kod';
        $message = 'Din slumpmässiga kod är: ' . $random_code;

        // Data som ska skickas i formfält
        $data = array(
            'from' => 'postmaster@sandbox92fa9355d2ba47daa8646868b9080ed6.mailgun.org',
            'to' => $recipient,
            'subject' => $subject,
            'text' => $message
        );

        // Skapa en cURL-resurs
        $ch = curl_init();

        // Ange cURL-alternativ
        curl_setopt($ch, CURLOPT_URL, "https://api.mailgun.net/v3/$domain/messages");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, "api:$api_key");

        // Utför cURL-anropet
        $response = curl_exec($ch);

        // Kontrollera om det uppstod några fel
        if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
        } else {
            echo 'Mail sent successfully!';
        }

        // Stäng cURL-resursen
        curl_close($ch);


    } else {
        echo 'E-postadressen finns inte i användartabellen.';
    }
}


// Funktion för att kontrollera om e-postadressen finns i användartabellen
function emailExistsInDatabase($email) {
    // Anslut till din databas
    $mysqli = new mysqli("db", "root", "notSecureChangeMe", "uppgift2");

    // Kontrollera anslutningen
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli->connect_error;
        exit();
    }

    // Förbered och utför en SQL-fråga för att kontrollera om e-postadressen finns i tabellen
    $query = "SELECT COUNT(*) AS count FROM users WHERE email = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();

    // Kontrollera om utförandet av frågan lyckades
    if (!$stmt->execute()) {
        echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        exit();
    }

    // Hämta antalet rader som matchar e-postadressen
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $count = $row['count'];

    // Stäng anslutningen till databasen
    $stmt->close();
    $mysqli->close();

    // Returnera true om antalet rader är större än 0, vilket innebär att e-postadressen finns i tabellen
    return $count > 0;
}

// Funktion för att hämta användarens id från users-tabellen baserat på e-postadressen
function getUserIdFromEmail($email) {
    // Anslut till din databas
    $mysqli = new mysqli("db", "root", "notSecureChangeMe", "uppgift2");

    // Kontrollera anslutningen
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli->connect_error;
        exit();
    }

    // Förbered och utför en SQL-fråga för att hämta användarens id från users-tabellen
    $query = "SELECT id FROM users WHERE email = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Hämta användarens id från resultatet
    $row = $result->fetch_assoc();
    $user_id = $row['id'];

    // Stäng anslutningen till databasen
    $stmt->close();
    $mysqli->close();

    return $user_id;
}

// Funktion för att spara den genererade koden och användarens id i databasen
function saveResetCodeToDatabase($email, $code) {
    // Hämta användar-ID från users-tabellen baserat på e-postadressen
    $user_id = getUserIdFromEmail($email);

    // Anslut till din databas
    $mysqli = new mysqli("db", "root", "notSecureChangeMe", "uppgift2");

    // Kontrollera anslutningen
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli->connect_error;
        exit();
    }

    // Förbered och utför en SQL-fråga för att spara koden och användar-ID i databasen
    $query = "INSERT INTO resetPassword (user_id, email, code) VALUES (?, ?, ?)";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("iss", $user_id, $email, $code);
    
    // Utför frågan
    if (!$stmt->execute()) {
        echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        exit();
    }

    // Stäng anslutningen till databasen
    $stmt->close();
    $mysqli->close();
}



// Funktion för att generera en slumpmässig kod
function generateRandomCode($length) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomCode = '';
    $max = strlen($characters) - 1;
    for ($i = 0; $i < $length; $i++) {
        $randomCode .= $characters[rand(0, $max)];
    }
    return $randomCode;
}
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Skicka kod via e-post</title>
</head>
<body>
    <h2>Ange din e-postadress för att få en kod</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="email">E-postadress:</label>
        <input type="email" id="email" name="email" required>
        <button type="submit">Skicka kod</button>
    </form>
</body>
</html>

<?php
include_once 'footer.php';
?>