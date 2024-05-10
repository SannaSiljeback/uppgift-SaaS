<?php
include_once 'functions.php';

include 'header.php';

// Kontrollera om formuläret har postats
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $verification_code = $_POST['verification_code'];

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
                // Kontrollera om koden stämmer överens med tabellen resetPassword
                if (!verifyCode($verification_code, $email)) {
                    $error_message = "Invalid verification code.";
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

// Funktion för att kontrollera om e-postadressen finns i databasen
function emailExists($email) {
    // Anslut till databasen och utför en SQL-fråga för att söka efter den angivna e-postadressen
    $mysqli = new mysqli("db", "root", "notSecureChangeMe", "uppgift2");

    // Kontrollera anslutningen
    if ($mysqli->connect_error) {
        error_log("Connection failed: ". $mysqli->connect_error);
        return false;
    }

    $query = "SELECT email FROM users WHERE email =?";
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

// Funktion för att verifiera användarens inloggningsuppgifter
function verifyLogin($email, $password) {
    try {
        $mysqli = new mysqli("db", "root", "notSecureChangeMe", "uppgift2");
        if ($mysqli->connect_error) {
            throw new Exception("Connection failed: ". $mysqli->connect_error);
        }

        $query = "SELECT password FROM users WHERE email =?";
        $stmt = $mysqli->prepare($query);
        if (!$stmt) {
            throw new Exception("Prepare failed: ". $mysqli->error);
        }

        $stmt->bind_param("s", $email);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: ". $stmt->error);
        }

        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $stored_password = $row['password'];

            if ($password === $stored_password) {
                return true;
            }
        }

        $stmt->close();
        $mysqli->close();
    } catch (Exception $e) {
        error_log("An error occurred: ". $e->getMessage());
        return false;
    }
}

// Funktion för att ändra användarens lösenord
function changePassword($email, $newPassword) {
    $mysqli = new mysqli("db", "root", "notSecureChangeMe", "uppgift2");

    if ($mysqli->connect_error) {
        die("Connection failed: ". $mysqli->connect_error);
    }

    $query = "UPDATE users SET password =? WHERE email =?";
    $stmt = $mysqli->prepare($query);

    $stmt->bind_param("ss", $newPassword, $email);

    $result = $stmt->execute();

    $stmt->close();
    $mysqli->close();

    return $result;
}

// Funktion för att kontrollera om koden stämmer med tabellen resetPassword
function verifyCode($verification_code, $email) {
    // Anslut till databasen
    $mysqli = new mysqli("db", "root", "notSecureChangeMe", "uppgift2");

    // Kontrollera anslutningen
    if ($mysqli->connect_error) {
        error_log("Connection failed: " . $mysqli->connect_error);
        return false;
    }

    // Förbered en SQL-fråga för att hämta koden från tabellen resetPassword
    $query = "SELECT code FROM resetPassword WHERE code = ? AND email = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("ss", $verification_code, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Om det finns en matchande kod i tabellen, returnera true, annars false
    if ($result->num_rows === 1) {
        return true;
    } else {
        return false;
    }

    $stmt->close();
    $mysqli->close();
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
            <label for="verification_code">Verification Code:</label>
            <input type="text" id="verification_code" name="verification_code" required>
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