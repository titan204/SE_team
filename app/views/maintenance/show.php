<?php $pageTitle = 'Work Order #' . $order['id']; ob_start(); ?>

<style>
  .wo-detail-card { background:#fff; border:1px solid #dee2e6; border-radius:10px; padding:1.5rem; box-shadow:0 2px 8px rgba(0,0,0,.05); }
  .log-item       { border-left:3px solid #dee2e6; padding:.4rem .8rem; margin-bottom:.4rem; }
  .log-item.completed  { border-color:#198754; }
  .log-item.closed     { border-color:#212529; }
  .log-item.rejected   { border-color:#dc3545; }
  .log-item.created    { border-color:#0d6efd; }
</style>

<div class="container-fluid py-3">
  <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h4 class="mb-0">
      <i class="bi bi-wrench me-2"></i>Work Order #<?= $order['id'] ?>
      <span class="badge <?= $order['type'] === 'emergency' ? 'bg-danger' : 'bg-primary' ?> ms-1">
        <?= ucfirst($order['type']) ?>
      </span>
    </h4>
    <a href="<?= APP_URL ?>/?url=maintenance/index" class="btn btn-sm btn-outline-secondary">
      <i class="bi bi-arrow-left me-1"></i>Back to List
    </a>
  </div>

  <?php foreach (['maint_success','maint_error'] as $k): ?>
    <?php if (!empty($_SESSION[$k])): ?>
      <div class="alert alert-<?= $k === 'maint_success' ? 'success' : 'danger' ?> alert-dismissible fade show py-2">
        <?= htmlspecialchars($_SESSION[$k]) ?>
        <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
      </div>
      <?php unset($_SESSION[$k]); ?>
    <?php endif; ?>
  <?php endforeach; ?>

  <div class="row g-3">

    <!-- Left: details -->
    <div class="col-lg-8">
      <div class="wo-detail-card mb-3">
        <h6 class="fw-semibold mb-3">Details</h6>
        <div class="row g-2 small">
          <div class="col-6 col-md-3"><span class="text-muted d-block">Priority</span>
            <?php $pc = match($order['priority']) { 'emergency'=>'danger','high'=>'warning','normal'=>'primary',default=>'secondary' }; ?>
            <span class="badge bg-<?= $pc ?>"><?= ucfirst($order['priority']) ?></span>
          </div>
          <div class="col-6 col-md-3"><span class="text-muted d-block">Status</span>
            <?php $sc = match($order['status']) { 'open'=>'warning text-dark','in_progress'=>'info text-dark','completed'=>'success','closed'=>'dark','rejected'=>'danger',default=>'secondary' }; ?>
            <span class="badge bg-<?= $sc ?>"><?= ucwords(str_replace('_',' ',$order['status'])) ?></span>
          </div>
          <div class="col-6 col-md-3"><span class="text-muted d-block">Location</span>
            <?= $order['room_number'] ? 'Room ' . htmlspecialchars($order['room_number'])
                                      : htmlspecialchars($order['asset_name'] ?? '—') ?>
          </div>
          <div class="col-6 col-md-3"><span class="text-muted d-block">Assigned To</span>
            <?= htmlspecialchars($order['assigned_name'] ?? '—') ?>
          </div>
          <div class="col-6 col-md-3"><span class="text-muted d-block">Created By</span>
            <?= htmlspecialchars($order['created_by_name'] ?? '—') ?>
          </div>
          <div class="col-6 col-md-3"><span class="text-muted d-block">Created At</span>
            <?= htmlspecialchars($order['created_at']) ?>
          </div>
          <?php if ($order['completed_at']): ?>
          <div class="col-6 col-md-3"><span class="text-muted d-block">Completed At</span>
            <?= htmlspecialchars($order['completed_at']) ?>
          </div>
          <?php endif; ?>
          <?php if ($order['supervisor_name']): ?>
          <div class="col-6 col-md-3"><span class="text-muted d-block">Supervisor</span>
            <?= htmlspecialchars($order['supervisor_name']) ?>
          </div>
          <?php endif; ?>
        </div>

        <hr>
        <h6 class="fw-semibold">Description</h6>
        <p class="mb-0"><?= nl2br(htmlspecialchars($order['description'])) ?></p>

        <?php if ($order['work_performed']): ?>
          <hr>
          <h6 class="fw-semibold">Work Performed</h6>
          <p class="mb-0"><?= nl2br(htmlspecialchars($order['work_performed'])) ?></p>
          <?php if ($order['time_spent_minutes']): ?>
            <small class="text-muted">Time: <?= (int)$order['time_spent_minutes'] ?> minutes</small>
          <?php endif; ?>
        <?php endif; ?>

        <?php if ($order['rejection_reason']): ?>
          <hr>
          <div class="alert alert-danger py-2 mb-0">
            <strong>Rejection Reason:</strong> <?= htmlspecialchars($order['rejection_reason']) ?>
          </div>
        <?php endif; ?>
      </div>

      <!-- Activity log -->
      <div class="wo-detail-card">
        <h6 class="fw-semibold mb-3"><i class="bi bi-clock-history me-1"></i>Activity Log</h6>
        <?php if (empty($logs)): ?>
          <p class="text-muted small mb-0">No log entries.</p>
        <?php else: ?>
          <?php foreach ($logs as $log): ?>
            <div class="log-item <?= str_contains($log['action'], 'complete') ? 'completed' : (str_contains($log['action'], 'close') ? 'closed' : (str_contains($log['action'], 'reject') ? 'rejected' : (str_contains($log['action'], 'create') ? 'created' : ''))) ?>">
              <div class="d-flex justify-content-between">
                <span class="fw-semibold small"><?= ucwords(str_replace('_', ' ', $log['action'])) ?></span>
                <span class="text-muted small"><?= htmlspecialchars($log['created_at']) ?></span>
              </div>
              <div class="small text-muted">by <?= htmlspecialchars($log['performed_by_name'] ?? 'System') ?></div>
              <?php if ($log['notes']): ?>
                <div class="small mt-1"><?= htmlspecialchars($log['notes']) ?></div>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

    <!-- Right: actions -->
    <div class="col-lg-4">

      <!-- Progress update -->
      <?php if (in_array($order['status'], ['open','in_progress'])): ?>
      <div class="wo-detail-card mb-3">
        <h6 class="fw-semibold mb-2"><i class="bi bi-pencil me-1"></i>Update Progress</h6>
        <form method="POST" action="<?= APP_URL ?>/?url=maintenance/progress/<?= $order['id'] ?>">
          <textarea name="notes" class="form-control form-control-sm mb-2" rows="3"
                    placeholder="Progress notes…" required></textarea>
          <button type="submit" class="btn btn-sm btn-outline-primary w-100" id="btn-progress-<?= $order['id'] ?>">
            <i class="bi bi-arrow-clockwise me-1"></i>Update Progress
          </button>
        </form>
      </div>
      <?php endif; ?>

      <!-- Technician complete -->
      <?php if (in_array($order['status'], ['open','in_progress','pending_parts'])): ?>
      <div class="wo-detail-card mb-3">
        <h6 class="fw-semibold mb-2"><i class="bi bi-check-circle me-1"></i>Mark Complete</h6>
        <form method="POST" action="<?= APP_URL ?>/?url=maintenance/complete/<?= $order['id'] ?>">
          <textarea name="work_performed" class="form-control form-control-sm mb-2" rows="3"
                    placeholder="Describe work performed (required)…" required></textarea>
          <div class="mb-2">
            <input type="number" name="time_spent_minutes" class="form-control form-control-sm"
                   placeholder="Time spent (minutes)" min="1">
          </div>
          <button type="submit" class="btn btn-sm btn-success w-100" id="btn-complete-<?= $order['id'] ?>">
            <i class="bi bi-check-lg me-1"></i>Mark as Complete
          </button>
        </form>
      </div>
      <?php endif; ?>

      <!-- Supervisor close -->
      <?php if ($order['status'] === 'completed'): ?>
      <div class="wo-detail-card mb-3">
        <h6 class="fw-semibold mb-2"><i class="bi bi-lock me-1"></i>Supervisor Sign-Off</h6>
        <form method="POST" action="<?= APP_URL ?>/?url=maintenance/close/<?= $order['id'] ?>">
          <button type="submit" class="btn btn-sm btn-dark w-100" id="btn-close-<?= $order['id'] ?>"
                  onclick="return confirm('Close this work order?')">
            <i class="bi bi-shield-check me-1"></i>Close Work Order
          </button>
        </form>

        <div class="mt-2">
          <button class="btn btn-sm btn-outline-danger w-100" data-bs-toggle="collapse"
                  data-bs-target="#reject-form-<?= $order['id'] ?>">
            <i class="bi bi-x-circle me-1"></i>Reject
          </button>
          <div class="collapse mt-2" id="reject-form-<?= $order['id'] ?>">
            <form method="POST" action="<?= APP_URL ?>/?url=maintenance/reject/<?= $order['id'] ?>">
              <textarea name="rejection_reason" class="form-control form-control-sm mb-1" rows="2"
                        placeholder="Rejection reason…" required></textarea>
              <button type="submit" class="btn btn-sm btn-danger w-100"
                      id="btn-reject-<?= $order['id'] ?>">Confirm Reject</button>
            </form>
          </div>
        </div>
      </div>
      <?php endif; ?>

    </div><!-- col-lg-4 -->
  </div><!-- row -->
</div>

<?php $content = ob_get_clean(); require VIEW_PATH . '/layouts/main.php'; ?>
