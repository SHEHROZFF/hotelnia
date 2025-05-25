<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "../../../dbcon/Database.php";

// Ensure no output before headers
ob_start();
header('Content-Type: application/json');

try {
    // Validate input
    if (!isset($_POST['user_id']) || !is_numeric($_POST['user_id'])) {
        throw new Exception("Invalid user ID");
    }

    $user_id = intval($_POST['user_id']);

    // Create database connection
    $database = new Database();
    $db = $database->getConnection();

    // Get user details
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ? AND role = 'user' AND email_verified = 0");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception("Guest not found or already verified");
    }

    // Generate verification token
    $token = bin2hex(random_bytes(32));
    $expires_at = date('Y-m-d H:i:s', strtotime('+24 hours'));

    // Store verification token
    $stmt = $db->prepare("
        INSERT INTO email_verifications (
            user_id,
            token,
            expires_at,
            created_at
        ) VALUES (
            ?, ?, ?,
            CURRENT_TIMESTAMP
        )
    ");

    $stmt->execute([
        $user_id,
        $token,
        $expires_at
    ]);

    // Prepare verification email
    $verification_link = "http://" . $_SERVER['HTTP_HOST'] . "/verify-email.php?token=" . $token;
    $to = $user['email'];
    $subject = "Verify your email address";
    $message = "
        <html>
        <head>
            <title>Email Verification</title>
        </head>
        <body>
            <h2>Welcome to " . $_SERVER['HTTP_HOST'] . "</h2>
            <p>Please click the link below to verify your email address:</p>
            <p><a href='" . $verification_link . "'>" . $verification_link . "</a></p>
            <p>This link will expire in 24 hours.</p>
            <p>If you did not create an account, please ignore this email.</p>
        </body>
        </html>
    ";

    // Email headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: " . $_SERVER['HTTP_HOST'] . " <noreply@" . $_SERVER['HTTP_HOST'] . ">" . "\r\n";

    // Send email
    if(!mail($to, $subject, $message, $headers)) {
        throw new Exception("Failed to send verification email");
    }

    echo json_encode([
        'success' => true,
        'message' => 'Verification email sent successfully'
    ]);

} catch(Exception $e) {
    // Clear any output buffers
    ob_clean();
    
    error_log("Error sending verification email: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error sending verification email: ' . $e->getMessage()
    ]);
}

// End output buffer
ob_end_flush();
?> 