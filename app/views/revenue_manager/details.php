<?php
$pageTitle = 'Revenue Manager Details';
ob_start();
?>

<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= APP_URL ?>/dashboard">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= APP_URL ?>/revenue_manager">Revenue Manager</a></li>
            <li class="breadcrumb-item active" aria-current="page">Report Details</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0"><i class="bi bi-receipt-cutoff"></i> Daily Revenue Summary</h2>
        <a href="<?= APP_URL ?>/revenue_manager/edit/1" class="btn btn-outline-primary">
            <i class="bi bi-pencil-square"></i> Edit Placeholder
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-cash-stack"></i> Billing Snapshot</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr><th>Summary Type</th><td>Read-only revenue panel</td></tr>
                        <tr><th>Period</th><td>Today</td></tr>
                        <tr><th>Folio Source</th><td>Aggregation hook placeholder</td></tr>
                        <tr><th>Audit Trail</th><td>Enabled for reporting actions</td></tr>
                        <tr><th>Upgrade Trigger</th><td>VIP monitor available</td></tr>
                        <tr><th>Status</th><td><span class="badge bg-success">Snapshot Ready</span></td></tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-link-45deg"></i> Integration Panels</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Billing Summary Hook</label>
                        <input type="text" class="form-control" value="Read-only billing totals and open-balance summary placeholder" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Folio Aggregation Hook</label>
                        <input type="text" class="form-control" value="Grouped folio and invoice feed placeholder" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Audit Notes</label>
                        <textarea class="form-control" rows="4" readonly>Placeholder audit trail notes for report generation, financial review, and access logging.</textarea>
                    </div>
                    <a href="<?= APP_URL ?>/reports/revenue" class="btn btn-outline-dark">Open Existing Revenue Report</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require VIEW_PATH . '/layouts/main.php';
?>
