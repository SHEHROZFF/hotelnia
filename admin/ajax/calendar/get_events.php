<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "../../../dbcon/Database.php";

// Ensure no output before headers
ob_start();
header('Content-Type: application/json');

try {
    // Validate input
    if (!isset($_GET['start']) || !isset($_GET['end'])) {
        throw new Exception("Start and end dates are required");
    }

    // Create database connection
    $database = new Database();
    $db = $database->getConnection();

    // Build query conditions
    $conditions = ["b.check_in_date <= ? AND b.check_out_date >= ?"];
    $params = [$_GET['end'], $_GET['start']];

    if (isset($_GET['hotel_id']) && !empty($_GET['hotel_id'])) {
        $conditions[] = "r.hotel_id = ?";
        $params[] = $_GET['hotel_id'];
    }

    if (isset($_GET['room_id']) && !empty($_GET['room_id'])) {
        $conditions[] = "r.room_id = ?";
        $params[] = $_GET['room_id'];
    }

    $whereClause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

    // Get bookings within date range
    $query = "
        SELECT 
            b.*,
            u.name as guest_name,
            u.email as guest_email,
            h.hotel_name,
            r.room_type,
            r.price_per_night
        FROM bookings b
        LEFT JOIN users u ON b.user_id = u.id
        LEFT JOIN rooms r ON b.product_id = r.room_id
        LEFT JOIN hotels h ON r.hotel_id = h.hotel_id
        $whereClause
        ORDER BY b.check_in_date ASC
    ";

    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format dates and numbers
    foreach ($bookings as &$booking) {
        $booking['check_in_date'] = date('Y-m-d', strtotime($booking['check_in_date']));
        $booking['check_out_date'] = date('Y-m-d', strtotime($booking['check_out_date']));
        $booking['total_price'] = floatval($booking['total_price']);
        $booking['price_per_night'] = floatval($booking['price_per_night']);
    }
    unset($booking);

    echo json_encode([
        'success' => true,
        'data' => $bookings
    ]);

} catch(Exception $e) {
    // Clear any output buffers
    ob_clean();
    
    error_log("Error getting calendar events: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error getting calendar events: ' . $e->getMessage()
    ]);
}

// End output buffer
ob_end_flush();
?> 