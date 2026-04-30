<?php
$pageTitle = 'Room Cost Analysis';
ob_start();
?>

<div class="container mt-4">
    <!-- Housekeeping System: future cleaning and turnaround cost references can feed this table. -->
    <!-- Billing System: future read-only folio allocations can be summarized beside room cost totals. -->
    <!-- Reservation System: future occupancy and booking pressure can influence displayed cost context. -->

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= APP_URL ?>/dashboard">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= APP_URL ?>/revenue_manager_virtual_inventory">Virtual Inventory</a></li>
            <li class="breadcrumb-item active" aria-current="page">Room Cost Analysis</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0"><i class="bi bi-door-open"></i> Room Cost Analysis</h2>
        <a href="<?= APP_URL ?>/revenue_manager_virtual_inventory" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-muted text-uppercase">Tracked Rooms</small>
                    <h3 class="mt-2 mb-0">120</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-muted text-uppercase">Average Virtual Cost</small>
                    <h3 class="mt-2 mb-0">$153</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-muted text-uppercase">Highest Impact Tier</small>
                    <h3 class="mt-2 mb-0">Executive Floor</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-table"></i> Room Virtual Cost Breakdown</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Room</th>
                        <th>Housekeeping Reference</th>
                        <th>Service Cost Layer</th>
                        <th>Folio Visibility</th>
                        <th>Revenue Effect</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>101</strong></td>
                        <td>Standard Turnover</td>
                        <td>$95</td>
                        <td>Read-only linked</td>
                        <td><span class="badge bg-success">Low Impact</span></td>
                    </tr>
                    <tr>
                        <td><strong>305</strong></td>
                        <td>Priority Ready Room</td>
                        <td>$148</td>
                        <td>Pending summary</td>
                        <td><span class="badge bg-warning text-dark">Medium Impact</span></td>
                    </tr>
                    <tr>
                        <td><strong>418</strong></td>
                        <td>VIP Preparation</td>
                        <td>$212</td>
                        <td>Visible in report layer</td>
                        <td><span class="badge bg-danger">High Impact</span></td>
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
