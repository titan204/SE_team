<?php $pageTitle = 'Room Details'; ?>

<?php require VIEW_PATH . '/layouts/main.php'; ?>

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

    
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/rooms">Rooms</a></li>
            <li class="breadcrumb-item active">Room <?= htmlspecialchars($room['room_number']) ?></li>
        </ol>
    </nav>

    
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']) ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']) ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="row">

        
        <div class="col-md-5">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Room Details</h5>
                    <a href="<?= BASE_URL ?>/rooms/edit/<?= $room['id'] ?>"
                       class="btn btn-sm btn-outline-primary">Edit</a>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr><th>Room Number</th><td><?= htmlspecialchars($room['room_number']) ?></td></tr>
                        <tr>
                            <th>Type</th>
              
                            <td><?= htmlspecialchars($room['type_name']) ?></td>
                        </tr>
                        <tr><th>Floor</th><td><?= (int)$room['floor'] ?></td></tr>
                        <tr><th>Price/Night</th><td>$<?= number_format($room['base_price'], 2) ?></td></tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <span class="badge bg-<?= $badgeMap[$room['status']] ?? 'dark' ?> fs-6">
                                    <?= htmlspecialchars($room['status']) ?>
                                </span>
                            </td>
                        </tr>
                        <?php if (!empty($room['notes'])): ?>
                            <tr><th>Notes</th><td><?= htmlspecialchars($room['notes']) ?></td></tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Change Status</h5></div>
                <div class="card-body">
                    <?php if (!empty($nextStatuses)): ?>
                        <form method="POST" action="<?= BASE_URL ?>/rooms/status/<?= $room['id'] ?>">
                            <label class="form-label">New status:</label>
                            <select name="status" class="form-select mb-3">
                                <?php foreach ($nextStatuses as $s): ?>
                                    <option value="<?= $s ?>"><?= $s ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-warning w-100">Update</button>
                        </form>
                    <?php else: ?>
                        <p class="text-muted mb-0">No transitions available.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>

    
    <h5 class="mt-2">Reservation History</h5>
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

    
    <h5>Housekeeping Tasks</h5>
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

    <!-- ── Maintenance Orders ── -->
    <h5>🔧 Maintenance Orders</h5>
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
