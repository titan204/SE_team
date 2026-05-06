<?php $pageTitle = 'Minibar Consumption — Room ' . htmlspecialchars($room['room_number']); ob_start(); ?>

<style>
  .mb-card { background:#fff; border:1px solid #e0d0c0; border-radius:10px; padding:1.5rem; box-shadow:0 2px 8px rgba(0,0,0,.06); }
  .mb-table thead { background:#4B2E2B; color:#fff; }
  .qty-input { width:80px; }
  .manual-section { background:#fff8f0; border:1px dashed #c09060; border-radius:8px; padding:1.2rem; }
</style>

<div class="container-fluid py-3">
  <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h4 class="mb-0">
      <i class="bi bi-basket me-2"></i>Minibar Log — Room
      <span class="badge bg-secondary"><?= htmlspecialchars($room['room_number']) ?></span>
    </h4>
    <div class="d-flex gap-2">
      <a href="<?= APP_URL ?>/?url=housekeeping/minibarLog/<?= (int)$room['id'] ?>"
         class="btn btn-sm btn-outline-secondary" id="btn-view-minibar-log">
        <i class="bi bi-journal-text me-1"></i>View Log
      </a>
      <a href="<?= APP_URL ?>/?url=housekeeping/index" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back
      </a>
    </div>
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

  <?php if (!$reservation): ?>
    <div class="alert alert-warning">
      <i class="bi bi-exclamation-triangle me-2"></i>
      <strong>No active guest in this room.</strong>
      Consumption can still be logged but charges will NOT be posted to a bill.
    </div>
  <?php else: ?>
    <div class="alert alert-info py-2 small">
      <i class="bi bi-person-check me-1"></i>
      Charges will be posted to <strong>Reservation #<?= $reservation['id'] ?></strong>.
    </div>
  <?php endif; ?>

  <form method="POST" action="<?= APP_URL ?>/?url=housekeeping/logMinibar/<?= (int)$room['id'] ?>">

    <div class="mb-card mb-4">
      <h6 class="mb-3 fw-semibold"><i class="bi bi-list-ul me-1"></i>Minibar Items</h6>

      <?php if (empty($inventory)): ?>
        <p class="text-muted">No minibar inventory configured for this room.</p>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table mb-table table-hover align-middle">
            <thead>
              <tr>
                <th>Item</th>
                <th>SKU</th>
                <th>Price</th>
                <th>In Stock</th>
                <th>Qty Consumed</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($inventory as $i => $inv): ?>
              <tr>
                <td><?= htmlspecialchars($inv['name']) ?></td>
                <td><code><?= htmlspecialchars($inv['sku']) ?></code></td>
                <td>$<?= number_format((float)$inv['price'], 2) ?></td>
                <td>
                  <?php if ((int)$inv['current_stock'] <= (int)$inv['reorder_threshold']): ?>
                    <span class="badge bg-warning text-dark"><?= $inv['current_stock'] ?></span>
                  <?php else: ?>
                    <span class="badge bg-success"><?= $inv['current_stock'] ?></span>
                  <?php endif; ?>
                </td>
                <td>
                  <input type="hidden"  name="items[<?= $i ?>][item_id]" value="<?= $inv['item_id'] ?>">
                  <input type="number" name="items[<?= $i ?>][quantity]"
                         class="form-control form-control-sm qty-input"
                         min="0" max="<?= $inv['current_stock'] ?>" value="0"
                         id="qty_<?= $inv['item_id'] ?>">
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

    <!-- Manual entry for unlisted items -->
    <div class="manual-section mb-4">
      <h6 class="fw-semibold mb-2"><i class="bi bi-pencil-square me-1"></i>Manual Entry <small class="text-muted">(item not in system)</small></h6>
      <div class="row g-2">
        <div class="col-md-5">
          <input type="text" name="manual_description" class="form-control form-control-sm"
                 placeholder="Item description (required if entering manually)">
        </div>
        <div class="col-md-2">
          <input type="number" name="manual_price" class="form-control form-control-sm"
                 step="0.01" min="0" placeholder="Unit price">
        </div>
        <div class="col-md-2">
          <input type="number" name="manual_qty" class="form-control form-control-sm"
                 min="1" value="1" placeholder="Qty">
        </div>
        <div class="col-md-3">
          <span class="text-muted small">⚑ Flagged for manager review</span>
        </div>
      </div>
    </div>

    <div class="d-flex gap-2">
      <button type="submit" class="btn btn-primary px-4" id="btn-log-minibar">
        <i class="bi bi-check-lg me-1"></i>Log Consumption
      </button>
      <a href="<?= APP_URL ?>/?url=housekeeping/index" class="btn btn-outline-secondary">Cancel</a>
    </div>
  </form>
</div>

<?php $content = ob_get_clean(); require VIEW_PATH . '/layouts/main.php'; ?>
