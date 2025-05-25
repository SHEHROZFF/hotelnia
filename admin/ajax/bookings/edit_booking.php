<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "../../../dbcon/Database.php";

// Ensure no output before headers
ob_start();
header('Content-Type: application/json');

try {
    // Validate input
    if (!isset($_POST['booking_id']) || !is_numeric($_POST['booking_id'])) {
        throw new Exception("Invalid booking ID");
    }

    $booking_id = intval($_POST['booking_id']);

    // Create database connection
    $database = new Database();
    $db = $database->getConnection();

    // Start transaction
    $db->beginTransaction();

    try {
        // Get current booking details
        $stmt = $db->prepare("SELECT * FROM bookings WHERE booking_id = ?");
        $stmt->execute([$booking_id]);
        $current_booking = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$current_booking) {
            throw new Exception("Booking not found");
        }

        // Check if dates are being changed
        $dates_changed = 
            $_POST['check_in_date'] != $current_booking['check_in_date'] ||
            $_POST['check_out_date'] != $current_booking['check_out_date'];

        if ($dates_changed) {
            // Check if room is available for the new dates
            $stmt = $db->prepare("
                SELECT COUNT(*) as booking_count
                FROM bookings 
                WHERE product_id = ? 
                AND booking_id != ?
                AND status IN ('confirmed', 'checked_in')
                AND (
                    (check_in_date <= ? AND check_out_date >= ?) OR
                    (check_in_date <= ? AND check_out_date >= ?) OR
                    (check_in_date >= ? AND check_out_date <= ?)
                )
            ");
            
            $stmt->execute([
                $current_booking['product_id'],
                $booking_id,
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
        }

        // Check room capacity
        $stmt = $db->prepare("SELECT capacity FROM rooms WHERE room_id = ?");
        $stmt->execute([$current_booking['product_id']]);
        $room = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($_POST['guests'] > $room['capacity']) {
            throw new Exception("Number of guests exceeds room capacity");
        }

        // Update booking
        $stmt = $db->prepare("
            UPDATE bookings 
            SET 
                check_in_date = ?,
                check_out_date = ?,
                guests = ?,
                total_price = ?,
                status = ?,
                special_requests = ?,
                updated_at = CURRENT_TIMESTAMP
            WHERE booking_id = ?
        ");

        $stmt->execute([
            $_POST['check_in_date'],
            $_POST['check_out_date'],
            $_POST['guests'],
            $_POST['total_price'],
            $_POST['status'],
            $_POST['special_requests'] ?? null,
            $booking_id
        ]);

        // Commit transaction
        $db->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Booking updated successfully'
        ]);

    } catch (Exception $e) {
        // Rollback on error
        $db->rollBack();
        throw $e;
    }

} catch(Exception $e) {
    // Clear any output buffers
    ob_clean();
    
    error_log("Error updating booking: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error updating booking: ' . $e->getMessage()
    ]);
}

// End output buffer
ob_end_flush();
?> 