<?php
include "header.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get booking ID from URL
$bookingId = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;

if ($bookingId <= 0) {
    header("Location: my-bookings.php");
    exit;
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Get booking details with hotel and room information
    $stmt = $conn->prepare("
        SELECT b.*, r.room_type, r.bed_type, r.room_size, 
               h.hotel_name, h.hotel_address, h.city, h.country, h.star_rating,
               u.name as user_name, u.email as user_email
        FROM bookings b
        JOIN rooms r ON b.product_id = r.room_id
        JOIN hotels h ON r.hotel_id = h.hotel_id
        JOIN users u ON b.user_id = u.id
        WHERE b.booking_id = ? AND b.user_id = ?
    ");
    $stmt->execute([$bookingId, $_SESSION['user_id']]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$booking) {
        $_SESSION['error'] = "Booking not found or access denied.";
        header("Location: my-bookings.php");
        exit;
    }

} catch (Exception $e) {
    $_SESSION['error'] = "An error occurred: " . $e->getMessage();
    header("Location: my-bookings.php");
    exit;
}

// Calculate nights
$checkInDate = new DateTime($booking['check_in_date']);
$checkOutDate = new DateTime($booking['check_out_date']);
$nights = $checkInDate->diff($checkOutDate)->days;
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Success Message -->
            <div class="alert alert-success text-center mb-4">
                <i class="fas fa-check-circle fa-3x mb-3"></i>
                <h3>Booking Confirmed!</h3>
                <p class="mb-0">Your booking has been successfully confirmed. You will receive a confirmation email shortly.</p>
            </div>

            <!-- Booking Details Card -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-calendar-check me-2"></i>Booking Confirmation</h4>
                </div>
                <div class="card-body">
                    <!-- Booking Reference -->
                    <div class="row mb-4">
                        <div class="col-12 text-center">
                            <h5>Booking Reference: <span class="text-primary">#<?php echo str_pad($booking['booking_id'], 6, '0', STR_PAD_LEFT); ?></span></h5>
                            <p class="text-muted">Status: <span class="badge bg-success"><?php echo ucfirst($booking['status']); ?></span></p>
                        </div>
                    </div>

                    <!-- Guest Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6><i class="fas fa-user me-2"></i>Guest Information</h6>
                            <p><strong>Name:</strong> <?php echo htmlspecialchars($booking['user_name']); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($booking['user_email']); ?></p>
                            <p><strong>Guests:</strong> <?php echo $booking['guests']; ?> <?php echo $booking['guests'] == 1 ? 'Guest' : 'Guests'; ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-calendar me-2"></i>Stay Details</h6>
                            <p><strong>Check-in:</strong> <?php echo date('F j, Y', strtotime($booking['check_in_date'])); ?></p>
                            <p><strong>Check-out:</strong> <?php echo date('F j, Y', strtotime($booking['check_out_date'])); ?></p>
                            <p><strong>Duration:</strong> <?php echo $nights; ?> <?php echo $nights == 1 ? 'Night' : 'Nights'; ?></p>
                        </div>
                    </div>

                    <!-- Hotel Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6><i class="fas fa-hotel me-2"></i>Hotel Information</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong><?php echo htmlspecialchars($booking['hotel_name']); ?></strong></p>
                                    <div class="hotel-stars mb-2">
                                        <?php for ($i = 0; $i < $booking['star_rating']; $i++): ?>
                                        <i class="fas fa-star text-warning"></i>
                                        <?php endfor; ?>
                                        <?php for ($i = $booking['star_rating']; $i < 5; $i++): ?>
                                        <i class="far fa-star text-warning"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <p><?php echo htmlspecialchars($booking['hotel_address']); ?></p>
                                    <p><?php echo htmlspecialchars($booking['city']) . ", " . htmlspecialchars($booking['country']); ?></p>
                                </div>
                                <div class="col-md-6">
                                    <h6><i class="fas fa-bed me-2"></i>Room Details</h6>
                                    <p><strong>Room Type:</strong> <?php echo htmlspecialchars($booking['room_type']); ?></p>
                                    <p><strong>Bed Type:</strong> <?php echo htmlspecialchars($booking['bed_type']); ?></p>
                                    <?php if (!empty($booking['room_size'])): ?>
                                    <p><strong>Room Size:</strong> <?php echo htmlspecialchars($booking['room_size']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Special Requests -->
                    <?php if (!empty($booking['special_requests'])): ?>
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6><i class="fas fa-comment me-2"></i>Special Requests</h6>
                            <p><?php echo nl2br(htmlspecialchars($booking['special_requests'])); ?></p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Payment Summary -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6><i class="fas fa-credit-card me-2"></i>Payment Summary</h6>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Room Rate (per night):</span>
                                        <span>$<?php echo number_format($booking['total_price'] / $nights, 2); ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Number of nights:</span>
                                        <span><?php echo $nights; ?></span>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between">
                                        <strong>Total Paid:</strong>
                                        <strong class="text-success">$<?php echo number_format($booking['total_price'], 2); ?></strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Important Information -->
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-2"></i>Important Information</h6>
                        <ul class="mb-0">
                            <li>Check-in time: 3:00 PM</li>
                            <li>Check-out time: 11:00 AM</li>
                            <li>Please bring a valid ID for check-in</li>
                            <li>A confirmation email has been sent to your registered email address</li>
                        </ul>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row mt-4">
                        <div class="col-12 text-center">
                            <a href="my-bookings.php" class="btn btn-primary me-2">
                                <i class="fas fa-list me-2"></i>View All Bookings
                            </a>
                            <a href="index.php" class="btn btn-outline-secondary">
                                <i class="fas fa-home me-2"></i>Back to Home
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hotel-stars {
    font-size: 14px;
}

.alert-success i {
    color: #28a745;
}

.card-header {
    border-bottom: 1px solid rgba(255,255,255,0.2);
}

.badge {
    font-size: 0.9em;
}
</style>

<?php include "footer.php"; ?> 