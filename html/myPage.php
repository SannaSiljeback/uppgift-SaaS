<?php
include_once 'functions.php';
include 'header.php';

if ($_SESSION['user_role'] == 'customer') {
    ?>
    <div class="container">
        <div class="welcome">
            <p>Välkommen till mina sidor, <?php echo $_SESSION['user_firstName']; ?></p>
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
            <p>Välkommen till mina sidor, <?php echo $firstName; ?></p>
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
?>

<footer>
    <?php include 'footer.php'; ?>
</footer>