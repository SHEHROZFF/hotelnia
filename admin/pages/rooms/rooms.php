<?php include_once('../../../global.php');?>
<?php include_once "../../header.php" ?>

<!-- Main content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Room Management</h1>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title">All Rooms</h5>
            <h6 class="card-subtitle text-muted">Manage hotel rooms and their details</h6>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6 d-flex gap-2">
                    <select class="form-select w-auto" id="hotelFilter">
                        <option value="">All Hotels</option>
                        <!-- Hotels will be loaded dynamically -->
                    </select>
                    <select class="form-select w-auto" id="availabilityFilter">
                        <option value="">All Status</option>
                        <option value="1">Available</option>
                        <option value="0">Unavailable</option>
                    </select>
                </div>
                <div class="col-md-6 text-md-end">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoomModal">
                        <i class="bi bi-plus-lg"></i> Add Room
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover" id="roomsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Hotel</th>
                            <th>Room Type</th>
                            <th>Capacity</th>
                            <th>Price/Night</th>
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

<!-- Add Room Modal -->
<div class="modal fade" id="addRoomModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Room</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addRoomForm" enctype="multipart/form-data">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Hotel</label>
                            <select class="form-select" name="hotel_id" required>
                                <!-- Hotels will be loaded dynamically -->
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Room Type</label>
                            <input type="text" class="form-control" name="room_type" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="room_description" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Price per Night</label>
                            <input type="number" class="form-control" name="price_per_night" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Capacity</label>
                            <input type="number" class="form-control" name="capacity" value="2" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Bed Type</label>
                            <select class="form-select" name="bed_type">
                                <option value="Twin">Twin</option>
                                <option value="Queen">Queen</option>
                                <option value="King">King</option>
                                <option value="Multiple">Multiple</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Room Size (sqm)</label>
                            <input type="text" class="form-control" name="room_size">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="is_available">
                                <option value="1">Available</option>
                                <option value="0">Unavailable</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Room Images</label>
                            <input type="file" class="form-control" name="room_images[]" multiple accept="image/*" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveRoom">Save Room</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Room Modal -->
<div class="modal fade" id="editRoomModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Room</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editRoomForm" enctype="multipart/form-data">
                    <input type="hidden" name="room_id">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Hotel</label>
                            <select class="form-select" name="hotel_id" required>
                                <!-- Hotels will be loaded dynamically -->
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Room Type</label>
                            <input type="text" class="form-control" name="room_type" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="room_description" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Price per Night</label>
                            <input type="number" class="form-control" name="price_per_night" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Capacity</label>
                            <input type="number" class="form-control" name="capacity" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Bed Type</label>
                            <select class="form-select" name="bed_type">
                                <option value="Twin">Twin</option>
                                <option value="Queen">Queen</option>
                                <option value="King">King</option>
                                <option value="Multiple">Multiple</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Room Size (sqm)</label>
                            <input type="text" class="form-control" name="room_size">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="is_available">
                                <option value="1">Available</option>
                                <option value="0">Unavailable</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Current Images</label>
                            <div class="room-images-container d-flex flex-wrap gap-2">
                                <!-- Room images will be loaded dynamically -->
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Add New Images</label>
                            <input type="file" class="form-control" name="room_images[]" multiple accept="image/*">
                            <small class="text-muted">Leave empty to keep existing images</small>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="updateRoom">Update Room</button>
            </div>
        </div>
    </div>
</div>

<?php include_once "../../footer.php"; ?>

<script>
$(document).ready(function() {
    // Load hotels for dropdowns
    function loadHotels() {
        $.ajax({
            url: '../../ajax/hotels/get_hotels.php',
            type: 'GET',
            success: function(response) {
                if(response.data) {
                    var hotels = response.data;
                    var options = '<option value="">Select Hotel</option>';
                    hotels.forEach(function(hotel) {
                        options += `<option value="${hotel.hotel_id}">${hotel.hotel_name}</option>`;
                    });
                    $('select[name="hotel_id"]').html(options);
                    $('#hotelFilter').html('<option value="">All Hotels</option>' + options);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading hotels:', error);
                toastr.error('Error loading hotels');
            }
        });
    }

    // Initialize DataTable
    var roomsTable = $('#roomsTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: '../../ajax/rooms/get_rooms.php',
            type: 'GET'
        },
        columns: [
            { data: 'room_id' },
            { data: 'hotel_name' },
            { data: 'room_type' },
            { 
                data: 'capacity',
                render: function(data) {
                    return data + ' guests';
                }
            },
            { 
                data: 'price_per_night',
                render: function(data) {
                    return '$' + parseFloat(data).toFixed(2);
                }
            },
            { 
                data: 'is_available',
                render: function(data) {
                    return data == 1 ? 
                        '<span class="badge bg-success">Available</span>' : 
                        '<span class="badge bg-danger">Unavailable</span>';
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
                                <li><button class="dropdown-item view-room" data-id="${data.room_id}">View details</button></li>
                                <li><button class="dropdown-item edit-room" data-id="${data.room_id}">Edit room</button></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><button class="dropdown-item text-danger delete-room" data-id="${data.room_id}">Delete room</button></li>
                            </ul>
                        </div>
                    `;
                }
            }
        ]
    });

    // Initial load of hotels
    loadHotels();

    // Filter handling
    $('#hotelFilter, #availabilityFilter').change(function() {
        var hotel = $('#hotelFilter').val();
        var availability = $('#availabilityFilter').val();
        
        roomsTable.ajax.url('../../ajax/rooms/get_rooms.php' + 
            (hotel ? '?hotel_id=' + hotel : '') +
            (availability ? (hotel ? '&' : '?') + 'is_available=' + availability : '')
        ).load();
    });

    // Save room
    $('#saveRoom').click(function() {
        var formData = new FormData($('#addRoomForm')[0]);
        
        $.ajax({
            url: '../../ajax/rooms/add_room.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if(response.success) {
                    $('#addRoomModal').modal('hide');
                    $('#addRoomForm')[0].reset();
                    roomsTable.ajax.reload();
                    toastr.success('Room added successfully!');
                } else {
                    toastr.error(response.message || 'Error adding room');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error adding room:', error);
                toastr.error('Error adding room');
            }
        });
    });

    // View room details
    $(document).on('click', '.view-room', function() {
        var id = $(this).data('id');
        
        // Remove any existing view modals
        $('.view-room-modal').remove();
        
        // Show loading notification
        toastr.info('Loading room details...');
        
        $.ajax({
            url: '../../ajax/rooms/get_room.php',
            type: 'GET',
            data: { room_id: id },
            success: function(response) {
                if(response.success && response.data) {
                    var room = response.data;
                    
                    // Create modal content
                    var modalContent = `
                        <div class="modal-header">
                            <h5 class="modal-title">${room.room_type}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Details</h6>
                                    <p><strong>Hotel:</strong> ${room.hotel_name || 'N/A'}</p>
                                    <p><strong>Capacity:</strong> ${room.capacity || 0} guests</p>
                                    <p><strong>Price per Night:</strong> $${parseFloat(room.price_per_night || 0).toFixed(2)}</p>
                                    <p><strong>Bed Type:</strong> ${room.bed_type || 'N/A'}</p>
                                    <p><strong>Room Size:</strong> ${room.room_size ? room.room_size + ' sqm' : 'N/A'}</p>
                                    <p><strong>Status:</strong> ${room.is_available == 1 ? 
                                        '<span class="badge bg-success">Available</span>' : 
                                        '<span class="badge bg-danger">Unavailable</span>'}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6>Booking Statistics</h6>
                                    <p><strong>Total Bookings:</strong> ${room.total_bookings || 0}</p>
                                    <p><strong>Current Booking:</strong> ${room.current_booking ? 'Yes' : 'No'}</p>
                                    ${room.current_booking_details ? `
                                        <div class="mt-2">
                                            <h6>Current Guest</h6>
                                            <p><strong>Name:</strong> ${room.current_booking_details.guest_name || 'N/A'}</p>
                                            <p><strong>Email:</strong> ${room.current_booking_details.guest_email || 'N/A'}</p>
                                        </div>
                                    ` : ''}
                                </div>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h6>Description</h6>
                                    <p>${room.room_description || 'No description available.'}</p>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-12">
                                    <h6>Images</h6>
                                    <div class="row">
                                        ${room.images && room.images.length > 0 ? 
                                            room.images.map(image => `
                                                <div class="col-md-4 mb-3">
                                                    <img src="${image.image_path}" 
                                                         class="img-fluid rounded" 
                                                         alt="Room Image"
                                                         style="width: 100%; height: 200px; object-fit: cover;">
                                                    ${image.is_primary ? '<span class="badge bg-primary position-absolute top-0 start-0 m-2">Primary</span>' : ''}
                                                </div>
                                            `).join('') : 
                                            '<div class="col-12"><p class="text-muted">No images available</p></div>'}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary edit-room" data-id="${room.room_id}">Edit Room</button>
                        </div>
                    `;

                    // Create modal element
                    var modalElement = document.createElement('div');
                    modalElement.className = 'modal fade view-room-modal';
                    modalElement.innerHTML = `<div class="modal-dialog modal-lg"><div class="modal-content">${modalContent}</div></div>`;
                    document.body.appendChild(modalElement);

                    // Initialize and show modal
                    var viewModal = new bootstrap.Modal(modalElement);
                    viewModal.show();

                    // Handle modal cleanup
                    modalElement.addEventListener('hidden.bs.modal', function() {
                        viewModal.dispose();
                        modalElement.remove();
                    });
                } else {
                    toastr.error(response.message || 'Error loading room details');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading room details:', error);
                toastr.error('Error loading room details. Please try again.');
            }
        });
    });

    // Edit room
    $(document).on('click', '.edit-room', function() {
        var id = $(this).data('id');
        $.ajax({
            url: '../../ajax/rooms/get_room.php',
            type: 'GET',
            data: { room_id: id },
            success: function(response) {
                if(response.success) {
                    var room = response.data;
                    
                    // Populate form fields
                    $('#editRoomForm input[name="room_id"]').val(room.room_id);
                    $('#editRoomForm select[name="hotel_id"]').val(room.hotel_id);
                    $('#editRoomForm input[name="room_type"]').val(room.room_type);
                    $('#editRoomForm textarea[name="room_description"]').val(room.room_description);
                    $('#editRoomForm input[name="price_per_night"]').val(room.price_per_night);
                    $('#editRoomForm input[name="capacity"]').val(room.capacity);
                    $('#editRoomForm select[name="bed_type"]').val(room.bed_type);
                    $('#editRoomForm input[name="room_size"]').val(room.room_size);
                    $('#editRoomForm select[name="is_available"]').val(room.is_available);
                    
                    // Display current images
                    var imagesHtml = '';
                    if(room.images && room.images.length > 0) {
                        room.images.forEach(function(image) {
                            imagesHtml += `
                                <div class="position-relative">
                                    <img src="${image.image_path}" class="img-thumbnail" style="width: 150px; height: 100px; object-fit: cover;">
                                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 delete-image" 
                                            data-image-id="${image.image_id}">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                            `;
                        });
                    } else {
                        imagesHtml = '<p class="text-muted">No images available</p>';
                    }
                    $('.room-images-container').html(imagesHtml);
                    
                    // Show edit modal
                    $('#editRoomModal').modal('show');
                } else {
                    toastr.error(response.message || 'Error loading room details');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading room details:', error);
                toastr.error('Error loading room details');
            }
        });
    });

    // Update room
    $('#updateRoom').click(function() {
        var formData = new FormData($('#editRoomForm')[0]);
        
        $.ajax({
            url: '../../ajax/rooms/edit_room.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if(response.success) {
                    $('#editRoomModal').modal('hide');
                    roomsTable.ajax.reload();
                    toastr.success('Room updated successfully!');
                } else {
                    toastr.error(response.message || 'Error updating room');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error updating room:', error);
                toastr.error('Error updating room');
            }
        });
    });

    // Delete room image
    $(document).on('click', '.delete-image', function(e) {
        e.preventDefault();
        var imageId = $(this).data('image-id');
        var imageElement = $(this).parent();
        
        if(confirm('Are you sure you want to delete this image?')) {
            $.ajax({
                url: '../../ajax/rooms/delete_room_image.php',
                type: 'POST',
                data: { image_id: imageId },
                success: function(response) {
                    if(response.success) {
                        imageElement.remove();
                        toastr.success('Image deleted successfully!');
                    } else {
                        toastr.error(response.message || 'Error deleting image');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error deleting image:', error);
                    toastr.error('Error deleting image');
                }
            });
        }
    });

    // Delete room
    $(document).on('click', '.delete-room', function() {
        var id = $(this).data('id');
        if(confirm('Are you sure you want to delete this room? This action cannot be undone.')) {
            $.ajax({
                url: '../../ajax/rooms/delete_room.php',
                type: 'POST',
                data: { room_id: id },
                success: function(response) {
                    if(response.success) {
                        roomsTable.ajax.reload();
                        toastr.success('Room deleted successfully!');
                    } else {
                        toastr.error(response.message || 'Error deleting room');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error deleting room:', error);
                    toastr.error('Error deleting room');
                }
            });
        }
    });
});
</script>