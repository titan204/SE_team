<?php
$pageTitle = 'New Housekeeper Assignment';
ob_start();
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="mb-0"><i class="bi bi-plus-circle"></i> New Housekeeper Assignment</h2>
                <a href="<?= APP_URL ?>/housekeeper" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="<?= APP_URL ?>/housekeeper/store">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Room</label>
                                <select class="form-select" name="room_id">
                                    <option value="">Select room</option>
                                    <option value="305">Room 305</option>
                                    <option value="418">Room 418</option>
                                    <option value="221">Room 221</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Task Type</label>
                                <select class="form-select" name="task_type">
                                    <option value="">Select task</option>
                                    <option value="departure_clean">Departure Clean</option>
                                    <option value="vip_refresh">VIP Refresh</option>
                                    <option value="inspection_hold">Inspection Hold</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Room State</label>
                                <select class="form-select" name="room_state">
                                    <option value="dirty">Dirty</option>
                                    <option value="cleaning">In Cleaning</option>
                                    <option value="ready">Ready</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Front Desk Dependency</label>
                                <select class="form-select" name="reservation_hook">
                                    <option value="room_readiness">Room readiness dependency</option>
                                    <option value="early_check_in">Early check-in validation</option>
                                    <option value="vip_arrival">VIP arrival trigger</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Assignment Notes</label>
                                <textarea class="form-control" name="notes" rows="4" placeholder="Placeholder notes for housekeeping handoff and room status updates."></textarea>
                            </div>
                        </div>

                        <div class="card bg-light border-0 mt-4">
                            <div class="card-body">
                                <h6 class="mb-2">Integration Notes</h6>
                                <p class="text-muted mb-0">This placeholder form is prepared for reservation, inspection, and maintenance hooks without saving any business data yet.</p>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="<?= APP_URL ?>/housekeeper" class="btn btn-secondary me-2">Cancel</a>
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
