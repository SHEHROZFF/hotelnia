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
    $conditions = ["role = 'user'"];
    $params = [];

    if (isset($_GET['verification']) && $_GET['verification'] !== '') {
        $conditions[] = "email_verified = ?";
        $params[] = $_GET['verification'];
    }

    if (isset($_GET['booking']) && !empty($_GET['booking'])) {
        switch ($_GET['booking']) {
            case 'active':
                $conditions[] = "EXISTS (
                    SELECT 1 FROM bookings b 
                    WHERE b.user_id = users.id 
                    AND b.status IN ('confirmed', 'checked_in')
                )";
                break;
            case 'past':
                $conditions[] = "EXISTS (
                    SELECT 1 FROM bookings b 
                    WHERE b.user_id = users.id 
                    AND b.status = 'checked_out'
                )";
                break;
            case 'none':
                $conditions[] = "NOT EXISTS (
                    SELECT 1 FROM bookings b 
                    WHERE b.user_id = users.id
                )";
                break;
        }
    }

    $whereClause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

    // Get users with booking statistics
    $query = "
        SELECT 
            u.*,
            COUNT(DISTINCT b.booking_id) as total_bookings,
            SUM(CASE WHEN b.status IN ('confirmed', 'checked_in') THEN 1 ELSE 0 END) as active_bookings,
            SUM(CASE WHEN b.status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_bookings,
            SUM(CASE WHEN b.status IN ('checked_out', 'cancelled') THEN b.total_price ELSE 0 END) as total_spent
        FROM users u
        LEFT JOIN bookings b ON u.id = b.user_id
        $whereClause
        GROUP BY u.id
        ORDER BY u.created_at DESC
    ";

    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Clear any output buffers
    ob_clean();
    
    echo json_encode([
        "draw" => isset($_GET['draw']) ? intval($_GET['draw']) : 1,
        "recordsTotal" => count($users),
        "recordsFiltered" => count($users),
        "data" => $users
    ]);

} catch(Exception $e) {
    // Clear any output buffers
    ob_clean();
    
    error_log("Database error in get_guests.php: " . $e->getMessage());
    
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