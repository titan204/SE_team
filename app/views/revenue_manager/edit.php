<?php
$pageTitle = 'Edit Revenue Report Preset';
ob_start();
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="mb-0"><i class="bi bi-pencil-square"></i> Edit Revenue Report Preset</h2>
                <a href="<?= APP_URL ?>/revenue_manager/show/1" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Details
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="<?= APP_URL ?>/revenue_manager/update/1">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Preset Name</label>
                                <input type="text" class="form-control" name="preset_name" value="Daily Revenue Summary">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Summary Status</label>
                                <select class="form-select" name="summary_status">
                                    <option value="draft">Draft</option>
                                    <option value="ready" selected>Snapshot Ready</option>
                                    <option value="archived">Archived</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Folio Hook</label>
                                <select class="form-select" name="folio_hook">
                                    <option value="summary" selected>Billing Summary</option>
                                    <option value="aggregation">Folio Aggregation</option>
                                    <option value="audit">Audit Trail</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Upgrade Watch</label>
                                <select class="form-select" name="upgrade_watch">
                                    <option value="enabled" selected>Enabled</option>
                                    <option value="disabled">Disabled</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Notes</label>
                                <textarea class="form-control" name="notes" rows="4">Placeholder note for audit hooks, folio visibility, and report behavior.</textarea>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="<?= APP_URL ?>/revenue_manager/show/1" class="btn btn-secondary me-2">Cancel</a>
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
