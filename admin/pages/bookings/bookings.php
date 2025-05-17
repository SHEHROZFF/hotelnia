<?php
include_once('../../../global.php');?>
<?php include_once "../../header.php" ?>



<!-- Main content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div
        class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Bookings</h1>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title">All Bookings</h5>
            <h6 class="card-subtitle text-muted">Manage your hotel bookings and reservations</h6>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6 d-flex gap-2">
                    <select class="form-select w-auto" id="statusFilter">
                        <option value="">All Statuses</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="pending">Pending</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="col-md-6 text-md-end">
                    <button class="btn btn-primary">New Booking</button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover" id="bookingsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Guest</th>
                            <th>Room Type</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Status</th>
                            <th>Amount</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>B001</td>
                            <td>John Doe</td>
                            <td>Deluxe</td>
                            <td>Apr 15, 2025</td>
                            <td>Apr 18, 2025</td>
                            <td><span class="badge bg-success">Confirmed</span></td>
                            <td>$450.00</td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#">View details</a></li>
                                        <li><a class="dropdown-item" href="#">Edit booking</a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item text-danger" href="#">Cancel booking</a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>B002</td>
                            <td>Sarah Davis</td>
                            <td>Suite</td>
                            <td>Apr 12, 2025</td>
                            <td>Apr 17, 2025</td>
                            <td><span class="badge bg-success">Confirmed</span></td>
                            <td>$1,250.00</td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#">View details</a></li>
                                        <li><a class="dropdown-item" href="#">Edit booking</a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item text-danger" href="#">Cancel booking</a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>B003</td>
                            <td>Robert Kim</td>
                            <td>Standard</td>
                            <td>Apr 20, 2025</td>
                            <td>Apr 22, 2025</td>
                            <td><span class="badge bg-warning text-dark">Pending</span></td>
                            <td>$240.00</td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#">View details</a></li>
                                        <li><a class="dropdown-item" href="#">Edit booking</a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item text-danger" href="#">Cancel booking</a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>B004</td>
                            <td>Emily Johnson</td>
                            <td>Family</td>
                            <td>Apr 25, 2025</td>
                            <td>Apr 29, 2025</td>
                            <td><span class="badge bg-success">Confirmed</span></td>
                            <td>$780.00</td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#">View details</a></li>
                                        <li><a class="dropdown-item" href="#">Edit booking</a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item text-danger" href="#">Cancel booking</a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>B005</td>
                            <td>Michael Patel</td>
                            <td>Deluxe Suite</td>
                            <td>May 2, 2025</td>
                            <td>May 9, 2025</td>
                            <td><span class="badge bg-success">Confirmed</span></td>
                            <td>$2,100.00</td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#">View details</a></li>
                                        <li><a class="dropdown-item" href="#">Edit booking</a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item text-danger" href="#">Cancel booking</a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>B006</td>
                            <td>Lisa Chen</td>
                            <td>Standard</td>
                            <td>May 5, 2025</td>
                            <td>May 7, 2025</td>
                            <td><span class="badge bg-danger">Cancelled</span></td>
                            <td>$240.00</td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#">View details</a></li>
                                        <li><a class="dropdown-item" href="#">Edit booking</a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item text-danger" href="#">Cancel booking</a>
                                        </li>
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
    const table = $('#bookingsTable').DataTable({
        responsive: true,
        order: [
            [0, 'desc']
        ]
    });

    // Status filter
    $('#statusFilter').on('change', function() {
        const status = $(this).val();
        table.column(5).search(status).draw();
    });
});
</script>

<?php include_once "../../footer.php" ?>