<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "../../../dbcon/Database.php";

// Ensure no output before headers
ob_start();
header('Content-Type: application/json');

try {
    // Validate input
    if (!isset($_POST['hotel_id']) || !is_numeric($_POST['hotel_id'])) {
        throw new Exception("Invalid hotel ID");
    }

    $hotel_id = intval($_POST['hotel_id']);

    // Create database connection
    $database = new Database();
    $db = $database->getConnection();

    // Start transaction
    $db->beginTransaction();

    try {
        // Delete from hotel_amenities
        $stmt = $db->prepare("DELETE FROM hotel_amenities WHERE hotel_id = ?");
        $stmt->execute([$hotel_id]);

        // Delete from hotel_images
        $stmt = $db->prepare("DELETE FROM hotel_images WHERE hotel_id = ?");
        $stmt->execute([$hotel_id]);

        // Delete from rooms
        $stmt = $db->prepare("DELETE FROM rooms WHERE hotel_id = ?");
        $stmt->execute([$hotel_id]);

        // Finally delete the hotel
        $stmt = $db->prepare("DELETE FROM hotels WHERE hotel_id = ?");
        $stmt->execute([$hotel_id]);

        // Commit transaction
        $db->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Hotel deleted successfully'
        ]);

    } catch (Exception $e) {
        // Rollback on error
        $db->rollBack();
        throw $e;
    }

} catch(Exception $e) {
    // Clear any output buffers
    ob_clean();
    
    error_log("Error deleting hotel: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error deleting hotel: ' . $e->getMessage()
    ]);
}

// End output buffer
ob_end_flush();
?> 