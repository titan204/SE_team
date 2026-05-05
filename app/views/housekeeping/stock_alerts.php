<?php $pageTitle = 'Low-Stock Alerts'; ob_start(); ?>

<style>
  .alert-card  { background:#fff; border:1px solid #e0d0c0; border-radius:10px; padding:1.5rem; box-shadow:0 2px 8px rgba(0,0,0,.06); margin-bottom:1.2rem; }
  .alert-table thead { background:#4B2E2B; color:#fff; }
  .alert-table tbody tr:hover { background:#fff8f0; }
  .badge-escalated { background:#dc3545; color:#fff; font-size:.72rem; padding:.25em .6em; border-radius:4px; }
  .badge-active    { background:#fd7e14; color:#fff; font-size:.72rem; padding:.25em .6em; border-radius:4px; }
  .req-section { background:#f8f9fa; border:1px dashed #ced4da; border-radius:8px; padding:1.2rem; }
</style>

<div class="container-fluid py-3">

  <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h4 class="mb-0"><i class="bi bi-exclamation-triangle me-2 text-warning"></i>Low-Stock Alerts</h4>
    <a href="<?= APP_URL ?>/?url=housekeeping/index" class="btn btn-sm btn-outline-secondary">
      <i class="bi bi-arrow-left me-1"></i>Back
    </a>
  </div>

  <?php if (!empty($_SESSION['hk_success'])): ?>
    <div class="alert alert-success alert-dismissible fade show py-2">
      <?= htmlspecialchars($_SESSION['hk_success']) ?>
      <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['hk_success']); ?>
  <?php endif; ?>

  <?php if (!empty($_SESSION['hk_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show py-2">
      <?= htmlspecialchars($_SESSION['hk_error']) ?>
      <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['hk_error']); ?>
  <?php endif; ?>

  <!-- Active Alerts Table -->
  <div class="alert-card">
    <h6 class="fw-semibold mb-3"><i class="bi bi-bell me-1"></i>Active Alerts</h6>

    <?php if (empty($alerts)): ?>
      <p class="text-muted mb-0"><i class="bi bi-check-circle text-success me-1"></i>All stock levels are within thresholds. No alerts.</p>
    <?php else: ?>
      <?php
        // Batch notification message (UC31 Step 2d)
        $names = array_column($alerts, 'item_name');
        $batchMsg = count($names) > 1
          ? count($names) . ' items need restocking: ' . implode(', ', $names)
          : '1 item needs restocking: ' . ($names[0] ?? '');
      ?>
      <div class="alert alert-warning py-2 small mb-3">
        <i class="bi bi-megaphone me-1"></i><strong>Dashboard notification:</strong> <?= htmlspecialchars($batchMsg) ?>
      </div>

      <form method="POST" action="<?= APP_URL ?>/?url=housekeeping/createRequisition" id="requisition-form">
        <div class="table-responsive">
          <table class="table alert-table table-hover align-middle mb-0" id="stock-alerts-table">
            <thead>
              <tr>
                <th><input type="checkbox" id="selectAllAlerts" onchange="toggleAllAlerts(this)"></th>
                <th>Item</th>
                <th>Location</th>
                <th>Current Stock</th>
                <th>Min Threshold</th>
                <th>Escalated</th>
                <th>Created</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
            <?php foreach ($alerts as $a): ?>
              <tr>
                <td>
                  <input type="checkbox" name="alert_ids[]" value="<?= (int)$a['id'] ?>"
                         class="alert-check form-check-input">
                </td>
                <td><strong><?= htmlspecialchars($a['item_name']) ?></strong>
                  <small class="text-muted d-block"><?= htmlspecialchars($a['unit']) ?></small></td>
                <td><?= htmlspecialchars($a['location']) ?></td>
                <td><span class="badge bg-danger"><?= (int)$a['current_stock'] ?></span></td>
                <td><?= (int)$a['min_threshold'] ?></td>
                <td>
                  <?php if ($a['escalated']): ?>
                    <span class="badge-escalated">ESCALATED</span>
                  <?php else: ?>
                    <span class="badge-active">Active</span>
                  <?php endif; ?>
                </td>
                <td><small><?= htmlspecialchars($a['created_at'] ?? '') ?></small></td>
                <td>
                  <!-- Acknowledge -->
                  <form method="POST"
                        action="<?= APP_URL ?>/?url=housekeeping/acknowledgeAlert/<?= (int)$a['id'] ?>"
                        style="display:inline;">
                    <button type="submit" class="btn btn-sm btn-outline-success"
                            id="btn-ack-<?= (int)$a['id'] ?>">
                      <i class="bi bi-check"></i> Ack
                    </button>
                  </form>
                  <!-- Dismiss (supervisor/manager only) -->
                  <?php
                    $role = $_SESSION['user_role'] ?? '';
                    if (in_array($role, ['supervisor', 'manager'])):
                  ?>
                  <button type="button" class="btn btn-sm btn-outline-danger ms-1"
                          onclick="openDismissModal(<?= (int)$a['id'] ?>)"
                          id="btn-dismiss-<?= (int)$a['id'] ?>">
                    <i class="bi bi-x"></i> Dismiss
                  </button>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <!-- Restocking Requisition Section -->
        <div class="req-section mt-3">
          <h6 class="fw-semibold mb-2"><i class="bi bi-cart-plus me-1"></i>Create Restocking Requisition</h6>
          <p class="text-muted small mb-2">Select alerts above, then specify quantities needed below.</p>
          <?php foreach ($supplyItems as $i => $si): ?>
            <div class="row g-2 align-items-center mb-1">
              <div class="col-md-5 small"><?= htmlspecialchars($si['name']) ?>
                <span class="text-muted">(stock: <?= (int)$si['current_stock'] ?> / min: <?= (int)$si['min_threshold'] ?>)</span>
              </div>
              <div class="col-md-3">
                <input type="hidden" name="items[<?= $i ?>][item_id]" value="<?= (int)$si['id'] ?>">
                <input type="number" name="items[<?= $i ?>][quantity_needed]"
                       class="form-control form-control-sm" min="0" value="0"
                       placeholder="Qty needed" id="req_qty_<?= (int)$si['id'] ?>">
              </div>
              <div class="col-md-4 text-muted small"><?= htmlspecialchars($si['unit']) ?></div>
            </div>
          <?php endforeach; ?>
          <button type="submit" class="btn btn-primary btn-sm mt-2 px-4" id="btn-create-requisition">
            <i class="bi bi-send me-1"></i>Submit Requisition
          </button>
        </div>
      </form>
    <?php endif; ?>
  </div>

</div>

<!-- Dismiss Modal (supervisor/manager) -->
<div class="modal fade" id="dismissModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-x-circle me-2 text-danger"></i>Dismiss Alert</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" id="dismiss-form">
        <div class="modal-body">
          <p class="text-muted small">Dismissing logs a reason and resolves the alert. Use for false thresholds only.</p>
          <div class="mb-3">
            <label for="dismiss_reason" class="form-label fw-semibold">Reason <span class="text-danger">*</span></label>
            <textarea id="dismiss_reason" name="reason" class="form-control" rows="2"
                      placeholder="e.g. Threshold misconfigured, item discontinued…" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger" id="btn-confirm-dismiss">Dismiss Alert</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function toggleAllAlerts(cb) {
  document.querySelectorAll('.alert-check').forEach(c => c.checked = cb.checked);
}
function openDismissModal(alertId) {
  document.getElementById('dismiss-form').action =
    '<?= APP_URL ?>/?url=housekeeping/dismissAlert/' + alertId;
  document.getElementById('dismiss_reason').value = '';
  new bootstrap.Modal(document.getElementById('dismissModal')).show();
}
</script>

<?php $content = ob_get_clean(); require VIEW_PATH . '/layouts/main.php'; ?>
