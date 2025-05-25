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

    // Get total revenue for last 30 days
    $stmt = $db->prepare("
        SELECT COALESCE(SUM(total_price), 0) as total_revenue
        FROM bookings
        WHERE created_at >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
        AND status != 'cancelled'
    ");
    $stmt->execute();
    $revenue = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get occupancy rate for current month
    $stmt = $db->prepare("
        SELECT 
            COUNT(DISTINCT b.product_id) * DATEDIFF(CURRENT_DATE, DATE_FORMAT(CURRENT_DATE, '%Y-%m-01')) as total_room_days,
            SUM(DATEDIFF(LEAST(check_out_date, CURRENT_DATE), GREATEST(check_in_date, DATE_FORMAT(CURRENT_DATE, '%Y-%m-01')))) as occupied_days
        FROM bookings b
        WHERE 
            check_in_date <= CURRENT_DATE 
            AND check_out_date >= DATE_FORMAT(CURRENT_DATE, '%Y-%m-01')
            AND status IN ('confirmed', 'checked_in')
    ");
    $stmt->execute();
    $occupancy = $stmt->fetch(PDO::FETCH_ASSOC);
    $occupancyRate = $occupancy['total_room_days'] > 0 
        ? round(($occupancy['occupied_days'] / $occupancy['total_room_days']) * 100, 1)
        : 0;

    // Get total bookings for last 30 days
    $stmt = $db->prepare("
        SELECT COUNT(*) as total_bookings
        FROM bookings 
        WHERE created_at >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
    ");
    $stmt->execute();
    $bookings = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get average stay duration
    $stmt = $db->prepare("
        SELECT AVG(DATEDIFF(check_out_date, check_in_date)) as average_stay
        FROM bookings
        WHERE status NOT IN ('cancelled')
        AND created_at >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
    ");
    $stmt->execute();
    $avgStay = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => [
            'total_revenue' => floatval($revenue['total_revenue']),
            'occupancy_rate' => $occupancyRate,
            'total_bookings' => intval($bookings['total_bookings']),
            'average_stay' => floatval($avgStay['average_stay'])
        ]
    ]);

} catch(Exception $e) {
    // Clear any output buffers
    ob_clean();
    
    error_log("Error getting summary stats: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error getting summary stats: ' . $e->getMessage()
    ]);
}

// End output buffer
ob_end_flush();
?> 