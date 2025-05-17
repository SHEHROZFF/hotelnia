<?php
include_once 'global.php';

// Check if token is provided
if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $user = new User($db, $security);
    $result = $user->verifyEmail($token);
    
    // Store verification result message in session
    $_SESSION['verification_result'] = $result;
} else {
    // No token provided
    $_SESSION['verification_result'] = [
        'success' => false,
        'message' => 'No verification token provided.'
    ];
}

// Redirect to the login page
header('Location: Login.php');
exit;
?> 