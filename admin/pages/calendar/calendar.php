<?php
include_once('../../../global.php');?>
<?php include_once "../../header.php" ?>



<!-- Main content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div
        class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Booking Calendar</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" id="prevBtn">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <button type="button" class="btn btn-primary btn-outline-secondary" id="nextBtn">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="todayBtn">
                Today
            </button>
            <div class="dropdown ms-2">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="viewDropdown"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    Month
                </button>
                <ul class="dropdown-menu" aria-labelledby="viewDropdown">
                    <li><a class="dropdown-item" href="#" data-view="dayGridMonth">Month</a></li>
                    <li><a class="dropdown-item" href="#" data-view="timeGridWeek">Week</a></li>
                    <li><a class="dropdown-item" href="#" data-view="timeGridDay">Day</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-9">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Booking Calendar</h5>
                    <h6 class="card-subtitle text-muted">View and manage all bookings</h6>
                </div>
                <div class="card-body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title" id="selectedDate">April 12, 2025</h5>
                    <h6 class="card-subtitle text-muted"><span id="bookingCount">2</span> bookings for this date
                    </h6>
                </div>
                <div class="card-body">
                    <div id="dayBookings">
                        <div class="booking-item border rounded p-3 mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-medium">John Doe</span>
                                <span class="badge bg-success">confirmed</span>
                            </div>
                            <div class="text-muted small">Deluxe Room</div>
                            <div class="small">Apr 15 - Apr 18, 2025</div>
                        </div>
                        <div class="booking-item border rounded p-3 mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-medium">Sarah Davis</span>
                                <span class="badge bg-success">confirmed</span>
                            </div>
                            <div class="text-muted small">Suite</div>
                            <div class="small">Apr 12 - Apr 17, 2025</div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-primary w-100">Add New Booking</button>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: false,
        events: [{
                title: 'John Doe - Deluxe',
                start: '2025-04-15',
                end: '2025-04-18',
                color: '#198754'
            },
            {
                title: 'Sarah Davis - Suite',
                start: '2025-04-12',
                end: '2025-04-17',
                color: '#198754'
            },
            {
                title: 'Robert Kim - Standard',
                start: '2025-04-20',
                end: '2025-04-22',
                color: '#ffc107'
            },
            {
                title: 'Emily Johnson - Family',
                start: '2025-04-25',
                end: '2025-04-29',
                color: '#198754'
            }
        ],
        eventClick: function(info) {
            alert('Booking: ' + info.event.title);
        }
    });
    calendar.render();

    // Button handlers
    document.getElementById('prevBtn').addEventListener('click', function() {
        calendar.prev();
    });

    document.getElementById('nextBtn').addEventListener('click', function() {
        calendar.next();
    });

    document.getElementById('todayBtn').addEventListener('click', function() {
        calendar.today();
    });

    // View dropdown handlers
    document.querySelectorAll('[data-view]').forEach(item => {
        item.addEventListener('click', event => {
            const view = event.target.getAttribute('data-view');
            calendar.changeView(view);
            document.getElementById('viewDropdown').textContent = event.target.textContent;
        });
    });
});
</script>
<?php include_once "../../footer.php" ?>