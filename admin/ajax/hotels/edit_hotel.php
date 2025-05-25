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
        // Update hotel details
        $stmt = $db->prepare("
            UPDATE hotels 
            SET 
                hotel_name = ?,
                hotel_description = ?,
                hotel_address = ?,
                city = ?,
                country = ?,
                star_rating = ?,
                price_range_start = ?,
                price_range_end = ?,
                hotel_type = ?,
                is_active = ?,
                updated_at = CURRENT_TIMESTAMP
            WHERE hotel_id = ?
        ");

        $stmt->execute([
            $_POST['hotel_name'],
            $_POST['hotel_description'],
            $_POST['hotel_address'],
            $_POST['city'],
            $_POST['country'],
            $_POST['star_rating'],
            $_POST['price_range_start'],
            $_POST['price_range_end'],
            $_POST['hotel_type'],
            isset($_POST['is_active']) ? 1 : 0,
            $hotel_id
        ]);

        // Update amenities
        $stmt = $db->prepare("DELETE FROM hotel_amenities WHERE hotel_id = ?");
        $stmt->execute([$hotel_id]);

        if (isset($_POST['amenities']) && is_array($_POST['amenities'])) {
            $stmt = $db->prepare("INSERT INTO hotel_amenities (hotel_id, amenity_id) VALUES (?, ?)");
            foreach ($_POST['amenities'] as $amenity_id) {
                $stmt->execute([$hotel_id, $amenity_id]);
            }
        }

        // Handle image uploads if any
        if (!empty($_FILES['hotel_images']['name'][0])) {
            $uploadDir = '../../../assets/images/Hotel Booking/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            foreach ($_FILES['hotel_images']['tmp_name'] as $key => $tmp_name) {
                $fileName = uniqid() . '_' . $_FILES['hotel_images']['name'][$key];
                $filePath = $uploadDir . $fileName;
                
                if (move_uploaded_file($tmp_name, $filePath)) {
                    $relativePath = 'assets/images/Hotel Booking/' . $fileName;
                    
                    $stmt = $db->prepare("
                        INSERT INTO hotel_images (hotel_id, image_path, is_primary, sort_order) 
                        VALUES (?, ?, 0, (SELECT COALESCE(MAX(sort_order), 0) + 1 FROM hotel_images hi WHERE hotel_id = ?))
                    ");
                    $stmt->execute([$hotel_id, $relativePath, $hotel_id]);
                }
            }
        }

        // Commit transaction
        $db->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Hotel updated successfully'
        ]);

    } catch (Exception $e) {
        // Rollback on error
        $db->rollBack();
        throw $e;
    }

} catch(Exception $e) {
    // Clear any output buffers
    ob_clean();
    
    error_log("Error updating hotel: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error updating hotel: ' . $e->getMessage()
    ]);
}

// End output buffer
ob_end_flush();
?> 