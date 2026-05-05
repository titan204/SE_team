<?php
$pageTitle = 'Limit Check';
ob_start();
?>

<div class="container mt-4">
    <!-- Cost Limit References: threshold values monitored here are structural placeholders. -->
    <!-- Billing System: future folio cost thresholds can be referenced for limit enforcement. -->
    <!-- Reservation System: future room-count caps per category can feed limit checks here. -->

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= APP_URL ?>/dashboard">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= APP_URL ?>/revenue_manager_virtual_inventory">Virtual Inventory</a></li>
            <li class="breadcrumb-item active" aria-current="page">Limit Check</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0"><i class="bi bi-shield-check"></i> Limit Check</h2>
        <a href="<?= APP_URL ?>/revenue_manager_virtual_inventory" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-muted text-uppercase">Total Thresholds</small>
                    <h3 class="mt-2 mb-0">12</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-muted text-uppercase">Within Limit</small>
                    <h3 class="mt-2 mb-0 text-success">9</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-muted text-uppercase">Near Limit</small>
                    <h3 class="mt-2 mb-0 text-warning">3</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-muted text-uppercase">Exceeded</small>
                    <h3 class="mt-2 mb-0 text-danger">1</h3>
                    <small class="text-muted">2 Crit + 3 Warn = 5 alerts total</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-sliders"></i> Cost Threshold Monitoring</h5>
            <span class="badge bg-secondary">Limit Placeholder</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Threshold Name</th>
                        <th>Category</th>
                        <th>Limit Value</th>
                        <th>Current Value</th>
                        <th>Usage %</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Room Cost Cap — Standard</strong></td>
                        <td>Room Cost</td>
                        <td>$7,000</td>
                        <td>$5,700 <small class="text-muted">(60 rooms × $95)</small></td>
                        <td>
                            <div class="progress" style="height:6px"><div class="progress-bar bg-success" style="width:81%"></div></div>
                            <small class="text-muted">81%</small>
                        </td>
                        <td><span class="badge bg-success">Within Limit</span></td>
                    </tr>
                    <tr class="table-warning">
                        <td><strong>Room Cost Cap — VIP</strong></td>
                        <td>Room Cost</td>
                        <td>$6,000</td>
                        <td>$5,300 <small class="text-muted">(25 rooms × $212)</small></td>
                        <td>
                            <div class="progress" style="height:6px"><div class="progress-bar bg-warning" style="width:88%"></div></div>
                            <small class="text-muted">88%</small>
                        </td>
                        <td><span class="badge bg-warning text-dark">Near Limit</span></td>
                    </tr>
                    <tr class="table-danger">
                        <td><strong>Housekeeping Daily Cap</strong></td>
                        <td>Operations</td>
                        <td>$2,500 / day</td>
                        <td>$2,800 <small class="text-muted">(today’s peak — 94 rooms)</small></td>
                        <td>
                            <div class="progress" style="height:6px"><div class="progress-bar bg-danger" style="width:100%"></div></div>
                            <small class="text-muted">112%</small>
                        </td>
                        <td><span class="badge bg-danger">Exceeded → Alert #1</span></td>
                    </tr>
                    <tr class="table-warning">
                        <td><strong>F&amp;B Service Monthly Limit</strong></td>
                        <td>Service Layer</td>
                        <td>$7,500</td>
                        <td>$6,800 <small class="text-muted">(restaurant + room service + events)</small></td>
                        <td>
                            <div class="progress" style="height:6px"><div class="progress-bar bg-warning" style="width:91%"></div></div>
                            <small class="text-muted">91%</small>
                        </td>
                        <td><span class="badge bg-warning text-dark">Near Limit → Alert #5</span></td>
                    </tr>
                    <tr>
                        <td><strong>Guest Folio Exposure Cap</strong></td>
                        <td>Billing Reference</td>
                        <td>$1,500 / folio</td>
                        <td>$830 <small class="text-muted">(avg per tracked guest profile)</small></td>
                        <td>
                            <div class="progress" style="height:6px"><div class="progress-bar bg-success" style="width:55%"></div></div>
                            <small class="text-muted">55%</small>
                        </td>
                        <td><span class="badge bg-success">Within Limit</span></td>
                    </tr>
                    <tr class="table-warning">
                        <td><strong>Maintenance Monthly Cap</strong></td>
                        <td>Operations</td>
                        <td>$8,000</td>
                        <td>$7,200 <small class="text-muted">(HVAC overhaul + 12 routine jobs)</small></td>
                        <td>
                            <div class="progress" style="height:6px"><div class="progress-bar bg-warning" style="width:90%"></div></div>
                            <small class="text-muted">90%</small>
                        </td>
                        <td><span class="badge bg-warning text-dark">Near Limit → Alert #4</span></td>
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
