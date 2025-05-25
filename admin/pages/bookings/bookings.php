<?php
include_once('../../../global.php');?>
<?php include_once "../../header.php" ?>



<!-- Main content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div
        class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Booking Management</h1>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title">All Bookings</h5>
            <h6 class="card-subtitle text-muted">Manage hotel bookings and reservations</h6>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-8 d-flex gap-2">
                    <select class="form-select w-auto" id="hotelFilter">
                        <option value="">All Hotels</option>
                        <!-- Hotels will be loaded dynamically -->
                    </select>
                    <select class="form-select w-auto" id="statusFilter">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="checked_in">Checked In</option>
                        <option value="checked_out">Checked Out</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                    <input type="date" class="form-control w-auto" id="dateFilter" placeholder="Filter by date">
                </div>
                <div class="col-md-4 text-md-end">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBookingModal">
                        <i class="bi bi-plus-lg"></i> New Booking
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover" id="bookingsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Guest</th>
                            <th>Hotel/Room</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be loaded dynamically -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- Add Booking Modal -->
<div class="modal fade" id="addBookingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Booking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addBookingForm">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Hotel</label>
                            <select class="form-select" name="hotel_id" required>
                                <option value="">Select Hotel</option>
                                <!-- Hotels will be loaded dynamically -->
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Room</label>
                            <select class="form-select" name="room_id" required disabled>
                                <option value="">Select Room</option>
                                <!-- Rooms will be loaded based on hotel selection -->
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Guest</label>
                            <select class="form-select" name="user_id" required>
                                <option value="">Select Guest</option>
                                <!-- Guests will be loaded dynamically -->
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Number of Guests</label>
                            <input type="number" class="form-control" name="guests" min="1" value="1" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Check In Date</label>
                            <input type="date" class="form-control" name="check_in_date" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Check Out Date</label>
                            <input type="date" class="form-control" name="check_out_date" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Special Requests</label>
                            <textarea class="form-control" name="special_requests" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="alert alert-info" id="priceCalculation">
                                Total nights: <span id="totalNights">0</span><br>
                                Price per night: $<span id="pricePerNight">0.00</span><br>
                                Total price: $<span id="totalPrice">0.00</span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveBooking">Create Booking</button>
            </div>
        </div>
    </div>
</div>

<!-- View/Edit Booking Modal -->
<div class="modal fade" id="editBookingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Booking Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editBookingForm">
                    <input type="hidden" name="booking_id">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" required>
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="checked_in">Checked In</option>
                                <option value="checked_out">Checked Out</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Guest</label>
                            <input type="text" class="form-control" id="guestName" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Hotel & Room</label>
                            <input type="text" class="form-control" id="hotelRoom" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Number of Guests</label>
                            <input type="number" class="form-control" name="guests" min="1" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Check In Date</label>
                            <input type="date" class="form-control" name="check_in_date" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Check Out Date</label>
                            <input type="date" class="form-control" name="check_out_date" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Special Requests</label>
                            <textarea class="form-control" name="special_requests" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Total Price</label>
                            <input type="number" class="form-control" name="total_price" step="0.01" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" id="cancelBooking">Cancel Booking</button>
                <button type="button" class="btn btn-primary" id="updateBooking">Update Booking</button>
            </div>
        </div>
    </div>
</div>

<?php include_once "../../footer.php" ?>

<script>
$(document).ready(function() {
    // Initialize DataTable
    var bookingsTable = $('#bookingsTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: '../../ajax/bookings/get_bookings.php',
            type: 'GET',
            data: function(d) {
                d.hotel_id = $('#hotelFilter').val();
                d.status = $('#statusFilter').val();
                d.date = $('#dateFilter').val();
            }
        },
        columns: [
            { data: 'booking_id' },
            { 
                data: 'guest_name',
                render: function(data, type, row) {
                    return `${data}<br><small class="text-muted">${row.guest_email}</small>`;
                }
            },
            { 
                data: 'hotel_name',
                render: function(data, type, row) {
                    return `${data}<br><small class="text-muted">${row.room_type}</small>`;
                }
            },
            { 
                data: 'check_in_date',
                render: function(data) {
                    return new Date(data).toLocaleDateString();
                }
            },
            { 
                data: 'check_out_date',
                render: function(data) {
                    return new Date(data).toLocaleDateString();
                }
            },
            { 
                data: 'total_price',
                render: function(data) {
                    return `$${parseFloat(data).toFixed(2)}`;
                }
            },
            { 
                data: 'status',
                render: function(data) {
                    const statusClasses = {
                        'pending': 'warning',
                        'confirmed': 'success',
                        'checked_in': 'info',
                        'checked_out': 'secondary',
                        'cancelled': 'danger'
                    };
                    return `<span class="badge bg-${statusClasses[data] || 'secondary'}">${data}</span>`;
                }
            },
            {
                data: null,
                render: function(data) {
                    return `
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><button class="dropdown-item edit-booking" data-id="${data.booking_id}">Edit booking</button></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><button class="dropdown-item text-success" onclick="updateBookingStatus(${data.booking_id}, 'confirmed')" ${data.status !== 'pending' ? 'disabled' : ''}>
                                    Confirm booking
                                </button></li>
                                <li><button class="dropdown-item text-info" onclick="updateBookingStatus(${data.booking_id}, 'checked_in')" ${data.status !== 'confirmed' ? 'disabled' : ''}>
                                    Check in
                                </button></li>
                                <li><button class="dropdown-item text-secondary" onclick="updateBookingStatus(${data.booking_id}, 'checked_out')" ${data.status !== 'checked_in' ? 'disabled' : ''}>
                                    Check out
                                </button></li>
                                <li><button class="dropdown-item text-danger" onclick="updateBookingStatus(${data.booking_id}, 'cancelled')" ${['checked_out', 'cancelled'].includes(data.status) ? 'disabled' : ''}>
                                    Cancel booking
                                </button></li>
                            </ul>
                        </div>
                    `;
                }
            }
        ]
    });

    // Load initial data
    loadHotels();
    loadGuests();

    // Add event listeners for modals
    $('#addBookingModal').on('show.bs.modal', function() {
        loadHotels();
        loadGuests();
    });

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
                    $('#hotelFilter, select[name="hotel_id"]').html(options);
                }
            }
        });
    }

    // Load guests for dropdown
    function loadGuests() {
        $.ajax({
            url: '../../ajax/users/get_users.php',
            type: 'GET',
            data: { role: 'user' },
            success: function(response) {
                if(response.data) {
                    var guests = response.data;
                    var options = '<option value="">Select Guest</option>';
                    guests.forEach(function(guest) {
                        options += `<option value="${guest.id}">${guest.name} (${guest.email})</option>`;
                    });
                    $('select[name="user_id"]').html(options);
                }
            }
        });
    }

    // Load rooms when hotel is selected
    $('select[name="hotel_id"]').change(function() {
        var hotelId = $(this).val();
        if(hotelId) {
            $.ajax({
                url: '../../ajax/rooms/get_rooms.php',
                type: 'GET',
                data: { hotel_id: hotelId },
                success: function(response) {
                    if(response.data) {
                        var rooms = response.data;
                        var options = '<option value="">Select Room</option>';
                        rooms.forEach(function(room) {
                            if(room.is_available == 1) {
                                options += `<option value="${room.room_id}" 
                                    data-price="${room.price_per_night}"
                                    data-capacity="${room.capacity}">
                                    ${room.room_type} ($${room.price_per_night}/night)
                                </option>`;
                            }
                        });
                        $('select[name="room_id"]')
                            .html(options)
                            .prop('disabled', false);
                    }
                }
            });
        } else {
            $('select[name="room_id"]')
                .html('<option value="">Select Room</option>')
                .prop('disabled', true);
        }
    });

    // Calculate total price
    function calculateTotal() {
        var checkIn = new Date($('input[name="check_in_date"]').val());
        var checkOut = new Date($('input[name="check_out_date"]').val());
        var room = $('select[name="room_id"] option:selected');
        
        if(checkIn && checkOut && room.val()) {
            var nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
            var pricePerNight = parseFloat(room.data('price'));
            
            $('#totalNights').text(nights);
            $('#pricePerNight').text(pricePerNight.toFixed(2));
            $('#totalPrice').text((nights * pricePerNight).toFixed(2));
        }
    }

    $('input[name="check_in_date"], input[name="check_out_date"], select[name="room_id"]').change(calculateTotal);

    // Save booking
    $('#saveBooking').click(function() {
        var formData = new FormData($('#addBookingForm')[0]);
        formData.append('total_price', parseFloat($('#totalPrice').text()));
        
        $.ajax({
            url: '../../ajax/bookings/add_booking.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if(response.success) {
                    $('#addBookingModal').modal('hide');
                    $('#addBookingForm')[0].reset();
                    bookingsTable.ajax.reload();
                    toastr.success('Booking created successfully!');
                } else {
                    toastr.error(response.message || 'Error creating booking');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error creating booking:', error);
                toastr.error('Error creating booking');
            }
        });
    });

    // Load booking details for editing
    $(document).on('click', '.edit-booking', function() {
        var id = $(this).data('id');
        
        $.ajax({
            url: '../../ajax/bookings/get_booking.php',
            type: 'GET',
            data: { booking_id: id },
            success: function(response) {
                if(response.success && response.data) {
                    var booking = response.data;
                    
                    $('#editBookingForm input[name="booking_id"]').val(booking.booking_id);
                    $('#editBookingForm select[name="status"]').val(booking.status);
                    $('#editBookingForm input[name="guests"]').val(booking.guests);
                    $('#editBookingForm input[name="check_in_date"]').val(booking.check_in_date);
                    $('#editBookingForm input[name="check_out_date"]').val(booking.check_out_date);
                    $('#editBookingForm textarea[name="special_requests"]').val(booking.special_requests);
                    $('#editBookingForm input[name="total_price"]').val(booking.total_price);
                    
                    $('#guestName').val(booking.guest_name);
                    $('#hotelRoom').val(booking.hotel_name + ' - ' + booking.room_type);
                    
                    $('#editBookingModal').modal('show');
                } else {
                    toastr.error(response.message || 'Error loading booking details');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading booking details:', error);
                toastr.error('Error loading booking details');
            }
        });
    });

    // Update booking
    $('#updateBooking').click(function() {
        var formData = new FormData($('#editBookingForm')[0]);
        
        $.ajax({
            url: '../../ajax/bookings/edit_booking.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if(response.success) {
                    $('#editBookingModal').modal('hide');
                    bookingsTable.ajax.reload();
                    toastr.success('Booking updated successfully!');
                } else {
                    toastr.error(response.message || 'Error updating booking');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error updating booking:', error);
                toastr.error('Error updating booking');
            }
        });
    });

    // Quick status updates
    $(document).on('click', '.confirm-booking', function() {
        updateBookingStatus($(this).data('id'), 'confirmed');
    });

    $(document).on('click', '.checkin-booking', function() {
        updateBookingStatus($(this).data('id'), 'checked_in');
    });

    $(document).on('click', '.checkout-booking', function() {
        updateBookingStatus($(this).data('id'), 'checked_out');
    });

    $(document).on('click', '.cancel-booking, #cancelBooking', function() {
        var id = $(this).data('id') || $('#editBookingForm input[name="booking_id"]').val();
        if(confirm('Are you sure you want to cancel this booking?')) {
            updateBookingStatus(id, 'cancelled');
        }
    });

    function updateBookingStatus(bookingId, status) {
        $.ajax({
            url: '../../ajax/bookings/update_booking_status.php',
            type: 'POST',
            data: { 
                booking_id: bookingId,
                status: status
            },
            success: function(response) {
                if(response.success) {
                    $('#editBookingModal').modal('hide');
                    bookingsTable.ajax.reload();
                    toastr.success('Booking status updated successfully!');
                } else {
                    toastr.error(response.message || 'Error updating booking status');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error updating booking status:', error);
                toastr.error('Error updating booking status');
            }
        });
    }

    // Filter handling
    $('#hotelFilter, #statusFilter, #dateFilter').change(function() {
        bookingsTable.ajax.reload();
    });
});
</script>