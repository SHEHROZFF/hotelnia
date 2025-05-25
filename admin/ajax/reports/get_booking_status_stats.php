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

    // Get booking distribution by status
    $stmt = $db->prepare("
        SELECT 
            status,
            COUNT(*) as status_count
        FROM bookings
        WHERE created_at >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
        GROUP BY status
        ORDER BY status_count DESC
    ");
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format data for chart
    $labels = [];
    $values = [];
    foreach ($results as $row) {
        $labels[] = ucfirst($row['status']);
        $values[] = intval($row['status_count']);
    }

    echo json_encode([
        'success' => true,
        'data' => [
            'labels' => $labels,
            'values' => $values
        ]
    ]);

} catch(Exception $e) {
    // Clear any output buffers
    ob_clean();
    
    error_log("Error getting booking status stats: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error getting booking status stats: ' . $e->getMessage()
    ]);
}

// End output buffer
ob_end_flush();
?> 