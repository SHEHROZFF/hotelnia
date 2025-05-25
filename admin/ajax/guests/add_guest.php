<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "../../../dbcon/Database.php";

// Ensure no output before headers
ob_start();
header('Content-Type: application/json');

try {
    // Validate required fields
    $required_fields = ['name', 'email', 'password'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            throw new Exception("Missing required field: " . $field);
        }
    }

    // Validate email format
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Invalid email format");
    }

    // Validate password length
    if (strlen($_POST['password']) < 8) {
        throw new Exception("Password must be at least 8 characters long");
    }

    // Create database connection
    $database = new Database();
    $db = $database->getConnection();

    // Check if email already exists
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM users WHERE email = ?");
    $stmt->execute([$_POST['email']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result['count'] > 0) {
        throw new Exception("Email already exists");
    }

    // Hash password
    $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Insert user
    $stmt = $db->prepare("
        INSERT INTO users (
            name,
            email,
            password,
            role,
            email_verified,
            created_at,
            updated_at
        ) VALUES (
            ?, ?, ?,
            'user',
            ?,
            CURRENT_TIMESTAMP,
            CURRENT_TIMESTAMP
        )
    ");

    $stmt->execute([
        $_POST['name'],
        $_POST['email'],
        $password_hash,
        isset($_POST['email_verified']) ? 1 : 0
    ]);

    $user_id = $db->lastInsertId();

    echo json_encode([
        'success' => true,
        'message' => 'Guest added successfully',
        'user_id' => $user_id
    ]);

} catch(Exception $e) {
    // Clear any output buffers
    ob_clean();
    
    error_log("Error adding guest: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error adding guest: ' . $e->getMessage()
    ]);
}

// End output buffer
ob_end_flush();
?> 