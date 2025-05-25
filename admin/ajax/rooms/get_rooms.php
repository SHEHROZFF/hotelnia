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

    // Build query conditions
    $conditions = [];
    $params = [];

    if (isset($_GET['hotel_id']) && !empty($_GET['hotel_id'])) {
        $conditions[] = "r.hotel_id = ?";
        $params[] = $_GET['hotel_id'];
    }

    if (isset($_GET['is_available']) && $_GET['is_available'] !== '') {
        $conditions[] = "r.is_available = ?";
        $params[] = $_GET['is_available'];
    }

    $whereClause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

    // Get rooms with hotel names
    $query = "
        SELECT 
            r.*,
            h.hotel_name,
            (SELECT COUNT(*) FROM bookings b WHERE b.product_id = r.room_id) as total_bookings,
            EXISTS(
                SELECT 1 FROM bookings b 
                WHERE b.product_id = r.room_id 
                AND b.status = 'confirmed'
                AND CURRENT_DATE BETWEEN b.check_in_date AND b.check_out_date
            ) as is_occupied
        FROM rooms r
        LEFT JOIN hotels h ON r.hotel_id = h.hotel_id
        $whereClause
        ORDER BY r.created_at DESC
    ";

    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Clear any output buffers
    ob_clean();
    
    echo json_encode([
        "draw" => isset($_GET['draw']) ? intval($_GET['draw']) : 1,
        "recordsTotal" => count($rooms),
        "recordsFiltered" => count($rooms),
        "data" => $rooms
    ]);

} catch(Exception $e) {
    // Clear any output buffers
    ob_clean();
    
    error_log("Database error in get_rooms.php: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        "draw" => isset($_GET['draw']) ? intval($_GET['draw']) : 1,
        "recordsTotal" => 0,
        "recordsFiltered" => 0,
        "data" => [],
        "error" => $e->getMessage()
    ]);
}

// End output buffer
ob_end_flush();
?> 