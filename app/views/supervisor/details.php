<?php
$pageTitle = 'Supervisor Details';
ob_start();
?>

<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= APP_URL ?>/dashboard">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= APP_URL ?>/supervisor">Supervisor</a></li>
            <li class="breadcrumb-item active" aria-current="page">Oversight Details</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0"><i class="bi bi-check2-square"></i> Oversight Record - Room 418</h2>
        <a href="<?= APP_URL ?>/supervisor/edit/1" class="btn btn-outline-primary">
            <i class="bi bi-pencil-square"></i> Edit Placeholder
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-award"></i> Quality Inspection</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr><th>Room State</th><td><span class="badge bg-info text-dark">Inspecting</span></td></tr>
                        <tr><th>Reviewed By</th><td>Supervisor Placeholder</td></tr>
                        <tr><th>Housekeeper</th><td>Assigned Staff Placeholder</td></tr>
                        <tr><th>Inspection Outcome</th><td><span class="badge bg-secondary">Pending Approval</span></td></tr>
                        <tr><th>Maintenance Status</th><td>No blocking issue</td></tr>
                        <tr><th>RBAC Mode</th><td>Supervisor-only approval placeholder</td></tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-diagram-3"></i> Workflow Hooks</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Approval Endpoint</label>
                        <input type="text" class="form-control" value="/supervisor/update/{id}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Front Desk Release Dependency</label>
                        <input type="text" class="form-control" value="Check-in remains blocked until approval is confirmed" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Escalation Notes</label>
                        <textarea class="form-control" rows="4" readonly>Placeholder notes for inspection follow-up, quality score review, and maintenance escalation.</textarea>
                    </div>
                    <a href="<?= APP_URL ?>/housekeeper/show/2" class="btn btn-outline-dark">Open Housekeeper View</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require VIEW_PATH . '/layouts/main.php';
?>
