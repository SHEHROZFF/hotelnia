<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "../../../dbcon/Database.php";

// Ensure no output before headers
ob_start();
header('Content-Type: application/json');

try {
    // Validate input
    if (!isset($_GET['hotel_id']) || !is_numeric($_GET['hotel_id'])) {
        throw new Exception("Invalid hotel ID");
    }

    $hotel_id = intval($_GET['hotel_id']);

    // Create database connection
    $database = new Database();
    $db = $database->getConnection();

    // Get hotel details
    $stmt = $db->prepare("
        SELECT h.*, 
               COUNT(DISTINCT r.room_id) as total_rooms,
               COUNT(DISTINCT b.booking_id) as total_bookings
        FROM hotels h
        LEFT JOIN rooms r ON h.hotel_id = r.hotel_id
        LEFT JOIN bookings b ON r.room_id = b.product_id
        WHERE h.hotel_id = ?
        GROUP BY h.hotel_id
    ");
    $stmt->execute([$hotel_id]);
    $hotel = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$hotel) {
        throw new Exception("Hotel not found");
    }

    // Get hotel images
    $stmt = $db->prepare("SELECT * FROM hotel_images WHERE hotel_id = ? ORDER BY is_primary DESC, sort_order ASC");
    $stmt->execute([$hotel_id]);
    $hotel['images'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get hotel amenities
    $stmt = $db->prepare("
        SELECT a.* 
        FROM amenities a
        JOIN hotel_amenities ha ON a.amenity_id = ha.amenity_id
        WHERE ha.hotel_id = ?
        ORDER BY a.amenity_name
    ");
    $stmt->execute([$hotel_id]);
    $hotel['amenities'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get hotel rooms
    $stmt = $db->prepare("SELECT * FROM rooms WHERE hotel_id = ? ORDER BY price_per_night ASC");
    $stmt->execute([$hotel_id]);
    $hotel['rooms'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $hotel
    ]);

} catch(Exception $e) {
    // Clear any output buffers
    ob_clean();
    
    error_log("Error getting hotel details: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error getting hotel details: ' . $e->getMessage()
    ]);
}

// End output buffer
ob_end_flush();
?> 