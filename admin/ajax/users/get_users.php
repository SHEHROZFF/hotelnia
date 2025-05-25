<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "../../../dbcon/Database.php";

// Ensure no output before headers
ob_start();
header('Content-Type: application/json');

try {
    // Create database connection
    $database = new Database();
    $db = $database->getConnection();

    // Build query conditions
    $conditions = [];
    $params = [];

    // Filter by role if specified
    if (isset($_GET['role']) && !empty($_GET['role'])) {
        $conditions[] = "role = ?";
        $params[] = $_GET['role'];
    }

    // Add email verification filter if specified
    if (isset($_GET['verified']) && $_GET['verified'] !== '') {
        $conditions[] = "email_verified = ?";
        $params[] = $_GET['verified'];
    }

    $whereClause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

    // Get users
    $query = "
        SELECT 
            id,
            name,
            email,
            role,
            email_verified,
            created_at
        FROM users
        $whereClause
        ORDER BY name ASC
    ";

    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format data
    foreach ($users as &$user) {
        $user['email_verified'] = (bool)$user['email_verified'];
    }
    unset($user);

    echo json_encode([
        'success' => true,
        'data' => $users
    ]);

} catch(Exception $e) {
    // Clear any output buffers
    ob_clean();
    
    error_log("Error getting users: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error getting users: ' . $e->getMessage()
    ]);
}

// End output buffer
ob_end_flush();
?> 