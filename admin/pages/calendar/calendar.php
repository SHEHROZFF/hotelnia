<?php
include_once('../../../global.php');?>
<?php include_once "../../header.php" ?>

<!-- Main content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Calendar Management</h1>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Booking Calendar</h5>
            <h6 class="card-subtitle text-muted">View and manage bookings in calendar view</h6>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <select class="form-select" id="hotelFilter">
                        <option value="">All Hotels</option>
                        <!-- Hotels will be loaded dynamically -->
                    </select>
                </div>
                <div class="col-md-4">
                    <select class="form-select" id="roomFilter" disabled>
                        <option value="">All Rooms</option>
                        <!-- Rooms will be loaded based on hotel selection -->
                    </select>
                </div>
                <div class="col-md-4">
                    <select class="form-select" id="viewType">
                        <option value="month">Month View</option>
                        <option value="week">Week View</option>
                        <option value="day">Day View</option>
                    </select>
                </div>
            </div>

            <div id="calendar"></div>
        </div>
    </div>
</main>

<!-- Event Details Modal -->
<div class="modal fade" id="eventModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Booking Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Guest</label>
                    <div id="eventGuest" class="form-control-plaintext"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Hotel & Room</label>
                    <div id="eventRoom" class="form-control-plaintext"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Check In/Out</label>
                    <div id="eventDates" class="form-control-plaintext"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <div id="eventStatus"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Special Requests</label>
                    <div id="eventRequests" class="form-control-plaintext"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="editBooking">Edit Booking</button>
            </div>
        </div>
    </div>
</div>

<?php include_once "../../footer.php" ?>

<!-- Include FullCalendar library -->
<link href='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.10/main.min.css' rel='stylesheet' />
<link href='https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.10/main.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.10/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.10/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@6.1.10/main.min.js'></script>

<script>
$(document).ready(function() {
    var calendar;
    var currentBookingId;

    // Initialize calendar
    var calendarEl = document.getElementById('calendar');
    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: function(info, successCallback, failureCallback) {
            $.ajax({
                url: '../../ajax/calendar/get_events.php',
                type: 'GET',
                data: {
                    start: info.startStr,
                    end: info.endStr,
                    hotel_id: $('#hotelFilter').val(),
                    room_id: $('#roomFilter').val()
                },
                success: function(response) {
                    if(response.success) {
                        var events = response.data.map(function(booking) {
                            const statusColors = {
                                'pending': '#ffc107',
                                'confirmed': '#198754',
                                'checked_in': '#0dcaf0',
                                'checked_out': '#6c757d',
                                'cancelled': '#dc3545'
                            };
                            
                            return {
                                id: booking.booking_id,
                                title: `${booking.guest_name} - ${booking.room_type}`,
                                start: booking.check_in_date,
                                end: booking.check_out_date,
                                backgroundColor: statusColors[booking.status] || '#6c757d',
                                extendedProps: booking
                            };
                        });
                        successCallback(events);
                    } else {
                        failureCallback(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    failureCallback(error);
                }
            });
        },
        eventClick: function(info) {
            const booking = info.event.extendedProps;
            currentBookingId = booking.booking_id;
            
            $('#eventGuest').text(booking.guest_name + ' (' + booking.guest_email + ')');
            $('#eventRoom').text(booking.hotel_name + ' - ' + booking.room_type);
            $('#eventDates').text(
                new Date(booking.check_in_date).toLocaleDateString() + ' to ' + 
                new Date(booking.check_out_date).toLocaleDateString()
            );
            $('#eventStatus').html(`<span class="badge bg-${getStatusClass(booking.status)}">${booking.status}</span>`);
            $('#eventRequests').text(booking.special_requests || 'No special requests');
            
            $('#eventModal').modal('show');
        }
    });
    calendar.render();

    // Load hotels for filter
    function loadHotels() {
        $.ajax({
            url: '../../ajax/hotels/get_hotels.php',
            type: 'GET',
            success: function(response) {
                if(response.data) {
                    var hotels = response.data;
                    var options = '<option value="">All Hotels</option>';
                    hotels.forEach(function(hotel) {
                        options += `<option value="${hotel.hotel_id}">${hotel.hotel_name}</option>`;
                    });
                    $('#hotelFilter').html(options);
                }
            }
        });
    }

    // Load rooms when hotel is selected
    $('#hotelFilter').change(function() {
        var hotelId = $(this).val();
        if(hotelId) {
            $.ajax({
                url: '../../ajax/rooms/get_rooms.php',
                type: 'GET',
                data: { hotel_id: hotelId },
                success: function(response) {
                    if(response.data) {
                        var rooms = response.data;
                        var options = '<option value="">All Rooms</option>';
                        rooms.forEach(function(room) {
                            options += `<option value="${room.room_id}">${room.room_type}</option>`;
                        });
                        $('#roomFilter')
                            .html(options)
                            .prop('disabled', false);
                    }
                }
            });
        } else {
            $('#roomFilter')
                .html('<option value="">All Rooms</option>')
                .prop('disabled', true);
        }
        calendar.refetchEvents();
    });

    // Refresh calendar when room filter changes
    $('#roomFilter').change(function() {
        calendar.refetchEvents();
    });

    // Change calendar view
    $('#viewType').change(function() {
        calendar.changeView($(this).val());
    });

    // Edit booking button click
    $('#editBooking').click(function() {
        window.location.href = '../bookings/bookings.php?edit=' + currentBookingId;
    });

    // Helper function for status badge colors
    function getStatusClass(status) {
        const statusClasses = {
            'pending': 'warning',
            'confirmed': 'success',
            'checked_in': 'info',
            'checked_out': 'secondary',
            'cancelled': 'danger'
        };
        return statusClasses[status] || 'secondary';
    }

    // Initial load
    loadHotels();
});
</script>