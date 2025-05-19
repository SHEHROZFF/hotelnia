<?php
include_once('global.php');

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['user_name'] : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="
        default-src 'self'; 
        script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; 
        style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.googleapis.com; 
        font-src 'self' https://cdnjs.cloudflare.com https://fonts.gstatic.com data:; 
        img-src 'self' data:;
        connect-src 'self';">
    <title>Hotelina - Hotel Booking</title>
    <!-- CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/brands.min.css"
        integrity="sha512-58P9Hy7II0YeXLv+iFiLCv1rtLW47xmiRpC1oFafeKNShp8V5bKV/ciVbk2YfxXQMt58DjNfkXFOn62xE+g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./assets/Style.css">
    
    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</head>

<body>


    <!-- ----------------------------------header  start-------------------------------------------- -->
    <header>

        <nav class="navbar navbar-expand-lg  nav-bar-main">
            <div class="container-fluid">
                <div class="nav-logo">

                    <a class="logo" href="index.php"> <img src="./assets/images/logo.png" alt=""></a>
                </div>
                <div class="nav-Center d-flex">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarScroll" aria-controls="navbarScroll" aria-expanded="false"
                        aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarScroll">
                        <ul class="navbar-nav navbar-nav-scroll" style="--bs-scroll-height: 100px;">
                            <li class="nav-item brdr-lft-mnu">
                                <a class="nav-link active brdr-lft-mnu-link" aria-current="page"
                                    href="index.php">Home</a>
                            </li>
                            <li class="nav-item brdr-lft-mnu">
                                <a class="nav-link brdr-lft-mnu-link" href="Hotels.php">Hotels</a>
                            </li>
                            <li class="nav-item brdr-lft-mnu">
                                <a class="nav-link brdr-lft-mnu-link" href="About-us.php">About Us</a>
                            </li>
                            <li class="nav-item brdr-lft-mnu">
                                <a class="nav-link brdr-lft-mnu-link" href="Services.php">Services</a>
                            </li>
                            <li class="nav-item brdr-lft-mnu">
                                <a class="nav-link brdr-lft-mnu-link" href="blog.php">Blog</a>
                            </li>
                            <li class="nav-item brdr-lft-mnu">
                                <a class="nav-link brdr-lft-mnu-link" href="contact-us.php">Contact Us</a>
                            </li>



                            <div class="nav-right d-flex ">
                                <?php if ($isLoggedIn): ?>
                                    <div class="dropdown">
                                        <a class="brdr-lft-mnu-link btn-white dropdown-toggle" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fa-solid fa-user"></i> <?= htmlspecialchars($userName) ?>
                                        </a>
                                        <ul class="dropdown-menu" aria-labelledby="userDropdown">
                                            <li><a class="dropdown-item" href="profile.php">My Profile</a></li>
                                            <li><a class="dropdown-item" href="my-bookings.php">My Bookings</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item" href="ajax/process_logout.php">Logout</a></li>
                                        </ul>
                                    </div>
                                <?php else: ?>
                                    <a class="brdr-lft-mnu-link btn-white" href="Login.php"> <i
                                        class="fa-solid fa-right-to-bracket"></i> Login</a>


                                <a class="brdr-lft-mnu-link btn-primary" href="register.php"> <i
                                        class="fa-solid fa-user"></i>
                                    Register</a>
                                <?php endif; ?>
                            </div>


                        </ul>

                    </div>

                </div>
            </div>
        </nav>

    </header>

    <!-- ----------------------------------header End-------------------------------------------- -->