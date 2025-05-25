<?php
include "header.php";
require_once "classes/Hotel.php";

// Initialize Database and Hotel objects
$db = new Database();
$hotelObj = new Hotel($db);

// Get hotel ID from URL
$hotelId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Redirect if hotel ID is invalid
if ($hotelId <= 0) {
    header("Location: Hotels.php");
    exit;
}

// Get hotel details
$hotel = $hotelObj->getHotelById($hotelId);

// Redirect if hotel doesn't exist
if (!$hotel) {
    header("Location: Hotels.php");
    exit;
}

// Get hotel images
$hotelImages = $hotelObj->getHotelImages($hotelId);

// Get hotel amenities
$amenities = $hotelObj->getHotelAmenities($hotelId);

// Get hotel rooms
$rooms = $hotelObj->getHotelRooms($hotelId);

// Format primary image
$primaryImage = 'assets/images/Hotel Booking/hotel-11_cms_1739791039.jpg'; // Default image
foreach ($hotelImages as $image) {
    if ($image['is_primary'] == 1) {
        $primaryImage = $image['image_path'];
        break;
    }
}
?>

<!-- Hero Section with Hotel Image -->
<section class="hotel-detail-hero" style="background-image: url('<?php echo htmlspecialchars($primaryImage); ?>');">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center text-white">
                <h1 class="display-4 fw-bold"><?php echo htmlspecialchars($hotel['hotel_name']); ?></h1>
                <p class="lead"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($hotel['city'] . ', ' . $hotel['country']); ?></p>
                <div class="hotel-stars">
                    <?php for ($i = 0; $i < $hotel['star_rating']; $i++): ?>
                    <i class="fas fa-star"></i>
                    <?php endfor; ?>
                    <?php for ($i = $hotel['star_rating']; $i < 5; $i++): ?>
                    <i class="far fa-star"></i>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Hotel Details Section -->
<section class="hotel-details py-5">
    <div class="container">
        <div class="row">
            <!-- Hotel Information -->
            <div class="col-lg-8">
                <!-- Photo Gallery -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h4>Photo Gallery</h4>
                    </div>
                    <div class="card-body">
                        <div class="row gallery">
                            <?php foreach ($hotelImages as $image): ?>
                            <div class="col-md-4 mb-3">
                                <a href="<?php echo htmlspecialchars($image['image_path']); ?>" data-lightbox="hotel-gallery" data-title="<?php echo htmlspecialchars($hotel['hotel_name']); ?>">
                                    <img src="<?php echo htmlspecialchars($image['image_path']); ?>" class="img-fluid rounded" alt="Hotel Image">
                                </a>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Hotel Description -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h4>About This Hotel</h4>
                    </div>
                    <div class="card-body">
                        <p><?php echo nl2br(htmlspecialchars($hotel['hotel_description'])); ?></p>
                        
                        <h5 class="mt-4">Address</h5>
                        <p><?php echo htmlspecialchars($hotel['hotel_address']); ?><br>
                           <?php echo htmlspecialchars($hotel['city'] . ', ' . $hotel['country']); ?></p>
                    </div>
                </div>

                <!-- Amenities -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h4>Amenities</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach ($amenities as $amenity): ?>
                            <div class="col-md-4 mb-2">
                                <div class="amenity-item">
                                    <?php if (!empty($amenity['amenity_icon'])): ?>
                                    <i class="fas <?php echo $amenity['amenity_icon']; ?>"></i>
                                    <?php endif; ?>
                                    <?php echo htmlspecialchars($amenity['amenity_name']); ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Available Rooms -->
                <div class="card">
                    <div class="card-header">
                        <h4>Available Rooms</h4>
                    </div>
                    <div class="card-body">
                        <?php if (count($rooms) > 0): ?>
                            <?php 
                            // Get current date and tomorrow's date for availability check
                            $today = date('Y-m-d');
                            $tomorrow = date('Y-m-d', strtotime('+1 day'));
                            ?>
                            <?php foreach ($rooms as $room): ?>
                            <?php 
                            // Check real-time availability
                            $isAvailable = $hotelObj->checkRoomAvailability($room['room_id'], $today, $tomorrow);
                            ?>
                            <div class="room-item mb-4 p-3 border rounded">
                                <div class="row">
                                    <div class="col-md-4">
                                        <?php if (!empty($room['primary_image'])): ?>
                                        <img src="<?php echo htmlspecialchars($room['primary_image']); ?>" class="img-fluid rounded" alt="<?php echo htmlspecialchars($room['room_type']); ?>">
                                        <?php else: ?>
                                        <img src="assets/images/rooms/standard-room-1.jpg" class="img-fluid rounded" alt="<?php echo htmlspecialchars($room['room_type']); ?>">
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <h5><?php echo htmlspecialchars($room['room_type']); ?></h5>
                                            <span class="badge <?php echo $isAvailable ? 'bg-success' : 'bg-danger'; ?>">
                                                <?php echo $isAvailable ? 'Available' : 'Not Available'; ?>
                                            </span>
                                        </div>
                                        <p><?php echo nl2br(htmlspecialchars($room['room_description'])); ?></p>
                                        <div class="room-details d-flex flex-wrap">
                                            <div class="me-3 mb-2"><i class="fas fa-users"></i> <?php echo $room['capacity']; ?> guests</div>
                                            <div class="me-3 mb-2"><i class="fas fa-bed"></i> <?php echo htmlspecialchars($room['bed_type']); ?> bed</div>
                                            <?php if (!empty($room['room_size'])): ?>
                                            <div class="me-3 mb-2"><i class="fas fa-ruler-combined"></i> <?php echo htmlspecialchars($room['room_size']); ?></div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <div class="room-price">
                                                <span class="price-amount">$<?php echo number_format($room['price_per_night']); ?></span>
                                                <span class="price-period">per night</span>
                                            </div>
                                            <button type="button" class="btn btn-primary <?php echo !$isAvailable ? 'disabled' : ''; ?>" 
                                                    <?php echo $isAvailable ? 'onclick="bookRoom(' . $room['room_id'] . ', ' . $room['capacity'] . ')"' : 'disabled'; ?>>
                                                <?php echo $isAvailable ? 'Book Now' : 'Not Available'; ?>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <p>No rooms are currently available for this hotel. Please check back later or browse other hotels.</p>
                                <a href="Hotels.php" class="btn btn-primary mt-2">Browse Hotels</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Quick Booking Form -->
                <div class="card mb-4 booking-card sticky-top" style="top: 20px; z-index: 999;">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Book Your Stay</h4>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="booking.php">
                            <input type="hidden" name="hotel_id" value="<?php echo $hotelId; ?>">
                            
                            <div class="mb-3">
                                <label for="product_id" class="form-label">Select Room</label>
                                <select class="form-select" id="product_id" name="product_id" required onchange="updateGuestOptions()">
                                    <option value="">Choose a room...</option>
                                    <?php foreach ($rooms as $room): ?>
                                    <option value="<?php echo $room['room_id']; ?>" data-capacity="<?php echo $room['capacity']; ?>">
                                        <?php echo htmlspecialchars($room['room_type']); ?> - $<?php echo number_format($room['price_per_night']); ?>/night
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="check_in_date" class="form-label">Check-in Date</label>
                                <input type="date" class="form-control" id="check_in_date" name="check_in_date" required 
                                       min="<?php echo date('Y-m-d'); ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="check_out_date" class="form-label">Check-out Date</label>
                                <input type="date" class="form-control" id="check_out_date" name="check_out_date" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="guests" class="form-label">Number of Guests</label>
                                <select class="form-select" id="guests" name="guests" required>
                                    <option value="">Select number of guests</option>
                                </select>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">Check Availability</button>
                        </form>
                    </div>
                </div>

                <!-- Hotel Info Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h4>Hotel Information</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fas fa-map-marker-alt me-2"></i> <?php echo htmlspecialchars($hotel['city'] . ', ' . $hotel['country']); ?></li>
                            <li class="mb-2"><i class="fas fa-building me-2"></i> <?php echo htmlspecialchars($hotel['hotel_type']); ?></li>
                            <li class="mb-2">
                                <i class="fas fa-star me-2"></i> 
                                <?php echo $hotel['star_rating']; ?> Star Rating
                            </li>
                            <li class="mb-2"><i class="fas fa-money-bill-wave me-2"></i> $<?php echo number_format($hotel['min_price']); ?> - $<?php echo number_format($hotel['max_price']); ?> per night</li>
                        </ul>
                    </div>
                </div>

                <!-- Map -->
                <div class="card">
                    <div class="card-header">
                        <h4>Location</h4>
                    </div>
                    <div class="card-body p-0">
                        <div class="hotel-map">
                            <iframe 
                                width="100%" 
                                height="300" 
                                frameborder="0" 
                                style="border:0" 
                                src="https://www.google.com/maps/embed/v1/place?key=AIzaSyAB21_GeCS0_p-0KVbLw8WwFI4OCd5VY40&q=<?php echo urlencode($hotel['hotel_address'] . ', ' . $hotel['city'] . ', ' . $hotel['country']); ?>" 
                                allowfullscreen>
                            </iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Book room function for "Book Now" buttons
function bookRoom(roomId, capacity) {
    // Set the room in the booking form
    const roomSelect = document.getElementById('product_id');
    roomSelect.value = roomId;
    
    // Update guest options
    updateGuestOptions();
    
    // Scroll to booking form
    const bookingCard = document.querySelector('.booking-card');
    bookingCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
    
    // Add a highlight effect
    bookingCard.style.boxShadow = '0 0 20px rgba(0, 123, 255, 0.3)';
    setTimeout(() => {
        bookingCard.style.boxShadow = '';
    }, 2000);
}

// Update guest options based on selected room capacity
function updateGuestOptions() {
    const roomSelect = document.getElementById('product_id');
    const guestSelect = document.getElementById('guests');
    const selectedRoom = roomSelect.options[roomSelect.selectedIndex];
    
    // Clear existing options
    guestSelect.innerHTML = '<option value="">Select number of guests</option>';
    
    if (selectedRoom && selectedRoom.value) {
        const capacity = parseInt(selectedRoom.dataset.capacity);
        for (let i = 1; i <= capacity; i++) {
            const option = document.createElement('option');
            option.value = i;
            option.textContent = i + (i === 1 ? ' Guest' : ' Guests');
            guestSelect.appendChild(option);
        }
    }
}

// Set minimum dates for check-in and check-out
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date().toISOString().split('T')[0];
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    const tomorrowStr = tomorrow.toISOString().split('T')[0];
    
    const checkIn = document.getElementById('check_in_date');
    const checkOut = document.getElementById('check_out_date');
    
    // Set minimum dates
    checkIn.min = today;
    checkOut.min = tomorrowStr;
    
    // Set default values
    if (!checkIn.value) checkIn.value = today;
    if (!checkOut.value) checkOut.value = tomorrowStr;
    
    // Update check-out min date when check-in changes
    checkIn.addEventListener('change', function() {
        const nextDay = new Date(this.value);
        nextDay.setDate(nextDay.getDate() + 1);
        checkOut.min = nextDay.toISOString().split('T')[0];
        
        if (new Date(checkOut.value) <= new Date(this.value)) {
            checkOut.value = nextDay.toISOString().split('T')[0];
        }
    });
});

// Add this before the closing </body> tag
document.addEventListener('DOMContentLoaded', function() {
    const checkInDate = document.getElementById('check_in_date');
    const checkOutDate = document.getElementById('check_out_date');
    const roomSelect = document.getElementById('product_id');
    const submitButton = document.querySelector('button[type="submit"]');
    
    // Set minimum dates
    const today = new Date();
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);
    
    checkInDate.min = today.toISOString().split('T')[0];
    
    // Update check-out minimum date when check-in is selected
    checkInDate.addEventListener('change', function() {
        const selectedDate = new Date(this.value);
        const nextDay = new Date(selectedDate);
        nextDay.setDate(nextDay.getDate() + 1);
        checkOutDate.min = nextDay.toISOString().split('T')[0];
        checkOutDate.value = ''; // Reset checkout date
        checkAvailability();
    });
    
    // Check availability when checkout date changes
    checkOutDate.addEventListener('change', checkAvailability);
    roomSelect.addEventListener('change', checkAvailability);
    
    function checkAvailability() {
        const roomId = roomSelect.value;
        const checkIn = checkInDate.value;
        const checkOut = checkOutDate.value;
        
        if (!roomId || !checkIn || !checkOut) {
            return;
        }
        
        // Make AJAX call to check availability
        fetch(`ajax/check_availability.php?room_id=${roomId}&check_in=${checkIn}&check_out=${checkOut}`)
            .then(response => response.json())
            .then(data => {
                if (data.available) {
                    submitButton.removeAttribute('disabled');
                    submitButton.textContent = 'Check Availability';
                    submitButton.classList.remove('btn-danger');
                    submitButton.classList.add('btn-primary');
                } else {
                    submitButton.setAttribute('disabled', 'disabled');
                    submitButton.textContent = 'Not Available for Selected Dates';
                    submitButton.classList.remove('btn-primary');
                    submitButton.classList.add('btn-danger');
                }
            })
            .catch(error => {
                console.error('Error checking availability:', error);
                submitButton.setAttribute('disabled', 'disabled');
                submitButton.textContent = 'Error Checking Availability';
            });
    }
});
</script>

<!-- Add lightbox for gallery -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>

<style>
    .hotel-detail-hero {
        background-size: cover;
        background-position: center;
        height: 500px;
        display: flex;
        align-items: center;
        position: relative;
    }
    
    .hotel-detail-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
    }
    
    .hotel-detail-hero .container {
        position: relative;
        z-index: 1;
    }
    
    .hotel-stars {
        color: gold;
        font-size: 24px;
        margin-top: 10px;
    }
    
    .amenity-item {
        padding: 8px 0;
    }
    
    .amenity-item i {
        margin-right: 10px;
        color: #007bff;
    }
    
    .room-price {
        font-size: 18px;
    }
    
    .price-amount {
        font-weight: bold;
        color: #007bff;
    }
    
    .price-period {
        color: #6c757d;
        font-size: 14px;
    }
    
    .gallery img {
        height: 150px;
        object-fit: cover;
        width: 100%;
        transition: transform 0.3s ease;
    }
    
    .gallery img:hover {
        transform: scale(1.05);
    }
</style>

<?php include "footer.php"; ?> 