<?php
include_once 'functions.php';
include 'header.php';

//FRÅGA: ska man kunna välja roll när man skapar användare??

// Variabler för att lagra meddelanden
$error_message = '';
$success_message = '';

// Kontrollera om formuläret har postats
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Hämta inmatade värden från formuläret
    $email = $_POST['email'];
    $password = $_POST['password'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $role = $_POST['role']; // Hämta rollen från formuläret

    // Validera användarens inmatning (lägg till din egen validering om nödvändigt)
    if (empty($email) || empty($password) || empty($firstName) || empty($lastName)) {
        $error_message = "All fields are required.";
    } else {
        // Kontrollera om användarens e-postadress redan finns
        if (emailExists($email)) {
            $error_message = "Email already exists.";
        } else {
            // Skapa den nya användaren beroende på rollen
            if ($role === 'customer' || $role === 'subscriber') {
                if (createUser($email, $password, $firstName, $lastName, $role)) {
                    $success_message = ucfirst($role) . " created successfully.";
                } else {
                    $error_message = "Failed to create " . $role . ".";
                }
            } else {
                $error_message = "Invalid role.";
            }
        }
    }
}

// Anslutningsfunktion för att ansluta till databasen
function connectToDatabase() {
    $mysqli = new mysqli("db", "root", "notSecureChangeMe", "uppgift2");
    if ($mysqli->connect_error) {
        error_log("Connection failed: " . $mysqli->connect_error);
        return false;
    }
    return $mysqli;
}

// Funktion för att kontrollera om e-postadressen finns i databasen
function emailExists($email) {
    $mysqli = connectToDatabase();
    if (!$mysqli) {
        return false; // Hantera felaktig anslutning till databasen
    }

    $query = "SELECT email FROM users WHERE email = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        return true;
    } else {
        return false;
    }

    $stmt->close();
    $mysqli->close();
}

// Funktion för att skapa en ny kund eller prenumerant
function createUser($email, $password, $firstName, $lastName, $role) {
    // Anslut till databasen
    $mysqli = connectToDatabase();

    // Kontrollera anslutningen
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    // Förbered en SQL-fråga för att lägga till en ny användare
    $query = "INSERT INTO users (email, password, firstName, lastName, role) VALUES (?, ?, ?, ?, ?)";
    $stmt = $mysqli->prepare($query);

    // Om förberedelsen misslyckas, avsluta med ett felmeddelande
    if (!$stmt) {
        die("Prepare failed: " . $mysqli->error);
    }

    // Binda parametrarna och utför SQL-frågan
    $stmt->bind_param("sssss", $email, $password, $firstName, $lastName, $role);
    $result = $stmt->execute();

    // Stäng anslutningen och returnera resultatet av SQL-frågan
    $stmt->close();
    $mysqli->close();

    return $result;
}


?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User</title>
</head>
<body>
    <h2>Create User</h2>
    <?php if(isset($error_message)) { ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php } ?>
    <?php if(isset($success_message)) { ?>
        <p style="color: green;"><?php echo $success_message; ?></p>
    <?php } ?>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div>
            <label for="firstName">First Name:</label>
            <input type="text" id="firstName" name="firstName" required>
        </div>
        <div>
             <label for="lastName">Last Name:</label>
         <input type="text" id="lastName" name="lastName" required>
        </div>
        <div>
            <label for="role">Role:</label>
            <select id="role" name="role">
                <option value="customer">Customer</option>
                <option value="subscriber">Subscriber</option>
            </select>
        </div>
        <div>
            <button type="submit">Create User</button>
        </div>
    </form>
</body>
</html>

<?php include 'footer.php'; ?>