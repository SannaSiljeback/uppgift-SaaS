<?php
session_start();

include 'header.php';
echo "login page";



// Hårdkodade användaruppgifter
$username = "anvandare";
$password = "hemligt";
$user_role = "medlem";

// Om formuläret har postats
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Kontrollera om användarnamn och lösenord matchar
    if ($_POST['username'] === $username && $_POST['password'] === $password) {
        // Sätt session för inloggad användare och roll
        $_SESSION['user_id'] = $username;
        $_SESSION['user_roles'] = [$user_role];
        // Omdirigera till index eller annan sida efter inloggning
        header("Location: index.php");
        exit;
    } else {
        // Om användarnamn eller lösenord är felaktigt, visa felmeddelande
        $error_message = "Felaktigt användarnamn eller lösenord.";
    }
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

<?php if(isset($error_message)) { ?>
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