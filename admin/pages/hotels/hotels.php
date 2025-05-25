<?php
// include_once "../../check_admin.php";
include_once('../../../global.php');?>
<?php include_once "../../header.php" ?>

<!-- Main content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Hotels</h1>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title">All Hotels</h5>
            <h6 class="card-subtitle text-muted">Manage your hotel properties and details</h6>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6 d-flex gap-2">
                    <select class="form-select w-auto" id="typeFilter">
                        <option value="">All Types</option>
                        <option value="Hotel">Hotel</option>
                        <option value="Resort">Resort</option>
                        <option value="Apartment">Apartment</option>
                        <option value="Lodge">Lodge</option>
                    </select>
                    <select class="form-select w-auto" id="statusFilter">
                        <option value="">All Statuses</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                <div class="col-md-6 text-md-end">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addHotelModal">
                        <i class="bi bi-plus-lg"></i> Add Hotel
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover" id="hotelsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Location</th>
                            <th>Type</th>
                            <th>Rating</th>
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

<!-- Add Hotel Modal -->
<div class="modal fade" id="addHotelModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Hotel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addHotelForm" enctype="multipart/form-data">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Hotel Name</label>
                            <input type="text" class="form-control" name="hotel_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Hotel Type</label>
                            <select class="form-select" name="hotel_type" required>
                                <option value="Hotel">Hotel</option>
                                <option value="Resort">Resort</option>
                                <option value="Apartment">Apartment</option>
                                <option value="Lodge">Lodge</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="hotel_description" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Address</label>
                            <input type="text" class="form-control" name="hotel_address" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">City</label>
                            <input type="text" class="form-control" name="city" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Country</label>
                            <input type="text" class="form-control" name="country" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Star Rating</label>
                            <select class="form-select" name="star_rating" required>
                                <option value="3">3 Stars</option>
                                <option value="4">4 Stars</option>
                                <option value="5">5 Stars</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Price Range Start</label>
                            <input type="number" class="form-control" name="price_range_start" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Price Range End</label>
                            <input type="number" class="form-control" name="price_range_end" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Hotel Images</label>
                            <input type="file" class="form-control" name="hotel_images[]" multiple accept="image/*" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Amenities</label>
                            <div class="amenities-container">
                                <!-- Amenities will be loaded dynamically -->
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveHotel">Save Hotel</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Hotel Modal -->
<div class="modal fade" id="editHotelModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Hotel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editHotelForm" enctype="multipart/form-data">
                    <input type="hidden" name="hotel_id" id="edit_hotel_id">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Hotel Name</label>
                            <input type="text" class="form-control" name="hotel_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Hotel Type</label>
                            <select class="form-select" name="hotel_type" required>
                                <option value="Hotel">Hotel</option>
                                <option value="Resort">Resort</option>
                                <option value="Apartment">Apartment</option>
                                <option value="Lodge">Lodge</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="hotel_description" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Address</label>
                            <input type="text" class="form-control" name="hotel_address" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">City</label>
                            <input type="text" class="form-control" name="city" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Country</label>
                            <input type="text" class="form-control" name="country" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Star Rating</label>
                            <select class="form-select" name="star_rating" required>
                                <option value="3">3 Stars</option>
                                <option value="4">4 Stars</option>
                                <option value="5">5 Stars</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Price Range Start</label>
                            <input type="number" class="form-control" name="price_range_start" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Price Range End</label>
                            <input type="number" class="form-control" name="price_range_end" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Additional Hotel Images</label>
                            <input type="file" class="form-control" name="hotel_images[]" multiple accept="image/*">
                            <small class="text-muted">Leave empty to keep existing images</small>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Amenities</label>
                            <div class="amenities-container">
                                <!-- Amenities will be loaded dynamically -->
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="updateHotel">Update Hotel</button>
            </div>
        </div>
    </div>
</div>

<?php include_once "../../footer.php"; ?>

<script>
$(document).ready(function() {
    // Initialize DataTable
    var hotelsTable = $('#hotelsTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: '../../ajax/hotels/get_hotels.php',
            type: 'GET',
            error: function(xhr, error, thrown) {
                console.error('DataTables AJAX error:', error, thrown);
                if (xhr.responseText) {
                    console.error('Server response:', xhr.responseText);
                }
                toastr.error('Error loading hotels: ' + error);
            }
        },
        columns: [
            { data: 'hotel_id' },
            { data: 'hotel_name' },
            { 
                data: null,
                render: function(data) {
                    return data.city + ', ' + data.country;
                }
            },
            { data: 'hotel_type' },
            { 
                data: 'star_rating',
                render: function(data) {
                    return '⭐'.repeat(data);
                }
            },
            { 
                data: 'is_active',
                render: function(data) {
                    return data == 1 ? 
                        '<span class="badge bg-success">Active</span>' : 
                        '<span class="badge bg-danger">Inactive</span>';
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
                                <li><button class="dropdown-item view-hotel" data-id="${data.hotel_id}">View details</button></li>
                                <li><button class="dropdown-item edit-hotel" data-id="${data.hotel_id}">Edit hotel</button></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><button class="dropdown-item text-danger delete-hotel" data-id="${data.hotel_id}">Delete hotel</button></li>
                            </ul>
                        </div>
                    `;
                }
            }
        ]
    });

    // Load amenities
    function loadAmenities() {
        $.ajax({
            url: '../../ajax/hotels/get_amenities.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (!response || !response.data) {
                    console.error('Invalid amenities response:', response);
                    $('.amenities-container').html('Error: No amenities data available');
                    return;
                }

                var amenities = response.data;
                if (!Array.isArray(amenities)) {
                    console.error('Invalid amenities data format:', amenities);
                    $('.amenities-container').html('Error: Invalid amenities data format');
                    return;
                }

                let html = '';
                amenities.forEach(function(amenity) {
                    html += `
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="amenities[]" 
                                   value="${amenity.amenity_id}" id="amenity${amenity.amenity_id}">
                            <label class="form-check-label" for="amenity${amenity.amenity_id}">
                                <i class="bi bi-${amenity.amenity_icon}"></i> ${amenity.amenity_name}
                            </label>
                        </div>
                    `;
                });
                
                if (html === '') {
                    html = 'No amenities available';
                }
                
                $('.amenities-container').html(html);
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', status, error);
                if (xhr.responseText) {
                    console.error('Server response:', xhr.responseText);
                }
                $('.amenities-container').html('Error loading amenities: ' + error);
            }
        });
    }

    // Initial load of amenities
    loadAmenities();

    // Save hotel
    $('#saveHotel').click(function() {
        var formData = new FormData($('#addHotelForm')[0]);
        formData.append('is_active', 1);
        
        $.ajax({
            url: '../../ajax/hotels/add_hotel.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if(response.success) {
                    $('#addHotelModal').modal('hide');
                    $('#addHotelForm')[0].reset();
                    hotelsTable.ajax.reload();
                    toastr.success('Hotel added successfully!');
                } else {
                    toastr.error(response.message || 'Error adding hotel');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', status, error);
                if (xhr.responseText) {
                    console.error('Server response:', xhr.responseText);
                }
                toastr.error('Error adding hotel: ' + error);
            }
        });
    });

    // View hotel details
    $(document).on('click', '.view-hotel', function() {
        var id = $(this).data('id');
        
        // Show loading state
        toastr.info('Loading hotel details...');
        
        $.ajax({
            url: '../../ajax/hotels/get_hotel.php',
            type: 'GET',
            data: { hotel_id: id },
            dataType: 'json',
            success: function(response) {
                if(response.success && response.data) {
                    var hotel = response.data;
                    
                    // Remove any existing view modal
                    $('.view-hotel-modal').remove();
                    
                    // Create modal content
                    var modalContent = `
                        <div class="modal-header">
                            <h5 class="modal-title">${hotel.hotel_name}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Details</h6>
                                    <p><strong>Type:</strong> ${hotel.hotel_type}</p>
                                    <p><strong>Location:</strong> ${hotel.city}, ${hotel.country}</p>
                                    <p><strong>Address:</strong> ${hotel.hotel_address}</p>
                                    <p><strong>Rating:</strong> ${'⭐'.repeat(hotel.star_rating)}</p>
                                    <p><strong>Price Range:</strong> $${hotel.price_range_start} - $${hotel.price_range_end}</p>
                                    <p><strong>Status:</strong> ${hotel.is_active == 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>'}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6>Statistics</h6>
                                    <p><strong>Total Rooms:</strong> ${hotel.total_rooms || 0}</p>
                                    <p><strong>Total Bookings:</strong> ${hotel.total_bookings || 0}</p>
                                </div>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h6>Description</h6>
                                    <p>${hotel.hotel_description}</p>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-12">
                                    <h6>Amenities</h6>
                                    <div class="d-flex flex-wrap gap-2">
                                        ${hotel.amenities && hotel.amenities.length > 0 ? 
                                            hotel.amenities.map(amenity => `
                                                <span class="badge bg-secondary">
                                                    <i class="bi bi-${amenity.amenity_icon}"></i> ${amenity.amenity_name}
                                                </span>
                                            `).join('') : 
                                            '<p class="text-muted">No amenities listed</p>'
                                        }
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-12">
                                    <h6>Images</h6>
                                    <div class="row">
                                        ${hotel.images && hotel.images.length > 0 ? 
                                            hotel.images.map(image => `
                                                <div class="col-md-4 mb-3">
                                                    <img src="${image.image_path}" class="img-fluid rounded" alt="Hotel Image">
                                                </div>
                                            `).join('') : 
                                            '<p class="text-muted">No images available</p>'
                                        }
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-12">
                                    <h6>Rooms</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Type</th>
                                                    <th>Price/Night</th>
                                                    <th>Capacity</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                ${hotel.rooms && hotel.rooms.length > 0 ? 
                                                    hotel.rooms.map(room => `
                                                        <tr>
                                                            <td>${room.room_type}</td>
                                                            <td>$${room.price_per_night}</td>
                                                            <td>${room.capacity} guests</td>
                                                            <td>${room.is_available == 1 ? '<span class="badge bg-success">Available</span>' : '<span class="badge bg-danger">Unavailable</span>'}</td>
                                                        </tr>
                                                    `).join('') : 
                                                    '<tr><td colspan="4" class="text-center">No rooms available</td></tr>'
                                                }
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary edit-hotel" data-id="${hotel.hotel_id}">Edit Hotel</button>
                        </div>
                    `;

                    // Create modal element
                    var modalElement = document.createElement('div');
                    modalElement.className = 'modal fade view-hotel-modal';
                    modalElement.innerHTML = `<div class="modal-dialog modal-lg"><div class="modal-content">${modalContent}</div></div>`;
                    document.body.appendChild(modalElement);
                    
                    // Initialize and show modal
                    var viewModal = new bootstrap.Modal(modalElement);
                    viewModal.show();
                    
                    // Remove modal from DOM when hidden
                    modalElement.addEventListener('hidden.bs.modal', function() {
                        viewModal.dispose();
                        modalElement.remove();
                    });
                } else {
                    toastr.error(response.message || 'Error loading hotel details');
                    console.error('Server response:', response);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', status, error);
                console.error('Status code:', xhr.status);
                console.error('Response text:', xhr.responseText);
                toastr.error('Error loading hotel details. Please try again.');
            }
        });
    });

    // Edit hotel
    $(document).on('click', '.edit-hotel', function() {
        var id = $(this).data('id');
        $.ajax({
            url: '../../ajax/hotels/get_hotel.php',
            type: 'GET',
            data: { hotel_id: id },
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    var hotel = response.data;
                    
                    // Populate form fields
                    $('#edit_hotel_id').val(hotel.hotel_id);
                    $('#editHotelForm input[name="hotel_name"]').val(hotel.hotel_name);
                    $('#editHotelForm select[name="hotel_type"]').val(hotel.hotel_type);
                    $('#editHotelForm textarea[name="hotel_description"]').val(hotel.hotel_description);
                    $('#editHotelForm input[name="hotel_address"]').val(hotel.hotel_address);
                    $('#editHotelForm input[name="city"]').val(hotel.city);
                    $('#editHotelForm input[name="country"]').val(hotel.country);
                    $('#editHotelForm select[name="star_rating"]').val(hotel.star_rating);
                    $('#editHotelForm input[name="price_range_start"]').val(hotel.price_range_start);
                    $('#editHotelForm input[name="price_range_end"]').val(hotel.price_range_end);
                    
                    // Check amenities
                    $('#editHotelForm input[name="amenities[]"]').prop('checked', false);
                    hotel.amenities.forEach(function(amenity) {
                        $('#editHotelForm input[name="amenities[]"][value="' + amenity.amenity_id + '"]').prop('checked', true);
                    });
                    
                    // Show edit modal
                    $('#editHotelModal').modal('show');
                } else {
                    toastr.error(response.message || 'Error loading hotel details');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', status, error);
                if (xhr.responseText) {
                    console.error('Server response:', xhr.responseText);
                }
                toastr.error('Error loading hotel details: ' + error);
            }
        });
    });

    // Update hotel
    $('#updateHotel').click(function() {
        var formData = new FormData($('#editHotelForm')[0]);
        formData.append('is_active', 1);
        
        $.ajax({
            url: '../../ajax/hotels/edit_hotel.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if(response.success) {
                    $('#editHotelModal').modal('hide');
                    hotelsTable.ajax.reload();
                    toastr.success('Hotel updated successfully!');
                } else {
                    toastr.error(response.message || 'Error updating hotel');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', status, error);
                if (xhr.responseText) {
                    console.error('Server response:', xhr.responseText);
                }
                toastr.error('Error updating hotel: ' + error);
            }
        });
    });

    // Delete hotel
    $(document).on('click', '.delete-hotel', function() {
        var id = $(this).data('id');
        if(confirm('Are you sure you want to delete this hotel? This action cannot be undone.')) {
            $.ajax({
                url: '../../ajax/hotels/delete_hotel.php',
                type: 'POST',
                data: { hotel_id: id },
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        hotelsTable.ajax.reload();
                        toastr.success('Hotel deleted successfully!');
                    } else {
                        toastr.error(response.message || 'Error deleting hotel');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', status, error);
                    if (xhr.responseText) {
                        console.error('Server response:', xhr.responseText);
                    }
                    toastr.error('Error deleting hotel: ' + error);
                }
            });
        }
    });
});
</script> 