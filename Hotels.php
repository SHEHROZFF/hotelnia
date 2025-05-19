<?php include "header.php" ?>

<?php
// Initialize Database and Hotel objects
require_once "classes/Hotel.php";
$db = new Database();
$hotelObj = new Hotel($db);

// Get filter parameters from GET
$destination = isset($_GET['destination']) ? trim($_GET['destination']) : '';
$checkInDate = isset($_GET['check_in_date']) ? $_GET['check_in_date'] : '';
$hotelType = isset($_GET['hotel_type']) ? $_GET['hotel_type'] : '';
$minPrice = isset($_GET['min_price']) ? (int)$_GET['min_price'] : 0;
$maxPrice = isset($_GET['max_price']) ? (int)$_GET['max_price'] : 2000;

// Fix amenities array processing
$amenities = [];
if (isset($_GET['amenities'])) {
    // Handle both array and single value
    if (is_array($_GET['amenities'])) {
        $amenities = $_GET['amenities'];
    } else {
        $amenities = [$_GET['amenities']];
    }
}

$sort = isset($_GET['sort']) ? $_GET['sort'] : 'featured';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10; // Items per page

// Get hotel types for filter
$hotelTypes = $hotelObj->getAllHotelTypes();

// Get all amenities for filter
$allAmenities = $hotelObj->getAllAmenities();

// Build search criteria
$searchCriteria = [
    'destination' => $destination,
    'hotel_type' => $hotelType,
    'min_price' => $minPrice,
    'max_price' => $maxPrice,
    'amenities' => $amenities,
    'check_in_date' => $checkInDate,
    'sort' => $sort
];

// Get hotels based on search criteria
$hotels = $hotelObj->searchHotels($searchCriteria, $perPage, $page);
$totalHotels = $hotelObj->countSearchResults($searchCriteria);

// Calculate total pages for pagination
$totalPages = ceil($totalHotels / $perPage);

?>


    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center text-white">
                    <h1 class="display-4 fw-bold">Find Your Perfect Hotel</h1>
                    <?php if (!empty($destination)): ?>
                    <p class="lead">Search results for: <?php echo htmlspecialchars($destination); ?></p>
                    <?php else: ?>
                    <p class="lead">Browse our selection of premium hotels worldwide</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="main-content py-5">
        <div class="container">
            <div class="row">
                <!-- Sidebar Filters -->
                <div class="col-lg-3">
                    <div class="filtersidebar mb-4">
                        <div class="card-body">
                            <form action="Hotels.php" method="GET" id="filterForm">
                                <div class="mb-3">
                                    <label for="destination" class="form-label">Destination</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                                        <input type="text" class="form-control my-0" id="destination" name="destination"
                                            placeholder="e.g. New York" value="<?php echo htmlspecialchars($destination); ?>">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="check_in_date" class="form-label">When</label>
                                    <div class="input-group">
                                        <input type="date" class="form-control" id="check_in_date" name="check_in_date" value="<?php echo htmlspecialchars($checkInDate); ?>">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <h6 class="fw-bold">Types of Hotels</h6>
                                    <?php foreach ($hotelTypes as $type): ?>
                                    <div class="form-check">
                                        <input class="form-check-input hotel-type-filter" type="radio" name="hotel_type" id="<?php echo strtolower(str_replace(' ', '-', $type)); ?>" value="<?php echo htmlspecialchars($type); ?>" <?php echo ($hotelType === $type) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="<?php echo strtolower(str_replace(' ', '-', $type)); ?>"><?php echo htmlspecialchars($type); ?></label>
                                    </div>
                                    <?php endforeach; ?>
                                </div>

                                <div class="mb-3">
                                    <h6 class="fw-bold">Price Range</h6>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Min: $<span id="minPriceDisplay"><?php echo $minPrice; ?></span></span>
                                        <span>Max: $<span id="maxPriceDisplay"><?php echo $maxPrice; ?></span></span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <input type="number" class="form-control form-control-sm me-2" id="min_price" name="min_price" value="<?php echo $minPrice; ?>" min="0" max="2000">
                                        <input type="number" class="form-control form-control-sm" id="max_price" name="max_price" value="<?php echo $maxPrice; ?>" min="0" max="2000">
                                    </div>
                                    <input type="range" class="form-range" id="price_range" min="0" max="2000" value="<?php echo $maxPrice; ?>">
                                </div>

                                <div class="mb-3">
                                    <h6 class="fw-bold">Amenities</h6>
                                    <?php foreach ($allAmenities as $amenity): ?>
                                    <div class="form-check">
                                        <input class="form-check-input amenity-filter" type="checkbox" id="amenity-<?php echo $amenity['amenity_id']; ?>" name="amenities[]" value="<?php echo $amenity['amenity_id']; ?>" <?php echo in_array($amenity['amenity_id'], $amenities) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="amenity-<?php echo $amenity['amenity_id']; ?>">
                                            <?php if (!empty($amenity['amenity_icon'])): ?>
                                            <i class="fas <?php echo $amenity['amenity_icon']; ?>"></i>
                                            <?php endif; ?>
                                            <?php echo htmlspecialchars($amenity['amenity_name']); ?>
                                        </label>
                                    </div>
                                    <?php endforeach; ?>
                                </div>

                                <?php if (isset($_GET['sort'])): ?>
                                <input type="hidden" name="sort" value="<?php echo htmlspecialchars($_GET['sort']); ?>">
                                <?php endif; ?>

                                <button type="submit" class="btn btn-primary w-100">Apply Filter</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Hotel Listings -->
                <div class="col-lg-9">
                    <div class="">
                        <div class="topsort bg-light p-3 mb-4 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Showing <?php echo count($hotels); ?> of <?php echo $totalHotels; ?> Hotels</h5>
                            <div class="sort-options">
                                <select class="form-select" id="sortOptions">
                                    <option value="featured">Featured First</option>
                                    <option value="price_asc">Price: Low to High</option>
                                    <option value="price_desc">Price: High to Low</option>
                                    <option value="rating">Rating</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="htl-booking-ct">
                        <div class="container">
                            <div class="row">
                                <?php if (count($hotels) > 0): ?>
                                <div class="row hotel-booking-listing g-4">
                                    <?php foreach($hotels as $hotel): ?>
                                    <div class="col-md-6 mb-4">
                                        <div class="htlist-bk-ct">
                                            <div class="htlist-bk-ct-imag">
                                                <?php if (!empty($hotel['primary_image'])): ?>
                                                <img src="<?php echo htmlspecialchars($hotel['primary_image']); ?>" alt="<?php echo htmlspecialchars($hotel['hotel_name']); ?>">
                                                <?php else: ?>
                                                <img src="assets/images/Hotel Booking/hotel-11_cms_1739791039.jpg" alt="<?php echo htmlspecialchars($hotel['hotel_name']); ?>">
                                                <?php endif; ?>
                                            </div>
                                            <div class="htlist-bk-ct-inn">
                                                <h4><?php echo htmlspecialchars($hotel['hotel_name']); ?></h4>
                                                <div class="stars">
                                                    <?php for ($i = 0; $i < $hotel['star_rating']; $i++): ?>
                                                    <i class="fas fa-star"></i>
                                                    <?php endfor; ?>
                                                    <?php for ($i = $hotel['star_rating']; $i < 5; $i++): ?>
                                                    <i class="far fa-star"></i>
                                                    <?php endfor; ?>
                                                </div>
                                                <div class="location"> <i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($hotel['city'] . ', ' . $hotel['country']); ?></div>
                                                <div class="price">$<?php echo number_format($hotel['min_price']); ?> <span class="per-night">/ night</span></div>
                                                <div class="meta">
                                                    <span class="hotel-type"> <i class="fa-solid fa-hotel"></i> <?php echo htmlspecialchars($hotel['hotel_type']); ?></span>
                                                    <span class="amenities-count"><i class="fa-solid fa-list"></i> <?php echo $hotel['amenities_count']; ?> amenities</span>
                                                </div>
                                                <a href="hotel-details.php?id=<?php echo $hotel['hotel_id']; ?>" class="btn btn-primary mt-2">View Details</a>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>

                                <!-- Pagination -->
                                <?php if ($totalPages > 1): ?>
                                <div class="pagination-container mt-4">
                                    <nav aria-label="Page navigation">
                                        <ul class="pagination justify-content-center">
                                            <?php if ($page > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" aria-label="Previous">
                                                    <span aria-hidden="true">&laquo;</span>
                                                </a>
                                            </li>
                                            <?php endif; ?>
                                            
                                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                            <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"><?php echo $i; ?></a>
                                            </li>
                                            <?php endfor; ?>
                                            
                                            <?php if ($page < $totalPages): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" aria-label="Next">
                                                    <span aria-hidden="true">&raquo;</span>
                                                </a>
                                            </li>
                                            <?php endif; ?>
                                        </ul>
                                    </nav>
                                </div>
                                <?php endif; ?>
                                
                                <?php else: ?>
                                <div class="col-12 text-center py-5">
                                    <h3>No hotels found matching your criteria</h3>
                                    <p>Try adjusting your filters or search for a different destination.</p>
                                    <a href="Hotels.php" class="btn btn-primary mt-3">View All Hotels</a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- JavaScript for Price Range -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const minPrice = document.getElementById('min_price');
            const maxPrice = document.getElementById('max_price');
            const priceRange = document.getElementById('price_range');
            const minPriceDisplay = document.getElementById('minPriceDisplay');
            const maxPriceDisplay = document.getElementById('maxPriceDisplay');
            const filterForm = document.getElementById('filterForm');
            const sortOptions = document.getElementById('sortOptions');
            const applyFilterBtn = document.querySelector('button[type="submit"]');

            // Update max price when slider changes
            priceRange.addEventListener('input', function() {
                maxPrice.value = this.value;
                maxPriceDisplay.textContent = this.value;
            });

            // Update min price display when input changes
            minPrice.addEventListener('input', function() {
                minPriceDisplay.textContent = this.value;
            });

            // Update max price display when input changes
            maxPrice.addEventListener('input', function() {
                maxPriceDisplay.textContent = this.value;
                priceRange.value = this.value;
            });
            
            // DISABLE auto-submit for hotel type filters
            const hotelTypeFilters = document.querySelectorAll('.hotel-type-filter');
            hotelTypeFilters.forEach(filter => {
                // Remove any existing event listeners
                const oldFilter = filter.cloneNode(true);
                filter.parentNode.replaceChild(oldFilter, filter);
            });
            
            // DISABLE auto-submit for amenity filters
            const amenityFilters = document.querySelectorAll('.amenity-filter');
            amenityFilters.forEach(filter => {
                // Remove any existing event listeners
                const oldFilter = filter.cloneNode(true);
                filter.parentNode.replaceChild(oldFilter, filter);
            });

            // Sort options - we'll keep this one auto-submitting
            sortOptions.addEventListener('change', function() {
                // Create or update the hidden input for sort
                let sortInput = document.querySelector('input[name="sort"]');
                if (!sortInput) {
                    sortInput = document.createElement('input');
                    sortInput.type = 'hidden';
                    sortInput.name = 'sort';
                    filterForm.appendChild(sortInput);
                }
                sortInput.value = this.value;
                
                // Submit the form
                filterForm.submit();
            });
            
            // Apply Filter button event handler
            applyFilterBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Validate the form
                if (parseInt(minPrice.value) > parseInt(maxPrice.value)) {
                    alert('Minimum price cannot be greater than maximum price');
                    return;
                }
                
                // Get current URL parameters
                const urlParams = new URLSearchParams(window.location.search);
                const sortParam = urlParams.get('sort');
                
                // Ensure sort parameter is preserved
                if (sortParam && !filterForm.querySelector('input[name="sort"]')) {
                    const sortInput = document.createElement('input');
                    sortInput.type = 'hidden';
                    sortInput.name = 'sort';
                    sortInput.value = sortParam;
                    filterForm.appendChild(sortInput);
                }
                
                // Submit the form
                filterForm.submit();
            });
        });
    </script>

    <?php include "footer.php" ?>