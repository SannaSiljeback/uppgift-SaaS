<?php
echo 'Du har inte åtkomst till denna sidan...';

if (basename($_SERVER['PHP_SELF']) != 'index.php') {

    echo "<p><a href='index.php'>Gå tillbaka till startsidan</a></p>";
}
