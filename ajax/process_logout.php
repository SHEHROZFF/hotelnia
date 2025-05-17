<?php
require_once '../global.php';

// Process logout
$user = new User($db, $security);
$user->logout();

// Redirect to homepage
header('Location: ' . $urlval);
exit;
?> 