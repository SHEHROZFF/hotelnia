<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "../../../dbcon/Database.php";

// Ensure no output before headers
ob_start();
header('Content-Type: application/json');

try {
    // Validate required fields
    $required_fields = ['hotel_name', 'hotel_type', 'hotel_description', 'hotel_address', 'city', 'country', 'star_rating', 'price_range_start', 'price_range_end'];
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
        // Insert hotel
        $stmt = $db->prepare("
            INSERT INTO hotels (
                hotel_name, 
                hotel_description,
                hotel_address,
                city,
                country,
                star_rating,
                price_range_start,
                price_range_end,
                hotel_type,
                is_active,
                created_at,
                updated_at
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 
                CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
            )
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
            isset($_POST['is_active']) ? 1 : 0
        ]);

        $hotel_id = $db->lastInsertId();

        // Add amenities
        if (isset($_POST['amenities']) && is_array($_POST['amenities'])) {
            $stmt = $db->prepare("INSERT INTO hotel_amenities (hotel_id, amenity_id) VALUES (?, ?)");
            foreach ($_POST['amenities'] as $amenity_id) {
                $stmt->execute([$hotel_id, $amenity_id]);
            }
        }

        // Handle image uploads
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
                        VALUES (?, ?, ?, ?)
                    ");
                    $stmt->execute([
                        $hotel_id, 
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
            'message' => 'Hotel added successfully',
            'hotel_id' => $hotel_id
        ]);

    } catch (Exception $e) {
        // Rollback on error
        $db->rollBack();
        throw $e;
    }

} catch(Exception $e) {
    // Clear any output buffers
    ob_clean();
    
    error_log("Error adding hotel: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error adding hotel: ' . $e->getMessage()
    ]);
}

// End output buffer
ob_end_flush();
?> 