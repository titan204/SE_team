<?php
$pageTitle = 'Revenue Impact Report';
ob_start();
?>

<div class="container mt-4">
    <!-- Billing System: future folio-only reading can drive summary cards in this report. -->
    <!-- Housekeeping System: future operational cost references can appear as impact contributors. -->
    <!-- Reservation System: future booking volume influence can be reflected in revenue variance rows. -->

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= APP_URL ?>/dashboard">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= APP_URL ?>/revenue_manager_virtual_inventory">Virtual Inventory</a></li>
            <li class="breadcrumb-item active" aria-current="page">Revenue Impact Report</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0"><i class="bi bi-graph-up"></i> Revenue Impact Report</h2>
        <a href="<?= APP_URL ?>/revenue_manager_virtual_inventory" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-muted text-uppercase">Gross Virtual Revenue</small>
                    <h3 class="mt-2 mb-0">$86,250</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-muted text-uppercase">Cost Pressure</small>
                    <h3 class="mt-2 mb-0">$21,980</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-muted text-uppercase">Margin Signal</small>
                    <h3 class="mt-2 mb-0">74%</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-muted text-uppercase">Alerts</small>
                    <h3 class="mt-2 mb-0">3 Active</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-table"></i> Financial Summary Layout</h5>
            <span class="badge bg-secondary">Report Placeholder</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Segment</th>
                        <th>Revenue Signal</th>
                        <th>Cost Reference</th>
                        <th>Impact Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Corporate Rooms</td>
                        <td>$28,400</td>
                        <td>$8,500</td>
                        <td><span class="badge bg-success">Healthy</span></td>
                    </tr>
                    <tr>
                        <td>VIP Inventory</td>
                        <td>$17,900</td>
                        <td>$6,200</td>
                        <td><span class="badge bg-warning text-dark">Monitor</span></td>
                    </tr>
                    <tr>
                        <td>Extended Stay</td>
                        <td>$22,150</td>
                        <td>$4,980</td>
                        <td><span class="badge bg-info text-dark">Stable</span></td>
                    </tr>
                    <tr>
                        <td>Promotional Stock</td>
                        <td>$9,300</td>
                        <td>$2,300</td>
                        <td><span class="badge bg-secondary">Review</span></td>
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
