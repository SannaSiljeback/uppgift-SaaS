<?php
include_once 'functions.php';
include 'header.php';

$error_message = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $role = $_POST['role'];

    if (empty($email) || empty($password) || empty($firstName) || empty($lastName)) {
        $error_message = "All fields are required.";
    } else {
        if (emailExists($email)) {
            $error_message = "Email already exists.";
        } else {
            if ($role === 'customer' || $role === 'subscriber') {
                $userId = createUser($email, $password, $firstName, $lastName, $role);
                if ($userId) {
                    $success_message = ucfirst($role) . " created successfully.";
                    if ($role === 'customer') {
                        if (createNewsletter($userId, $email)) {
                            $success_message .= " Newsletter created.";
                        } else {
                            $error_message .= " Failed to create to newsletter.";
                        }
                    }
                } else {
                    $error_message = "Failed to create " . $role . ".";
                }
            } else {
                $error_message = "Invalid role.";
            }
        }
    }
}

function emailExists($email)
{
    $mysqli = connectToDatabase();
    if (!$mysqli) {
        return false;
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

function createUser($email, $password, $firstName, $lastName, $role)
{
    $mysqli = connectToDatabase();
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $query = "INSERT INTO users (email, password, firstName, lastName, role) VALUES (?, ?, ?, ?, ?)";
    $stmt = $mysqli->prepare($query);
    if (!$stmt) {
        die("Prepare failed: " . $mysqli->error);
    }

    $stmt->bind_param("sssss", $email, $hashedPassword, $firstName, $lastName, $role);
    $result = $stmt->execute();

    $lastId = $mysqli->insert_id;

    $stmt->close();
    $mysqli->close();

    return $lastId;
}

function createNewsletter($userId, $email)
{
    $mysqli = connectToDatabase();
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    $title = "";
    $description = "";

    $query = "INSERT INTO newsletters (owner, title, description) VALUES (?, ?, ?)";
    $stmt = $mysqli->prepare($query);
    if (!$stmt) {
        die("Prepare failed: " . $mysqli->error);
    }

    $stmt->bind_param("iss", $userId, $title, $description);
    $result = $stmt->execute();

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
    <div class="formContainer">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <h2>Skapa konto</h2>
            <?php if (isset($error_message)) { ?>
                <p style="color: red;"><?php echo $error_message; ?></p>
            <?php } ?>
            <?php if (isset($success_message)) { ?>
                <p style="color: green;"><?php echo $success_message; ?></p>
            <?php } ?>
            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" style="background-color: #FBF4EF;" required>
            </div>
            <div>
                <label for="password">Lösenord:</label>
                <input type="password" id="password" name="password" style="background-color: #FBF4EF;" required>
            </div>
            <div>
                <label for="firstName">Förnamn:</label>
                <input type="text" id="firstName" name="firstName" style="background-color: #FBF4EF;" required>
            </div>
            <div>
                <label for="lastName">Efternamn:</label>
                <input type="text" id="lastName" name="lastName" style="background-color: #FBF4EF;" required>
            </div>
            <div>
                <label for="role">Roll:</label>
                <select id="role" name="role" style="background-color: #FBF4EF;">
                    <option value="customer">Kund</option>
                    <option value="subscriber">Prenumerant</option>
                </select>
            </div>
            <div>
                <button type="submit" style="background-color: #FBF4EF;">Skapa konto</button>
            </div>
        </form>
    </div>
</body>

</html>

<footer>
    <?php include 'footer.php'; ?>
</footer>