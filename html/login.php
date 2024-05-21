<?php
ob_start();
include_once 'functions.php';
include 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $user = verifyLogin($username, $password);
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_firstName'] = $user['firstName'];

        header("Location: myPage.php");
        exit;
    } else {
        $error_message = "Felaktigt användarnamn eller lösenord.";
    }
}

function getUserRoles($userId)
{
    $mysqli = connectToDatabase();
    $query = "SELECT role FROM users WHERE id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $roles = array();

    while ($row = $result->fetch_assoc()) {
        $roles[] = $row['role'];
    }

    $stmt->close();
    $mysqli->close();

    return $roles;
}

function verifyLogin($username, $password)
{
    $mysqli = connectToDatabase();

    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            return array('id' => $user['id'], 'role' => $user['role'], 'firstName' => $user['firstName']);
        }
    }

    return false;

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

    <div class="container">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <h2>Inloggning</h2>

            <?php if (isset($error_message)) { ?>
                <p style="color: red;"><?php echo $error_message; ?></p>
            <?php } ?>

            <div>
                <label for="username">Användarnamn:</label>
                <input type="text" id="username" name="username" style="background-color: #FBF4EF;">
            </div>
            <div>
                <label for="password">Lösenord:</label>
                <input type="password" id="password" name="password" style="background-color: #FBF4EF;">
            </div>
            <div>
                <button type="submit" style="background-color: #FBF4EF;">Logga in</button>
            </div>
            <div>
                <a href="resetPassword.php" style="color:black;">Glömt lösenord?</a>
            </div>
        </form>
    </div>

</body>

</html>

<footer>
    <?php include 'footer.php'; ?>
</footer>

<?php
ob_end_flush();
?>