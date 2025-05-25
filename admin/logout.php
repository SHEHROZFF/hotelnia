<?php
require_once '../global.php';

// Use the existing User class logout method
$user = new User($db, $security);
$user->logout();

// Redirect to login page
header('Location: ' . $urlval . 'Login.php');
exit;
?> 