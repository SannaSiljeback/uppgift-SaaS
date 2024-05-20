<?php
include_once 'functions.php';

if ($_SESSION['user_role'] != 'subscriber') {
    header("Location: noAccess.php");
    exit;
}

$newsletters = getAllNewsletters();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_SESSION['user_id'])) {
        $action = $_POST['action'];
        $newsletter_id = $_POST['newsletter_id'];
        $user_id = $_SESSION['user_id'];

        handleSubscription($user_id, $newsletter_id, $action);
    } else {
        echo "Användaren är inte inloggad.";
    }
}

echo "<h2>Alla nyhetsbrev</h2>";
echo "<ul>";
foreach ($newsletters as $newsletter) {
    if (empty($newsletter['title']) || empty($newsletter['description'])) {
        continue;
    }

    echo "<li>";
    echo "<h3>{$newsletter['title']}</h3>";
    echo "<p>{$newsletter['description']}</p>";

    if (is_signed_in()) {
        $is_subscriber = checkSubscriberStatus($_SESSION['user_id'], $newsletter['id']);

        if ($is_subscriber) {
?>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <input type="hidden" name="newsletter_id" value="<?php echo $newsletter['id']; ?>">
                <button type="submit" name="action" value="unsubscribe">Avsluta prenumeration</button>
            </form>
        <?php
        } else {
        ?>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <input type="hidden" name="newsletter_id" value="<?php echo $newsletter['id']; ?>">
                <button type="submit" name="action" value="subscribe">Prenumerera</button>
            </form>
        <?php
        }
    } else {
        ?>
        <a href="login.php">Logga in för att prenumerera</a>
<?php
    }

    echo "</li>";
}
echo "</ul>";

function getAllNewsletters()
{
    $mysqli = connectToDatabase();
    $query = "SELECT id, title, description FROM newsletters";
    $result = $mysqli->query($query);
    $newsletters = array();

    while ($row = $result->fetch_assoc()) {
        $newsletters[] = $row;
    }

    $result->close();
    $mysqli->close();

    return $newsletters;
}

function checkSubscriberStatus($user_id, $newsletter_id)
{
    $mysqli = connectToDatabase();

    $query = "SELECT * FROM subscriptions WHERE user_id = ? AND newsletter_id = ?";
    $stmt = $mysqli->prepare($query);

    if (!$stmt) {
        die("Prepare failed: " . $mysqli->error);
    }

    $stmt->bind_param("ii", $user_id, $newsletter_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return true;
    } else {
        return false;
    }

    $stmt->close();
    $mysqli->close();
}


function handleSubscription($user_id, $newsletter_id, $action)
{
    $mysqli = connectToDatabase();

    if (userExists($mysqli, $user_id)) {
        if ($action == "subscribe") {
            $query = "INSERT INTO subscriptions (user_id, newsletter_id) VALUES (?,?)";
        } else {
            $query = "DELETE FROM subscriptions WHERE user_id =? AND newsletter_id =?";
        }

        $stmt = $mysqli->prepare($query);

        if (!$stmt) {
            die("Prepare failed: " . $mysqli->error);
        }

        $stmt->bind_param("ii", $user_id, $newsletter_id);
        $stmt->execute();
        $stmt->close();
    } else {
        echo "Användaren finns inte i users-tabellen.";
    }

    $mysqli->close();
}

function userExists($mysqli, $user_id)
{
    $query = "SELECT id FROM users WHERE id = ?";
    $stmt = $mysqli->prepare($query);

    if (!$stmt) {
        die("Prepare failed: " . $mysqli->error);
    }

    $stmt->bind_param("i", $user_id);
    if (!$stmt->execute()) {
        die("Query execution failed: " . $stmt->error);
    }

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return true;
    } else {
        return false;
    }

    $stmt->close();
}
?>

<?php include 'footer.php'; ?>