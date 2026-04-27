<?php
$pageTitle = 'New Revenue Report Preset';
ob_start();
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="mb-0"><i class="bi bi-plus-circle"></i> New Revenue Report Preset</h2>
                <a href="<?= APP_URL ?>/revenue_manager" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="<?= APP_URL ?>/revenue_manager/store">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Preset Name</label>
                                <input type="text" class="form-control" name="preset_name" placeholder="Daily Revenue Summary">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Reporting Scope</label>
                                <select class="form-select" name="scope">
                                    <option value="">Select scope</option>
                                    <option value="billing">Billing Summary</option>
                                    <option value="folios">Folio Aggregation</option>
                                    <option value="vip">VIP Upgrade Monitor</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Period Start</label>
                                <input type="date" class="form-control" name="period_start">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Period End</label>
                                <input type="date" class="form-control" name="period_end">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Audit / Notes</label>
                                <textarea class="form-control" name="notes" rows="4" placeholder="Placeholder notes for audit trail and reporting filters."></textarea>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="<?= APP_URL ?>/revenue_manager" class="btn btn-secondary me-2">Cancel</a>
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
