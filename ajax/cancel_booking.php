<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Only start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set JSON header
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to continue']);
    exit;
}

// Get and validate the POST data
$input = json_decode(file_get_contents('php://input'), true);
if (!isset($input['booking_id']) || !is_numeric($input['booking_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid booking ID']);
    exit;
}

require_once '../dbcon/Database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Get booking details
    $stmt = $conn->prepare("
        SELECT * FROM bookings 
        WHERE booking_id = ? AND user_id = ? AND status != 'cancelled'
    ");
    $stmt->execute([$input['booking_id'], $_SESSION['user_id']]);
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
    $stmt->execute([$input['booking_id'], $_SESSION['user_id']]);

    // If there's a payment intent, process refund through Stripe
    if (!empty($booking['payment_intent_id'])) {
        require_once '../vendor/autoload.php';
        
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
    echo json_encode([
        'success' => true, 
        'message' => 'Booking cancelled successfully'
    ]);

} catch (Exception $e) {
    if (isset($conn) && $conn->inTransaction()) {
        $conn->rollBack();
    }
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
}
?> 