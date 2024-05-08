<?php
include_once 'functions.php';
include 'header.php';

// Mailgun API-nyckel och domän
$api_key = 'e81d762c845ce3f5c6f722e2102b1ab7-ed54d65c-2e9c08b4';
$domain = 'sandbox92fa9355d2ba47daa8646868b9080ed6.mailgun.org';

// Mottagarens e-postadress
$recipient = 'sanna.s-96@hotmail.com';

// E-postens ämne och meddelande
$subject = 'Test';
$message = 'test i vs';

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
if(curl_errno($ch)){
    echo 'Curl error: ' . curl_error($ch);
} else {
    echo 'Mail sent successfully!';
}

// Stäng cURL-resursen
curl_close($ch);



include 'footer.php';
?>