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

    // Get total revenue for current month
    $stmt = $db->prepare("
        SELECT 
            SUM(total_price) as current_month_revenue,
            (SELECT SUM(total_price) 
             FROM bookings 
             WHERE MONTH(created_at) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)
             AND YEAR(created_at) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)
             AND status != 'cancelled') as last_month_revenue
        FROM bookings
        WHERE MONTH(created_at) = MONTH(CURRENT_DATE)
        AND YEAR(created_at) = YEAR(CURRENT_DATE)
        AND status != 'cancelled'
    ");
    $stmt->execute();
    $revenue = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Calculate revenue change percentage
    $currentRevenue = floatval($revenue['current_month_revenue']) ?? 0;
    $lastRevenue = floatval($revenue['last_month_revenue']) ?? 0;
    $revenueChange = $lastRevenue > 0 ? (($currentRevenue - $lastRevenue) / $lastRevenue) * 100 : 0;

    // Get bookings count
    $stmt = $db->prepare("
        SELECT 
            COUNT(*) as current_month_bookings,
            (SELECT COUNT(*) 
             FROM bookings 
             WHERE MONTH(created_at) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)
             AND YEAR(created_at) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)) as last_month_bookings
        FROM bookings
        WHERE MONTH(created_at) = MONTH(CURRENT_DATE)
        AND YEAR(created_at) = YEAR(CURRENT_DATE)
    ");
    $stmt->execute();
    $bookings = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Calculate bookings change percentage
    $currentBookings = intval($bookings['current_month_bookings']);
    $lastBookings = intval($bookings['last_month_bookings']);
    $bookingsChange = $lastBookings > 0 ? (($currentBookings - $lastBookings) / $lastBookings) * 100 : 0;

    // Get active guests (currently checked in)
    $stmt = $db->prepare("
        SELECT 
            COUNT(*) as current_guests,
            (SELECT COUNT(*) 
             FROM bookings 
             WHERE status = 'checked_in'
             AND DATE(created_at) = CURRENT_DATE - INTERVAL 1 DAY) as yesterday_guests
        FROM bookings
        WHERE status = 'checked_in'
        AND CURRENT_DATE BETWEEN check_in_date AND check_out_date
    ");
    $stmt->execute();
    $guests = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Calculate guests change
    $guestsChange = intval($guests['current_guests']) - intval($guests['yesterday_guests']);

    // Calculate occupancy rate
    $stmt = $db->prepare("
        SELECT 
            (SELECT COUNT(*) FROM rooms) as total_rooms,
            (SELECT COUNT(*) 
             FROM bookings 
             WHERE status = 'checked_in'
             AND CURRENT_DATE BETWEEN check_in_date AND check_out_date) as occupied_rooms,
            (SELECT COUNT(*) 
             FROM bookings 
             WHERE status = 'checked_in'
             AND DATE_SUB(CURRENT_DATE, INTERVAL 1 MONTH) BETWEEN check_in_date AND check_out_date) as last_month_occupied
    ");
    $stmt->execute();
    $occupancy = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $totalRooms = intval($occupancy['total_rooms']);
    $currentOccupancy = $totalRooms > 0 ? (intval($occupancy['occupied_rooms']) / $totalRooms) * 100 : 0;
    $lastMonthOccupancy = $totalRooms > 0 ? (intval($occupancy['last_month_occupied']) / $totalRooms) * 100 : 0;
    $occupancyChange = $lastMonthOccupancy > 0 ? ($currentOccupancy - $lastMonthOccupancy) : 0;

    echo json_encode([
        'success' => true,
        'data' => [
            'revenue' => [
                'amount' => $currentRevenue,
                'change' => round($revenueChange, 1)
            ],
            'bookings' => [
                'count' => $currentBookings,
                'change' => round($bookingsChange, 1)
            ],
            'guests' => [
                'count' => intval($guests['current_guests']),
                'change' => $guestsChange
            ],
            'occupancy' => [
                'rate' => round($currentOccupancy, 1),
                'change' => round($occupancyChange, 1)
            ]
        ]
    ]);

} catch(Exception $e) {
    // Clear any output buffers
    ob_clean();
    
    error_log("Error getting dashboard stats: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error getting dashboard stats: ' . $e->getMessage()
    ]);
}

// End output buffer
ob_end_flush();
?> 