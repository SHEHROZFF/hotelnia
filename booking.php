<?php
// Disable error display
error_reporting(0);
ini_set('display_errors', 0);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Clear any previous output
ob_clean();

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    header('Content-Type: application/json');
    
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['error' => 'Please login to continue']);
        exit;
    }

    try {
        require_once 'vendor/autoload.php';
        require_once 'dbcon/Database.php';

        // Initialize Stripe
        \Stripe\Stripe::setApiKey('sk_test_51OXlAIAZK57wNYnQQluuPOe6YHwpKCs2dZfKLaEe7Ye67OObYR3Hes3i0Vjo1yp450mlVWQ9ufvWWYYymF1mc33R00GwSCgwFi');
        
        // Get booking details from session or URL parameters
        $productId = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
        $hotelId = isset($_GET['hotel_id']) ? (int)$_GET['hotel_id'] : 0;
        $checkIn = isset($_GET['check_in_date']) ? $_GET['check_in_date'] : '';
        $checkOut = isset($_GET['check_out_date']) ? $_GET['check_out_date'] : '';
        $guests = isset($_GET['guests']) ? (int)$_GET['guests'] : 0;
        
        // Validate required data
        if (!$productId || !$hotelId || !$checkIn || !$checkOut || !$guests) {
            throw new Exception('Missing required booking information');
        }

        $db = new Database();
        $conn = $db->getConnection();
        
        // Get room details and calculate total price
        $stmt = $conn->prepare("SELECT price_per_night FROM rooms WHERE room_id = ? AND hotel_id = ?");
        $stmt->execute([$productId, $hotelId]);
        $room = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$room) {
            throw new Exception('Room not found');
        }
        
        // Calculate total price
        $checkInDate = new DateTime($checkIn);
        $checkOutDate = new DateTime($checkOut);
        $nights = $checkInDate->diff($checkOutDate)->days;
        $totalPrice = $room['price_per_night'] * $nights;

        $conn->beginTransaction();

        // Create Stripe Payment Intent
        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount' => (int)($totalPrice * 100), // Convert to cents
            'currency' => 'usd',
            'metadata' => [
                'user_id' => $_SESSION['user_id'],
                'hotel_id' => $hotelId,
                'room_id' => $productId,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'guests' => $guests
            ]
        ]);

        // Insert booking with pending status
        $stmt = $conn->prepare("INSERT INTO bookings (user_id, product_id, check_in_date, check_out_date, 
                                                     guests, total_price, status, special_requests, created_at, updated_at,
                                                     payment_intent_id) 
                               VALUES (?, ?, ?, ?, ?, ?, 'pending', ?, NOW(), NOW(), ?)");
        
        $specialRequests = isset($_POST['special_requests']) ? trim($_POST['special_requests']) : '';
        
        $stmt->execute([
            $_SESSION['user_id'],
            $productId,
            $checkIn,
            $checkOut,
            $guests,
            $totalPrice,
            $specialRequests,
            $paymentIntent->id
        ]);

        $bookingId = $conn->lastInsertId();
        $conn->commit();

        echo json_encode([
            'clientSecret' => $paymentIntent->client_secret,
            'bookingId' => $bookingId
        ]);
        exit;

    } catch (Exception $e) {
        if (isset($conn) && $conn->inTransaction()) {
            $conn->rollBack();
        }
        http_response_code(400);
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    }
}

// Regular page load starts here
require_once 'vendor/autoload.php';

// Add Stripe.js before including header
echo '<!DOCTYPE html>
<html>
<head>
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body>';

include "header.php";

// Initialize Stripe with your secret key
\Stripe\Stripe::setApiKey('sk_test_51OXlAIAZK57wNYnQQluuPOe6YHwpKCs2dZfKLaEe7Ye67OObYR3Hes3i0Vjo1yp450mlVWQ9ufvWWYYymF1mc33R00GwSCgwFi');

// Function to redirect with error message
function redirectWithError($url, $message) {
    $_SESSION['error'] = $message;
    if (!headers_sent()) {
        header("Location: " . $url);
        exit;
    } else {
        echo "<script>window.location.href = '" . $url . "';</script>";
        exit;
    }
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    redirectWithError('login.php', 'Please login to make a booking.');
}

// Validate required parameters
$required = ['product_id', 'hotel_id', 'check_in_date', 'check_out_date', 'guests'];
foreach ($required as $param) {
    if (!isset($_GET[$param]) || empty($_GET[$param])) {
        redirectWithError('index.php', 'Missing required booking information.');
    }
}

// Get and sanitize parameters
$productId = (int)$_GET['product_id'];
$hotelId = (int)$_GET['hotel_id'];
$checkIn = $_GET['check_in_date'];
$checkOut = $_GET['check_out_date'];
$guests = (int)$_GET['guests'];

// Validate dates
$today = date('Y-m-d');
$checkInDate = new DateTime($checkIn);
$checkOutDate = new DateTime($checkOut);
$todayDate = new DateTime($today);

if ($checkInDate < $todayDate) {
    redirectWithError("hotel-details.php?id=$hotelId", 'Check-in date cannot be in the past.');
}

if ($checkOutDate <= $checkInDate) {
    redirectWithError("hotel-details.php?id=$hotelId", 'Check-out date must be after check-in date.');
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Get room details
    $stmt = $conn->prepare("SELECT r.*, h.hotel_name, h.hotel_address as address, h.city, h.country 
                           FROM rooms r 
                           JOIN hotels h ON r.hotel_id = h.hotel_id 
                           WHERE r.room_id = ? AND r.hotel_id = ?");
    $stmt->execute([$productId, $hotelId]);
    $room = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$room) {
        throw new Exception("Room not found.");
    }

    // Validate guest count
    if ($guests > $room['capacity']) {
        redirectWithError("hotel-details.php?id=$hotelId", 
            "Selected guest count exceeds room capacity of {$room['capacity']} guests.");
    }

    // Check room availability
    $stmt = $conn->prepare("SELECT COUNT(*) as booked 
                           FROM bookings 
                           WHERE product_id = ? 
                           AND status != 'cancelled'
                           AND (
                               (check_in_date BETWEEN ? AND ?) 
                               OR (check_out_date BETWEEN ? AND ?)
                               OR (check_in_date <= ? AND check_out_date >= ?)
                           )");
    $stmt->execute([$productId, $checkIn, $checkOut, $checkIn, $checkOut, $checkIn, $checkOut]);
    $availability = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($availability['booked'] > 0) {
        redirectWithError("hotel-details.php?id=$hotelId", 
            'Room is not available for the selected dates.');
    }

    // Calculate total price
    $nights = $checkInDate->diff($checkOutDate)->days;
    $totalPrice = $room['price_per_night'] * $nights;

} catch (Exception $e) {
    redirectWithError("hotel-details.php?id=$hotelId", 
        'An error occurred: ' . $e->getMessage());
}
?>

<div class="container my-5">
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="card-title">Booking Details</h2>
                    <hr>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Hotel Information</h5>
                            <p><strong><?php echo htmlspecialchars($room['hotel_name']); ?></strong></p>
                            <p><?php echo htmlspecialchars($room['address']); ?></p>
                            <p><?php echo htmlspecialchars($room['city']) . ", " . htmlspecialchars($room['country']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <h5>Room Information</h5>
                            <p><strong>Room Type:</strong> <?php echo htmlspecialchars($room['room_type']); ?></p>
                            <p><strong>Capacity:</strong> <?php echo $room['capacity']; ?> guests</p>
                            <p><strong>Price per night:</strong> $<?php echo number_format($room['price_per_night'], 2); ?></p>
                        </div>
                    </div>

                    <form id="payment-form" method="POST">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5>Check-in/Check-out</h5>
                                <p><strong>Check-in:</strong> <?php echo date('F j, Y', strtotime($checkIn)); ?></p>
                                <p><strong>Check-out:</strong> <?php echo date('F j, Y', strtotime($checkOut)); ?></p>
                                <p><strong>Number of nights:</strong> <?php echo $nights; ?></p>
                            </div>
                            <div class="col-md-6">
                                <h5>Guest Information</h5>
                                <p><strong>Number of guests:</strong> <?php echo $guests; ?></p>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h5>Special Requests</h5>
                            <textarea class="form-control" name="special_requests" rows="3" placeholder="Enter any special requests or requirements..."></textarea>
                            <small class="text-muted">Optional: Let us know if you have any special requests.</small>
                        </div>

                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <h5>Price Summary</h5>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Room Rate (per night):</span>
                                    <span>$<?php echo number_format($room['price_per_night'], 2); ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Number of nights:</span>
                                    <span><?php echo $nights; ?></span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <strong>Total Price:</strong>
                                    <strong>$<?php echo number_format($totalPrice, 2); ?></strong>
                                </div>
                            </div>
                        </div>

                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <h5>Payment Details</h5>
                                <div id="card-element" class="mb-3">
                                    <!-- Stripe Elements will insert the card input form here -->
                                </div>
                                <div id="card-errors" class="alert alert-danger d-none" role="alert"></div>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" id="payment-button" class="btn btn-lg payment-button">
                                <span id="button-text">Pay $<?php echo number_format($totalPrice, 2); ?> and Confirm Booking</span>
                                <span id="spinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            </button>
                            <a href="hotel-details.php?id=<?php echo $hotelId; ?>" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Booking Policies</h5>
                    <hr>
                    <div class="mb-3">
                        <h6>Check-in/Check-out</h6>
                        <p>Check-in time: 3:00 PM<br>Check-out time: 11:00 AM</p>
                    </div>
                    <div class="mb-3">
                        <h6>Cancellation Policy</h6>
                        <p>Free cancellation up to 24 hours before check-in. After that, the first night will be charged.</p>
                    </div>
                    <div class="mb-3">
                        <h6>Payment</h6>
                        <p>Full payment will be processed upon confirmation of the booking.</p>
                    </div>
                    <div>
                        <h6>Additional Information</h6>
                        <p>Valid ID required at check-in.<br>No smoking in rooms.<br>Pets not allowed.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Stripe
    const stripe = Stripe('pk_test_51OXlAIAZK57wNYnQJNfcmMNa4p9xI681KyECP5FC3n2GZ9bMcUo0dB7gVOwNeIIYkAuQbnI5pPGuOJNZxyMbySZd00naBObXrO');
    const elements = stripe.elements();

    // Create card Element
    const cardElement = elements.create('card', {
        style: {
            base: {
                fontSize: '16px',
                color: '#424770',
                '::placeholder': {
                    color: '#aab7c4',
                },
            },
            invalid: {
                color: '#9e2146',
            },
        },
    });

    // Mount the card element
    cardElement.mount('#card-element');

    // Get form and button elements
    const form = document.getElementById('payment-form');
    const submitButton = document.querySelector('button.payment-button');
    const buttonText = document.getElementById('button-text');
    const spinner = document.getElementById('spinner');
    const cardErrors = document.getElementById('card-errors');

    // Add our payment form submission handler
    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        event.stopPropagation();
        
        const submitBtn = document.querySelector('button.payment-button');
        const btnText = document.getElementById('button-text');
        const spinnerElement = document.getElementById('spinner');
        
        // Disable form submission while processing
        submitBtn.disabled = true;
        btnText.classList.add('d-none');
        spinnerElement.classList.remove('d-none');
        cardErrors.classList.add('d-none');
        
        try {
            // Get current URL with parameters
            const currentUrl = window.location.href;
            
            // Create the booking and get the payment intent client secret
            const formData = new FormData(form);
            
            const response = await fetch(currentUrl, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });
            
            const responseText = await response.text();
            let data;
            
            try {
                data = JSON.parse(responseText);
            } catch (e) {
                console.error('Server response:', responseText);
                throw new Error('Invalid server response');
            }
            
            if (data.error) {
                throw new Error(data.error);
            }
            
            // Confirm the card payment
            const { error, paymentIntent } = await stripe.confirmCardPayment(data.clientSecret, {
                payment_method: {
                    card: cardElement,
                    billing_details: {
                        name: 'Customer', // You can get this from user session if available
                    }
                }
            });
            
            if (error) {
                throw new Error(error.message);
            }
            
            // Payment successful - redirect to booking confirmation
            if (paymentIntent.status === 'succeeded') {
                // Update booking status to confirmed
                const updateResponse = await fetch('update-booking-status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        booking_id: data.bookingId,
                        payment_intent_id: paymentIntent.id,
                        status: 'confirmed'
                    })
                });
                
                if (!updateResponse.ok) {
                    throw new Error('Failed to update booking status');
                }
                
                // Redirect to success page
                window.location.href = `booking-confirmation.php?booking_id=${data.bookingId}`;
            }
            
        } catch (error) {
            // Show error and re-enable form
            cardErrors.textContent = error.message;
            cardErrors.classList.remove('d-none');
            submitBtn.disabled = false;
            btnText.classList.remove('d-none');
            spinnerElement.classList.add('d-none');
            console.error('Payment error:', error);
        }
    });

    // Handle card Element errors
    cardElement.addEventListener('change', (event) => {
        if (event.error) {
            cardErrors.textContent = event.error.message;
            cardErrors.classList.remove('d-none');
        } else {
            cardErrors.classList.add('d-none');
        }
    });
});
</script>

<style>
#card-element {
    padding: 10px 12px;
    border: 1px solid #ced4da;
    border-radius: 4px;
    background-color: white;
}

#card-element.StripeElement--focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

#card-element.StripeElement--invalid {
    border-color: #dc3545;
}

/* Add custom styles for payment button */
.payment-button {
    background-color: #0d6efd;
    color: white;
    border: none;
}

.payment-button:hover {
    background-color: #0b5ed7;
    color: white;
}

.payment-button:disabled {
    background-color: #0d6efd;
    opacity: 0.65;
}
</style>

<?php include "footer.php"; ?>
</body>
</html> 