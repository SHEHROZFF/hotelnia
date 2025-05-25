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

    // Get recent bookings
    $stmt = $db->prepare("
        SELECT 
            b.booking_id,
            b.total_price as total_amount,
            b.check_in_date as check_in,
            b.check_out_date as check_out,
            u.name as guest_name,
            u.email as guest_email,
            r.room_type,
            DATEDIFF(b.check_out_date, b.check_in_date) as nights
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        JOIN rooms r ON b.product_id = r.room_id
        WHERE b.status != 'cancelled'
        ORDER BY b.created_at DESC
        LIMIT 5
    ");
    $stmt->execute();
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get total bookings for current month
    $stmt = $db->prepare("
        SELECT COUNT(*) as total
        FROM bookings
        WHERE MONTH(created_at) = MONTH(CURRENT_DATE)
        AND YEAR(created_at) = YEAR(CURRENT_DATE)
        AND status != 'cancelled'
    ");
    $stmt->execute();
    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    echo json_encode([
        'success' => true,
        'data' => [
            'bookings' => $bookings,
            'total_month' => intval($total)
        ]
    ]);

} catch(Exception $e) {
    // Clear any output buffers
    ob_clean();
    
    error_log("Error getting recent bookings: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error getting recent bookings: ' . $e->getMessage()
    ]);
}

// End output buffer
ob_end_flush();
?> 