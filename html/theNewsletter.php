<?php
include_once 'functions.php';

if ($_SESSION['user_role'] != 'subscriber') {
    header("Location: noAccess.php");
    exit;
}

$newsletters = getAllNewsletters();

echo "<h2>Alla nyhetsbrev</h2>";
echo "<ul>";
foreach ($newsletters as $newsletter) {
    if (empty($newsletter['title']) || empty($newsletter['description'])) {
        continue;
    }

    echo "<li>";
    echo "<form method='post' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "'>";
    echo "<h3 style='margin-top: 7px';>{$newsletter['title']}</h3>";
    echo "<p>{$newsletter['description']}</p>";

    if (is_signed_in()) {
        $is_subscriber = checkSubscriberStatus($_SESSION['user_id'], $newsletter['id']);

        if ($is_subscriber) {
            echo "<input type='hidden' name='newsletter_id' value='{$newsletter['id']}'>";
            echo "<button type='submit' name='action' value='unsubscribe' style='background-color: #FBF4EF'>Avsluta prenumeration</button>";
        } else {
            echo "<input type='hidden' name='newsletter_id' value='{$newsletter['id']}'>";
            echo "<button type='submit' name='action' value='subscribe' style='background-color: #FBF4EF'>Prenumerera</button>";
        }
    } else {
        echo "<a href='login.php'>Logga in för att prenumerera</a>";
    }

    echo "</form>";
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

?>
