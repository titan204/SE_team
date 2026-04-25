<?php
$pageTitle = 'Room Details';
ob_start();
?>

<?php
$badgeMap = [
    'available'    => 'success',
    'occupied'     => 'primary',
    'dirty'        => 'danger',
    'cleaning'     => 'warning text-dark',
    'inspecting'   => 'info text-dark',
    'out_of_order' => 'secondary',
];

$transitions = [
    'available'    => ['occupied',   'out_of_order'],
    'occupied'     => ['dirty',      'out_of_order'],
    'dirty'        => ['cleaning',   'out_of_order'],
    'cleaning'     => ['inspecting', 'out_of_order'],
    'inspecting'   => ['available',  'out_of_order'],
    'out_of_order' => ['available'],
];
$nextStatuses = $transitions[$room['status']] ?? [];
?>

<div class="container mt-4">

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= APP_URL ?>/rooms">Rooms</a></li>
            <li class="breadcrumb-item active">Room <?= htmlspecialchars($room['room_number']) ?></li>
        </ol>
    </nav>

    <!-- Flash messages -->
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= htmlspecialchars($_SESSION['success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= htmlspecialchars($_SESSION['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="row">

        <!-- Room Details Card -->
        <div class="col-md-5">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-door-open"></i> Room Details</h5>
                    <a href="<?= APP_URL ?>/rooms/edit/<?= $room['id'] ?>"
                       class="btn btn-sm btn-outline-primary">Edit</a>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr><th>Room Number</th><td><?= htmlspecialchars($room['room_number']) ?></td></tr>
                        <tr><th>Type</th><td><?= htmlspecialchars($room['type_name']) ?></td></tr>
                        <tr><th>Floor</th><td><?= (int)$room['floor'] ?></td></tr>
                        <tr><th>Price/Night</th><td>$<?= number_format($room['base_price'], 2) ?></td></tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <span class="badge bg-<?= $badgeMap[$room['status']] ?? 'dark' ?> fs-6">
                                    <?= htmlspecialchars(str_replace('_', ' ', $room['status'])) ?>
                                </span>
                            </td>
                        </tr>
                        <?php if (!empty($room['type_description'])): ?>
                            <tr><th>Type Info</th><td><?= htmlspecialchars($room['type_description']) ?></td></tr>
                        <?php endif; ?>
                        <?php if (!empty($room['notes'])): ?>
                            <tr><th>Notes</th><td><?= htmlspecialchars($room['notes']) ?></td></tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>

        <!-- Change Status Card -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0"><i class="bi bi-arrow-repeat"></i> Change Status</h5></div>
                <div class="card-body">
                    <?php if (!empty($nextStatuses)): ?>
                        <form method="POST" action="<?= APP_URL ?>/rooms/updateStatus/<?= $room['id'] ?>">
                            <label class="form-label">New status:</label>
                            <select name="status" class="form-select mb-3">
                                <?php foreach ($nextStatuses as $s): ?>
                                    <option value="<?= $s ?>"><?= str_replace('_', ' ', $s) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-warning w-100">Update Status</button>
                        </form>
                    <?php else: ?>
                        <p class="text-muted mb-0">No transitions available.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>

    <!-- Reservation History -->
    <h5 class="mt-2"><i class="bi bi-calendar-check"></i> Reservation History</h5>
    <?php if (empty($reservations)): ?>
        <p class="text-muted">No reservations for this room.</p>
    <?php else: ?>
        <div class="table-responsive mb-4">
            <table class="table table-sm table-striped">
                <thead class="table-light">
                    <tr><th>ID</th><th>Guest</th><th>Check-in</th><th>Check-out</th><th>Status</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($reservations as $res): ?>
                        <tr>
                            <td><?= $res['id'] ?></td>
                            <td><?= htmlspecialchars($res['guest_id']) ?></td>
                            <td><?= htmlspecialchars($res['check_in_date']) ?></td>
                            <td><?= htmlspecialchars($res['check_out_date']) ?></td>
                            <td><span class="badge bg-secondary"><?= $res['status'] ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <!-- Housekeeping Tasks -->
    <h5><i class="bi bi-brush"></i> Housekeeping Tasks</h5>
    <?php if (empty($housekeeping)): ?>
        <p class="text-muted">No housekeeping tasks.</p>
    <?php else: ?>
        <div class="table-responsive mb-4">
            <table class="table table-sm table-striped">
                <thead class="table-light">
                    <tr><th>Type</th><th>Status</th><th>Assigned To</th><th>Date</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($housekeeping as $task): ?>
                        <tr>
                            <td><?= htmlspecialchars($task['task_type']) ?></td>
                            <td><?= htmlspecialchars($task['status']) ?></td>
                            <td><?= htmlspecialchars($task['assigned_to'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($task['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <!-- Maintenance Orders -->
    <h5><i class="bi bi-wrench"></i> Maintenance Orders</h5>
    <?php if (empty($maintenance)): ?>
        <p class="text-muted">No maintenance orders.</p>
    <?php else: ?>
        <div class="table-responsive mb-4">
            <table class="table table-sm table-striped">
                <thead class="table-light">
                    <tr><th>Description</th><th>Priority</th><th>Status</th><th>Date</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($maintenance as $order): ?>
                        <tr>
                            <td><?= htmlspecialchars($order['description']) ?></td>
                            <td><?= htmlspecialchars($order['priority']) ?></td>
                            <td><?= htmlspecialchars($order['status']) ?></td>
                            <td><?= htmlspecialchars($order['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

</div>
<?php
$content = ob_get_clean();
require VIEW_PATH . '/layouts/main.php';
?>
