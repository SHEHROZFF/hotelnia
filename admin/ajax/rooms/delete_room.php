<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "../../../dbcon/Database.php";

// Ensure no output before headers
ob_start();
header('Content-Type: application/json');

try {
    // Validate input
    if (!isset($_POST['room_id']) || !is_numeric($_POST['room_id'])) {
        throw new Exception("Invalid room ID");
    }

    $room_id = intval($_POST['room_id']);

    // Create database connection
    $database = new Database();
    $db = $database->getConnection();

    // Start transaction
    $db->beginTransaction();

    try {
        // Check if room has any active bookings
        $stmt = $db->prepare("
            SELECT COUNT(*) as booking_count 
            FROM bookings 
            WHERE product_id = ? 
            AND status = 'confirmed'
            AND check_out_date >= CURRENT_DATE
        ");
        $stmt->execute([$room_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result['booking_count'] > 0) {
            throw new Exception("Cannot delete room: It has active bookings");
        }

        // Delete room images from storage
        $stmt = $db->prepare("SELECT image_path FROM room_images WHERE room_id = ?");
        $stmt->execute([$room_id]);
        $images = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($images as $image) {
            $filePath = "../../../" . $image['image_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // Delete from room_images
        $stmt = $db->prepare("DELETE FROM room_images WHERE room_id = ?");
        $stmt->execute([$room_id]);

        // Delete from bookings (historical records)
        $stmt = $db->prepare("DELETE FROM bookings WHERE product_id = ?");
        $stmt->execute([$room_id]);

        // Finally delete the room
        $stmt = $db->prepare("DELETE FROM rooms WHERE room_id = ?");
        $stmt->execute([$room_id]);

        // Commit transaction
        $db->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Room deleted successfully'
        ]);

    } catch (Exception $e) {
        // Rollback on error
        $db->rollBack();
        throw $e;
    }

} catch(Exception $e) {
    // Clear any output buffers
    ob_clean();
    
    error_log("Error deleting room: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error deleting room: ' . $e->getMessage()
    ]);
}

// End output buffer
ob_end_flush();
?> 