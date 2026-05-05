<?php
$pageTitle = 'Revenue Impact Report';
ob_start();

// Impact classification per room type
$impactMap = [
    'Standard' => ['Healthy',          'bg-success'],
    'Deluxe'   => ['Stable',           'bg-info text-dark'],
    'Suite'    => ['Monitor — Near Cap','bg-warning text-dark'],
];
?>

<div class="container mt-4">

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

    <!-- KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-muted text-uppercase">Gross Revenue This Month</small>
                    <h3 class="mt-2 mb-0 text-success">$<?= number_format($revenueMonth, 0) ?></h3>
                    <small class="text-muted"><?= (int)$totalOccupied ?> occupied rooms · actual payments</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-muted text-uppercase">Cost Pressure</small>
                    <h3 class="mt-2 mb-0 text-danger">$<?= number_format($costPool, 0) ?></h3>
                    <small class="text-muted">← Room Cost Watch (<?= (int)$totalRooms ?> rooms × 20% rate)</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-muted text-uppercase">Margin Signal</small>
                    <h3 class="mt-2 mb-0 text-primary"><?= $marginPct ?>%</h3>
                    <small class="text-muted">
                        ($<?= number_format($revenueMonth, 0) ?> − $<?= number_format($costPool, 0) ?>)
                        ÷ $<?= number_format($revenueMonth, 0) ?>
                    </small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 <?= $openAlerts > 0 ? 'border-warning' : '' ?>">
                <div class="card-body">
                    <small class="text-muted text-uppercase">Active Alerts</small>
                    <h3 class="mt-2 mb-0 <?= $openAlerts > 0 ? 'text-warning' : 'text-success' ?>">
                        <?= (int)$openAlerts ?>
                    </h3>
                    <small class="text-muted">
                        <?= $openAlerts > 0 ? 'Property-wide active alerts' : 'No active alerts' ?>
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Per-type Breakdown -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-table"></i> Financial Summary by Room Type</h5>
            <span class="badge bg-primary">Occupancy: <?= $occupancyPct ?>%</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Segment</th>
                        <th>Occupied / Total</th>
                        <th>Revenue Signal</th>
                        <th>Cost Reference</th>
                        <th>Impact Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($roomTypes as $rt):
                    // Revenue signal = base_price × occupied rooms (nightly estimate)
                    $revSignal = (float)$rt['base_price'] * (int)$rt['occupied_count'];
                    [$impactText, $impactBadge] = $impactMap[$rt['room_type']] ?? ['Review', 'bg-secondary'];
                ?>
                <tr>
                    <td>
                        <strong><?= htmlspecialchars($rt['room_type']) ?>
                        (<?= htmlspecialchars($rt['min_room']) ?>–<?= htmlspecialchars($rt['max_room']) ?>)</strong>
                    </td>
                    <td><?= (int)$rt['occupied_count'] ?> / <?= (int)$rt['room_count'] ?></td>
                    <td>$<?= number_format($revSignal, 0) ?></td>
                    <td>
                        $<?= number_format($rt['cost_ref'], 0) ?>
                        <small class="text-muted">
                            (<?= (int)$rt['room_count'] ?> × $<?= number_format($rt['base_price'] * 0.20, 0) ?>)
                        </small>
                    </td>
                    <td><span class="badge <?= $impactBadge ?>"><?= $impactText ?></span></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot class="table-dark fw-bold">
                    <tr>
                        <th>Total</th>
                        <th><?= (int)$totalOccupied ?> / <?= (int)$totalRooms ?> (<?= $occupancyPct ?>%)</th>
                        <th>$<?= number_format($revenueMonth, 0) ?> <small class="fw-normal">(this month)</small></th>
                        <th>$<?= number_format($costPool, 0) ?></th>
                        <th>Margin: <?= $marginPct ?>%</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <p class="text-muted small mt-2">
        <i class="bi bi-info-circle me-1"></i>
        Revenue signal = base nightly rate × occupied rooms (snapshot estimate).
        Actual revenue from <strong>payments</strong> table this month: <strong>$<?= number_format($revenueMonth, 0) ?></strong>.
    </p>

</div>

<?php
$content = ob_get_clean();
require VIEW_PATH . '/layouts/main.php';
?>
