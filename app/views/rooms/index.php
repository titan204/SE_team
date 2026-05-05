<?php
$pageTitle = 'All Rooms';
ob_start();
?>

<div class="container mt-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2><i class="bi bi-door-open"></i> All Rooms</h2>
        <?php if (($_SESSION['user_role'] ?? '') === 'manager'): ?>
        <a href="<?= APP_URL ?>/rooms/create" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add Room
        </a>
        <?php endif; ?>
    </div>

    <!-- Flash: success -->
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= htmlspecialchars($_SESSION['success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <!-- Flash: error -->
    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= htmlspecialchars($_SESSION['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Filters -->
    <div class="row g-2 mb-3">
        <div class="col-auto">
            <select id="filterStatus" class="form-select form-select-sm">
                <option value="">All Statuses</option>
                <option value="available">Available</option>
                <option value="occupied">Occupied</option>
                <option value="dirty">Dirty</option>
                <option value="cleaning">Cleaning</option>
                <option value="inspecting">Inspecting</option>
                <option value="out_of_order">Out of Order</option>
            </select>
        </div>
        <div class="col-auto">
            <select id="filterType" class="form-select form-select-sm">
                <option value="">All Types</option>
                <?php
                $types = array_unique(array_column($rooms, 'type_name'));
                foreach ($types as $t): ?>
                    <option value="<?= htmlspecialchars($t) ?>"><?= htmlspecialchars($t) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table table-hover align-middle" id="roomsTable">
            <thead class="table-dark">
                <tr>
                    <th>Room No.</th>
                    <th>Floor</th>
                    <th>Type</th>
                    <th>Price/Night</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($rooms)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No rooms found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($rooms as $room): ?>
                        <tr data-status="<?= $room['status'] ?>"
                            data-type="<?= htmlspecialchars($room['type_name']) ?>">

                            <td><strong><?= htmlspecialchars($room['room_number']) ?></strong></td>

                            <td><?= (int)$room['floor'] ?></td>

                            <td><?= htmlspecialchars($room['type_name']) ?></td>

                            <td>$<?= number_format($room['base_price'], 2) ?></td>

                            <td>
                                <?php
                                $badgeMap = [
                                    'available'    => 'success',
                                    'occupied'     => 'primary',
                                    'dirty'        => 'danger',
                                    'cleaning'     => 'warning text-dark',
                                    'inspecting'   => 'info text-dark',
                                    'out_of_order' => 'secondary',
                                ];
                                $badge = $badgeMap[$room['status']] ?? 'dark';
                                ?>
                                <span class="badge bg-<?= $badge ?>">
                                    <?= htmlspecialchars(str_replace('_', ' ', $room['status'])) ?>
                                </span>
                            </td>

                            <td>
                                <a href="<?= APP_URL ?>/?url=rooms/show/<?= $room['id'] ?>"
                                   class="btn btn-sm btn-outline-secondary">View</a>

                                <?php if (($_SESSION['user_role'] ?? '') === 'manager'): ?>
                                <a href="<?= APP_URL ?>/?url=rooms/edit/<?= $room['id'] ?>"
                                   class="btn btn-sm btn-outline-primary">Edit</a>

                                <form method="POST"
                                      action="<?= APP_URL ?>/?url=rooms/delete/<?= $room['id'] ?>"
                                      class="d-inline"
                                      onsubmit="return confirm('Delete room <?= htmlspecialchars($room['room_number']) ?>?')">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.getElementById('filterStatus').addEventListener('change', filterTable);
document.getElementById('filterType').addEventListener('change', filterTable);

function filterTable() {
    const status = document.getElementById('filterStatus').value;
    const type   = document.getElementById('filterType').value;

    document.querySelectorAll('#roomsTable tbody tr[data-status]').forEach(row => {
        const matchStatus = !status || row.dataset.status === status;
        const matchType   = !type   || row.dataset.type   === type;
        row.style.display = (matchStatus && matchType) ? '' : 'none';
    });
}
</script>
<?php
$content = ob_get_clean();
require VIEW_PATH . '/layouts/main.php';
?>
