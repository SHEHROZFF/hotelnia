<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "../../../dbcon/Database.php";

// Ensure no output before headers
ob_start();
header('Content-Type: application/json');

try {
    // Validate input
    if (!isset($_GET['booking_id']) || !is_numeric($_GET['booking_id'])) {
        throw new Exception("Invalid booking ID");
    }

    $booking_id = intval($_GET['booking_id']);

    // Create database connection
    $database = new Database();
    $db = $database->getConnection();

    // Get booking details with related information
    $stmt = $db->prepare("
        SELECT 
            b.*,
            u.name as guest_name,
            u.email as guest_email,
            h.hotel_name,
            r.room_type,
            r.price_per_night,
            r.capacity
        FROM bookings b
        LEFT JOIN users u ON b.user_id = u.id
        LEFT JOIN rooms r ON b.product_id = r.room_id
        LEFT JOIN hotels h ON r.hotel_id = h.hotel_id
        WHERE b.booking_id = ?
    ");
    
    $stmt->execute([$booking_id]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$booking) {
        throw new Exception("Booking not found");
    }

    echo json_encode([
        'success' => true,
        'data' => $booking
    ]);

} catch(Exception $e) {
    // Clear any output buffers
    ob_clean();
    
    error_log("Error getting booking details: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error getting booking details: ' . $e->getMessage()
    ]);
}

// End output buffer
ob_end_flush();
?> 