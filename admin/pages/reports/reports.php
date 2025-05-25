<?php
include_once('../../../global.php');?>
<?php include_once "../../header.php" ?>

<!-- Main content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Reports & Analytics</h1>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card text-bg-primary">
                <div class="card-body">
                    <h6 class="card-title">Total Revenue</h6>
                    <h3 class="card-text" id="totalRevenue">$0.00</h3>
                    <small class="text-white-50">Last 30 days</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-success">
                <div class="card-body">
                    <h6 class="card-title">Occupancy Rate</h6>
                    <h3 class="card-text" id="occupancyRate">0%</h3>
                    <small class="text-white-50">Current month</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-info">
                <div class="card-body">
                    <h6 class="card-title">Total Bookings</h6>
                    <h3 class="card-text" id="totalBookings">0</h3>
                    <small class="text-white-50">Last 30 days</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-warning">
                <div class="card-body">
                    <h6 class="card-title">Average Stay</h6>
                    <h3 class="card-text" id="averageStay">0</h3>
                    <small class="text-white-50">Nights per booking</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Revenue Trends</h5>
                    <h6 class="card-subtitle text-muted">Monthly revenue breakdown</h6>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Room Type Distribution</h5>
                    <h6 class="card-subtitle text-muted">Bookings by room type</h6>
                </div>
                <div class="card-body">
                    <canvas id="roomTypeChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Stats -->
    <div class="row g-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Top Performing Hotels</h5>
                    <h6 class="card-subtitle text-muted">By revenue and bookings</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm" id="hotelStatsTable">
                            <thead>
                                <tr>
                                    <th>Hotel</th>
                                    <th>Revenue</th>
                                    <th>Bookings</th>
                                    <th>Occupancy</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Booking Status Distribution</h5>
                    <h6 class="card-subtitle text-muted">Current month statistics</h6>
                </div>
                <div class="card-body">
                    <canvas id="bookingStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include_once "../../footer.php" ?>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
$(document).ready(function() {
    // Load summary statistics
    function loadSummaryStats() {
        $.ajax({
            url: '../../ajax/reports/get_summary_stats.php',
            type: 'GET',
            success: function(response) {
                if(response.success) {
                    const stats = response.data;
                    $('#totalRevenue').text('$' + parseFloat(stats.total_revenue).toFixed(2));
                    $('#occupancyRate').text(stats.occupancy_rate + '%');
                    $('#totalBookings').text(stats.total_bookings);
                    $('#averageStay').text(parseFloat(stats.average_stay).toFixed(1));
                }
            }
        });
    }

    // Initialize Revenue Chart
    function initRevenueChart() {
        $.ajax({
            url: '../../ajax/reports/get_revenue_trends.php',
            type: 'GET',
            success: function(response) {
                if(response.success) {
                    const data = response.data;
                    new Chart(document.getElementById('revenueChart'), {
                        type: 'line',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: 'Revenue',
                                data: data.values,
                                borderColor: '#0d6efd',
                                tension: 0.1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return '$' + value;
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            }
        });
    }

    // Initialize Room Type Chart
    function initRoomTypeChart() {
        $.ajax({
            url: '../../ajax/reports/get_room_type_stats.php',
            type: 'GET',
            success: function(response) {
                if(response.success) {
                    const data = response.data;
                    new Chart(document.getElementById('roomTypeChart'), {
                        type: 'doughnut',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                data: data.values,
                                backgroundColor: [
                                    '#0d6efd',
                                    '#198754',
                                    '#0dcaf0',
                                    '#ffc107',
                                    '#dc3545'
                                ]
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false
                        }
                    });
                }
            }
        });
    }

    // Initialize Booking Status Chart
    function initBookingStatusChart() {
        $.ajax({
            url: '../../ajax/reports/get_booking_status_stats.php',
            type: 'GET',
            success: function(response) {
                if(response.success) {
                    const data = response.data;
                    new Chart(document.getElementById('bookingStatusChart'), {
                        type: 'pie',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                data: data.values,
                                backgroundColor: [
                                    '#ffc107',
                                    '#198754',
                                    '#0dcaf0',
                                    '#6c757d',
                                    '#dc3545'
                                ]
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false
                        }
                    });
                }
            }
        });
    }

    // Load hotel statistics
    function loadHotelStats() {
        $.ajax({
            url: '../../ajax/reports/get_hotel_stats.php',
            type: 'GET',
            success: function(response) {
                if(response.success) {
                    const stats = response.data;
                    let html = '';
                    stats.forEach(function(hotel) {
                        html += `
                            <tr>
                                <td>${hotel.hotel_name}</td>
                                <td>$${parseFloat(hotel.revenue).toFixed(2)}</td>
                                <td>${hotel.bookings}</td>
                                <td>${hotel.occupancy}%</td>
                            </tr>
                        `;
                    });
                    $('#hotelStatsTable tbody').html(html);
                }
            }
        });
    }

    // Initial load
    loadSummaryStats();
    initRevenueChart();
    initRoomTypeChart();
    initBookingStatusChart();
    loadHotelStats();
});
</script> 