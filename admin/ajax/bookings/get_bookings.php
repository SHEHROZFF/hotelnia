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

    if (isset($_GET['status']) && !empty($_GET['status'])) {
        $conditions[] = "b.status = ?";
        $params[] = $_GET['status'];
    }

    if (isset($_GET['date']) && !empty($_GET['date'])) {
        $conditions[] = "(b.check_in_date <= ? AND b.check_out_date >= ?)";
        $params[] = $_GET['date'];
        $params[] = $_GET['date'];
    }

    $whereClause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

    // Get bookings with related information
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
        ORDER BY b.created_at DESC
    ";

    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Clear any output buffers
    ob_clean();
    
    echo json_encode([
        "draw" => isset($_GET['draw']) ? intval($_GET['draw']) : 1,
        "recordsTotal" => count($bookings),
        "recordsFiltered" => count($bookings),
        "data" => $bookings
    ]);

} catch(Exception $e) {
    // Clear any output buffers
    ob_clean();
    
    error_log("Database error in get_bookings.php: " . $e->getMessage());
    
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