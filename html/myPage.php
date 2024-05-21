<?php
include_once 'functions.php';
include 'header.php';

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

if ($_SESSION['user_role'] == 'customer') {
    ?>
    <div class="container">
        <div class="welcome">
            <h2>Välkommen till mina sidor, <?php echo $_SESSION['user_firstName']; ?></h2>
        </div>
        <div class="content">
            <div class="left">
                <?php include 'subscribers.php'; ?>
            </div>
            <div class="right">
                <?php include 'myNewsletter.php'; ?>
            </div>
        </div>
    </div>
    <?php
} elseif ($_SESSION['user_role'] == 'subscriber') {
    $firstName = $_SESSION['user_firstName'];
    ?>

    <div class="container">
        <div class="welcome">
            <h2>Välkommen till mina sidor, <?php echo $firstName; ?></h2>
        </div>
        <div class="content">
            <div class="left">
                <?php include 'mySubscriptions.php'; ?>
            </div>
            <div class="right">
                <?php include 'theNewsletter.php'; ?>
            </div>
        </div>
    </div>

    <?php
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

<footer>
    <?php include 'footer.php'; ?>
</footer>