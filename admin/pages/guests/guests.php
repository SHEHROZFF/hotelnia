<?php
include_once('../../../global.php');?>
<?php include_once "../../header.php" ?>




<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div
        class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Guest Management</h1>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Guest Management</h5>
            <h6 class="card-subtitle text-muted">Manage your hotel guests and their information</h6>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" id="searchInput" placeholder="Search guests...">
                    </div>
                </div>
                <div class="col-md-6 text-md-end">
                    <button class="btn btn-primary">Add New Guest</button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover" id="guestsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Visits</th>
                            <th>Last Stay</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>G001</td>
                            <td>John Doe</td>
                            <td>john.doe@example.com</td>
                            <td>+1 (555) 123-4567</td>
                            <td>3</td>
                            <td>Apr 18, 2025</td>
                            <td><span class="badge bg-success">Active</span></td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#">View profile</a></li>
                                        <li><a class="dropdown-item" href="#">Edit information</a></li>
                                        <li><a class="dropdown-item" href="#">Booking history</a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item" href="#">Create new booking</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>G002</td>
                            <td>Sarah Davis</td>
                            <td>sarah.davis@example.com</td>
                            <td>+1 (555) 234-5678</td>
                            <td>5</td>
                            <td>Apr 17, 2025</td>
                            <td><span class="badge bg-success">Active</span></td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#">View profile</a></li>
                                        <li><a class="dropdown-item" href="#">Edit information</a></li>
                                        <li><a class="dropdown-item" href="#">Booking history</a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item" href="#">Create new booking</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>G003</td>
                            <td>Robert Kim</td>
                            <td>robert.kim@example.com</td>
                            <td>+1 (555) 345-6789</td>
                            <td>1</td>
                            <td>Apr 22, 2025</td>
                            <td><span class="badge bg-success">Active</span></td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#">View profile</a></li>
                                        <li><a class="dropdown-item" href="#">Edit information</a></li>
                                        <li><a class="dropdown-item" href="#">Booking history</a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item" href="#">Create new booking</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>G004</td>
                            <td>Emily Johnson</td>
                            <td>emily.johnson@example.com</td>
                            <td>+1 (555) 456-7890</td>
                            <td>2</td>
                            <td>Apr 29, 2025</td>
                            <td><span class="badge bg-success">Active</span></td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#">View profile</a></li>
                                        <li><a class="dropdown-item" href="#">Edit information</a></li>
                                        <li><a class="dropdown-item" href="#">Booking history</a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item" href="#">Create new booking</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>G005</td>
                            <td>Michael Patel</td>
                            <td>michael.patel@example.com</td>
                            <td>+1 (555) 567-8901</td>
                            <td>7</td>
                            <td>May 9, 2025</td>
                            <td><span class="badge bg-success">Active</span></td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#">View profile</a></li>
                                        <li><a class="dropdown-item" href="#">Edit information</a></li>
                                        <li><a class="dropdown-item" href="#">Booking history</a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item" href="#">Create new booking</a></li>
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
    const table = $('#guestsTable').DataTable({
        responsive: true
    });

    // Search functionality
    $('#searchInput').on('keyup', function() {
        table.search(this.value).draw();
    });
});
</script>
<?php include_once "../../footer.php" ?>