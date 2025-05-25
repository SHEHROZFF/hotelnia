<?php
ob_start(); // Start output buffering
require_once 'vendor/autoload.php';
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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Set content type for JSON response
    header('Content-Type: application/json');
    
    try {
        $conn->beginTransaction();

        // Create Stripe Payment Intent
        try {
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => $totalPrice * 100, // Convert to cents
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

            // Return the client secret for the payment intent
            echo json_encode([
                'clientSecret' => $paymentIntent->client_secret,
                'bookingId' => $bookingId
            ]);
            exit;

        } catch (\Stripe\Exception\ApiErrorException $e) {
            $conn->rollBack();
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
            exit;
        }

    } catch (Exception $e) {
        $conn->rollBack();
        http_response_code(400);
        echo json_encode(['error' => "Failed to create booking: " . $e->getMessage()]);
        exit;
    }
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

                    <form id="payment-form" method="POST" action="">
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
                            <button type="button" id="pay-button" class="btn btn-primary btn-lg">
                                <span id="pay-button-text">Pay $<?php echo number_format($totalPrice, 2); ?> and Confirm Booking</span>
                                <span id="pay-spinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
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

<!-- Add Stripe.js -->
<script src="https://js.stripe.com/v3/"></script>

<script>
// Function to initialize Stripe payment
function initializeStripePayment() {
    console.log('Initializing Stripe payment...');
    
    // Check if Stripe is loaded
    if (typeof Stripe === 'undefined') {
        console.error('Stripe is not loaded yet, retrying...');
        setTimeout(initializeStripePayment, 100);
        return;
    }
    
    try {
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
        const cardElementContainer = document.getElementById('card-element');
        if (cardElementContainer) {
            cardElement.mount('#card-element');
            console.log('Card element mounted successfully');
        } else {
            console.error('Card element container not found');
            return;
        }

        // Get form elements
        const form = document.getElementById('payment-form');
        const payButton = document.getElementById('pay-button');
        const buttonText = document.getElementById('pay-button-text');
        const spinner = document.getElementById('pay-spinner');
        const cardErrors = document.getElementById('card-errors');

        if (!form || !payButton || !buttonText || !spinner || !cardErrors) {
            console.error('Required form elements not found');
            return;
        }

        // Handle payment button click
        payButton.addEventListener('click', async function(event) {
            event.preventDefault();
            event.stopPropagation();
            
            console.log('Payment button clicked');
            
            // Disable button and show spinner
            payButton.disabled = true;
            buttonText.classList.add('d-none');
            spinner.classList.remove('d-none');
            cardErrors.classList.add('d-none');
            
            try {
                // Create the booking and get payment intent
                console.log('Creating payment intent...');
                
                const formData = new FormData(form);
                
                const response = await fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                });
                
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                
                const data = await response.json();
                console.log('Payment intent response:', data);
                
                if (data.error) {
                    throw new Error(data.error);
                }
                
                // Confirm the card payment
                console.log('Confirming card payment...');
                const { error, paymentIntent } = await stripe.confirmCardPayment(data.clientSecret, {
                    payment_method: {
                        card: cardElement,
                        billing_details: {
                            name: 'Customer',
                        }
                    }
                });
                
                if (error) {
                    console.error('Payment error:', error);
                    throw new Error(error.message);
                }
                
                console.log('Payment successful:', paymentIntent);
                
                // Payment successful
                if (paymentIntent.status === 'succeeded') {
                    // Update booking status
                    console.log('Updating booking status...');
                    await fetch('update-booking-status.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            booking_id: data.bookingId,
                            payment_intent_id: paymentIntent.id,
                            status: 'confirmed'
                        })
                    });
                    
                    // Redirect to confirmation page
                    console.log('Redirecting to confirmation page...');
                    window.location.href = `booking-confirmation.php?booking_id=${data.bookingId}`;
                }
                
            } catch (error) {
                console.error('Error:', error);
                // Show error and re-enable button
                cardErrors.textContent = error.message;
                cardErrors.classList.remove('d-none');
                payButton.disabled = false;
                buttonText.classList.remove('d-none');
                spinner.classList.add('d-none');
            }
        });

        // Handle card element errors
        cardElement.addEventListener('change', function(event) {
            if (event.error) {
                cardErrors.textContent = event.error.message;
                cardErrors.classList.remove('d-none');
            } else {
                cardErrors.classList.add('d-none');
            }
        });
        
    } catch (error) {
        console.error('Error initializing Stripe:', error);
    }
}

// Wait for DOM to be ready, then initialize Stripe
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(initializeStripePayment, 500);
    });
} else {
    setTimeout(initializeStripePayment, 500);
}
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

#pay-button {
    position: relative;
}
</style>

<?php include "footer.php"; ?>
</body>
</html> 