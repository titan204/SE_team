<?php
$pageTitle = 'Supervisor Workspace';
ob_start();
?>

<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= APP_URL ?>/dashboard">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Supervisor</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1"><i class="bi bi-clipboard2-pulse"></i> Supervisor Workspace</h2>
            <p class="text-muted mb-0">Scaffold view for inspection approval, housekeeping oversight, and maintenance coordination.</p>
        </div>
        <a href="<?= APP_URL ?>/supervisor/create" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> New Oversight Record
        </a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-muted text-uppercase">Pending Inspections</small>
                    <h3 class="mt-2 mb-0">6</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-muted text-uppercase">Approval Queue</small>
                    <h3 class="mt-2 mb-0">3</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-muted text-uppercase">Maintenance Holds</small>
                    <h3 class="mt-2 mb-0">2</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-muted text-uppercase">RBAC Hooks</small>
                    <h3 class="mt-2 mb-0">Ready</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-shield-check"></i> Oversight Queue</h5>
            <span class="badge bg-secondary">Placeholder Data</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Room</th>
                        <th>Housekeeping Stage</th>
                        <th>Maintenance</th>
                        <th>Approval Need</th>
                        <th>Front Desk Impact</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>418</strong></td>
                        <td><span class="badge bg-info text-dark">Inspecting</span></td>
                        <td>Clear</td>
                        <td>Release room</td>
                        <td>VIP arrival waiting</td>
                        <td>
                            <a href="<?= APP_URL ?>/supervisor/show/1" class="btn btn-sm btn-outline-secondary">Details</a>
                            <a href="<?= APP_URL ?>/supervisor/edit/1" class="btn btn-sm btn-outline-primary">Edit</a>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>221</strong></td>
                        <td><span class="badge bg-warning text-dark">Ready</span></td>
                        <td>Flagged</td>
                        <td>Maintenance clearance</td>
                        <td>Check-in blocked</td>
                        <td>
                            <a href="<?= APP_URL ?>/supervisor/show/2" class="btn btn-sm btn-outline-secondary">Details</a>
                            <a href="<?= APP_URL ?>/supervisor/edit/2" class="btn btn-sm btn-outline-primary">Edit</a>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>507</strong></td>
                        <td><span class="badge bg-secondary">Awaiting Review</span></td>
                        <td>Clear</td>
                        <td>Quality sign-off</td>
                        <td>Upgrade trigger review</td>
                        <td>
                            <a href="<?= APP_URL ?>/supervisor/show/3" class="btn btn-sm btn-outline-secondary">Details</a>
                            <a href="<?= APP_URL ?>/supervisor/edit/3" class="btn btn-sm btn-outline-primary">Edit</a>
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
