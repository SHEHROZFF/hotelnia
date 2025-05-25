<?php
require_once "../global.php";
require_once "../dbcon/Database.php";

// Set headers
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'You must be logged in to cancel a booking.'
    ]);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$bookingId = isset($data['booking_id']) ? (int)$data['booking_id'] : 0;

if ($bookingId <= 0) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid booking ID.'
    ]);
    exit;
}

try {
    $db = new Database();
    $pdo = $db->getConnection();

    // First, check if the booking belongs to the user and is cancellable
    $stmt = $pdo->prepare("
        SELECT status, check_in_date 
        FROM bookings 
        WHERE booking_id = :booking_id 
        AND user_id = :user_id
    ");
    $stmt->execute([
        'booking_id' => $bookingId,
        'user_id' => $_SESSION['user_id']
    ]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$booking) {
        throw new Exception('Booking not found or does not belong to you.');
    }

    if ($booking['status'] === 'cancelled') {
        throw new Exception('This booking is already cancelled.');
    }

    if ($booking['status'] === 'checked_in' || $booking['status'] === 'checked_out' || $booking['status'] === 'completed') {
        throw new Exception('Cannot cancel a booking that is already ' . $booking['status'] . '.');
    }

    if (strtotime($booking['check_in_date']) <= time()) {
        throw new Exception('Cannot cancel a booking on or after the check-in date.');
    }

    // Update booking status to cancelled
    $stmt = $pdo->prepare("
        UPDATE bookings 
        SET status = 'cancelled', updated_at = NOW() 
        WHERE booking_id = :booking_id 
        AND user_id = :user_id
    ");
    
    if ($stmt->execute([
        'booking_id' => $bookingId,
        'user_id' => $_SESSION['user_id']
    ])) {
        // Send cancellation email (in a real project)
        // sendBookingCancellationEmail($_SESSION['user_email'], $bookingId);

        echo json_encode([
            'success' => true,
            'message' => 'Booking cancelled successfully.'
        ]);
    } else {
        throw new Exception('Failed to cancel booking.');
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 