<?php $pageTitle = 'Add New Room'; ?>

<?php require VIEW_PATH . '/layouts/main.php'; ?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>Add New Room</h2>
                <a href="<?= BASE_URL ?>/rooms" class="btn btn-outline-secondary">Back</a>
            </div>

            
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

            
            <form method="POST" action="<?= BASE_URL ?>/rooms/store">
                <div class="card">
                    <div class="card-body">

                        
                        <div class="mb-3">
                            <label class="form-label">Room Number <span class="text-danger">*</span></label>
                            <input type="text" name="room_number" class="form-control"
                                   placeholder="e.g. 101, 202A" required
                                   value="<?= htmlspecialchars($_SESSION['old_input']['room_number'] ?? '') ?>">
                        </div>

                        
                        <div class="mb-3">
                            <label class="form-label">Room Type <span class="text-danger">*</span></label>
                            <select name="room_type_id" class="form-select" required>
                                <option value="">-- Select Type --</option>
                                <?php foreach ($roomTypes as $type): ?>
                                    <option value="<?= $type['id'] ?>"
                                        <?= (($_SESSION['old_input']['room_type_id'] ?? '') == $type['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($type['name']) ?>
                                        ($<?= number_format($type['base_price'], 2) ?>/night)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        
                        <div class="mb-3">
                            <label class="form-label">Floor <span class="text-danger">*</span></label>
                            <input type="number" name="floor" class="form-control"
                                   min="1" required
                                   value="<?= htmlspecialchars($_SESSION['old_input']['floor'] ?? '') ?>">
                        </div>

                        
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="3"
                                      placeholder="Optional notes..."
                            ><?= htmlspecialchars($_SESSION['old_input']['notes'] ?? '') ?></textarea>
                        </div>

                        <p class="text-muted small mb-0">
                            Status is automatically set to <strong>available</strong>.
                        </p>

                    </div>
                    <div class="card-footer text-end">
                        <?php unset($_SESSION['old_input']); ?>
                        <a href="<?= BASE_URL ?>/rooms" class="btn btn-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Create Room</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>
