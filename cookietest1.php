<?php
// page1.php

session_start();

echo 'Welcome to page #1';

$_SESSION['favcolor'] = 'green';
$_SESSION['animal']   = 'cat';
$_SESSION['time']     = time();

echo '<br />&nbsp;';
echo '<br /><a href="cookietest2.php">page 2</a>';
echo '<br /><a href="cookietest2.php?' . SID . '">page 2</a>';
?>