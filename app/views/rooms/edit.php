<?php $pageTitle = 'Edit Room'; ?>

<?php require VIEW_PATH . '/layouts/main.php'; ?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2><i class="bi bi-pencil-square"></i> Edit Room <?= htmlspecialchars($room['room_number']) ?></h2>
                <a href="<?= APP_URL ?>/rooms/show/<?= $room['id'] ?>"
                   class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
            </div>

            <!-- Validation errors -->
            <?php if (!empty($_SESSION['errors'])): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($_SESSION['errors'] as $err): ?>
                            <li><?= htmlspecialchars($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php unset($_SESSION['errors']); ?>
            <?php endif; ?>

            <!-- Edit form -->
            <form method="POST" action="<?= APP_URL ?>/rooms/update/<?= $room['id'] ?>">
                <div class="card">
                    <div class="card-body">

                        <!-- Room Number -->
                        <div class="mb-3">
                            <label class="form-label">Room Number <span class="text-danger">*</span></label>
                            <input type="text" name="room_number" class="form-control" required
                                   value="<?= htmlspecialchars($room['room_number']) ?>">
                        </div>

                        <!-- Room Type dropdown -->
                        <div class="mb-3">
                            <label class="form-label">Room Type <span class="text-danger">*</span></label>
                            <select name="room_type_id" class="form-select" required>
                                <option value="">-- Select Type --</option>
                                <?php foreach ($roomTypes as $type): ?>
                                    <option value="<?= $type['id'] ?>"
                                        <?= ($type['id'] == $room['room_type_id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($type['name']) ?>
                                        ($<?= number_format($type['base_price'], 2) ?>/night)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Floor -->
                        <div class="mb-3">
                            <label class="form-label">Floor <span class="text-danger">*</span></label>
                            <input type="number" name="floor" class="form-control"
                                   min="1" required
                                   value="<?= (int)$room['floor'] ?>">
                        </div>

                        <!-- Notes -->
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="3"
                            ><?= htmlspecialchars($room['notes'] ?? '') ?></textarea>
                        </div>

                        <!-- Status info -->
                        <p class="text-muted small mb-0">
                            To change room status, use the
                            <a href="<?= APP_URL ?>/rooms/show/<?= $room['id'] ?>">Room Details</a> page.
                        </p>

                    </div>
                    <div class="card-footer text-end">
                        <a href="<?= APP_URL ?>/rooms/show/<?= $room['id'] ?>"
                           class="btn btn-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>