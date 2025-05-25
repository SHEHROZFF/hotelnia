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

    // Get hotel statistics
    $stmt = $db->prepare("
        SELECT 
            h.hotel_id,
            h.hotel_name,
            COUNT(DISTINCT b.booking_id) as bookings,
            COALESCE(SUM(b.total_price), 0) as revenue,
            (SELECT COUNT(*) FROM rooms WHERE hotel_id = h.hotel_id) as total_rooms,
            (SELECT COUNT(*) 
             FROM bookings b2 
             JOIN rooms r2 ON b2.product_id = r2.room_id 
             WHERE r2.hotel_id = h.hotel_id 
             AND b2.status = 'checked_in'
             AND CURRENT_DATE BETWEEN b2.check_in_date AND b2.check_out_date) as occupied_rooms
        FROM hotels h
        LEFT JOIN rooms r ON h.hotel_id = r.hotel_id
        LEFT JOIN bookings b ON r.room_id = b.product_id 
            AND b.status != 'cancelled'
            AND MONTH(b.created_at) = MONTH(CURRENT_DATE)
            AND YEAR(b.created_at) = YEAR(CURRENT_DATE)
        GROUP BY h.hotel_id, h.hotel_name
        ORDER BY revenue DESC
    ");
    $stmt->execute();
    $hotels = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calculate occupancy rate for each hotel
    foreach ($hotels as &$hotel) {
        $hotel['occupancy'] = $hotel['total_rooms'] > 0 
            ? round(($hotel['occupied_rooms'] / $hotel['total_rooms']) * 100, 1)
            : 0;
        $hotel['revenue'] = floatval($hotel['revenue']);
        $hotel['bookings'] = intval($hotel['bookings']);
    }
    unset($hotel);

    echo json_encode([
        'success' => true,
        'data' => $hotels
    ]);

} catch(Exception $e) {
    // Clear any output buffers
    ob_clean();
    
    error_log("Error getting hotel stats: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error getting hotel stats: ' . $e->getMessage()
    ]);
}

// End output buffer
ob_end_flush();
?> 