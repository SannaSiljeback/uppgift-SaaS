<?php
echo '<div class="centered">Du har inte åtkomst till denna sidan...';

if (basename($_SERVER['PHP_SELF']) != 'index.php') {
    echo "<p><a href='index.php'>Gå tillbaka till startsidan</a></p>";
}

echo '</div>';

echo '
<style>
.centered {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
}
</style>
';
?>