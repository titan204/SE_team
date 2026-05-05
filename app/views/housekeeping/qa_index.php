<?php $pageTitle = 'Quality Assurance'; ob_start(); ?>

<style>
  .qa-card  { background:#fff; border:1px solid #e0d0c0; border-radius:10px; padding:1.4rem; box-shadow:0 2px 8px rgba(0,0,0,.05); }
  .qa-table thead { background:#4B2E2B; color:#fff; }
  .qa-table tbody tr:hover { background:#fff8f0; }
  .badge-pass { background:#198754; }
  .badge-flag { background:#dc3545; }
</style>

<div class="container-fluid py-3">
  <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h4 class="mb-0"><i class="bi bi-clipboard2-check me-2"></i>Quality Assurance</h4>
    <a href="<?= APP_URL ?>/?url=housekeeping/qaTrends" class="btn btn-sm btn-outline-secondary">
      <i class="bi bi-bar-chart me-1"></i>QA Trends
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

  <!-- Tabs -->
  <ul class="nav nav-tabs mb-3" id="qaTabs">
    <li class="nav-item">
      <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-pending">
        Pending Inspection
        <span class="badge bg-secondary ms-1"><?= count($pendingRooms) ?></span>
      </button>
    </li>
    <li class="nav-item">
      <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-flagged">
        <span class="text-danger"><i class="bi bi-flag me-1"></i>Flagged by Guest Feedback</span>
        <span class="badge bg-danger ms-1"><?= count($flaggedRooms) ?></span>
      </button>
    </li>
  </ul>

  <div class="tab-content">

    <!-- Tab 1: Pending -->
    <div class="tab-pane fade show active" id="tab-pending">
      <div class="qa-card">
        <?php if (empty($pendingRooms)): ?>
          <p class="text-muted mb-0"><i class="bi bi-check-circle me-1"></i>All rooms have been inspected today.</p>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table qa-table align-middle mb-0">
              <thead>
                <tr>
                  <th>Room</th><th>Floor</th><th>Type</th><th>Status</th><th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($pendingRooms as $r): ?>
                <tr>
                  <td><strong><?= htmlspecialchars($r['room_number']) ?></strong></td>
                  <td>Floor <?= $r['floor'] ?></td>
                  <td><?= htmlspecialchars($r['room_type']) ?></td>
                  <td>
                    <span class="badge bg-secondary"><?= ucfirst($r['status']) ?></span>
                  </td>
                  <td>
                    <a href="<?= APP_URL ?>/?url=housekeeping/qaInspect/<?= (int)$r['id'] ?>"
                       class="btn btn-sm btn-primary" id="btn-inspect-<?= $r['id'] ?>">
                      <i class="bi bi-clipboard-check me-1"></i>Start Inspection
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

    <!-- Tab 2: Flagged -->
    <div class="tab-pane fade" id="tab-flagged">
      <div class="qa-card">
        <?php if (empty($flaggedRooms)): ?>
          <p class="text-muted mb-0">No rooms flagged by guest feedback.</p>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table qa-table align-middle mb-0">
              <thead>
                <tr>
                  <th>Room</th><th>Floor</th><th>Type</th><th>Feedback Rating</th><th>Comment</th><th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($flaggedRooms as $r): ?>
                <tr>
                  <td><strong><?= htmlspecialchars($r['room_number']) ?></strong></td>
                  <td>Floor <?= $r['floor'] ?></td>
                  <td><?= htmlspecialchars($r['room_type']) ?></td>
                  <td>
                    <span class="badge badge-flag"><?= $r['feedback_rating'] ?>/5</span>
                  </td>
                  <td class="small text-muted" style="max-width:220px"><?= htmlspecialchars(substr($r['comments'] ?? '', 0, 100)) ?>…</td>
                  <td>
                    <a href="<?= APP_URL ?>/?url=housekeeping/qaInspect/<?= (int)$r['id'] ?>"
                       class="btn btn-sm btn-danger" id="btn-urgent-inspect-<?= $r['id'] ?>">
                      <i class="bi bi-exclamation-triangle me-1"></i>Urgent Inspect
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

  </div><!-- tab-content -->
</div>

<?php $content = ob_get_clean(); require VIEW_PATH . '/layouts/main.php'; ?>
