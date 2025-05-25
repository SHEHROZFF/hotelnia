<?php
// Check if user is logged in and has admin role
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    // Not logged in or not an admin, redirect to login
    header('Location: ' . $urlval . 'Login.php');
    exit;
}
?> 