<?php
$pageTitle = 'Trigger Alerts';
ob_start();
?>

<div class="container mt-4">
    <!-- Cross-Module Notification Placeholder: alert triggers are structural scaffolds. -->
    <!-- Billing System: future cost overrun alerts from folio data can fire here. -->
    <!-- Housekeeping System: future cost-spike notifications from operations can route here. -->
    <!-- Reservation System: future overbooking and demand-surge alerts can surface here. -->

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= APP_URL ?>/dashboard">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= APP_URL ?>/revenue_manager_virtual_inventory">Virtual Inventory</a></li>
            <li class="breadcrumb-item active" aria-current="page">Trigger Alerts</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0"><i class="bi bi-bell-fill"></i> Trigger Alerts</h2>
        <a href="<?= APP_URL ?>/revenue_manager_virtual_inventory" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card h-100 border-danger">
                <div class="card-body">
                    <small class="text-muted text-uppercase">Critical Alerts</small>
                    <h3 class="mt-2 mb-0 text-danger">2</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 border-warning">
                <div class="card-body">
                    <small class="text-muted text-uppercase">Warning Alerts</small>
                    <h3 class="mt-2 mb-0 text-warning">3</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-muted text-uppercase">Info Alerts</small>
                    <h3 class="mt-2 mb-0 text-info">0</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-muted text-uppercase">Total Active</small>
                    <h3 class="mt-2 mb-0">5</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-exclamation-triangle-fill text-danger"></i> Active Alert Queue</h5>
            <span class="badge bg-secondary">Alert Placeholder</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Alert Name</th>
                        <th>Module Source</th>
                        <th>Triggered At</th>
                        <th>Threshold Ref</th>
                        <th>Severity</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="table-danger">
                        <td>1</td>
                        <td><strong>Housekeeping Cost Exceeded</strong><br><small class="text-muted">Today’s spend $2,800 vs $2,500 daily cap (112%) — 94 room turnovers</small></td>
                        <td>Operations</td>
                        <td>2026-05-03 08:14</td>
                        <td>Housekeeping Daily Cap</td>
                        <td><span class="badge bg-danger"><i class="bi bi-exclamation-octagon-fill me-1"></i>Critical</span></td>
                    </tr>
                    <tr class="table-danger">
                        <td>2</td>
                        <td><strong>Overbooking Detected — Executive Suite</strong><br><small class="text-muted">Virtual max override triggered for room cluster 301–335 (15/25 occupied)</small></td>
                        <td>Reservation Reference</td>
                        <td>2026-05-03 09:02</td>
                        <td>Virtual Max Override</td>
                        <td><span class="badge bg-danger"><i class="bi bi-exclamation-octagon-fill me-1"></i>Critical</span></td>
                    </tr>
                    <tr class="table-warning">
                        <td>3</td>
                        <td><strong>VIP Room Cost Near Cap</strong><br><small class="text-muted">$5,300 reached (88% of $6,000 cap) — 25 VIP rooms at $212/room</small></td>
                        <td>Room Cost</td>
                        <td>2026-05-03 10:45</td>
                        <td>Room Cost Cap — VIP</td>
                        <td><span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle-fill me-1"></i>Warning</span></td>
                    </tr>
                    <tr class="table-warning">
                        <td>4</td>
                        <td><strong>Maintenance Monthly Cap 90% Reached</strong><br><small class="text-muted">$7,200 spent of $8,000 cap — HVAC overhaul + 12 routine jobs</small></td>
                        <td>Operations</td>
                        <td>2026-05-03 11:30</td>
                        <td>Maintenance Monthly Cap</td>
                        <td><span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle-fill me-1"></i>Warning</span></td>
                    </tr>
                    <tr class="table-warning">
                        <td>5</td>
                        <td><strong>F&amp;B Service Near Monthly Limit</strong><br><small class="text-muted">$6,800 reached (91% of $7,500 cap) — restaurant, room service &amp; events</small></td>
                        <td>Service Layer</td>
                        <td>2026-05-03 12:00</td>
                        <td>F&amp;B Service Monthly Limit</td>
                        <td><span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle-fill me-1"></i>Warning</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-diagram-2"></i> Alert Trigger Map</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="card bg-light h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-receipt fs-2 text-primary"></i>
                            <h6 class="mt-2 mb-1">Billing Reference</h6>
                            <p class="text-muted small mb-0">Folio cost overruns feed into alert triggers as read-only references.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-house-gear fs-2 text-success"></i>
                            <h6 class="mt-2 mb-1">Housekeeping Reference</h6>
                            <p class="text-muted small mb-0">Operational cost spikes trigger threshold alerts via cross-module hooks.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-calendar-check fs-2 text-warning"></i>
                            <h6 class="mt-2 mb-1">Reservation Reference</h6>
                            <p class="text-muted small mb-0">Overbooking and demand-surge events route through this alert endpoint.</p>
                        </div>
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
