<?php
include_once('../../../global.php');?>
<?php include_once "../../header.php" ?>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div
        class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Room Management</h1>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Room Management</h5>
            <h6 class="card-subtitle text-muted">Manage your hotel rooms and their availability</h6>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6 d-flex gap-2">
                    <select class="form-select w-auto" id="statusFilter">
                        <option value="">All Statuses</option>
                        <option value="available">Available</option>
                        <option value="occupied">Occupied</option>
                        <option value="maintenance">Maintenance</option>
                    </select>
                </div>
                <div class="col-md-6 text-md-end">
                    <button class="btn btn-primary">Add New Room</button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover" id="roomsTable">
                    <thead>
                        <tr>
                            <th>Room</th>
                            <th>Type</th>
                            <th>Capacity</th>
                            <th>Price/Night</th>
                            <th>Status</th>
                            <th>Amenities</th>
                            <th>Floor</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>R101</td>
                            <td>Standard</td>
                            <td>2 persons</td>
                            <td>$120.00</td>
                            <td><span class="badge bg-success">Available</span></td>
                            <td>Wi-Fi, TV, AC</td>
                            <td>1st Floor</td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#">View details</a></li>
                                        <li><a class="dropdown-item" href="#">Edit room</a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item" href="#">Set as maintenance</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>R102</td>
                            <td>Standard</td>
                            <td>2 persons</td>
                            <td>$120.00</td>
                            <td><span class="badge bg-primary">Occupied</span></td>
                            <td>Wi-Fi, TV, AC</td>
                            <td>1st Floor</td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#">View details</a></li>
                                        <li><a class="dropdown-item" href="#">Edit room</a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item" href="#">Set as maintenance</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>R201</td>
                            <td>Deluxe</td>
                            <td>2 persons</td>
                            <td>$150.00</td>
                            <td><span class="badge bg-success">Available</span></td>
                            <td>Wi-Fi, TV, AC, Mini Bar</td>
                            <td>2nd Floor</td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#">View details</a></li>
                                        <li><a class="dropdown-item" href="#">Edit room</a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item" href="#">Set as maintenance</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>R202</td>
                            <td>Deluxe</td>
                            <td>2 persons</td>
                            <td>$150.00</td>
                            <td><span class="badge bg-warning text-dark">Maintenance</span></td>
                            <td>Wi-Fi, TV, AC, Mini Bar</td>
                            <td>2nd Floor</td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#">View details</a></li>
                                        <li><a class="dropdown-item" href="#">Edit room</a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item" href="#">Set as available</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>R301</td>
                            <td>Suite</td>
                            <td>4 persons</td>
                            <td>$250.00</td>
                            <td><span class="badge bg-success">Available</span></td>
                            <td>Wi-Fi, TV, AC, Mini Bar, Jacuzzi</td>
                            <td>3rd Floor</td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#">View details</a></li>
                                        <li><a class="dropdown-item" href="#">Edit room</a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item" href="#">Set as maintenance</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>



<script>
$(document).ready(function() {
    // Initialize DataTable
    const table = $('#roomsTable').DataTable({
        responsive: true
    });

    // Status filter
    $('#statusFilter').on('change', function() {
        const status = $(this).val();
        table.column(4).search(status).draw();
    });
});
</script>
<?php include_once "../../footer.php" ?>