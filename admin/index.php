<?php include_once "../global.php" ?>
<?php include_once "check_admin.php" ?>
<?php include "header.php" ?>



<!-- Main content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div
        class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Dashboard</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="pages/calendar/calendar.php" class="btn btn-sm btn-outline-primary">View Calendar</a>
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
                    <h2 class="card-title" id="totalRevenue">$0.00</h2>
                    <p class="card-text" id="revenueChange">
                        <span class="text-success">+0% from last month</span>
                    </p>
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
                    <h2 class="card-title" id="totalBookings">0</h2>
                    <p class="card-text" id="bookingsChange">
                        <span class="text-success">+0% from last month</span>
                    </p>
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
                    <h2 class="card-title" id="activeGuests">0</h2>
                    <p class="card-text" id="guestsChange">
                        <span class="text-success">+0 since yesterday</span>
                    </p>
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
                    <h2 class="card-title" id="occupancyRate">0%</h2>
                    <p class="card-text" id="occupancyChange">
                        <span class="text-success">+0% from last month</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Recent Bookings -->
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Revenue Overview</h5>
                    <h6 class="card-subtitle text-muted">Monthly revenue for the last 12 months</h6>
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
                    <h6 class="card-subtitle text-muted" id="bookingsSubtitle">Loading bookings...</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush" id="recentBookingsList">
                        <!-- Bookings will be loaded dynamically -->
                    </ul>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
// Function to format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
}

// Function to get initials from name
function getInitials(name) {
    return name.split(' ')
        .map(word => word[0])
        .join('')
        .toUpperCase();
}

// Function to update stats card
function updateStatsCard(value, change, valueId, changeId, isPercentage = true, isCurrency = false) {
    const valueElement = document.getElementById(valueId);
    const changeElement = document.getElementById(changeId);
    
    valueElement.textContent = isCurrency ? formatCurrency(value) : (isPercentage ? value + '%' : value);
    
    const changeText = isPercentage ? 
        (change > 0 ? `+${change}% from last month` : `${change}% from last month`) :
        (change > 0 ? `+${change} since yesterday` : `${change} since yesterday`);
    
    changeElement.innerHTML = `<span class="text-${change >= 0 ? 'success' : 'danger'}">${changeText}</span>`;
}

// Load dashboard statistics
fetch('ajax/dashboard/get_dashboard_stats.php')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const stats = data.data;
            
            // Update revenue card
            updateStatsCard(
                stats.revenue.amount,
                stats.revenue.change,
                'totalRevenue',
                'revenueChange',
                true,
                true
            );
            
            // Update bookings card
            updateStatsCard(
                stats.bookings.count,
                stats.bookings.change,
                'totalBookings',
                'bookingsChange'
            );
            
            // Update guests card
            updateStatsCard(
                stats.guests.count,
                stats.guests.change,
                'activeGuests',
                'guestsChange',
                false
            );
            
            // Update occupancy card
            updateStatsCard(
                stats.occupancy.rate,
                stats.occupancy.change,
                'occupancyRate',
                'occupancyChange'
            );
        }
    })
    .catch(error => console.error('Error loading dashboard stats:', error));

// Load revenue chart
fetch('ajax/dashboard/get_revenue_chart.php')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const ctx = document.getElementById('revenueChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.data.labels,
                    datasets: [{
                        label: 'Revenue',
                        data: data.data.values,
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
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return formatCurrency(value);
                                }
                            }
                        }
                    }
                }
            });
        }
    })
    .catch(error => console.error('Error loading revenue chart:', error));

// Load recent bookings
fetch('ajax/dashboard/get_recent_bookings.php')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update subtitle
            document.getElementById('bookingsSubtitle').textContent = 
                `You made ${data.data.total_month} bookings this month.`;
            
            // Update bookings list
            const bookingsList = document.getElementById('recentBookingsList');
            let html = '';
            
            data.data.bookings.forEach(booking => {
                html += `
                    <li class="list-group-item px-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle me-3">${getInitials(booking.guest_name)}</div>
                                <div>
                                    <h6 class="mb-0">${booking.guest_name}</h6>
                                    <small class="text-muted">${booking.room_type} - ${booking.nights} nights</small>
                                </div>
                            </div>
                            <span class="fw-bold">${formatCurrency(booking.total_amount)}</span>
                        </div>
                    </li>
                `;
            });
            
            bookingsList.innerHTML = html;
        }
    })
    .catch(error => console.error('Error loading recent bookings:', error));
</script>

<?php include "footer.php" ?>