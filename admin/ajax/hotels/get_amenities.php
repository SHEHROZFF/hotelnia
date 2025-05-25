<?php
// Enable error reporting for debugging
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

    // Test database connection
    if (!$db) {
        throw new Exception("Database connection failed");
    }

    $stmt = $db->prepare("
        SELECT 
            amenity_id,
            amenity_name,
            amenity_icon,
            amenity_description
        FROM amenities 
        ORDER BY amenity_name
    ");

    if (!$stmt) {
        throw new Exception("Failed to prepare statement: " . $db->errorInfo()[2]);
    }

    $stmt->execute();
    $amenities = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Clear any output buffers
    ob_clean();
    
    echo json_encode([
        'success' => true,
        'data' => $amenities
    ]);

} catch(Exception $e) {
    // Clear any output buffers
    ob_clean();
    
    error_log("Database error in get_amenities.php: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'data' => [],
        'debug_info' => [
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]
    ]);
}

// End output buffer
ob_end_flush();
?> 