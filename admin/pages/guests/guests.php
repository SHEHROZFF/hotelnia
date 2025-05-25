<?php
include_once('../../../global.php');?>
<?php include_once "../../header.php" ?>

<!-- Main content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Guest Management</h1>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title">All Guests</h5>
            <h6 class="card-subtitle text-muted">Manage hotel guests and their information</h6>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-8 d-flex gap-2">
                    <select class="form-select w-auto" id="verificationFilter">
                        <option value="">All Verification Status</option>
                        <option value="1">Verified</option>
                        <option value="0">Unverified</option>
                    </select>
                    <select class="form-select w-auto" id="bookingFilter">
                        <option value="">All Booking Status</option>
                        <option value="active">Has Active Booking</option>
                        <option value="past">Has Past Bookings</option>
                        <option value="none">No Bookings</option>
                    </select>
                </div>
                <div class="col-md-4 text-md-end">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addGuestModal">
                        <i class="bi bi-plus-lg"></i> Add Guest
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover" id="guestsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Joined Date</th>
                            <th>Total Bookings</th>
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

<!-- Add Guest Modal -->
<div class="modal fade" id="addGuestModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Guest</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addGuestForm">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" required>
                            <small class="text-muted">Minimum 8 characters</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" name="confirm_password" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="email_verified" value="1">
                                <label class="form-check-label">Mark Email as Verified</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveGuest">Add Guest</button>
            </div>
        </div>
    </div>
</div>

<!-- View Guest Modal -->
<div class="modal fade" id="viewGuestModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Guest Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 border-end">
                        <h6 class="mb-3">Personal Information</h6>
                        <form id="editGuestForm">
                            <input type="hidden" name="user_id">
                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">New Password</label>
                                <input type="password" class="form-control" name="password">
                                <small class="text-muted">Leave empty to keep current password</small>
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="email_verified" value="1">
                                    <label class="form-check-label">Email Verified</label>
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary" id="updateGuest">Update Information</button>
                        </form>

                        <hr>

                        <h6 class="mb-3">Account Statistics</h6>
                        <div class="list-group list-group-flush">
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                Total Bookings
                                <span class="badge bg-primary rounded-pill" id="totalBookings">0</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                Active Bookings
                                <span class="badge bg-success rounded-pill" id="activeBookings">0</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                Cancelled Bookings
                                <span class="badge bg-danger rounded-pill" id="cancelledBookings">0</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                Total Spent
                                <span class="badge bg-info rounded-pill" id="totalSpent">$0</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <h6 class="mb-3">Booking History</h6>
                        <div class="table-responsive">
                            <table class="table table-sm" id="guestBookingsTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Hotel/Room</th>
                                        <th>Dates</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Bookings will be loaded dynamically -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="deleteGuest">Delete Guest</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php include_once "../../footer.php" ?>

<script>
$(document).ready(function() {
    // Initialize DataTable
    var guestsTable = $('#guestsTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: '../../ajax/guests/get_guests.php',
            type: 'GET',
            data: function(d) {
                d.verification = $('#verificationFilter').val();
                d.booking = $('#bookingFilter').val();
            }
        },
        columns: [
            { data: 'id' },
            { data: 'name' },
            { data: 'email' },
            { 
                data: 'created_at',
                render: function(data) {
                    return new Date(data).toLocaleDateString();
                }
            },
            { 
                data: 'total_bookings',
                render: function(data) {
                    return `<span class="badge bg-info">${data}</span>`;
                }
            },
            { 
                data: 'email_verified',
                render: function(data) {
                    return data == 1 ? 
                        '<span class="badge bg-success">Verified</span>' : 
                        '<span class="badge bg-warning">Unverified</span>';
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
                                <li><button class="dropdown-item view-guest" data-id="${data.id}">View details</button></li>
                                <li><button class="dropdown-item send-verification" data-id="${data.id}" ${data.email_verified == 1 ? 'disabled' : ''}>
                                    Send verification email
                                </button></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><button class="dropdown-item text-danger delete-guest" data-id="${data.id}">Delete guest</button></li>
                            </ul>
                        </div>
                    `;
                }
            }
        ]
    });

    // Save guest
    $('#saveGuest').click(function() {
        var formData = new FormData($('#addGuestForm')[0]);
        
        if (formData.get('password') !== $('#addGuestForm input[name="confirm_password"]').val()) {
            toastr.error('Passwords do not match');
            return;
        }
        
        $.ajax({
            url: '../../ajax/guests/add_guest.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if(response.success) {
                    $('#addGuestModal').modal('hide');
                    $('#addGuestForm')[0].reset();
                    guestsTable.ajax.reload();
                    toastr.success('Guest added successfully!');
                } else {
                    toastr.error(response.message || 'Error adding guest');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error adding guest:', error);
                toastr.error('Error adding guest');
            }
        });
    });

    // View guest details
    $(document).on('click', '.view-guest', function() {
        var id = $(this).data('id');
        
        // Show loading state
        $('#viewGuestModal .modal-content').addClass('loading');
        $('#viewGuestModal').modal('show');
        
        $.ajax({
            url: '../../ajax/guests/get_guest.php',
            type: 'GET',
            data: { user_id: id },
            success: function(response) {
                if(response.success && response.data) {
                    var guest = response.data;
                    
                    // Fill form fields
                    $('#editGuestForm input[name="user_id"]').val(guest.id);
                    $('#editGuestForm input[name="name"]').val(guest.name);
                    $('#editGuestForm input[name="email"]').val(guest.email);
                    $('#editGuestForm input[name="email_verified"]').prop('checked', guest.email_verified === 1);
                    
                    // Update statistics
                    $('#totalBookings').text(guest.total_bookings);
                    $('#activeBookings').text(guest.active_bookings);
                    $('#cancelledBookings').text(guest.cancelled_bookings);
                    $('#totalSpent').text('$' + parseFloat(guest.total_spent).toFixed(2));
                    
                    // Load booking history
                    var bookingsHtml = '';
                    if(guest.bookings && guest.bookings.length > 0) {
                        guest.bookings.forEach(function(booking) {
                            bookingsHtml += `
                                <tr>
                                    <td>${booking.booking_id}</td>
                                    <td>
                                        <div>${booking.hotel_name || 'N/A'}</div>
                                        <small class="text-muted">${booking.room_type || 'N/A'}</small>
                                    </td>
                                    <td>
                                        <div>${new Date(booking.check_in_date).toLocaleDateString()}</div>
                                        <small class="text-muted">${new Date(booking.check_out_date).toLocaleDateString()}</small>
                                    </td>
                                    <td>$${parseFloat(booking.total_price).toFixed(2)}</td>
                                    <td><span class="badge bg-${getStatusClass(booking.status)}">${booking.status}</span></td>
                                </tr>
                            `;
                        });
                    } else {
                        bookingsHtml = '<tr><td colspan="5" class="text-center">No bookings found</td></tr>';
                    }
                    $('#guestBookingsTable tbody').html(bookingsHtml);
                } else {
                    toastr.error(response.message || 'Error loading guest details');
                    $('#viewGuestModal').modal('hide');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading guest details:', error);
                toastr.error('Error loading guest details');
                $('#viewGuestModal').modal('hide');
            },
            complete: function() {
                // Remove loading state
                $('#viewGuestModal .modal-content').removeClass('loading');
            }
        });
    });

    // Update guest
    $('#updateGuest').click(function() {
        var formData = new FormData($('#editGuestForm')[0]);
        
        $.ajax({
            url: '../../ajax/guests/edit_guest.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if(response.success) {
                    guestsTable.ajax.reload();
                    toastr.success('Guest information updated successfully!');
                } else {
                    toastr.error(response.message || 'Error updating guest');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error updating guest:', error);
                toastr.error('Error updating guest');
            }
        });
    });

    // Send verification email
    $(document).on('click', '.send-verification', function() {
        var id = $(this).data('id');
        
        $.ajax({
            url: '../../ajax/guests/send_verification.php',
            type: 'POST',
            data: { user_id: id },
            success: function(response) {
                if(response.success) {
                    toastr.success('Verification email sent successfully!');
                } else {
                    toastr.error(response.message || 'Error sending verification email');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error sending verification email:', error);
                toastr.error('Error sending verification email');
            }
        });
    });

    // Delete guest
    $(document).on('click', '.delete-guest, #deleteGuest', function() {
        var id = $(this).data('id') || $('#editGuestForm input[name="user_id"]').val();
        
        if(confirm('Are you sure you want to delete this guest? This action cannot be undone.')) {
            $.ajax({
                url: '../../ajax/guests/delete_guest.php',
                type: 'POST',
                data: { user_id: id },
                success: function(response) {
                    if(response.success) {
                        $('#viewGuestModal').modal('hide');
                        guestsTable.ajax.reload();
                        toastr.success('Guest deleted successfully!');
                    } else {
                        toastr.error(response.message || 'Error deleting guest');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error deleting guest:', error);
                    toastr.error('Error deleting guest');
                }
            });
        }
    });

    // Filter handling
    $('#verificationFilter, #bookingFilter').change(function() {
        guestsTable.ajax.reload();
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

    // Add CSS for loading state
    $('<style>')
        .text(`
            .modal-content.loading {
                position: relative;
            }
            .modal-content.loading:after {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(255,255,255,0.8);
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .modal-content.loading:before {
                content: 'Loading...';
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                z-index: 1;
            }
        `)
        .appendTo('head');
});
</script>