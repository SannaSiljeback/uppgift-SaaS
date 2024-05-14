<?php
include_once 'functions.php';
include 'header.php';
?>

<!-- Visa alla nyhetsbrev -->
<?php include 'allNewsletters.php'; ?>

<!-- Knapp för att logga in -->
<form action="login.php" method="get">
    <button type="submit">Logga in</button>
</form>

<!-- Knapp för att registrera -->
<form action="createAccount.php" method="get">
    <button type="submit">Registrera</button>
</form>

<?php include 'footer.php'; ?>