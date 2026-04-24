<?php
$pageTitle = 'Front Desk Dashboard';
$todayArrivals = $todayArrivals ?? 0;
$todayDepartures = $todayDepartures ?? 0;
$inHouse = $inHouse ?? 0;
$recentReservations = $recentReservations ?? [];

ob_start();
?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold">Front Desk Operations</h2>
        <p class="text-muted">Welcome to the Front Desk dashboard. Manage check-ins, check-outs, and current guests.</p>
    </div>
</div>

<div class="row mb-4">
    <!-- Today's Arrivals -->
    <div class="col-md-4">
        <div class="card text-white bg-primary shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase fw-semibold mb-1">Today's Arrivals</h6>
                        <h2 class="mb-0 fw-bold"><?= $todayArrivals ?></h2>
                    </div>
                    <div>
                        <i class="bi bi-box-arrow-in-right" style="font-size: 2.5rem; opacity: 0.8;"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0">
                <a href="<?= APP_URL ?>/reservations" class="text-white text-decoration-none">View pending <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <!-- Today's Departures -->
    <div class="col-md-4">
        <div class="card text-white bg-warning shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase fw-semibold mb-1 text-dark">Today's Departures</h6>
                        <h2 class="mb-0 fw-bold text-dark"><?= $todayDepartures ?></h2>
                    </div>
                    <div>
                        <i class="bi bi-box-arrow-right text-dark" style="font-size: 2.5rem; opacity: 0.8;"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0">
                <a href="<?= APP_URL ?>/reservations" class="text-dark text-decoration-none">View departures <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <!-- In-House Guests -->
    <div class="col-md-4">
        <div class="card text-white bg-success shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase fw-semibold mb-1">In-House Guests</h6>
                        <h2 class="mb-0 fw-bold"><?= $inHouse ?></h2>
                    </div>
                    <div>
                        <i class="bi bi-people" style="font-size: 2.5rem; opacity: 0.8;"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0">
                <a href="<?= APP_URL ?>/guests" class="text-white text-decoration-none">Manage guests <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="bi bi-calendar-check me-2"></i>Recent Reservations</h5>
                <a href="<?= APP_URL ?>/reservations/create" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-lg"></i> New Booking
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Guest</th>
                                <th>Room</th>
                                <th>Check-in</th>
                                <th>Check-out</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentReservations)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No recent reservations found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentReservations as $res): ?>
                                    <tr>
                                        <td class="fw-medium"><?= htmlspecialchars($res['guest_name']) ?></td>
                                        <td><?= htmlspecialchars($res['room_number']) ?></td>
                                        <td><?= date('M d, Y', strtotime($res['check_in_date'])) ?></td>
                                        <td><?= date('M d, Y', strtotime($res['check_out_date'])) ?></td>
                                        <td>
                                            <?php if ($res['status'] === 'pending'): ?>
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            <?php elseif ($res['status'] === 'confirmed'): ?>
                                                <span class="badge bg-primary">Confirmed</span>
                                            <?php elseif ($res['status'] === 'checked_in'): ?>
                                                <span class="badge bg-success">Checked In</span>
                                            <?php elseif ($res['status'] === 'checked_out'): ?>
                                                <span class="badge bg-secondary">Checked Out</span>
                                            <?php else: ?>
                                                <span class="badge bg-dark"><?= ucfirst($res['status']) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="<?= APP_URL ?>/reservations/show/<?= $res['id'] ?>" class="btn btn-sm btn-outline-primary">View</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white text-center py-3">
                <a href="<?= APP_URL ?>/reservations" class="btn btn-outline-secondary btn-sm">View All Reservations</a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-lightning-charge me-2"></i>Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-3">
                    <a href="<?= APP_URL ?>/guests/create" class="btn btn-outline-primary text-start">
                        <i class="bi bi-person-plus me-2"></i> Register New Guest
                    </a>
                    <a href="<?= APP_URL ?>/rooms" class="btn btn-outline-success text-start">
                        <i class="bi bi-door-open me-2"></i> View Room Availability
                    </a>
                    <a href="<?= APP_URL ?>/billing" class="btn btn-outline-secondary text-start">
                        <i class="bi bi-receipt me-2"></i> Process Payment
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require VIEW_PATH . '/layouts/main.php';
?>
