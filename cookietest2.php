<?php
// page2.php

session_start();

echo 'Welcome to page #2<br />';

echo $_SESSION['favcolor']; // green
echo $_SESSION['animal'];   // cat
echo date('Y m d H:i:s', $_SESSION['time']);

echo '<br /><a href="cookietest1.php">page 1</a>';
echo '<br /><a href="cookietest1.php?' . SID . '">page 1</a>';
?>