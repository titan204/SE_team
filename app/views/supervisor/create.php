<?php
$pageTitle = 'New Supervisor Oversight Record';
ob_start();
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="mb-0"><i class="bi bi-plus-circle"></i> New Supervisor Oversight Record</h2>
                <a href="<?= APP_URL ?>/supervisor" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="<?= APP_URL ?>/supervisor/store">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Room</label>
                                <select class="form-select" name="room_id">
                                    <option value="">Select room</option>
                                    <option value="418">Room 418</option>
                                    <option value="221">Room 221</option>
                                    <option value="507">Room 507</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Oversight Type</label>
                                <select class="form-select" name="oversight_type">
                                    <option value="">Select oversight type</option>
                                    <option value="inspection">Inspection</option>
                                    <option value="approval">Approval</option>
                                    <option value="escalation">Escalation</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Approval Stage</label>
                                <select class="form-select" name="approval_status">
                                    <option value="pending">Pending</option>
                                    <option value="approved">Approved</option>
                                    <option value="rework">Needs Rework</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Maintenance Dependency</label>
                                <select class="form-select" name="maintenance_status">
                                    <option value="clear">Clear</option>
                                    <option value="flagged">Flagged</option>
                                    <option value="blocked">Blocked</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Supervisor Notes</label>
                                <textarea class="form-control" name="notes" rows="4" placeholder="Placeholder notes for quality inspection, approvals, and maintenance follow-up."></textarea>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="<?= APP_URL ?>/supervisor" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Save Placeholder</button>
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
