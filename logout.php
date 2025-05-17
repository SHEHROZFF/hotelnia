<?php
// Include global settings
include_once 'global.php';

// Log the user out
$user = new User($db, $security);
$user->logout();

// Redirect to the homepage
header('Location: ' . $urlval);
exit;
?> 