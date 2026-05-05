<?php $pageTitle = 'Work Orders'; ob_start(); ?>

<style>
  .wo-card  { background:#fff; border:1px solid #dee2e6; border-radius:10px; padding:1.4rem; box-shadow:0 2px 8px rgba(0,0,0,.05); }
  .wo-table thead { background:#212529; color:#fff; }
  .wo-table tbody tr:hover { background:#f8f9fa; }
  .badge-emergency { background:#dc3545; }
  .badge-high      { background:#fd7e14; }
  .badge-normal    { background:#0d6efd; }
  .badge-low       { background:#6c757d; }
</style>

<div class="container-fluid py-3">
  <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h4 class="mb-0"><i class="bi bi-wrench-adjustable me-2"></i>Maintenance Work Orders</h4>
    <div class="d-flex gap-2">
      <a href="<?= APP_URL ?>/?url=maintenance/emergency" class="btn btn-danger btn-sm">
        <i class="bi bi-exclamation-triangle me-1"></i>Log Emergency
      </a>
      <a href="<?= APP_URL ?>/?url=maintenance/preventative" class="btn btn-primary btn-sm">
        <i class="bi bi-calendar-plus me-1"></i>Schedule Preventative
      </a>
    </div>
  </div>

  <?php foreach (['maint_success','maint_error'] as $key): ?>
    <?php if (!empty($_SESSION[$key])): ?>
      <div class="alert alert-<?= $key === 'maint_success' ? 'success' : 'danger' ?> alert-dismissible fade show py-2">
        <?= htmlspecialchars($_SESSION[$key]) ?>
        <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
      </div>
      <?php unset($_SESSION[$key]); ?>
    <?php endif; ?>
  <?php endforeach; ?>

  <!-- Filters -->
  <div class="wo-card mb-3">
    <form method="GET" action="<?= APP_URL ?>/" class="row g-2 align-items-end">
      <input type="hidden" name="url" value="maintenance/index">
      <div class="col-md-2">
        <label class="form-label small mb-1">Status</label>
        <select name="status" class="form-select form-select-sm">
          <option value="">All</option>
          <?php foreach (['open','in_progress','pending_parts','completed','closed','rejected'] as $s): ?>
            <option value="<?= $s ?>" <?= ($filters['status'] ?? '') === $s ? 'selected' : '' ?>><?= ucwords(str_replace('_',' ',$s)) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label small mb-1">Priority</label>
        <select name="priority" class="form-select form-select-sm">
          <option value="">All</option>
          <?php foreach (['emergency','high','normal','low'] as $p): ?>
            <option value="<?= $p ?>" <?= ($filters['priority'] ?? '') === $p ? 'selected' : '' ?>><?= ucfirst($p) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label small mb-1">Type</label>
        <select name="type" class="form-select form-select-sm">
          <option value="">All</option>
          <option value="emergency"    <?= ($filters['type'] ?? '') === 'emergency'    ? 'selected' : '' ?>>Emergency</option>
          <option value="preventative" <?= ($filters['type'] ?? '') === 'preventative' ? 'selected' : '' ?>>Preventative</option>
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label small mb-1">Technician</label>
        <select name="technician" class="form-select form-select-sm">
          <option value="">All</option>
          <?php foreach ($technicians as $t): ?>
            <option value="<?= $t['id'] ?>" <?= ($filters['technician'] ?? '') == $t['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($t['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label small mb-1">From</label>
        <input type="date" name="date_from" class="form-control form-control-sm" value="<?= htmlspecialchars($filters['date_from'] ?? '') ?>">
      </div>
      <div class="col-md-1">
        <label class="form-label small mb-1">To</label>
        <input type="date" name="date_to" class="form-control form-control-sm" value="<?= htmlspecialchars($filters['date_to'] ?? '') ?>">
      </div>
      <div class="col-md-1">
        <button type="submit" class="btn btn-sm btn-outline-primary w-100">Filter</button>
      </div>
    </form>
  </div>

  <!-- Table -->
  <div class="wo-card">
    <?php if (empty($orders)): ?>
      <p class="text-muted mb-0">No work orders match the current filters.</p>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table wo-table align-middle mb-0">
          <thead>
            <tr>
              <th>#ID</th><th>Type</th><th>Location</th><th>Description</th>
              <th>Priority</th><th>Status</th><th>Assigned To</th><th>Created</th><th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($orders as $o): ?>
            <tr>
              <td><strong>#<?= $o['id'] ?></strong></td>
              <td>
                <?php if ($o['type'] === 'emergency'): ?>
                  <span class="badge bg-danger">Emergency</span>
                <?php else: ?>
                  <span class="badge bg-primary">Preventative</span>
                <?php endif; ?>
              </td>
              <td>
                <?php if ($o['room_number']): ?>
                  <i class="bi bi-door-closed me-1"></i>Room <?= htmlspecialchars($o['room_number']) ?>
                <?php elseif ($o['asset_name']): ?>
                  <i class="bi bi-gear me-1"></i><?= htmlspecialchars($o['asset_name']) ?>
                <?php else: ?>
                  <span class="text-muted">—</span>
                <?php endif; ?>
              </td>
              <td class="small" style="max-width:200px"><?= htmlspecialchars(substr($o['description'], 0, 80)) ?>…</td>
              <td>
                <?php
                  $pClass = match($o['priority']) {
                      'emergency' => 'badge-emergency', 'high' => 'badge-high',
                      'normal'    => 'badge-normal',    default => 'badge-low',
                  };
                ?>
                <span class="badge <?= $pClass ?>"><?= ucfirst($o['priority']) ?></span>
              </td>
              <td>
                <?php
                  $sClass = match($o['status']) {
                      'open'          => 'bg-warning text-dark',
                      'in_progress'   => 'bg-info text-dark',
                      'pending_parts' => 'bg-secondary',
                      'completed'     => 'bg-success',
                      'closed'        => 'bg-dark',
                      'rejected'      => 'bg-danger',
                      default         => 'bg-secondary',
                  };
                ?>
                <span class="badge <?= $sClass ?>"><?= ucwords(str_replace('_',' ',$o['status'])) ?></span>
              </td>
              <td class="small"><?= htmlspecialchars($o['assigned_name'] ?? '—') ?></td>
              <td class="small"><?= date('d M', strtotime($o['created_at'])) ?></td>
              <td>
                <a href="<?= APP_URL ?>/?url=maintenance/show/<?= $o['id'] ?>"
                   class="btn btn-sm btn-outline-secondary" id="btn-wo-view-<?= $o['id'] ?>">
                  <i class="bi bi-eye"></i>
                </a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php $content = ob_get_clean(); require VIEW_PATH . '/layouts/main.php'; ?>
