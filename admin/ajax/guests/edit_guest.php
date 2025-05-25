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

    if (!isset($_POST['name']) || empty($_POST['name'])) {
        throw new Exception("Name is required");
    }

    if (!isset($_POST['email']) || empty($_POST['email'])) {
        throw new Exception("Email is required");
    }

    // Validate email format
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Invalid email format");
    }

    $user_id = intval($_POST['user_id']);

    // Create database connection
    $database = new Database();
    $db = $database->getConnection();

    // Check if email exists for other users
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM users WHERE email = ? AND id != ?");
    $stmt->execute([$_POST['email'], $user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result['count'] > 0) {
        throw new Exception("Email already exists for another user");
    }

    // Start building update query
    $updateFields = [
        "name = ?",
        "email = ?",
        "email_verified = ?",
        "updated_at = CURRENT_TIMESTAMP"
    ];
    $params = [
        $_POST['name'],
        $_POST['email'],
        isset($_POST['email_verified']) ? 1 : 0
    ];

    // Add password update if provided
    if (isset($_POST['password']) && !empty($_POST['password'])) {
        if (strlen($_POST['password']) < 8) {
            throw new Exception("Password must be at least 8 characters long");
        }
        $updateFields[] = "password = ?";
        $params[] = password_hash($_POST['password'], PASSWORD_DEFAULT);
    }

    // Add user_id to params
    $params[] = $user_id;

    // Update user
    $stmt = $db->prepare("
        UPDATE users 
        SET " . implode(", ", $updateFields) . "
        WHERE id = ? AND role = 'user'
    ");

    $stmt->execute($params);

    if ($stmt->rowCount() === 0) {
        throw new Exception("Guest not found or no changes made");
    }

    echo json_encode([
        'success' => true,
        'message' => 'Guest information updated successfully'
    ]);

} catch(Exception $e) {
    // Clear any output buffers
    ob_clean();
    
    error_log("Error updating guest: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error updating guest: ' . $e->getMessage()
    ]);
}

// End output buffer
ob_end_flush();
?> 