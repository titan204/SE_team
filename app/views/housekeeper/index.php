<?php
$pageTitle = 'Housekeeper Workspace';
ob_start();
?>

<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= APP_URL ?>/dashboard">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Housekeeper</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1"><i class="bi bi-brush"></i> Housekeeper Workspace</h2>
            <p class="text-muted mb-0">Scaffold view for room readiness, assignment flow, and front-desk dependency hooks.</p>
        </div>
        <a href="<?= APP_URL ?>/housekeeper/create" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> New Assignment
        </a>
    </div>

    <div class="alert alert-info">
        This module is a UI-only scaffold. Reservation, billing, and housekeeping logic will be connected later.
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-muted text-uppercase">Assigned Rooms</small>
                    <h3 class="mt-2 mb-0">12</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-muted text-uppercase">Ready For Inspection</small>
                    <h3 class="mt-2 mb-0">4</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-muted text-uppercase">VIP Arrival Hooks</small>
                    <h3 class="mt-2 mb-0">2</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-muted text-uppercase">Maintenance Flags</small>
                    <h3 class="mt-2 mb-0">1</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-list-task"></i> Assignment Board</h5>
            <span class="badge bg-secondary">Placeholder Data</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Room</th>
                        <th>Task</th>
                        <th>Room State</th>
                        <th>Reservation Hook</th>
                        <th>Readiness</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>305</strong></td>
                        <td>Departure Clean</td>
                        <td><span class="badge bg-danger">Dirty</span></td>
                        <td>Early check-in pending</td>
                        <td><span class="badge bg-warning text-dark">In Cleaning</span></td>
                        <td>
                            <a href="<?= APP_URL ?>/housekeeper/show/1" class="btn btn-sm btn-outline-secondary">Details</a>
                            <a href="<?= APP_URL ?>/housekeeper/edit/1" class="btn btn-sm btn-outline-primary">Edit</a>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>418</strong></td>
                        <td>VIP Refresh</td>
                        <td><span class="badge bg-warning text-dark">Cleaning</span></td>
                        <td>Upgrade trigger watch</td>
                        <td><span class="badge bg-info text-dark">Supervisor Review</span></td>
                        <td>
                            <a href="<?= APP_URL ?>/housekeeper/show/2" class="btn btn-sm btn-outline-secondary">Details</a>
                            <a href="<?= APP_URL ?>/housekeeper/edit/2" class="btn btn-sm btn-outline-primary">Edit</a>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>221</strong></td>
                        <td>Inspection Hold</td>
                        <td><span class="badge bg-info text-dark">Ready</span></td>
                        <td>Check-in release required</td>
                        <td><span class="badge bg-secondary">Awaiting Approval</span></td>
                        <td>
                            <a href="<?= APP_URL ?>/housekeeper/show/3" class="btn btn-sm btn-outline-secondary">Details</a>
                            <a href="<?= APP_URL ?>/housekeeper/edit/3" class="btn btn-sm btn-outline-primary">Edit</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require VIEW_PATH . '/layouts/main.php';
?>
