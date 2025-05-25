<?php
include "header.php";
require_once "classes/Hotel.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header("Location: login.php");
    exit;
}

// Initialize Database
$db = new Database();
$hotelObj = new Hotel($db);

// Get user's bookings
$userId = $_SESSION['user_id'];
$pdo = $db->getConnection();

$sql = "SELECT 
            b.*, 
            r.room_type,
            r.price_per_night,
            h.hotel_name,
            h.hotel_address,
            h.city,
            h.country,
            (SELECT image_path FROM hotel_images WHERE hotel_id = h.hotel_id AND is_primary = 1 LIMIT 1) as hotel_image,
            (SELECT image_path FROM room_images WHERE room_id = r.room_id AND is_primary = 1 LIMIT 1) as room_image
        FROM bookings b
        JOIN rooms r ON b.product_id = r.room_id
        JOIN hotels h ON r.hotel_id = h.hotel_id
        WHERE b.user_id = :user_id
        ORDER BY b.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $userId]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Status badge colors
$statusColors = [
    'pending' => 'warning',
    'confirmed' => 'success',
    'cancelled' => 'danger',
    'completed' => 'info',
    'checked_in' => 'primary',
    'checked_out' => 'secondary'
];

// Add debug output
if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['error']) . '</div>';
    unset($_SESSION['error']);
}
?>

<!-- My Bookings Section -->
<div class="container py-5">
    <h1 class="mb-4">My Bookings</h1>

    <?php if (empty($bookings)): ?>
    <div class="alert alert-info">
        <p class="mb-0">You don't have any bookings yet. <a href="hotels.php">Browse our hotels</a> to make your first booking!</p>
    </div>
    <?php else: ?>
    
    <div class="row">
        <?php foreach ($bookings as $booking): ?>
        <div class="col-12 mb-4">
            <div class="card booking-card">
                <div class="card-body">
                    <div class="row">
                        <!-- Hotel/Room Image -->
                        <div class="col-md-3">
                            <img src="<?php echo htmlspecialchars($booking['hotel_image'] ?? $booking['room_image'] ?? 'assets/images/placeholder.jpg'); ?>" 
                                 class="img-fluid rounded" 
                                 alt="<?php echo htmlspecialchars($booking['hotel_name']); ?>">
                        </div>
                        
                        <!-- Booking Details -->
                        <div class="col-md-6">
                            <h4><?php echo htmlspecialchars($booking['hotel_name']); ?></h4>
                            <p class="text-muted">
                                <?php echo htmlspecialchars($booking['room_type']); ?><br>
                                <?php echo htmlspecialchars($booking['hotel_address'] . ', ' . $booking['city'] . ', ' . $booking['country']); ?>
                            </p>
                            
                            <div class="booking-details">
                                <p>
                                    <strong>Check-in:</strong> <?php echo date('M d, Y', strtotime($booking['check_in_date'])); ?><br>
                                    <strong>Check-out:</strong> <?php echo date('M d, Y', strtotime($booking['check_out_date'])); ?><br>
                                    <strong>Guests:</strong> <?php echo htmlspecialchars($booking['guests']); ?><br>
                                    <strong>Total Price:</strong> $<?php echo number_format($booking['total_price'], 2); ?><br>
                                    <strong>Booking ID:</strong> #<?php echo str_pad($booking['booking_id'], 6, '0', STR_PAD_LEFT); ?><br>
                                    <strong>Status:</strong> 
                                    <span class="badge bg-<?php echo $statusColors[$booking['status']] ?? 'secondary'; ?>">
                                        <?php echo ucfirst(htmlspecialchars($booking['status'])); ?>
                                    </span>
                                </p>
                            </div>
                        </div>
                        
                        <!-- Actions -->
                        <div class="col-md-3">
                            <div class="d-grid gap-2">
                                <?php 
                                    $bookingId = (int)$booking['booking_id'];
                                    $detailsUrl = "booking-details.php?id=" . $bookingId;
                                ?>
                                <a href="<?php echo htmlspecialchars($detailsUrl); ?>" 
                                   class="btn btn-primary"
                                   onclick="console.log('Clicking view details for booking <?php echo $bookingId; ?>');">
                                    View Details
                                </a>
                                   
                                <?php if ($booking['status'] === 'pending' || $booking['status'] === 'confirmed'): ?>
                                    <?php if (strtotime($booking['check_in_date']) > time()): ?>
                                        <button type="button" 
                                                class="btn btn-outline-danger"
                                                onclick="cancelBooking(<?php echo $bookingId; ?>)">
                                            Cancel Booking
                                        </button>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Cancel Booking Modal -->
<div class="modal fade" id="cancelBookingModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancel Booking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to cancel this booking? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" id="confirmCancelBtn">Yes, Cancel Booking</button>
            </div>
        </div>
    </div>
</div>

<script>
let bookingToCancel = null;
const cancelModal = new bootstrap.Modal(document.getElementById('cancelBookingModal'));

function cancelBooking(bookingId) {
    bookingToCancel = bookingId;
    cancelModal.show();
}

document.getElementById('confirmCancelBtn').addEventListener('click', function() {
    if (!bookingToCancel) return;
    
    fetch('ajax/cancel_booking.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            booking_id: bookingToCancel
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Error cancelling booking');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error cancelling booking');
    })
    .finally(() => {
        cancelModal.hide();
        bookingToCancel = null;
    });
});

function validateBookingLink(link, bookingId) {
    if (!bookingId) {
        alert('Invalid booking ID');
        return false;
    }
    return true;
}
</script>

<style>
.booking-card {
    transition: transform 0.2s;
    border: 1px solid #dee2e6;
}

.booking-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.booking-details {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    margin-top: 10px;
}

.badge {
    font-size: 0.9em;
    padding: 0.5em 0.8em;
}
</style>

<?php include "footer.php"; ?> 