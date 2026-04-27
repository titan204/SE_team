<?php
$pageTitle = 'Revenue Manager Workspace';
ob_start();
?>

<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= APP_URL ?>/dashboard">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Revenue Manager</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1"><i class="bi bi-graph-up-arrow"></i> Revenue Manager Workspace</h2>
            <p class="text-muted mb-0">Scaffold view for read-only billing summaries, folio aggregation hooks, and reporting placeholders.</p>
        </div>
        <a href="<?= APP_URL ?>/revenue_manager/create" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> New Report Preset
        </a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-muted text-uppercase">Billing Summary</small>
                    <h3 class="mt-2 mb-0">$124,500</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-muted text-uppercase">Open Folio Hooks</small>
                    <h3 class="mt-2 mb-0">18</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-muted text-uppercase">Upgrade Watch</small>
                    <h3 class="mt-2 mb-0">5</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-muted text-uppercase">Audit Trail Hooks</small>
                    <h3 class="mt-2 mb-0">Ready</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Reporting Panels</h5>
            <span class="badge bg-secondary">Read-Only Placeholder</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Panel</th>
                        <th>Source Hook</th>
                        <th>Period</th>
                        <th>Status</th>
                        <th>Audit Visibility</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Daily Revenue Summary</strong></td>
                        <td>Billing + Folio</td>
                        <td>Today</td>
                        <td><span class="badge bg-success">Snapshot Ready</span></td>
                        <td>Read-only</td>
                        <td>
                            <a href="<?= APP_URL ?>/revenue_manager/show/1" class="btn btn-sm btn-outline-secondary">Details</a>
                            <a href="<?= APP_URL ?>/revenue_manager/edit/1" class="btn btn-sm btn-outline-primary">Edit</a>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>VIP Upgrade Monitor</strong></td>
                        <td>Reservation Signals</td>
                        <td>Next 48 Hours</td>
                        <td><span class="badge bg-warning text-dark">Watching</span></td>
                        <td>Audit hook enabled</td>
                        <td>
                            <a href="<?= APP_URL ?>/revenue_manager/show/2" class="btn btn-sm btn-outline-secondary">Details</a>
                            <a href="<?= APP_URL ?>/revenue_manager/edit/2" class="btn btn-sm btn-outline-primary">Edit</a>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Open Folio Exposure</strong></td>
                        <td>Folio Aggregation</td>
                        <td>Month To Date</td>
                        <td><span class="badge bg-info text-dark">Pending Connection</span></td>
                        <td>Read-only</td>
                        <td>
                            <a href="<?= APP_URL ?>/revenue_manager/show/3" class="btn btn-sm btn-outline-secondary">Details</a>
                            <a href="<?= APP_URL ?>/revenue_manager/edit/3" class="btn btn-sm btn-outline-primary">Edit</a>
                        </td>
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
