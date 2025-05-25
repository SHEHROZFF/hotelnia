<?php
header('Content-Type: application/json');

require_once '../dbcon/Database.php';
require_once '../classes/Hotel.php';

try {
    // Validate input
    if (!isset($_GET['room_id']) || !isset($_GET['check_in']) || !isset($_GET['check_out'])) {
        throw new Exception('Missing required parameters');
    }

    $roomId = (int)$_GET['room_id'];
    $checkIn = $_GET['check_in'];
    $checkOut = $_GET['check_out'];

    // Validate dates
    $checkInDate = new DateTime($checkIn);
    $checkOutDate = new DateTime($checkOut);
    $today = new DateTime();

    if ($checkInDate < $today) {
        throw new Exception('Check-in date cannot be in the past');
    }

    if ($checkOutDate <= $checkInDate) {
        throw new Exception('Check-out date must be after check-in date');
    }

    // Check availability
    $db = new Database();
    $hotelObj = new Hotel($db);
    $isAvailable = $hotelObj->checkRoomAvailability($roomId, $checkIn, $checkOut);

    echo json_encode([
        'available' => $isAvailable,
        'message' => $isAvailable ? 'Room is available' : 'Room is not available for the selected dates'
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'available' => false,
        'error' => $e->getMessage()
    ]);
} 