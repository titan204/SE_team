<?php
$pageTitle = 'Edit Supervisor Oversight';
ob_start();
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="mb-0"><i class="bi bi-pencil-square"></i> Edit Supervisor Oversight</h2>
                <a href="<?= APP_URL ?>/supervisor/show/1" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Details
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="<?= APP_URL ?>/supervisor/update/1">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Room</label>
                                <input type="text" class="form-control" name="room_label" value="Room 418">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Quality Status</label>
                                <select class="form-select" name="quality_status">
                                    <option value="pending" selected>Pending Approval</option>
                                    <option value="approved">Approved</option>
                                    <option value="rework">Needs Rework</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Maintenance Hold</label>
                                <select class="form-select" name="maintenance_status">
                                    <option value="clear" selected>Clear</option>
                                    <option value="flagged">Flagged</option>
                                    <option value="blocked">Blocked</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Front Desk Release</label>
                                <select class="form-select" name="release_status">
                                    <option value="waiting" selected>Waiting Approval</option>
                                    <option value="released">Released</option>
                                    <option value="held">Held</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Review Notes</label>
                                <textarea class="form-control" name="notes" rows="4">Placeholder note for approval outcome, inspection comments, and escalation tracking.</textarea>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="<?= APP_URL ?>/supervisor/show/1" class="btn btn-secondary me-2">Cancel</a>
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
