<?php include "header.php" ?>


    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center text-white">
                    <h1 class="display-4 fw-bold">Discover Your Next Adventure with Us</h1>
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
                            <div class="mb-3">
                                <label for="destination" class="form-label">Destination</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" class="form-control my-0" id="destination"
                                        placeholder="e.g. New York">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="date" class="form-label">When</label>
                                <div class="input-group">
                                    <input type="date" class="form-control" id="date">
                                </div>
                            </div>

                            <div class="mb-3">
                                <h6 class="fw-bold">Types of Hotels</h6>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="hotelType" id="hotel">
                                    <label class="form-check-label" for="hotel">Hotel</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="hotelType" id="apartment">
                                    <label class="form-check-label" for="apartment">Apartment</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="hotelType" id="resort">
                                    <label class="form-check-label" for="resort">Resort</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="hotelType" id="motel">
                                    <label class="form-check-label" for="motel">Motel</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="hotelType" id="hostel">
                                    <label class="form-check-label" for="hostel">Hostel</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="hotelType" id="guesthouse">
                                    <label class="form-check-label" for="guesthouse">Guesthouse</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="hotelType" id="lodge">
                                    <label class="form-check-label" for="lodge">Lodge</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="hotelType" id="villa">
                                    <label class="form-check-label" for="villa">Villa</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="hotelType" id="cottage">
                                    <label class="form-check-label" for="cottage">Cottage</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="hotelType" id="bungalow">
                                    <label class="form-check-label" for="bungalow">Bungalow</label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <h6 class="fw-bold">Price Range</h6>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Min</span>
                                    <span>Max</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <input type="number" class="form-control form-control-sm me-2" value="10">
                                    <input type="number" class="form-control form-control-sm" value="500">
                                </div>
                                <input type="range" class="form-range" min="0" max="1000" value="500">
                            </div>

                            <div class="mb-3">
                                <h6 class="fw-bold">Locations</h6>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="alberta">
                                    <label class="form-check-label" for="alberta">Alberta Canada</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="chicago">
                                    <label class="form-check-label" for="chicago">Chicago River North</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="newyork">
                                    <label class="form-check-label" for="newyork">New York USA</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="london">
                                    <label class="form-check-label" for="london">London, United Kingdom</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="paris">
                                    <label class="form-check-label" for="paris">Paris France</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="newyork2">
                                    <label class="form-check-label" for="newyork2">New York USA</label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <h6 class="fw-bold">Services</h6>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="airTickets">
                                    <label class="form-check-label" for="airTickets">Air Tickets</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="specialRates">
                                    <label class="form-check-label" for="specialRates">Special Rates</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="phoneReservation">
                                    <label class="form-check-label" for="phoneReservation">Phone Reservation</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="travelInsurance">
                                    <label class="form-check-label" for="travelInsurance">Travel Insurance</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="hotelsBooking">
                                    <label class="form-check-label" for="hotelsBooking">Hotels Booking</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="freeRides">
                                    <label class="form-check-label" for="freeRides">Free Rides</label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <h6 class="fw-bold">Cuisine</h6>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="american">
                                    <label class="form-check-label" for="american">American</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="chinese">
                                    <label class="form-check-label" for="chinese">Chinese</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="italian">
                                    <label class="form-check-label" for="italian">Italian</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="mexican">
                                    <label class="form-check-label" for="mexican">Mexican</label>
                                </div>
                            </div>

                            <button class="btn btn-primary w-100">Apply Filter</button>
                        </div>
                    </div>
                </div>

                <!-- Hotel Listings -->
                <div class="col-lg-9">
                    <div class="">
                        <div class="topsort bg-light">
                            <h5 class="mb-0"><bb>Showing 6 Search Results
                                </bb>
                            </h5>
                        </div>
                    </div>

                    <div class="htl-booking-ct">
                        <div class="container">
                            <div class="row">
                                <div class="row hotel-booking-listing  g-4">
                                    <div class="col-md-4">

                                        <div class="htlist-bk-ct">
                                            <div class="htlist-bk-ct-imag">
                                                <img src="./assets/images/Hotel Booking/hotel-11_cms_1739791039.jpg"
                                                    alt="">
                                            </div>
                                            <div class="htlist-bk-ct-inn">
                                                <h4>Paris International Hotel</h4>
                                                <img class="rating" src="assets/images/rating.png" alt="">
                                                <div class="location"> <i class="fa-solid fa-location-dot"></i> Alberta
                                                    Canada</div>
                                                <div class="price">$850</div>
                                                <div class="meta">
                                                    <span class="hotel-type"> <i class="fa-solid fa-hotel"></i>
                                                        Hotel</span>
                                                    <span class="people-type"><i class="fa-solid fa-users"></i> 2</span>
                                                    <span class="star-type"><i class="fa-solid fa-star"></i> 5
                                                        star</span>
                                                </div>
                                                <a class="btn-white" href="#">
                                                    View Detail</a>


                                            </div>

                                        </div>
                                    </div>


                                    <div class="col-md-4">
                                        <div class="htlist-bk-ct">
                                            <div class="htlist-bk-ct-imag">
                                                <img src="./assets/images/Hotel Booking/hotel-8_cms_1739560157.jpg"
                                                    alt="">
                                            </div>
                                            <div class="htlist-bk-ct-inn">
                                                <h4>Paris International Hotel</h4>
                                                <img class="rating" src="assets/images/rating.png" alt="">
                                                <div class="location"> <i class="fa-solid fa-location-dot"></i> Alberta
                                                    Canada</div>
                                                <div class="price">$850</div>
                                                <div class="meta">
                                                    <span class="hotel-type"> <i class="fa-solid fa-hotel"></i>
                                                        Hotel</span>
                                                    <span class="people-type"><i class="fa-solid fa-users"></i> 2</span>
                                                    <span class="star-type"><i class="fa-solid fa-star"></i> 5
                                                        star</span>
                                                </div>
                                                <a class="btn-white" href="#">
                                                    View Detail</a>


                                            </div>

                                        </div>


                                    </div>


                                    <div class="col-md-4">
                                        <div class="htlist-bk-ct">
                                            <div class="htlist-bk-ct-imag">
                                                <img src="./assets/images/Hotel Booking/hotel-1_cms_1737822083.jpg"
                                                    alt="">
                                            </div>
                                            <div class="htlist-bk-ct-inn">
                                                <h4>Paris International Hotel</h4>
                                                <img class="rating" src="assets/images/rating.png" alt="">
                                                <div class="location"> <i class="fa-solid fa-location-dot"></i> Alberta
                                                    Canada</div>
                                                <div class="price">$850</div>
                                                <div class="meta">
                                                    <span class="hotel-type"> <i class="fa-solid fa-hotel"></i>
                                                        Hotel</span>
                                                    <span class="people-type"><i class="fa-solid fa-users"></i> 2</span>
                                                    <span class="star-type"><i class="fa-solid fa-star"></i> 5
                                                        star</span>
                                                </div>
                                                <a class="btn-white" href="#">
                                                    View Detail</a>


                                            </div>

                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="htlist-bk-ct">
                                            <div class="htlist-bk-ct-imag">
                                                <img src="./assets/images/Hotel Booking/hotel-12_cms_1739962777.jpg"
                                                    alt="">
                                            </div>
                                            <div class="htlist-bk-ct-inn">
                                                <h4>Paris International Hotel</h4>
                                                <img class="rating" src="assets/images/rating.png" alt="">
                                                <div class="location"> <i class="fa-solid fa-location-dot"></i> Alberta
                                                    Canada</div>
                                                <div class="price">$850</div>
                                                <div class="meta">
                                                    <span class="hotel-type"> <i class="fa-solid fa-hotel"></i>
                                                        Hotel</span>
                                                    <span class="people-type"><i class="fa-solid fa-users"></i> 2</span>
                                                    <span class="star-type"><i class="fa-solid fa-star"></i> 5
                                                        star</span>
                                                </div>
                                                <a class="btn-white" href="#">
                                                    View Detail</a>


                                            </div>

                                        </div>
                                    </div>


                                    <div class="col-md-4">
                                        <div class="htlist-bk-ct">
                                            <div class="htlist-bk-ct-imag">
                                                <img src="./assets/images/Hotel Booking/hotel-1_cms_1737822083.jpg"
                                                    alt="">
                                            </div>
                                            <div class="htlist-bk-ct-inn">
                                                <h4>Paris International Hotel</h4>
                                                <img class="rating" src="assets/images/rating.png" alt="">
                                                <div class="location"> <i class="fa-solid fa-location-dot"></i> Alberta
                                                    Canada</div>
                                                <div class="price">$850</div>
                                                <div class="meta">
                                                    <span class="hotel-type"> <i class="fa-solid fa-hotel"></i>
                                                        Hotel</span>
                                                    <span class="people-type"><i class="fa-solid fa-users"></i> 2</span>
                                                    <span class="star-type"><i class="fa-solid fa-star"></i> 5
                                                        star</span>
                                                </div>
                                                <a class="btn-white" href="#">
                                                    View Detail</a>


                                            </div>

                                        </div>
                                    </div>


                                    <div class="col-md-4">
                                        <div class="htlist-bk-ct">
                                            <div class="htlist-bk-ct-imag">
                                                <img src="./assets/images/Hotel Booking/hotel-8_cms_1739560157.jpg"
                                                    alt="">
                                            </div>
                                            <div class="htlist-bk-ct-inn">
                                                <h4>Paris International Hotel</h4>
                                                <img class="rating" src="assets/images/rating.png" alt="">
                                                <div class="location"> <i class="fa-solid fa-location-dot"></i> Alberta
                                                    Canada</div>
                                                <div class="price">$850</div>
                                                <div class="meta">
                                                    <span class="hotel-type"> <i class="fa-solid fa-hotel"></i>
                                                        Hotel</span>
                                                    <span class="people-type"><i class="fa-solid fa-users"></i> 2</span>
                                                    <span class="star-type"><i class="fa-solid fa-star"></i> 5
                                                        star</span>
                                                </div>
                                                <a class="btn-white" href="#">
                                                    View Detail</a>


                                            </div>

                                        </div>

                                    </div>










                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <nav aria-label="Page navigation ">
                        <ul class="pagination justify-content-center py-5">
                            <li class="page-item disabled">
                                <a class="page-link" href="#" tabindex="-1" aria-disabled="true">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            
                            <li class="page-item">
                                <a class="page-link" href="#">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>

        </div>
    </section>

    <?php include "footer.php" ?>