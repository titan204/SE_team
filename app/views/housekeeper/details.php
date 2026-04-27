<?php
$pageTitle = 'Housekeeper Details';
ob_start();
?>

<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= APP_URL ?>/dashboard">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= APP_URL ?>/housekeeper">Housekeeper</a></li>
            <li class="breadcrumb-item active" aria-current="page">Assignment Details</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0"><i class="bi bi-door-open"></i> Room 305 Assignment</h2>
        <a href="<?= APP_URL ?>/housekeeper/edit/1" class="btn btn-outline-primary">
            <i class="bi bi-pencil-square"></i> Edit Placeholder
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-clipboard-check"></i> Readiness Snapshot</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr><th>Task Type</th><td>Departure Clean</td></tr>
                        <tr><th>Assigned To</th><td>Housekeeper Placeholder</td></tr>
                        <tr><th>Room State</th><td><span class="badge bg-warning text-dark">In Cleaning</span></td></tr>
                        <tr><th>Inspection Status</th><td><span class="badge bg-secondary">Pending Supervisor</span></td></tr>
                        <tr><th>Maintenance Flag</th><td>No active hold</td></tr>
                        <tr><th>Front Desk Dependency</th><td>Early check-in validation</td></tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-arrow-left-right"></i> Integration Hooks</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Reservation Workflow Hook</label>
                        <input type="text" class="form-control" value="Check-in pending release after readiness confirmation" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">VIP / Upgrade Trigger</label>
                        <input type="text" class="form-control" value="VIP preparation hook available" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Housekeeping Notes</label>
                        <textarea class="form-control" rows="4" readonly>Placeholder note area for room readiness comments, consumables, and inspection handoff.</textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="<?= APP_URL ?>/housekeeper" class="btn btn-secondary">Back</a>
                        <a href="<?= APP_URL ?>/supervisor/show/1" class="btn btn-outline-dark">Open Supervisor View</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require VIEW_PATH . '/layouts/main.php';
?>
