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
        // Update room details
        $stmt = $db->prepare("
            UPDATE rooms 
            SET 
                hotel_id = ?,
                room_type = ?,
                room_description = ?,
                price_per_night = ?,
                capacity = ?,
                bed_type = ?,
                room_size = ?,
                is_available = ?,
                updated_at = CURRENT_TIMESTAMP
            WHERE room_id = ?
        ");

        $stmt->execute([
            $_POST['hotel_id'],
            $_POST['room_type'],
            $_POST['room_description'],
            $_POST['price_per_night'],
            $_POST['capacity'],
            $_POST['bed_type'],
            $_POST['room_size'],
            isset($_POST['is_available']) ? 1 : 0,
            $room_id
        ]);

        // Handle image uploads if any
        if (!empty($_FILES['room_images']['name'][0])) {
            $uploadDir = '../../../assets/images/rooms/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            foreach ($_FILES['room_images']['tmp_name'] as $key => $tmp_name) {
                $fileName = uniqid() . '_' . $_FILES['room_images']['name'][$key];
                $filePath = $uploadDir . $fileName;
                
                if (move_uploaded_file($tmp_name, $filePath)) {
                    $relativePath = 'assets/images/rooms/' . $fileName;
                    
                    $stmt = $db->prepare("
                        INSERT INTO room_images (room_id, image_path, is_primary, sort_order) 
                        VALUES (?, ?, 0, (SELECT COALESCE(MAX(sort_order), 0) + 1 FROM room_images ri WHERE room_id = ?))
                    ");
                    $stmt->execute([$room_id, $relativePath, $room_id]);
                }
            }
        }

        // Commit transaction
        $db->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Room updated successfully'
        ]);

    } catch (Exception $e) {
        // Rollback on error
        $db->rollBack();
        throw $e;
    }

} catch(Exception $e) {
    // Clear any output buffers
    ob_clean();
    
    error_log("Error updating room: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error updating room: ' . $e->getMessage()
    ]);
}

// End output buffer
ob_end_flush();
?> 