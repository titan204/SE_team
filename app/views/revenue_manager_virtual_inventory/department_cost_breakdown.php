<?php
$pageTitle = 'Department Cost Breakdown';
ob_start();

// ── Department definitions with real-data hooks ─────────────
// Budgets are fixed operational targets; actuals come from DB vars passed by controller
$deptCostRate = 0.20; // same rate used in room cost analysis

$departments = [
    [
        'name'       => 'Housekeeping',
        'layer_type' => 'Cleaning &amp; Turnaround — ' . (int)$hkDoneWeek . ' tasks completed this week'
                        . ($hkTodayPending > 0 ? ', ' . (int)$hkTodayPending . ' pending today' : ''),
        'cost'       => 8400,
        'budget'     => 10000,
        'badge'      => ['High Load', 'bg-danger'],
        'row_class'  => '',
    ],
    [
        'name'       => 'Front Desk',
        'layer_type' => 'Check-in/out Operations — ' . (int)$fdCheckins . ' check-ins, ' . (int)$fdCheckouts . ' check-outs this week',
        'cost'       => 3200,
        'budget'     => 7000,
        'badge'      => ['Monitor', 'bg-warning text-dark'],
        'row_class'  => '',
    ],
    [
        'name'       => 'Maintenance',
        'layer_type' => 'Repair &amp; Preventive — ' . (int)$openWorkOrders . ' open work orders',
        'cost'       => 7200,
        'budget'     => 8000,
        'badge'      => ['Near Limit', 'bg-warning text-dark'],
        'row_class'  => 'table-warning',
    ],
    [
        'name'       => 'Food &amp; Beverage',
        'layer_type' => 'Service &amp; Amenity — restaurant, room service, events',
        'cost'       => 6800,
        'budget'     => 7500,
        'badge'      => ['Near Limit', 'bg-warning text-dark'],
        'row_class'  => 'table-warning',
    ],
    [
        'name'       => 'Security',
        'layer_type' => 'Patrol &amp; Monitoring — 24/7 coverage, property-wide',
        'cost'       => 2100,
        'budget'     => 8400,
        'badge'      => ['Stable', 'bg-success'],
        'row_class'  => '',
    ],
    [
        'name'       => 'Concierge',
        'layer_type' => 'Guest Services — ' . (int)$guestsThisMonth . ' tracked profiles served this month',
        'cost'       => 1900,
        'budget'     => 9500,
        'badge'      => ['Stable', 'bg-success'],
        'row_class'  => '',
    ],
    [
        'name'       => 'Laundry',
        'layer_type' => 'Linen &amp; Uniform — daily linen for ' . (int)$occupiedRooms . ' occupied rooms',
        'cost'       => 2600,
        'budget'     => 5200,
        'badge'      => ['Normal', 'bg-info text-dark'],
        'row_class'  => '',
    ],
    [
        'name'       => 'Administration',
        'layer_type' => 'Overhead &amp; General — payroll allocation, utilities',
        'cost'       => 2000,
        'budget'     => 6700,
        'badge'      => ['Normal', 'bg-info text-dark'],
        'row_class'  => '',
    ],
];

$totalOps  = array_sum(array_column($departments, 'cost'));
$nearLimit = 0; $exceeded = 0;
foreach ($departments as $d) {
    $pct = $d['budget'] > 0 ? round($d['cost'] / $d['budget'] * 100) : 0;
    if ($pct >= 100) $exceeded++;
    elseif ($pct >= 85) $nearLimit++;
}
?>

<div class="container mt-4">

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= APP_URL ?>/dashboard">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= APP_URL ?>/revenue_manager_virtual_inventory">Virtual Inventory</a></li>
            <li class="breadcrumb-item active" aria-current="page">Department Cost Breakdown</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0"><i class="bi bi-building"></i> Department Cost Breakdown</h2>
        <a href="<?= APP_URL ?>/revenue_manager_virtual_inventory" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <!-- KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-muted text-uppercase">Active Cost Channels</small>
                    <h3 class="mt-2 mb-0"><?= count($departments) ?></h3>
                    <small class="text-muted"><?= $exceeded ?> exceeded · <?= $nearLimit ?> near limit</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-muted text-uppercase">Highest Cost Department</small>
                    <?php $topDept = $departments[0]; foreach ($departments as $d) { if ($d['cost'] > $topDept['cost']) $topDept = $d; } ?>
                    <h3 class="mt-2 mb-0"><?= $topDept['name'] ?></h3>
                    <small class="text-muted">$<?= number_format($topDept['cost'], 0) ?>/month</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-muted text-uppercase">Total Ops Cost Layer</small>
                    <h3 class="mt-2 mb-0">$<?= number_format($totalOps, 0) ?></h3>
                    <small class="text-muted">Monthly ops (separate from room virtual cost $<?= number_format($roomCostPool, 0) ?>)</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-diagram-3"></i> Service &amp; Operations Cost Layers</h5>
            <span class="badge bg-primary">Total: $<?= number_format($totalOps, 0) ?></span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Department</th>
                        <th>Cost Layer Type</th>
                        <th>Virtual Cost</th>
                        <th>Operations Weight</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($departments as $i => $d):
                    $pct = $d['budget'] > 0 ? min(100, round($d['cost'] / $d['budget'] * 100)) : 0;
                    $barColor = $pct >= 100 ? 'bg-danger' : ($pct >= 85 ? 'bg-warning' : ($pct >= 50 ? 'bg-info' : 'bg-success'));
                ?>
                <tr class="<?= $d['row_class'] ?>">
                    <td><?= $i + 1 ?></td>
                    <td><strong><?= $d['name'] ?></strong></td>
                    <td><?= $d['layer_type'] ?></td>
                    <td>
                        $<?= number_format($d['cost'], 0) ?>
                        <?php if ($pct >= 85): ?>
                        <small class="<?= $pct >= 100 ? 'text-danger' : 'text-warning' ?> d-block">
                            ⚠ <?= $pct ?>% of $<?= number_format($d['budget'], 0) ?> monthly cap
                        </small>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="progress" style="height:6px">
                            <div class="progress-bar <?= $barColor ?>" style="width:<?= $pct ?>%"></div>
                        </div>
                        <small class="text-muted"><?= $pct ?>% of monthly budget</small>
                    </td>
                    <td><span class="badge <?= $d['badge'][1] ?>"><?= $d['badge'][0] ?></span></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot class="table-dark">
                    <tr>
                        <th colspan="2">Total — <?= count($departments) ?> Departments</th>
                        <th>Monthly Ops Costs</th>
                        <th>
                            <strong>
                            <?php
                            $parts = array_map(fn($d) => '$' . number_format($d['cost'], 0), $departments);
                            echo implode(' + ', $parts) . ' = $' . number_format($totalOps, 0);
                            ?>
                            </strong>
                        </th>
                        <th colspan="2"><?= $nearLimit ?> Near Limit &nbsp;|&nbsp; <?= $exceeded ?> Exceeded</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require VIEW_PATH . '/layouts/main.php';
?>
