<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "../../../dbcon/Database.php";

// Ensure no output before headers
ob_start();
header('Content-Type: application/json');

try {
    // Validate required fields
    $required_fields = ['hotel_id', 'room_type', 'room_description', 'price_per_night', 'capacity'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            throw new Exception("Missing required field: " . $field);
        }
    }

    // Create database connection
    $database = new Database();
    $db = $database->getConnection();

    // Start transaction
    $db->beginTransaction();

    try {
        // Insert room
        $stmt = $db->prepare("
            INSERT INTO rooms (
                hotel_id,
                room_type,
                room_description,
                price_per_night,
                capacity,
                bed_type,
                room_size,
                is_available,
                created_at,
                updated_at
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?,
                CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
            )
        ");

        $stmt->execute([
            $_POST['hotel_id'],
            $_POST['room_type'],
            $_POST['room_description'],
            $_POST['price_per_night'],
            $_POST['capacity'],
            $_POST['bed_type'] ?? 'Queen',
            $_POST['room_size'] ?? null,
            isset($_POST['is_available']) ? 1 : 0
        ]);

        $room_id = $db->lastInsertId();

        // Handle image uploads
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
                        VALUES (?, ?, ?, ?)
                    ");
                    $stmt->execute([
                        $room_id, 
                        $relativePath,
                        $key === 0 ? 1 : 0, // First image is primary
                        $key + 1
                    ]);
                }
            }
        }

        // Commit transaction
        $db->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Room added successfully',
            'room_id' => $room_id
        ]);

    } catch (Exception $e) {
        // Rollback on error
        $db->rollBack();
        throw $e;
    }

} catch(Exception $e) {
    // Clear any output buffers
    ob_clean();
    
    error_log("Error adding room: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error adding room: ' . $e->getMessage()
    ]);
}

// End output buffer
ob_end_flush();
?> 