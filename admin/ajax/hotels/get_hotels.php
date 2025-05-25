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
            h.hotel_id,
            h.hotel_name,
            h.hotel_description,
            h.hotel_address,
            h.city,
            h.country,
            h.star_rating,
            h.price_range_start,
            h.price_range_end,
            h.hotel_type,
            h.is_active,
            h.created_at,
            COUNT(DISTINCT r.room_id) as total_rooms,
            COUNT(DISTINCT b.booking_id) as total_bookings
        FROM hotels h
        LEFT JOIN rooms r ON h.hotel_id = r.hotel_id
        LEFT JOIN bookings b ON r.room_id = b.product_id
        GROUP BY h.hotel_id
        ORDER BY h.created_at DESC
    ");

    if (!$stmt) {
        throw new Exception("Failed to prepare statement: " . $db->errorInfo()[2]);
    }

    $stmt->execute();
    $hotels = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Clear any output buffers
    ob_clean();
    
    echo json_encode([
        "draw" => isset($_GET['draw']) ? intval($_GET['draw']) : 1,
        "recordsTotal" => count($hotels),
        "recordsFiltered" => count($hotels),
        "data" => $hotels
    ]);

} catch(Exception $e) {
    // Clear any output buffers
    ob_clean();
    
    error_log("Database error in get_hotels.php: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        "draw" => isset($_GET['draw']) ? intval($_GET['draw']) : 1,
        "recordsTotal" => 0,
        "recordsFiltered" => 0,
        "data" => [],
        "error" => $e->getMessage(),
        "debug_info" => [
            "file" => $e->getFile(),
            "line" => $e->getLine()
        ]
    ]);
}

// End output buffer
ob_end_flush();
?> 