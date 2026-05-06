<?php
$pageTitle = 'Room Cost Analysis';
ob_start();
?>

<div class="container mt-4">
    <!-- Housekeeping System: cleaning and turnaround cost references feed this table via hkDoneWeek. -->
    <!-- Billing System: read-only folio allocations summarised beside room cost totals. -->
    <!-- Reservation System: occupancy and booking pressure influence displayed cost context. -->

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

    <!-- ── KPI Summary Cards ── -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-muted text-uppercase">Tracked Rooms</small>
                    <h3 class="mt-2 mb-0"><?= (int)$totalRooms ?></h3>
                    <small class="text-muted">
                        <?= (int)$totalOccupied ?> currently occupied
                        (<?= $occupancyPct ?>%)
                    </small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-muted text-uppercase">Avg Virtual Cost / Room</small>
                    <h3 class="mt-2 mb-0">$<?= number_format($avgCostPerRoom, 0) ?></h3>
                    <small class="text-muted">
                        <?= (int)$totalRooms ?> rooms × $<?= number_format($avgCostPerRoom, 0) ?>
                        = $<?= number_format($totalPool, 0) ?> total
                    </small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-muted text-uppercase">Total Room Cost Watch</small>
                    <h3 class="mt-2 mb-0 text-primary">$<?= number_format($totalPool, 0) ?></h3>
                    <small class="text-muted">Sum across all <?= count($roomTypes) ?> room type clusters</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-muted text-uppercase">Highest Impact Tier</small>
                    <h3 class="mt-2 mb-0"><?= htmlspecialchars($topTier['room_type'] ?? '—') ?></h3>
                    <small class="text-muted">
                        $<?= number_format($topTier['service_cost_per_room'] ?? 0, 0) ?>/room avg
                        (base $<?= number_format($topTier['base_price'] ?? 0, 0) ?>/night)
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- ── HK context bar ── -->
    <div class="alert alert-info d-flex align-items-center gap-2 mb-3 py-2" role="alert">
        <i class="bi bi-brush-fill"></i>
        <span>
            <strong><?= (int)$hkDoneWeek ?></strong> housekeeping tasks completed this week
            &nbsp;·&nbsp; <?= (int)$totalOccupied ?> rooms currently occupied
            &nbsp;·&nbsp; Occupancy: <strong><?= $occupancyPct ?>%</strong>
        </span>
    </div>

    <!-- ── Breakdown Table ── -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="bi bi-table"></i> Room Virtual Cost Breakdown
                <small class="text-muted fw-normal ms-2">
                    — <?= (int)$totalRooms ?> rooms across <?= count($roomTypes) ?> types
                </small>
            </h5>
            <span class="badge bg-primary">Total Pool: $<?= number_format($totalPool, 0) ?></span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Room Range</th>
                        <th>Cluster / Category</th>
                        <th>Housekeeping Reference</th>
                        <th>Service Cost / Room</th>
                        <th>Folio Visibility</th>
                        <th>Revenue Effect</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                // Classify each room type for display
                $hkLabels = [
                    'Standard' => 'Standard Turnover — ' . $hkDoneWeek . ' completed this week',
                    'Deluxe'   => 'Extended-Stay Deep Clean — weekly schedule active',
                    'Suite'    => 'VIP Preparation — priority turnaround active',
                ];
                $folioLabels = [
                    'Standard' => 'Read-only linked to folio',
                    'Deluxe'   => 'Pending folio summary',
                    'Suite'    => 'Visible in revenue report layer',
                ];
                $impactLabels = [
                    'Standard' => ['Low Impact',  'bg-success'],
                    'Deluxe'   => ['Stable',       'bg-info text-dark'],
                    'Suite'    => ['High Impact',  'bg-danger'],
                ];

                foreach ($roomTypes as $rt):
                    $typeName  = $rt['room_type'];
                    $hkRef     = $hkLabels[$typeName]    ?? 'Standard Turnover';
                    $folioRef  = $folioLabels[$typeName] ?? 'Summarised in report';
                    [$impactText, $impactBadge] = $impactLabels[$typeName] ?? ['Review', 'bg-secondary'];
                ?>
                <tr>
                    <td>
                        <strong><?= htmlspecialchars($rt['min_room']) ?>–<?= htmlspecialchars($rt['max_room']) ?></strong>
                    </td>
                    <td><?= htmlspecialchars($typeName) ?> (<?= (int)$rt['room_count'] ?> rooms)</td>
                    <td><?= htmlspecialchars($hkRef) ?></td>
                    <td>
                        $<?= number_format($rt['service_cost_per_room'], 0) ?>
                        <small class="text-muted">
                            × <?= (int)$rt['room_count'] ?> =
                            <strong>$<?= number_format($rt['cluster_total'], 0) ?></strong>
                        </small>
                    </td>
                    <td><?= htmlspecialchars($folioRef) ?></td>
                    <td><span class="badge <?= $impactBadge ?>"><?= $impactText ?></span></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot class="table-dark">
                    <tr>
                        <th colspan="3">Total — <?= (int)$totalRooms ?> Rooms</th>
                        <th colspan="3">
                            <strong>
                            <?php
                            $parts = array_map(
                                fn($rt) => '$' . number_format($rt['cluster_total'], 0),
                                $roomTypes
                            );
                            echo implode(' + ', $parts);
                            echo ' = $' . number_format($totalPool, 0);
                            ?>
                            </strong>
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- ── Cost rate note ── -->
    <p class="text-muted small mt-2">
        <i class="bi bi-info-circle me-1"></i>
        Virtual service cost calculated at <strong>20% of base nightly rate</strong> per room
        (operational overhead estimate). Base rates: Standard $500/night · Deluxe $800/night · Suite $1,500/night.
    </p>

</div>

<?php
$content = ob_get_clean();
require VIEW_PATH . '/layouts/main.php';
?>
