<?php include "header.php" ?>

<?php
// Initialize Database and Hotel objects 
require_once "classes/Hotel.php";
$db = new Database();
$hotelObj = new Hotel($db);

// Get top destinations
$topDestinations = $hotelObj->getTopDestinations(6);

// Get featured hotels
$featuredHotels = $hotelObj->getFeaturedHotels(6);
?>


    <div class="main-content">

        <!-- ----------------------------------Banner start-------------------------------------------- -->
        <div class="banner-section">

            <div class="container">
                <div class="row">
                    <div class="banner-content">
                        <h1>
                            Welcome to Hotelina</h1>
                        <p>Discover Your Perfect Stay - Book Luxurious Hotels Worldwide</p>
                    </div>
                </div>
                <div class="row">
                    <div class="banner-form">
                        <button class="hotels-btn"> <i class="fa-solid fa-hotel"></i> Hotels</button>
                        <form action="Hotels.php" method="GET">
                            <div class="row d-flex mb-3 align-items-end justify-content-center">
                                <div class="col-12 col-md-5 mb-3 mb-md-0">
                                    <label for="destination" class="form-label">Destination</label>
                                    <input type="text" class="form-control" id="destination" name="destination" placeholder="e.g. New York, Paris, Dubai">
                                </div>
                                <div class="col-12 col-md-5 mb-3 mb-md-0">
                                    <label for="check_in_date" class="form-label">When</label>
                                    <input type="date" class="form-control" id="check_in_date" name="check_in_date" placeholder="mm/dd/yyyy">
                                </div>
                                <div class="col-12 col-md-2 text-end">
                                    <button type="submit" class="btn btn-sec search-btn px-4 w-100">Search</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="row">
                    <div class="typeJsWrapper">
                        <h4>What we offer?<span> Hotel Booking </span></h4>
                    </div>
                </div>

            </div>
        </div>

        <!-- ----------------------------------Banner  End-------------------------------------------- -->

        <!-- ---------------------------------Top Destination Section  Start-------------------------------------------- -->

        <div class="tp-dst-sc">
            <div class="container ">
                <div class="row tp-dest-ct">
                    <h2>Top Destinations</h2>
                    <p>Explore our most popular destinations and find the perfect spot for your next adventure</p>
                </div>
                <div class="htlst-mn row d-flex">
                    <?php foreach ($topDestinations as $index => $destination): ?>
                    <div class="hotel-listing">
                        <a href="Hotels.php?destination=<?php echo urlencode($destination['city']); ?>" class="htlist">
                            <div class="listing-img">
                                <img src="./assets/images/listing/0<?php echo ($index % 6) + 1; ?>_1709631426.jpg" alt="<?php echo htmlspecialchars($destination['city']); ?>">
                            </div>
                            <h4><?php echo htmlspecialchars($destination['city']); ?></h4>
                            <p><?php echo $destination['hotel_count']; ?> Listing<?php echo $destination['hotel_count'] > 1 ? 's' : ''; ?></p>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <!-- ---------------------------------Top Destination Section End-------------------------------------------- -->

        <div class="offer-banner">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <div class="offer-banner-1">
                            <h2>25% Off
                            </h2>
                            <p>Explore the World, One Destination at a Time</p>
                            <a class="btn-sec" href="Hotels.php"> Book Now</a>

                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="offer-banner-2">
                            <h2>25% Off
                            </h2>
                            <p>Explore the World, One Destination at a Time</p>
                            <a class="btn-sec" href="Hotels.php"> Book Now</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <!-- ---------------------------------Hotel booking listing Section Start-------------------------------------------- -->
        <div class="htl-pr-booking">
            <div class="container">
                <div class="row">
                    <h2>Featured Hotels</h2>
                    <p>Indulge in world-class hospitality and exceptional comfort at our top-rated hotels</p>

                    <div class="row hotel-booking-listing py-4">
                        <div class="swiper mySwiper">
                            <div class="swiper-wrapper">
                                <?php foreach ($featuredHotels as $hotel): ?>
                                <div class="swiper-slide">
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
                                            <img class="rating" src="assets/images/rating.png" alt="">
                                            <div class="location"> <i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($hotel['city'] . ', ' . $hotel['country']); ?></div>
                                            <div class="price">$<?php echo number_format($hotel['min_price']); ?></div>
                                            <div class="meta">
                                                <span class="hotel-type"> <i class="fa-solid fa-hotel"></i> <?php echo htmlspecialchars($hotel['hotel_type']); ?></span>
                                                <span class="people-type"><i class="fa-solid fa-users"></i> 2</span>
                                                <span class="star-type"><i class="fa-solid fa-star"></i> <?php echo $hotel['star_rating']; ?> star</span>
                                            </div>
                                            <a href="hotel-details.php?id=<?php echo $hotel['hotel_id']; ?>" class="btn btn-primary mt-2">View Details</a>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="swiper-button-next"></div>
                            <div class="swiper-button-prev"></div>
                            <div class="swiper-pagination"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- ---------------------------------Hotel booking listing Section  End-------------------------------------------- -->

        <!-- ---------------------------------Choose us Section  Start-------------------------------------------- -->

        <div class="choose-section">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <div class="ch-sc-tp-ct">
                            <h2>Why Choose Us?</h2>
                            <p>

                                We're dedicated to providing you with the best travel experiences possible. Here's why
                                travelers choose us time and time again:
                            </p>
        </div>


                        <div class="choose-list">
                            <div class="cl-single">
                                <div class="icon">
                                    <i class="fa-solid fa-wallet"></i>
                                </div>
                                <div class="cl-content">
                                    <h4>Best Deals & Offers</h4>
                                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                                </div>
                            </div>
                            <div class="cl-single">
                                <div class="icon">
                                    <i class="fa-solid fa-calendar-days"></i>
                            </div>
                                <div class="cl-content">
                                    <h4>Flexible Date Selection</h4>
                                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                                </div>
                            </div>
                            <div class="cl-single">
                                <div class="icon">
                                    <i class="fa-solid fa-headset"></i>
                            </div>
                                <div class="cl-content">
                                    <h4>24/7 Support</h4>
                                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                                </div>
                            </div>
                            <div class="cl-single">
                                <div class="icon">
                                    <i class="fa-solid fa-credit-card"></i>
                            </div>
                                <div class="cl-content">
                                    <h4>Secure Payments</h4>
                                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="ch-img-1">
                            <img src="./assets/images/slider/4_1709631227.jpg" alt="">
                        </div>
                    </div>
                </div>


            </div>
        </div>
        <!-- ---------------------------------Choose us Section  End-------------------------------------------- -->

        <!-- ---------------------------------Testimonial Section  Start-------------------------------------------- -->

        <div class="testimonial-section">
            <div class="container">
                <div class="row">
                    <div class="col-12 text-center mb-5">
                        <h2>What Our Customers Say</h2>
                        <p>Read genuine reviews from our satisfied customers</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="testimonial-card">
                            <div class="testimonial-content">
                                <div class="stars">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                                <p>"The hotel booking was seamless, and our stay was outstanding. The staff went above and beyond to make our trip memorable."</p>
                                <div class="testimonial-author">
                                    <h5>Emily Johnson</h5>
                                    <p>New York, USA</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="testimonial-card">
                            <div class="testimonial-content">
                                <div class="stars">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                                <p>"I've used many hotel booking sites, but this one offers the best deals and customer service. I'll definitely be booking with them again."</p>
                                <div class="testimonial-author">
                                    <h5>David Smith</h5>
                                    <p>London, UK</p>
                            </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="testimonial-card">
                            <div class="testimonial-content">
                                <div class="stars">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                </div>
                                <p>"The website was easy to navigate, and I found exactly what I was looking for. The hotel recommendations were spot on for my needs."</p>
                                <div class="testimonial-author">
                                    <h5>Sophia Patel</h5>
                                    <p>Dubai, UAE</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ---------------------------------Testimonial Section  End-------------------------------------------- -->

        <!-- ---------------------------------Blog Section  Start-------------------------------------------- -->

        <div class="blog-section">
            <div class="container">
                <div class="row mb-5">
                    <div class="col-12 text-center">
                        <h2>Travel Tips & Insights</h2>
                        <p>Stay updated with our latest travel guides and hotel recommendations</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="blog-card">
                            <div class="blog-image">
                                <img src="assets/images/blog/blog-1.jpg" alt="Blog Post 1">
                </div>
                            <div class="blog-content">
                                <h4>Top 10 Luxury Hotels in Europe</h4>
                                <p class="blog-meta">July 15, 2023 | Travel</p>
                                <p>Discover the most luxurious and elegant hotels across Europe for an unforgettable vacation experience.</p>
                                <a href="#" class="read-more">Read More</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="blog-card">
                            <div class="blog-image">
                                <img src="assets/images/blog/blog-2.jpg" alt="Blog Post 2">
                                </div>
                            <div class="blog-content">
                                <h4>Budget Travel: Save Money on Accommodations</h4>
                                <p class="blog-meta">August 5, 2023 | Budget Travel</p>
                                <p>Expert tips and tricks to help you find affordable yet comfortable accommodations for your next trip.</p>
                                <a href="#" class="read-more">Read More</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="blog-card">
                            <div class="blog-image">
                                <img src="assets/images/blog/blog-3.jpg" alt="Blog Post 3">
                            </div>
                            <div class="blog-content">
                                <h4>Family-Friendly Resorts Around the World</h4>
                                <p class="blog-meta">September 12, 2023 | Family Travel</p>
                                <p>The best resorts and hotels that cater to families with children, featuring activities for all ages.</p>
                                <a href="#" class="read-more">Read More</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-12 text-center">
                        <a href="Blog.php" class="btn btn-primary px-4">View All Blog Posts</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- ---------------------------------Blog Section  End-------------------------------------------- -->

        <!-- ---------------------------------Newsletter Section  Start-------------------------------------------- -->

        <div class="newsletter-section">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8 text-center">
                        <h2>Subscribe to Our Newsletter</h2>
                        <p>Stay updated with our latest offers, travel tips, and exclusive deals</p>
                        <form class="newsletter-form mt-4">
                            <div class="input-group">
                                <input type="email" class="form-control" placeholder="Your Email Address" required>
                                <button class="btn btn-primary" type="submit">Subscribe</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- ---------------------------------Newsletter Section  End-------------------------------------------- -->

    </div>

    <?php include "footer.php" ?>

