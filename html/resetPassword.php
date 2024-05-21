<?php
include_once 'functions.php';
include_once '.env';
include 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $api_key = getenv('API_KEY');
    $domain = getenv('MAILGUN_DOMAIN');

    $recipient = $_POST['email'] ?? '';

    $email_exists = emailExistsInDatabase($recipient);

    if ($email_exists) {
        $random_code = generateRandomCode(6);
        saveResetCodeToDatabase($recipient, $random_code);

        $subject = 'Din slumpmässiga kod';
        $message = 'Din slumpmässiga kod är: ' . $random_code;

        $data = array(
            'from' => 'postmaster@sandbox92fa9355d2ba47daa8646868b9080ed6.mailgun.org',
            'to' => $recipient,
            'subject' => $subject,
            'text' => $message
        );

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://api.mailgun.net/v3/$domain/messages");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, "api:$api_key");

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
        } else {
            // Echo-meddelanden i mitten av skärmen
            echo '<div class="message-container">';
            echo '<p>Mail skickat!</p>';
            echo '<p>En kod har skickats till din e-postadress. <a href="newPassword.php">Klicka här</a> för att gå vidare och återställa ditt lösenord.</p>';
            echo '</div>';
        }

        curl_close($ch);
    } else {
        // Echo-meddelande i mitten av skärmen
        echo '<div class="message-container">';
        echo '<p>E-postadressen finns inte i användartabellen.</p>';
        echo '</div>';
    }
}

function emailExistsInDatabase($email)
{
    $mysqli = connectToDatabase();

    $query = "SELECT COUNT(*) AS count FROM users WHERE email = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();

    if (!$stmt->execute()) {
        echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        exit();
    }

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $count = $row['count'];

    $stmt->close();
    $mysqli->close();

    return $count > 0;
}

function getUserIdFromEmail($email)
{
    $mysqli = connectToDatabase();

    $query = "SELECT id FROM users WHERE email = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $user_id = $row['id'];

    $stmt->close();
    $mysqli->close();

    return $user_id;
}

function saveResetCodeToDatabase($email, $code)
{
    $user_id = getUserIdFromEmail($email);
    $mysqli = connectToDatabase();

    $query = "INSERT INTO resetPassword (user_id, email, code) VALUES (?, ?, ?)";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("iss", $user_id, $email, $code);

    if (!$stmt->execute()) {
        echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        exit();
    }

    $stmt->close();
    $mysqli->close();
}

function generateRandomCode($length)
{
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

    <div class="formContainer">

        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <h2>Ange din e-postadress för att få en kod</h2>
            <label for="email">E-postadress:</label>
            <input type="email" id="email" name="email" required>
            <button type="submit">Skicka kod</button>
        </form>
    </div>
</body>

</html>

<footer>
    <?php include 'footer.php'; ?>
</footer>