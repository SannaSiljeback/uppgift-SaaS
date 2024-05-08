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

    // Generera en slumpmässig kod
    $random_code = generateRandomCode(6); // Generera en 6-teckens slumpmässig kod

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