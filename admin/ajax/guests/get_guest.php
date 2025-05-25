<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "../../../dbcon/Database.php";

// Ensure no output before headers
ob_start();
header('Content-Type: application/json');

try {
    // Validate input
    if (!isset($_GET['user_id']) || !is_numeric($_GET['user_id'])) {
        throw new Exception("Invalid user ID");
    }

    $user_id = intval($_GET['user_id']);

    // Create database connection
    $database = new Database();
    $db = $database->getConnection();

    // Get user details with booking statistics
    $stmt = $db->prepare("
        SELECT 
            u.id,
            u.name,
            u.email,
            u.email_verified,
            u.created_at,
            COALESCE(COUNT(DISTINCT b.booking_id), 0) as total_bookings,
            COALESCE(SUM(CASE WHEN b.status IN ('confirmed', 'checked_in') THEN 1 ELSE 0 END), 0) as active_bookings,
            COALESCE(SUM(CASE WHEN b.status = 'cancelled' THEN 1 ELSE 0 END), 0) as cancelled_bookings,
            COALESCE(SUM(CASE WHEN b.status IN ('checked_out', 'cancelled') THEN b.total_price ELSE 0 END), 0) as total_spent
        FROM users u
        LEFT JOIN bookings b ON u.id = b.user_id
        WHERE u.id = ? AND u.role = 'user'
        GROUP BY u.id, u.name, u.email, u.email_verified, u.created_at
    ");
    
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception("Guest not found");
    }

    // Convert numeric strings to proper types
    $user['total_bookings'] = intval($user['total_bookings']);
    $user['active_bookings'] = intval($user['active_bookings']);
    $user['cancelled_bookings'] = intval($user['cancelled_bookings']);
    $user['total_spent'] = floatval($user['total_spent']);
    $user['email_verified'] = intval($user['email_verified']);

    // Get user's bookings
    $stmt = $db->prepare("
        SELECT 
            b.booking_id,
            b.check_in_date,
            b.check_out_date,
            b.total_price,
            b.status,
            b.created_at,
            h.hotel_name,
            r.room_type
        FROM bookings b
        LEFT JOIN rooms r ON b.product_id = r.room_id
        LEFT JOIN hotels h ON r.hotel_id = h.hotel_id
        WHERE b.user_id = ?
        ORDER BY b.created_at DESC
    ");
    
    $stmt->execute([$user_id]);
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format booking data
    foreach ($bookings as &$booking) {
        $booking['total_price'] = floatval($booking['total_price']);
        $booking['check_in_date'] = date('Y-m-d', strtotime($booking['check_in_date']));
        $booking['check_out_date'] = date('Y-m-d', strtotime($booking['check_out_date']));
    }
    unset($booking); // Break the reference

    $user['bookings'] = $bookings;

    echo json_encode([
        'success' => true,
        'data' => $user
    ]);

} catch(Exception $e) {
    // Clear any output buffers
    ob_clean();
    
    error_log("Error getting guest details: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error getting guest details: ' . $e->getMessage()
    ]);
}

// End output buffer
ob_end_flush();
?> 