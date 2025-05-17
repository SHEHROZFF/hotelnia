<nav id="sidebar" class="col  -md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">
        <div class="d-flex align-items-center pb-3 mb-3 border-bottom">
            <div class="nav-logo">

                <a class="logo" href="index.php"> <img style="width:200px !important;" class="w-100"
                        src="<?php  echo $urlval?>assets/images/logo.png" alt=""></a>
            </div>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="<?php echo  $urlval?>admin/index.php">
                    <i class="bi bi-house me-2"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo  $urlval?>admin/pages/calendar/calendar.php">
                    <i class="bi bi-calendar me-2"></i>
                    Calendar
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo  $urlval?>admin/pages/bookings/bookings.php">
                    <i class="bi bi-journal-check me-2"></i>
                    Bookings
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo  $urlval?>admin/pages/rooms/rooms.php">
                    <i class="bi bi-door-closed me-2"></i>
                    Rooms
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo  $urlval?>admin/pages/guests/guests.php">
                    <i class="bi bi-people me-2"></i>
                    Guests
                </a>
            </li>
        </ul>

        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
            <span>Administration</span>
        </h6>
        <ul class="nav flex-column mb-2">
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="bi bi-gear me-2"></i>
                    Settings
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="bi bi-box-arrow-right me-2"></i>
                    Logout
                </a>
            </li>
        </ul>
    </div>
</nav>