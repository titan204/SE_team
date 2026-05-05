<?php
$pageTitle = 'Reports';
ob_start();
?>
<h1 class="h3 mb-4"><i class="bi bi-bar-chart me-2"></i>Reports</h1>
<div class="row g-3">
    <div class="col-md-4">
        <a href="<?= APP_URL ?>/?url=reports/occupancy" class="card text-decoration-none text-dark border-0 shadow-sm h-100 d-block p-4">
            <div class="fs-1 text-primary"><i class="bi bi-bar-chart-fill"></i></div>
            <h5 class="mt-2">Occupancy Report</h5>
            <p class="text-muted small mb-0">Daily and per-room-type occupancy rates over a selected period.</p>
        </a>
    </div>
    <div class="col-md-4">
        <a href="<?= APP_URL ?>/?url=reports/revenue" class="card text-decoration-none text-dark border-0 shadow-sm h-100 d-block p-4">
            <div class="fs-1 text-success"><i class="bi bi-graph-up"></i></div>
            <h5 class="mt-2">Revenue Report</h5>
            <p class="text-muted small mb-0">Total and daily revenue breakdown by charge type.</p>
        </a>
    </div>
    <?php if (strtolower($_SESSION['user_role'] ?? '') === 'manager'): ?>
    <div class="col-md-4">
        <a href="<?= APP_URL ?>/?url=reports/audit" class="card text-decoration-none text-dark border-0 shadow-sm h-100 d-block p-4">
            <div class="fs-1 text-warning"><i class="bi bi-shield-check"></i></div>
            <h5 class="mt-2">Audit Log</h5>
            <p class="text-muted small mb-0">Full system audit trail — searchable and exportable.</p>
        </a>
    </div>
    <?php endif; ?>
</div>
<?php $content = ob_get_clean(); require VIEW_PATH . '/layouts/main.php'; ?>
