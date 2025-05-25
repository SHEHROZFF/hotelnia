<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "../../../dbcon/Database.php";

// Ensure no output before headers
ob_start();
header('Content-Type: application/json');

try {
    // Validate input
    if (!isset($_GET['room_id']) || !is_numeric($_GET['room_id'])) {
        throw new Exception("Invalid room ID");
    }

    $room_id = intval($_GET['room_id']);

    // Create database connection
    $database = new Database();
    $db = $database->getConnection();

    // Get room details with hotel name
    $stmt = $db->prepare("
        SELECT 
            r.*,
            h.hotel_name,
            (SELECT COUNT(*) FROM bookings b WHERE b.product_id = r.room_id) as total_bookings,
            EXISTS(
                SELECT 1 FROM bookings b 
                WHERE b.product_id = r.room_id 
                AND b.status = 'confirmed'
                AND CURRENT_DATE BETWEEN b.check_in_date AND b.check_out_date
            ) as current_booking
        FROM rooms r
        LEFT JOIN hotels h ON r.hotel_id = h.hotel_id
        WHERE r.room_id = ?
    ");
    $stmt->execute([$room_id]);
    $room = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$room) {
        throw new Exception("Room not found");
    }

    // Get room images
    $stmt = $db->prepare("
        SELECT * FROM room_images 
        WHERE room_id = ? 
        ORDER BY is_primary DESC, sort_order ASC
    ");
    $stmt->execute([$room_id]);
    $room['images'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get current booking if any
    $stmt = $db->prepare("
        SELECT 
            b.*,
            u.name as guest_name,
            u.email as guest_email
        FROM bookings b
        LEFT JOIN users u ON b.user_id = u.id
        WHERE b.product_id = ?
        AND b.status = 'confirmed'
        AND CURRENT_DATE BETWEEN b.check_in_date AND b.check_out_date
        LIMIT 1
    ");
    $stmt->execute([$room_id]);
    $room['current_booking_details'] = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $room
    ]);

} catch(Exception $e) {
    // Clear any output buffers
    ob_clean();
    
    error_log("Error getting room details: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error getting room details: ' . $e->getMessage()
    ]);
}

// End output buffer
ob_end_flush();
?> 