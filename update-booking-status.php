<?php
session_start();
require_once 'dbcon/Database.php';

// Set content type for JSON response
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Validate required fields
if (!isset($input['booking_id']) || !isset($input['payment_intent_id']) || !isset($input['status'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

$bookingId = (int)$input['booking_id'];
$paymentIntentId = $input['payment_intent_id'];
$status = $input['status'];

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Verify that the booking belongs to the current user
    $stmt = $conn->prepare("SELECT user_id FROM bookings WHERE booking_id = ?");
    $stmt->execute([$bookingId]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$booking || $booking['user_id'] != $_SESSION['user_id']) {
        http_response_code(403);
        echo json_encode(['error' => 'Access denied']);
        exit;
    }
    
    // Update booking status
    $stmt = $conn->prepare("UPDATE bookings SET status = ?, payment_intent_id = ?, updated_at = NOW() WHERE booking_id = ?");
    $stmt->execute([$status, $paymentIntentId, $bookingId]);
    
    echo json_encode(['success' => true, 'message' => 'Booking status updated successfully']);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?> 