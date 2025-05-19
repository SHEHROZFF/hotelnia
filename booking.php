<?php
include "header.php";
require_once "classes/Hotel.php";

// Initialize Database and Hotel objects
$db = new Database();
$hotelObj = new Hotel($db);

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);

// Get room ID from URL
$roomId = isset($_GET['room_id']) ? (int)$_GET['room_id'] : 0;
$hotelId = isset($_GET['hotel_id']) ? (int)$_GET['hotel_id'] : 0;
$checkIn = isset($_GET['check_in']) ? $_GET['check_in'] : '';
$checkOut = isset($_GET['check_out']) ? $_GET['check_out'] : '';
$guests = isset($_GET['guests']) ? (int)$_GET['guests'] : 1;

// If no room ID but hotel ID is provided, redirect to hotel details
if ($roomId <= 0 && $hotelId > 0) {
    header("Location: hotel-details.php?id=" . $hotelId);
    exit;
}

// If no room ID and no hotel ID, redirect to Hotels
if ($roomId <= 0 && $hotelId <= 0) {
    header("Location: Hotels.php");
    exit;
}

// Get room details
$room = null;
foreach ($hotelObj->getHotelRooms($hotelId) as $r) {
    if ($r['room_id'] == $roomId) {
        $room = $r;
        break;
    }
}

// If room doesn't exist, redirect
if (!$room) {
    header("Location: Hotels.php");
    exit;
}

// Get hotel details
$hotel = $hotelObj->getHotelById($room['hotel_id']);

// If hotel doesn't exist, redirect
if (!$hotel) {
    header("Location: Hotels.php");
    exit;
}

// Set default dates if not provided
if (empty($checkIn)) {
    $checkIn = date('Y-m-d');
}
if (empty($checkOut)) {
    $checkOut = date('Y-m-d', strtotime('+1 day', strtotime($checkIn)));
}

// Calculate nights and total price
$nights = max(1, ceil((strtotime($checkOut) - strtotime($checkIn)) / (60 * 60 * 24)));
$totalPrice = $room['price_per_night'] * $nights;

// Handle form submission
$bookingComplete = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if user is logged in
    if (!$isLoggedIn) {
        $errors[] = "You must be logged in to book a room. Please <a href='Login.php'>login</a> or <a href='register.php'>register</a> first.";
    } else {
        // Get form data
        $firstName = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
        $lastName = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
        $specialRequests = isset($_POST['special_requests']) ? trim($_POST['special_requests']) : '';
        
        // Validate form data
        if (empty($firstName)) {
            $errors[] = "First name is required.";
        }
        if (empty($lastName)) {
            $errors[] = "Last name is required.";
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Valid email is required.";
        }
        if (empty($phone)) {
            $errors[] = "Phone number is required.";
        }
        
        // If no errors, process booking
        if (empty($errors)) {
            $bookingData = [
                'user_id' => $_SESSION['user_id'],
                'product_id' => $roomId,
                'check_in_date' => $checkIn,
                'check_out_date' => $checkOut,
                'guests' => $guests,
                'total_price' => $totalPrice,
                'status' => 'pending'
            ];
            
            $pdo = $db->getConnection();
            $sql = "INSERT INTO bookings (user_id, product_id, check_in_date, check_out_date, guests, total_price, status, created_at, updated_at) 
                    VALUES (:user_id, :product_id, :check_in_date, :check_out_date, :guests, :total_price, :status, NOW(), NOW())";
            
            $stmt = $pdo->prepare($sql);
            
            if ($stmt->execute($bookingData)) {
                $bookingComplete = true;
                $bookingId = $pdo->lastInsertId();
                
                // Send confirmation email (in a real project)
                // sendBookingConfirmationEmail($email, $bookingId, $hotel, $room, $checkIn, $checkOut, $nights, $totalPrice);
            } else {
                $errors[] = "An error occurred while processing your booking. Please try again.";
            }
        }
    }
}
?>

<!-- Booking Section -->
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">Book Your Stay</h1>
            
            <?php if ($bookingComplete): ?>
            <div class="alert alert-success">
                <h4>Booking Successful!</h4>
                <p>Thank you for booking with us. Your reservation has been confirmed.</p>
                <p>You will receive a confirmation email shortly with all the details of your booking.</p>
                <div class="mt-4">
                    <a href="index.php" class="btn btn-primary">Return to Homepage</a>
                    <a href="Hotels.php" class="btn btn-outline-primary ms-2">Browse More Hotels</a>
                </div>
            </div>
            <?php else: ?>
            
            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <h4>Please fix the following errors:</h4>
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            
            <div class="row">
                <!-- Booking Form -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h4>Booking Details</h4>
                        </div>
                        <div class="card-body">
                            <form action="" method="POST">
                                <input type="hidden" name="room_id" value="<?php echo $roomId; ?>">
                                <input type="hidden" name="check_in" value="<?php echo $checkIn; ?>">
                                <input type="hidden" name="check_out" value="<?php echo $checkOut; ?>">
                                <input type="hidden" name="guests" value="<?php echo $guests; ?>">
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="first_name" class="form-label">First Name</label>
                                        <input type="text" class="form-control" id="first_name" name="first_name" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="last_name" class="form-label">Last Name</label>
                                        <input type="text" class="form-control" id="last_name" name="last_name" required>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email Address</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control" id="phone" name="phone" required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="special_requests" class="form-label">Special Requests (optional)</label>
                                    <textarea class="form-control" id="special_requests" name="special_requests" rows="3"></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <h5>Payment Information</h5>
                                    <div class="alert alert-info">
                                        <p class="mb-0">This is a demo site. No actual payment will be processed.</p>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-6 mb-3">
                                            <label for="card_number" class="form-label">Card Number</label>
                                            <input type="text" class="form-control" id="card_number" placeholder="**** **** **** ****" disabled>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="card_name" class="form-label">Name on Card</label>
                                            <input type="text" class="form-control" id="card_name" disabled>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label for="card_expiry" class="form-label">Expiry Date</label>
                                            <input type="text" class="form-control" id="card_expiry" placeholder="MM/YY" disabled>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="card_cvv" class="form-label">CVV</label>
                                            <input type="text" class="form-control" id="card_cvv" placeholder="***" disabled>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                                    <label class="form-check-label" for="terms">
                                        I agree to the <a href="#">Terms and Conditions</a> and <a href="#">Privacy Policy</a>
                                    </label>
                                </div>
                                
                                <?php if ($isLoggedIn): ?>
                                <button type="submit" class="btn btn-primary btn-lg w-100">Complete Booking</button>
                                <?php else: ?>
                                <div class="alert alert-warning">
                                    <p>You must be logged in to complete your booking.</p>
                                    <a href="Login.php?redirect=booking.php?room_id=<?php echo $roomId; ?>&check_in=<?php echo $checkIn; ?>&check_out=<?php echo $checkOut; ?>&guests=<?php echo $guests; ?>" class="btn btn-primary">Login</a>
                                    <a href="register.php" class="btn btn-outline-primary ms-2">Register</a>
                                </div>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Booking Summary -->
                <div class="col-lg-4">
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">Booking Summary</h4>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="row">
                                    <div class="col-5">
                                        <?php if (!empty($room['primary_image'])): ?>
                                        <img src="<?php echo htmlspecialchars($room['primary_image']); ?>" class="img-fluid rounded" alt="<?php echo htmlspecialchars($room['room_type']); ?>">
                                        <?php else: ?>
                                        <img src="assets/images/rooms/standard-room-1.jpg" class="img-fluid rounded" alt="<?php echo htmlspecialchars($room['room_type']); ?>">
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-7">
                                        <h5><?php echo htmlspecialchars($hotel['hotel_name']); ?></h5>
                                        <p class="mb-0"><?php echo htmlspecialchars($room['room_type']); ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="booking-details mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span><i class="fas fa-calendar-check me-2"></i> Check-in:</span>
                                    <span><?php echo date('D, M j, Y', strtotime($checkIn)); ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span><i class="fas fa-calendar-times me-2"></i> Check-out:</span>
                                    <span><?php echo date('D, M j, Y', strtotime($checkOut)); ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span><i class="fas fa-moon me-2"></i> Nights:</span>
                                    <span><?php echo $nights; ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span><i class="fas fa-users me-2"></i> Guests:</span>
                                    <span><?php echo $guests; ?></span>
                                </div>
                            </div>
                            
                            <div class="price-details mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Room Rate:</span>
                                    <span>$<?php echo number_format($room['price_per_night'], 2); ?> × <?php echo $nights; ?> nights</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Taxes & Fees:</span>
                                    <span>Included</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between fw-bold">
                                    <span>Total:</span>
                                    <span>$<?php echo number_format($totalPrice, 2); ?></span>
                                </div>
                            </div>
                            
                            <div class="cancellation-policy mt-3 pt-3 border-top">
                                <h6><i class="fas fa-info-circle me-2"></i> Cancellation Policy</h6>
                                <p class="small mb-0">Free cancellation before <?php echo date('M j, Y', strtotime('-1 day', strtotime($checkIn))); ?>. After this date, you will be charged the full amount.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Need Assistance?</h5>
                        </div>
                        <div class="card-body">
                            <p><i class="fas fa-phone me-2"></i> Call us at: <strong>+1-800-123-4567</strong></p>
                            <p class="mb-0"><i class="fas fa-envelope me-2"></i> Email: <strong>support@hotelina.com</strong></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    .booking-details span {
        font-size: 16px;
    }
    
    .price-details {
        font-size: 16px;
    }
    
    .price-details .fw-bold {
        font-size: 18px;
    }
</style>

<?php include "footer.php"; ?> 