<?php include_once "../global.php" ?>
<?php include "header.php" ?>



<!-- Main content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div
        class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Dashboard</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="calendar.html" class="btn btn-sm btn-outline-primary">View Calendar</a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4 mb-4">
        <div class="col">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="card-subtitle text-muted">Total Revenue</h6>
                        <i class="bi bi-currency-dollar text-muted"></i>
                    </div>
                    <h2 class="card-title">$45,231.89</h2>
                    <p class="card-text text-success small">+20.1% from last month</p>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="card-subtitle text-muted">Bookings</h6>
                        <i class="bi bi-journal-check text-muted"></i>
                    </div>
                    <h2 class="card-title">+2350</h2>
                    <p class="card-text text-success small">+180.1% from last month</p>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="card-subtitle text-muted">Active Guests</h6>
                        <i class="bi bi-people text-muted"></i>
                    </div>
                    <h2 class="card-title">+573</h2>
                    <p class="card-text text-success small">+201 since yesterday</p>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="card-subtitle text-muted">Room Occupancy</h6>
                        <i class="bi bi-building text-muted"></i>
                    </div>
                    <h2 class="card-title">78%</h2>
                    <p class="card-text text-success small">+10.1% from last month</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Recent Bookings -->
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Overview</h5>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="250"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Recent Bookings</h5>
                    <h6 class="card-subtitle text-muted">You made 265 bookings this month.</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-3">JD</div>
                                    <div>
                                        <h6 class="mb-0">John Doe</h6>
                                        <small class="text-muted">Deluxe Room - 3 nights</small>
                                    </div>
                                </div>
                                <span class="fw-bold">+$450.00</span>
                            </div>
                        </li>
                        <li class="list-group-item px-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-3">SD</div>
                                    <div>
                                        <h6 class="mb-0">Sarah Davis</h6>
                                        <small class="text-muted">Suite - 5 nights</small>
                                    </div>
                                </div>
                                <span class="fw-bold">+$1,250.00</span>
                            </div>
                        </li>
                        <li class="list-group-item px-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-3">RK</div>
                                    <div>
                                        <h6 class="mb-0">Robert Kim</h6>
                                        <small class="text-muted">Standard Room - 2 nights</small>
                                    </div>
                                </div>
                                <span class="fw-bold">+$240.00</span>
                            </div>
                        </li>
                        <li class="list-group-item px-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-3">EJ</div>
                                    <div>
                                        <h6 class="mb-0">Emily Johnson</h6>
                                        <small class="text-muted">Family Room - 4 nights</small>
                                    </div>
                                </div>
                                <span class="fw-bold">+$780.00</span>
                            </div>
                        </li>
                        <li class="list-group-item px-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-3">MP</div>
                                    <div>
                                        <h6 class="mb-0">Michael Patel</h6>
                                        <small class="text-muted">Deluxe Suite - 7 nights</small>
                                    </div>
                                </div>
                                <span class="fw-bold">+$2,100.00</span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Revenue Chart
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Revenue',
                data: [24000, 28500, 26000, 32000, 38000, 48000, 52000, 50000, 42000, 40000, 36000,
                    48000
                ],
                backgroundColor: '#198754',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            }
        }
    });
    </script>
    <?php include "footer.php" ?>