<?php
include_once 'functions.php';

include 'header.php';

// Kontrollera om formuläret har postats
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validera användarens inmatning
    if ($new_password !== $confirm_password) {
        $error_message = "New password and confirm password do not match.";
    } else {
        // Kontrollera om användarens nuvarande lösenord är korrekt
        if (!verifyLogin($email, $current_password)) {
            $error_message = "Current password is incorrect.";
        } else {
            // Uppdatera användarens lösenord i databasen
            if (changePassword($email, $new_password)) {
                $success_message = "Password changed successfully.";
            } else {
                $error_message = "Failed to change password.";
            }
        }
    }

    // Funktion för att kontrollera om e-postadressen finns i databasen
function emailExists($email) {
    // Anslut till databasen och utför en SQL-fråga för att söka efter den angivna e-postadressen
    $mysqli = new mysqli("db", "root", "notSecureChangeMe", "uppgift2");

    // Kontrollera anslutningen
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli->connect_error;
        exit();
    }

    // Förbered och utför en SQL-fråga för att söka efter den angivna e-postadressen
    $query = "SELECT email FROM users WHERE email = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Kontrollera om det finns en rad som matchar e-postadressen
    if ($result->num_rows === 1) {
        // E-postadressen finns i databasen
        return true;
    } else {
        // E-postadressen finns inte i databasen
        return false;
    }

    // Stäng anslutningen till databasen
    $stmt->close();
    $mysqli->close();
}

// Kontrollera om formuläret har postats
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validera användarens inmatning
    if ($new_password !== $confirm_password) {
        $error_message = "New password and confirm password do not match.";
    } else {
        // Kontrollera om användarens e-postadress finns i databasen
        if (!emailExists($email)) {
            $error_message = "Email does not exist.";
        } else {
            // Kontrollera om användarens nuvarande lösenord är korrekt
            if (!verifyLogin($email, $current_password)) {
                $error_message = "Current password is incorrect.";
            } else {
                // Uppdatera användarens lösenord i databasen
                if (changePassword($email, $new_password)) {
                    $success_message = "Password changed successfully.";
                } else {
                    $error_message = "Failed to change password.";
                }
            }
        }
    }
}
}





// Funktion för att verifiera användarens inloggningsuppgifter
function verifyLogin($email, $password) {
    // Antag att du använder en databas för att lagra användaruppgifter
    // Anslut till din databas
    $mysqli = new mysqli("db", "root", "notSecureChangeMe", "uppgift2");

    // Kontrollera anslutningen
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli->connect_error;
        exit();
    }

    // Förbered och utför en SQL-fråga för att hämta användarens lösenord baserat på e-postadressen
    $query = "SELECT password FROM users WHERE email = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Kontrollera om det finns en rad som matchar e-postadressen
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $stored_password = $row['password'];

        // Jämför det lagrade lösenordet med det angivna lösenordet
        if (password_verify($password, $stored_password)) {
            // Lösenordet är korrekt
            return true;
        }
    }

    // Stäng anslutningen till databasen
    $stmt->close();
    $mysqli->close();

    // Om ingen matchning hittades eller lösenordet är felaktigt, returnera false
    return false;
}

// Funktion för att ändra användarens lösenord
function changePassword($email, $newPassword) {
    // Antag att du använder en databas för att lagra användaruppgifter
    // Anslut till din databas
    $mysqli = new mysqli("db", "root", "notSecureChangeMe", "uppgift2");

    // Kontrollera anslutningen
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli->connect_error;
        exit();
    }

    // Kryptera det nya lösenordet innan det sparas i databasen
    $hashed_password = password_hash($newPassword, PASSWORD_DEFAULT);

    // Förbered och utför en SQL-fråga för att uppdatera användarens lösenord baserat på e-postadressen
    $query = "UPDATE users SET password = ? WHERE email = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("ss", $hashed_password, $email);
    $success = $stmt->execute();

    // Stäng anslutningen till databasen
    $stmt->close();
    $mysqli->close();

    // Returnera true om lösenordet ändrades framgångsrikt, annars false
    return $success;
}

?>



<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
</head>
<body>
    <h2>Change Password</h2>
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
            <label for="current_password">Current Password:</label>
            <input type="password" id="current_password" name="current_password" required>
        </div>
        <div>
            <label for="new_password">New Password:</label>
            <input type="password" id="new_password" name="new_password" required>
        </div>
        <div>
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>
        <div>
            <button type="submit">Change Password</button>
        </div>
    </form>
</body>
</html>


<?php
include 'footer.php';
?>