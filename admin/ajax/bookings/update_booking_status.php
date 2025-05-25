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

    if (!isset($_POST['status']) || empty($_POST['status'])) {
        throw new Exception("Status is required");
    }

    $booking_id = intval($_POST['booking_id']);
    $status = $_POST['status'];

    // Validate status
    $valid_statuses = ['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled'];
    if (!in_array($status, $valid_statuses)) {
        throw new Exception("Invalid status");
    }

    // Create database connection
    $database = new Database();
    $db = $database->getConnection();

    // Start transaction
    $db->beginTransaction();

    try {
        // Get current booking details
        $stmt = $db->prepare("SELECT * FROM bookings WHERE booking_id = ?");
        $stmt->execute([$booking_id]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$booking) {
            throw new Exception("Booking not found");
        }

        // Validate status transition
        $valid_transition = true;
        $error_message = "";

        switch ($status) {
            case 'confirmed':
                if (!in_array($booking['status'], ['pending'])) {
                    $valid_transition = false;
                    $error_message = "Can only confirm pending bookings";
                }
                break;
            
            case 'checked_in':
                if (!in_array($booking['status'], ['confirmed'])) {
                    $valid_transition = false;
                    $error_message = "Can only check in confirmed bookings";
                }
                // Check if check-in date is today or in the past
                if (strtotime($booking['check_in_date']) > strtotime('today')) {
                    $valid_transition = false;
                    $error_message = "Cannot check in before check-in date";
                }
                break;
            
            case 'checked_out':
                if (!in_array($booking['status'], ['checked_in'])) {
                    $valid_transition = false;
                    $error_message = "Can only check out checked-in bookings";
                }
                break;
            
            case 'cancelled':
                if (!in_array($booking['status'], ['pending', 'confirmed'])) {
                    $valid_transition = false;
                    $error_message = "Cannot cancel a booking that is already " . $booking['status'];
                }
                break;
        }

        if (!$valid_transition) {
            throw new Exception($error_message);
        }

        // Update booking status
        $stmt = $db->prepare("
            UPDATE bookings 
            SET 
                status = ?,
                updated_at = CURRENT_TIMESTAMP
            WHERE booking_id = ?
        ");

        $stmt->execute([$status, $booking_id]);

        // Commit transaction
        $db->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Booking status updated successfully'
        ]);

    } catch (Exception $e) {
        // Rollback on error
        $db->rollBack();
        throw $e;
    }

} catch(Exception $e) {
    // Clear any output buffers
    ob_clean();
    
    error_log("Error updating booking status: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error updating booking status: ' . $e->getMessage()
    ]);
}

// End output buffer
ob_end_flush();
?> 