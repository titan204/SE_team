<?php
$pageTitle = 'Revenue Manager Virtual Inventory';
ob_start();
?>

<div class="container mt-4">
    <!-- Billing System: future read-only folio summary widgets can surface here. -->
    <!-- Housekeeping System: future room-cost reference widgets can surface here. -->
    <!-- Reservation System: future booking-impact widgets can surface here. -->

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= APP_URL ?>/dashboard">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Virtual Inventory</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1"><i class="bi bi-cash-coin"></i> Revenue Manager Virtual Inventory</h2>
            <p class="text-muted mb-0">Structural dashboard for virtual cost tracking, guest consumption review, and revenue impact analysis.</p>
        </div>
        <a href="<?= APP_URL ?>/revenue_manager" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Revenue Manager
        </a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-muted text-uppercase">Room Cost Watch</small>
                    <h3 class="mt-2 mb-1">$18,400</h3>
                    <p class="text-muted mb-3">Placeholder virtual cost total by room cluster.</p>
                    <a href="<?= APP_URL ?>/revenue_manager_virtual_inventory/roomCostAnalysis/101" class="btn btn-sm btn-outline-primary">Open Section</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-muted text-uppercase">Guest Consumption</small>
                    <h3 class="mt-2 mb-1">324</h3>
                    <p class="text-muted mb-3">Placeholder tracked profiles with virtual consumption indicators.</p>
                    <a href="<?= APP_URL ?>/revenue_manager_virtual_inventory/guestConsumption/501" class="btn btn-sm btn-outline-primary">Open Section</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-muted text-uppercase">Department Costs</small>
                    <h3 class="mt-2 mb-1">8</h3>
                    <p class="text-muted mb-3">Placeholder breakdown channels for service and operations cost layers.</p>
                    <a href="<?= APP_URL ?>/revenue_manager_virtual_inventory/departmentCostBreakdown" class="btn btn-sm btn-outline-primary">Open Section</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-muted text-uppercase">Alert Triggers</small>
                    <h3 class="mt-2 mb-1">5</h3>
                    <p class="text-muted mb-3">Placeholder threshold checks for cost limits and impact alerts.</p>
                    <a href="<?= APP_URL ?>/revenue_manager_virtual_inventory/triggerAlerts" class="btn btn-sm btn-outline-primary">Open Section</a>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-grid-1x2"></i> Dashboard Sections</h5>
            <span class="badge bg-secondary">UI Placeholder</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Section</th>
                        <th>Purpose</th>
                        <th>Integration Reference</th>
                        <th>Route</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Dashboard</strong></td>
                        <td>Virtual inventory overview</td>
                        <td>Billing, housekeeping, reservations</td>
                        <td><a href="<?= APP_URL ?>/revenue_manager_virtual_inventory/dashboard" class="btn btn-sm btn-outline-secondary">Open</a></td>
                    </tr>
                    <tr>
                        <td><strong>Room Cost Analysis</strong></td>
                        <td>Per-room virtual cost structure</td>
                        <td>Housekeeping reference + folio visibility</td>
                        <td><a href="<?= APP_URL ?>/revenue_manager_virtual_inventory/roomCostAnalysis/101" class="btn btn-sm btn-outline-secondary">Open</a></td>
                    </tr>
                    <tr>
                        <td><strong>Guest Consumption</strong></td>
                        <td>Per-guest virtual consumption profile</td>
                        <td>Billing reference + reservation influence</td>
                        <td><a href="<?= APP_URL ?>/revenue_manager_virtual_inventory/guestConsumption/501" class="btn btn-sm btn-outline-secondary">Open</a></td>
                    </tr>
                    <tr>
                        <td><strong>Revenue Impact Report</strong></td>
                        <td>Impact summary and financial abstraction layer</td>
                        <td>Reservation influence + folio summary</td>
                        <td><a href="<?= APP_URL ?>/revenue_manager_virtual_inventory/revenueImpactReport" class="btn btn-sm btn-outline-secondary">Open</a></td>
                    </tr>
                    <tr>
                        <td><strong>Limit Check</strong></td>
                        <td>Threshold monitoring structure</td>
                        <td>Cost limit references only</td>
                        <td><a href="<?= APP_URL ?>/revenue_manager_virtual_inventory/limitCheck" class="btn btn-sm btn-outline-secondary">Open</a></td>
                    </tr>
                    <tr>
                        <td><strong>Trigger Alerts</strong></td>
                        <td>Alert-ready structural endpoint</td>
                        <td>Cross-module notification placeholder</td>
                        <td><a href="<?= APP_URL ?>/revenue_manager_virtual_inventory/triggerAlerts" class="btn btn-sm btn-outline-secondary">Open</a></td>
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
