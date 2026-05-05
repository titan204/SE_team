<?php $pageTitle = 'Task Board'; ?>
<?php ob_start(); ?>

<style>
  .hk-badge-pending     { background:#f0a500; color:#fff; }
  .hk-badge-in_progress { background:#2196a5; color:#fff; }
  .hk-badge-done        { background:#3a8a3a; color:#fff; }
  .hk-badge-skipped     { background:#888;    color:#fff; }
  .hk-type-cleaning     { background:#c08552; color:#fff; }
  .hk-type-inspection   { background:#4b2e2b; color:#fff; }
  .hk-type-turndown     { background:#8c5a3c; color:#fff; }
  .hk-type-restocking   { background:#6a9a6a; color:#fff; }
  .hk-type-deep_clean   { background:#7a3a7a; color:#fff; }
  .hk-type-minibar_check{ background:#3a5a8a; color:#fff; }
  .status-badge, .type-badge {
    font-size:.75rem; padding:.28em .7em; border-radius:20px; font-weight:600;
  }
  .table td, .table th { vertical-align:middle; }
  .empty-state { text-align:center; padding:3rem 1rem; color:#a08060; }
  .empty-state i { font-size:3rem; opacity:.4; }
  .filter-card { background:#fff; border:1px solid #e8d5c0; border-radius:10px; padding:1.2rem 1.5rem; margin-bottom:1.5rem; box-shadow:0 2px 8px rgba(192,133,82,.08); }
  .table-card  { background:#fff; border:1px solid #e8d5c0; border-radius:10px; overflow:hidden; box-shadow:0 2px 8px rgba(192,133,82,.08); }
  .page-header { border-bottom:2px solid var(--accent); padding-bottom:.75rem; margin-bottom:1.5rem; }
  .page-header h2 { color:var(--dark); font-weight:700; }
  .summary-card { border-radius:10px; border:none; box-shadow:0 2px 8px rgba(192,133,82,.1); }
  .sync-bar { font-size:.8rem; color:#888; padding:.4rem .5rem; border-top:1px solid #f0e4d0; }
  .score-badge { font-size:.78rem; padding:.2em .6em; border-radius:12px; font-weight:700; }
  .score-great { background:#3a8a3a; color:#fff; }
  .score-good  { background:#2196a5; color:#fff; }
  .score-avg   { background:#f0a500; color:#fff; }
  .score-low   { background:#c04040; color:#fff; }
</style>

<div class="container-fluid py-3">

  <!-- Header -->
  <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h2><i class="bi bi-kanban me-2"></i>Housekeeping Task Board</h2>
    <div class="d-flex align-items-center gap-3">
      <span class="text-muted" style="font-size:.8rem;">
        <i class="bi bi-arrow-repeat me-1" id="syncIcon"></i>
        Sync in <strong id="hk-countdown">30</strong>s
        &nbsp;·&nbsp; Last updated: <span id="hk-last-update" style="color:var(--accent);"><?= $lastUpdate ? date('H:i:s', strtotime($lastUpdate)) : '—' ?></span>
      </span>
      <button onclick="location.reload()" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-clockwise"></i></button>
    </div>
  </div>

  <!-- Flash messages -->
  <?php if (!empty($_SESSION['hk_success'])): ?>
    <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($_SESSION['hk_success']) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php unset($_SESSION['hk_success']); ?>
  <?php endif; ?>
  <?php if (!empty($_SESSION['hk_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($_SESSION['hk_error']) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php unset($_SESSION['hk_error']); ?>
  <?php endif; ?>

  <!-- Summary Counts -->
  <div class="row g-3 mb-3">
    <?php
    $summaryItems = [
      ['label'=>'Pending',     'key'=>'pending',     'icon'=>'bi-clock',        'color'=>'#f0a500'],
      ['label'=>'In Progress', 'key'=>'in_progress', 'icon'=>'bi-arrow-repeat', 'color'=>'#2196a5'],
      ['label'=>'Done',        'key'=>'done',         'icon'=>'bi-check-circle','color'=>'#3a8a3a'],
      ['label'=>'Skipped',     'key'=>'skipped',      'icon'=>'bi-skip-forward','color'=>'#888'],
    ];
    foreach ($summaryItems as $si): ?>
    <div class="col-6 col-md-3">
      <div class="card summary-card text-center py-3">
        <i class="bi <?= $si['icon'] ?> mb-1" style="font-size:1.5rem;color:<?= $si['color'] ?>;"></i>
        <div style="font-size:1.6rem;font-weight:700;color:<?= $si['color'] ?>;"><?= $counts[$si['key']] ?></div>
        <div style="font-size:.82rem;color:#888;"><?= $si['label'] ?></div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Filters -->
  <div class="filter-card">
    <form method="GET" action="<?= APP_URL ?>/index.php" class="row g-2 align-items-end">
      <input type="hidden" name="url" value="housekeeping/index">
      <div class="col-md-3">
        <label class="form-label fw-semibold" style="color:var(--dark);font-size:.88rem;">Status</label>
        <select name="status" class="form-select form-select-sm">
          <option value="">All Statuses</option>
          <?php foreach (['pending','in_progress','done','skipped'] as $s): ?>
          <option value="<?= $s ?>" <?= ($statusFilter===$s)?'selected':'' ?>><?= ucwords(str_replace('_',' ',$s)) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label fw-semibold" style="color:var(--dark);font-size:.88rem;">Task Type</label>
        <select name="task_type" class="form-select form-select-sm">
          <option value="">All Types</option>
          <?php foreach (['cleaning','inspection','turndown','restocking'] as $t): ?>
          <option value="<?= $t ?>" <?= ($typeFilter===$t)?'selected':'' ?>><?= ucfirst($t) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-4 d-flex gap-2">
        <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-funnel me-1"></i>Filter</button>
        <a href="<?= APP_URL ?>/index.php?url=housekeeping/index" class="btn btn-sm btn-outline-secondary"><i class="bi bi-x me-1"></i>Clear</a>
      </div>
    </form>
  </div>

  <!-- Task Table -->
  <div class="table-card">
    <?php if (empty($tasks)): ?>
      <div class="empty-state">
        <i class="bi bi-kanban d-block mb-3"></i>
        <p class="mb-0">No tasks found<?= $statusFilter ? ' for status "'.htmlspecialchars($statusFilter).'"' : '' ?>.</p>
      </div>
    <?php else: ?>
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead>
          <tr>
            <th>#</th>
            <th>Room</th>
            <th>Type</th>
            <th>Status</th>
            <th>Assigned To</th>
            <th>Notes</th>
            <th>Score</th>
            <th>Request Date</th>
            <th>Completed</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($tasks as $t): ?>
          <tr>
            <td><strong style="color:var(--dark);">#<?= $t['id'] ?></strong></td>
            <td><span style="font-weight:600;color:var(--dark);">Room <?= htmlspecialchars($t['room_number']) ?></span></td>
            <td><span class="type-badge hk-type-<?= $t['task_type'] ?>"><?= ucfirst($t['task_type']) ?></span></td>
            <td><span class="status-badge hk-badge-<?= $t['status'] ?>"><?= ucwords(str_replace('_',' ',$t['status'])) ?></span></td>
            <td><?= $t['assigned_name'] ? htmlspecialchars($t['assigned_name']) : '<span class="text-muted">Unassigned</span>' ?></td>
            <td style="max-width:200px;font-size:.85rem;"><?= htmlspecialchars(mb_strimwidth($t['notes'] ?? '', 0, 60, '…')) ?></td>
            <td>
              <?php
                $sc = $t['quality_score'];
                if ($sc !== null && $sc !== '') {
                    $sc = (int)$sc;
                    $cls = $sc >= 90 ? 'score-great' : ($sc >= 75 ? 'score-good' : ($sc >= 60 ? 'score-avg' : 'score-low'));
                    $lbl = $sc >= 90 ? '★ Excellent' : ($sc >= 75 ? '✓ Good' : ($sc >= 60 ? '~ Average' : '✗ Low'));
                    echo "<span class='score-badge $cls'>$sc/100 &mdash; $lbl</span>";
                } else { echo '<span class="text-muted">—</span>'; }
              ?>
            </td>
            <td style="font-size:.82rem;color:#888;white-space:nowrap;"><?= date('d M Y H:i', strtotime($t['created_at'])) ?></td>
            <td style="font-size:.82rem;white-space:nowrap;">
              <?php if ($t['completed_at']): ?>
                <span style="color:#3a8a3a;font-weight:600;"><i class="bi bi-check-circle me-1"></i><?= date('d M Y H:i', strtotime($t['completed_at'])) ?></span>
              <?php else: ?>
                <span class="text-muted">—</span>
              <?php endif; ?>
            </td>
            <td>
              <?php if ($t['status'] === 'pending'): ?>
                <form method="POST" action="<?= APP_URL ?>/index.php?url=housekeeping/updateStatus/<?= $t['id'] ?>" class="d-inline">
                  <input type="hidden" name="status" value="in_progress">
                  <button type="submit" class="btn btn-sm" style="background:#2196a5;color:#fff;font-size:.75rem;" title="Start">
                    <i class="bi bi-play-fill"></i> Start
                  </button>
                </form>
                <form method="POST" action="<?= APP_URL ?>/index.php?url=housekeeping/updateStatus/<?= $t['id'] ?>" class="d-inline">
                  <input type="hidden" name="status" value="skipped">
                  <button type="submit" class="btn btn-sm btn-outline-secondary" style="font-size:.75rem;" onclick="return confirm('Skip this task?')" title="Skip">
                    <i class="bi bi-skip-forward"></i>
                  </button>
                </form>
              <?php elseif ($t['status'] === 'in_progress'): ?>
                <form method="POST" action="<?= APP_URL ?>/index.php?url=housekeeping/updateStatus/<?= $t['id'] ?>" class="d-inline">
                  <input type="hidden" name="status" value="done">
                  <button type="submit" class="btn btn-sm" style="background:#3a8a3a;color:#fff;font-size:.75rem;" title="Mark Done">
                    <i class="bi bi-check-lg"></i> Done
                  </button>
                </form>
              <?php else: ?>
                <span class="text-muted" style="font-size:.8rem;">—</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <div class="px-3 py-2" style="color:var(--accent2);font-size:.85rem;">
      Showing <?= count($tasks) ?> task(s)
    </div>
    <?php endif; ?>
  </div>

</div>

<?php $content = ob_get_clean(); ?>
<?php require VIEW_PATH . '/layouts/main.php'; ?>

<script>
// ── Task Board Live Sync (30s polling) ──
(function() {
    let countdown = 30;
    let lastTs    = '<?= addslashes($lastUpdate ?? '') ?>';
    const countEl = document.getElementById('hk-countdown');
    const tsEl    = document.getElementById('hk-last-update');
    const syncIcon= document.getElementById('syncIcon');

    function tick() {
        countdown--;
        if (countEl) countEl.textContent = countdown;
        if (countdown <= 0) {
            syncIcon && syncIcon.classList.add('spin-anim');
            fetch('<?= APP_URL ?>/index.php?url=housekeeping/boardData', {credentials:'same-origin'})
                .then(r => r.json())
                .then(data => {
                    if (data.lastUpdate && data.lastUpdate !== lastTs) {
                        lastTs = data.lastUpdate;
                        if (tsEl) tsEl.textContent = data.lastUpdate.substring(11,19);
                        location.reload(); // board changed — reload for full accuracy
                    }
                    countdown = 30;
                    syncIcon && syncIcon.classList.remove('spin-anim');
                })
                .catch(() => { countdown = 30; });
        }
    }
    setInterval(tick, 1000);
})();
</script>
<style>
@keyframes spin { to { transform:rotate(360deg); } }
.spin-anim { display:inline-block; animation:spin .6s linear infinite; }
</style>
