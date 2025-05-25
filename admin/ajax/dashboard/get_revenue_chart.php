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

    // Get monthly revenue for the last 12 months
    $stmt = $db->prepare("
        SELECT 
            DATE_FORMAT(created_at, '%Y-%m') as month,
            SUM(total_price) as revenue
        FROM bookings
        WHERE created_at >= DATE_SUB(CURRENT_DATE, INTERVAL 12 MONTH)
        AND status != 'cancelled'
        GROUP BY DATE_FORMAT(created_at, '%Y-%m')
        ORDER BY month ASC
    ");
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format data for chart
    $labels = [];
    $data = [];
    foreach ($results as $row) {
        $date = new DateTime($row['month'] . '-01');
        $labels[] = $date->format('M Y');
        $data[] = floatval($row['revenue']);
    }

    echo json_encode([
        'success' => true,
        'data' => [
            'labels' => $labels,
            'values' => $data
        ]
    ]);

} catch(Exception $e) {
    // Clear any output buffers
    ob_clean();
    
    error_log("Error getting revenue chart data: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error getting revenue chart data: ' . $e->getMessage()
    ]);
}

// End output buffer
ob_end_flush();
?> 