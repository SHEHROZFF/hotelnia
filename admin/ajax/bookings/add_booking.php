<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "../../../dbcon/Database.php";

// Ensure no output before headers
ob_start();
header('Content-Type: application/json');

try {
    // Validate required fields
    $required_fields = ['user_id', 'room_id', 'check_in_date', 'check_out_date', 'guests', 'total_price'];
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
        // Check if room is available for the selected dates
        $stmt = $db->prepare("
            SELECT COUNT(*) as booking_count
            FROM bookings 
            WHERE product_id = ? 
            AND status IN ('confirmed', 'checked_in')
            AND (
                (check_in_date <= ? AND check_out_date >= ?) OR
                (check_in_date <= ? AND check_out_date >= ?) OR
                (check_in_date >= ? AND check_out_date <= ?)
            )
        ");
        
        $stmt->execute([
            $_POST['room_id'],
            $_POST['check_in_date'],
            $_POST['check_in_date'],
            $_POST['check_out_date'],
            $_POST['check_out_date'],
            $_POST['check_in_date'],
            $_POST['check_out_date']
        ]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['booking_count'] > 0) {
            throw new Exception("Room is not available for the selected dates");
        }

        // Check room capacity
        $stmt = $db->prepare("SELECT capacity FROM rooms WHERE room_id = ?");
        $stmt->execute([$_POST['room_id']]);
        $room = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($_POST['guests'] > $room['capacity']) {
            throw new Exception("Number of guests exceeds room capacity");
        }

        // Insert booking
        $stmt = $db->prepare("
            INSERT INTO bookings (
                user_id,
                product_id,
                check_in_date,
                check_out_date,
                guests,
                total_price,
                status,
                special_requests,
                created_at,
                updated_at
            ) VALUES (
                ?, ?, ?, ?, ?, ?,
                'pending',
                ?,
                CURRENT_TIMESTAMP,
                CURRENT_TIMESTAMP
            )
        ");

        $stmt->execute([
            $_POST['user_id'],
            $_POST['room_id'],
            $_POST['check_in_date'],
            $_POST['check_out_date'],
            $_POST['guests'],
            $_POST['total_price'],
            $_POST['special_requests'] ?? null
        ]);

        $booking_id = $db->lastInsertId();

        // Commit transaction
        $db->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Booking created successfully',
            'booking_id' => $booking_id
        ]);

    } catch (Exception $e) {
        // Rollback on error
        $db->rollBack();
        throw $e;
    }

} catch(Exception $e) {
    // Clear any output buffers
    ob_clean();
    
    error_log("Error creating booking: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error creating booking: ' . $e->getMessage()
    ]);
}

// End output buffer
ob_end_flush();
?> 