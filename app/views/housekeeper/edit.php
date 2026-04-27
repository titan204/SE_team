<?php
$pageTitle = 'Edit Housekeeper Assignment';
ob_start();
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="mb-0"><i class="bi bi-pencil-square"></i> Edit Housekeeper Assignment</h2>
                <a href="<?= APP_URL ?>/housekeeper/show/1" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Details
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="<?= APP_URL ?>/housekeeper/update/1">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Assigned Room</label>
                                <input type="text" class="form-control" name="room_label" value="Room 305">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Readiness Status</label>
                                <select class="form-select" name="readiness_status">
                                    <option value="dirty">Dirty</option>
                                    <option value="cleaning" selected>In Cleaning</option>
                                    <option value="ready">Ready</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Inspection Handoff</label>
                                <select class="form-select" name="inspection_status">
                                    <option value="pending" selected>Pending Supervisor</option>
                                    <option value="approved">Approved</option>
                                    <option value="rework">Needs Rework</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Maintenance Dependency</label>
                                <select class="form-select" name="maintenance_flag">
                                    <option value="clear" selected>Clear</option>
                                    <option value="flagged">Flagged</option>
                                    <option value="blocked">Blocked</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Workflow Notes</label>
                                <textarea class="form-control" name="notes" rows="4">Placeholder note for readiness progression and front-desk release.</textarea>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="<?= APP_URL ?>/housekeeper/show/1" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Placeholder</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require VIEW_PATH . '/layouts/main.php';
?>
