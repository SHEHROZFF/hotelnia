<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "../../../dbcon/Database.php";

// Ensure no output before headers
ob_start();
header('Content-Type: application/json');

try {
    // Validate input
    if (!isset($_POST['image_id']) || !is_numeric($_POST['image_id'])) {
        throw new Exception("Invalid image ID");
    }

    $image_id = intval($_POST['image_id']);

    // Create database connection
    $database = new Database();
    $db = $database->getConnection();

    // Start transaction
    $db->beginTransaction();

    try {
        // Get image details
        $stmt = $db->prepare("
            SELECT ri.*, COUNT(ri2.image_id) as total_images
            FROM room_images ri
            LEFT JOIN room_images ri2 ON ri.room_id = ri2.room_id
            WHERE ri.image_id = ?
            GROUP BY ri.image_id
        ");
        $stmt->execute([$image_id]);
        $image = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$image) {
            throw new Exception("Image not found");
        }

        // Don't allow deletion if it's the only image and it's primary
        if ($image['total_images'] == 1) {
            throw new Exception("Cannot delete the only image of the room");
        }

        // Delete the physical file
        $filePath = "../../../" . $image['image_path'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // If this was the primary image, make another image primary
        if ($image['is_primary'] == 1) {
            $stmt = $db->prepare("
                UPDATE room_images 
                SET is_primary = 1 
                WHERE room_id = ? 
                AND image_id != ? 
                ORDER BY sort_order ASC 
                LIMIT 1
            ");
            $stmt->execute([$image['room_id'], $image_id]);
        }

        // Delete the image record
        $stmt = $db->prepare("DELETE FROM room_images WHERE image_id = ?");
        $stmt->execute([$image_id]);

        // Reorder remaining images
        $stmt = $db->prepare("
            UPDATE room_images 
            SET sort_order = (@row_number:=@row_number + 1)
            WHERE room_id = ?
            ORDER BY is_primary DESC, sort_order ASC
        ");
        $db->query("SET @row_number = 0");
        $stmt->execute([$image['room_id']]);

        // Commit transaction
        $db->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Image deleted successfully'
        ]);

    } catch (Exception $e) {
        // Rollback on error
        $db->rollBack();
        throw $e;
    }

} catch(Exception $e) {
    // Clear any output buffers
    ob_clean();
    
    error_log("Error deleting room image: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error deleting room image: ' . $e->getMessage()
    ]);
}

// End output buffer
ob_end_flush();
?> 