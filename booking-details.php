<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include header first which will handle session start
include "header.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Please login to view booking details.";
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header("Location: login.php");
    exit;
}

// Check if booking ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Invalid booking ID provided.";
    header("Location: my-bookings.php");
    exit;
}

require_once 'dbcon/Database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Get booking details with room and hotel information
    $stmt = $conn->prepare("
        SELECT 
            b.*,
            r.room_type,
            r.capacity,
            r.price_per_night,
            h.hotel_name,
            h.hotel_address,
            h.city,
            h.country,
            hi.image_path as hotel_image,
            ri.image_path as room_image
        FROM bookings b
        JOIN rooms r ON b.product_id = r.room_id
        JOIN hotels h ON r.hotel_id = h.hotel_id
        LEFT JOIN hotel_images hi ON h.hotel_id = hi.hotel_id AND hi.is_primary = 1
        LEFT JOIN room_images ri ON r.room_id = ri.room_id AND ri.is_primary = 1
        WHERE b.booking_id = ? AND b.user_id = ?
    ");
    
    $bookingId = (int)$_GET['id'];
    $userId = (int)$_SESSION['user_id'];
    
    echo "<!-- Debug: Executing query for booking_id: $bookingId, user_id: $userId -->\n";
    
    $stmt->execute([$bookingId, $userId]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$booking) {
        $_SESSION['error'] = "Booking not found or you don't have permission to view it.";
        header("Location: my-bookings.php");
        exit;
    }

    // Calculate number of nights
    $checkIn = new DateTime($booking['check_in_date']);
    $checkOut = new DateTime($booking['check_out_date']);
    $nights = $checkIn->diff($checkOut)->days;

} catch (Exception $e) {
    error_log("Booking details error: " . $e->getMessage());
    $_SESSION['error'] = "An error occurred while fetching booking details: " . $e->getMessage();
    header("Location: my-bookings.php");
    exit;
}

// Display any error messages
if (isset($_SESSION['error'])) {
    echo '<div class="container mt-3"><div class="alert alert-danger">' . htmlspecialchars($_SESSION['error']) . '</div></div>';
    unset($_SESSION['error']);
}

// Display any success messages
if (isset($_SESSION['success'])) {
    echo '<div class="container mt-3"><div class="alert alert-success">' . htmlspecialchars($_SESSION['success']) . '</div></div>';
    unset($_SESSION['success']);
}
?>

<div class="container my-5">
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="card-title mb-0">Booking Details</h2>
                        <span class="badge <?php 
                            echo match($booking['status']) {
                                'confirmed' => 'bg-success',
                                'pending' => 'bg-warning',
                                'cancelled' => 'bg-danger',
                                default => 'bg-secondary'
                            };
                        ?>"><?php echo ucfirst($booking['status']); ?></span>
                    </div>
                    <hr>

                    <!-- Hotel Image -->
                    <?php if (!empty($booking['hotel_image']) || !empty($booking['room_image'])): ?>
                    <div class="text-center mb-4">
                        <img src="<?php echo htmlspecialchars($booking['hotel_image'] ?? $booking['room_image'] ?? 'assets/images/placeholder.jpg'); ?>" 
                             class="img-fluid rounded" 
                             alt="<?php echo htmlspecialchars($booking['hotel_name']); ?>"
                             style="max-height: 300px; width: auto;">
                    </div>
                    <?php endif; ?>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Hotel Information</h5>
                            <p><strong><?php echo htmlspecialchars($booking['hotel_name']); ?></strong></p>
                            <p><?php echo htmlspecialchars($booking['hotel_address']); ?></p>
                            <p><?php echo htmlspecialchars($booking['city']) . ", " . htmlspecialchars($booking['country']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <h5>Room Information</h5>
                            <p><strong>Room Type:</strong> <?php echo htmlspecialchars($booking['room_type']); ?></p>
                            <p><strong>Capacity:</strong> <?php echo $booking['capacity']; ?> guests</p>
                            <p><strong>Price per night:</strong> $<?php echo number_format($booking['price_per_night'], 2); ?></p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Stay Details</h5>
                            <p><strong>Check-in:</strong> <?php echo date('F j, Y', strtotime($booking['check_in_date'])); ?></p>
                            <p><strong>Check-out:</strong> <?php echo date('F j, Y', strtotime($booking['check_out_date'])); ?></p>
                            <p><strong>Number of nights:</strong> <?php echo $nights; ?></p>
                        </div>
                        <div class="col-md-6">
                            <h5>Guest Information</h5>
                            <p><strong>Number of guests:</strong> <?php echo $booking['guests']; ?></p>
                            <?php if (!empty($booking['special_requests'])): ?>
                                <h5 class="mt-3">Special Requests</h5>
                                <p><?php echo htmlspecialchars($booking['special_requests']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h5>Price Summary</h5>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Room Rate (per night):</span>
                                <span>$<?php echo number_format($booking['price_per_night'], 2); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Number of nights:</span>
                                <span><?php echo $nights; ?></span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <strong>Total Price:</strong>
                                <strong>$<?php echo number_format($booking['total_price'], 2); ?></strong>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <?php if ($booking['status'] !== 'cancelled' && strtotime($booking['check_in_date']) > time()): ?>
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">
                                Cancel Booking
                            </button>
                        <?php endif; ?>
                        <a href="my-bookings.php" class="btn btn-outline-secondary">Back to My Bookings</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Booking Information</h5>
                    <hr>
                    <div class="mb-3">
                        <h6>Booking ID</h6>
                        <p>#<?php echo str_pad($booking['booking_id'], 8, '0', STR_PAD_LEFT); ?></p>
                    </div>
                    <div class="mb-3">
                        <h6>Booking Date</h6>
                        <p><?php echo date('F j, Y', strtotime($booking['created_at'])); ?></p>
                    </div>
                    <div class="mb-3">
                        <h6>Last Updated</h6>
                        <p><?php echo date('F j, Y', strtotime($booking['updated_at'])); ?></p>
                    </div>
                    <div class="mb-3">
                        <h6>Payment Status</h6>
                        <p><?php echo ucfirst($booking['status']); ?></p>
                    </div>
                    <?php if (!empty($booking['payment_intent_id'])): ?>
                        <div>
                            <h6>Payment Reference</h6>
                            <p class="text-muted"><?php echo $booking['payment_intent_id']; ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Booking Modal -->
<?php if ($booking['status'] !== 'cancelled' && strtotime($booking['check_in_date']) > time()): ?>
<div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelModalLabel">Cancel Booking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to cancel this booking?</p>
                <p class="text-danger">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <form action="cancel-booking.php" method="POST" style="display: inline;">
                    <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                    <button type="submit" class="btn btn-danger">Cancel Booking</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<style>
.badge {
    font-size: 1rem;
    padding: 0.5rem 1rem;
}
</style>

<?php include "footer.php"; ?> 