<?php
include "header.php";

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Please login to continue.";
    header("Location: login.php");
    exit;
}

// Check if booking ID is provided
if (!isset($_POST['booking_id']) || !is_numeric($_POST['booking_id'])) {
    $_SESSION['error'] = "Invalid booking ID.";
    header("Location: my-bookings.php");
    exit;
}

require_once 'dbcon/Database.php';
require_once 'vendor/autoload.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Get booking details
    $stmt = $conn->prepare("
        SELECT * FROM bookings 
        WHERE booking_id = ? AND user_id = ? AND status != 'cancelled'
    ");
    $stmt->execute([$_POST['booking_id'], $_SESSION['user_id']]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$booking) {
        throw new Exception("Booking not found or already cancelled.");
    }

    // Check if check-in date is in the future
    if (strtotime($booking['check_in_date']) <= time()) {
        throw new Exception("Cannot cancel bookings that have already started or completed.");
    }

    // Start transaction
    $conn->beginTransaction();

    // Update booking status
    $stmt = $conn->prepare("
        UPDATE bookings 
        SET status = 'cancelled', 
            updated_at = NOW() 
        WHERE booking_id = ? AND user_id = ?
    ");
    $stmt->execute([$_POST['booking_id'], $_SESSION['user_id']]);

    // If there's a payment intent, process refund through Stripe
    if (!empty($booking['payment_intent_id'])) {
        // Initialize Stripe with your secret key
        \Stripe\Stripe::setApiKey('sk_test_51OXlAIAZK57wNYnQQluuPOe6YHwpKCs2dZfKLaEe7Ye67OObYR3Hes3i0Vjo1yp450mlVWQ9ufvWWYYymF1mc33R00GwSCgwFi');

        try {
            // Create refund
            $refund = \Stripe\Refund::create([
                'payment_intent' => $booking['payment_intent_id']
            ]);
            
            // Log the refund for record keeping
            error_log("Refund processed for booking {$booking['booking_id']}: {$refund->id}");
            
        } catch (\Stripe\Exception\ApiErrorException $e) {
            // Log the error but don't stop the cancellation process
            error_log("Stripe refund error for booking {$booking['booking_id']}: " . $e->getMessage());
        }
    }

    $conn->commit();
    $_SESSION['success'] = "Booking cancelled successfully.";

} catch (Exception $e) {
    if (isset($conn) && $conn->inTransaction()) {
        $conn->rollBack();
    }
    $_SESSION['error'] = $e->getMessage();
}

// Redirect back to booking details
header("Location: booking-details.php?id=" . $_POST['booking_id']);
exit;
?> 