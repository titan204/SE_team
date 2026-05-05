<?php $pageTitle = 'My Tasks'; ?>
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
  .status-badge, .type-badge {
    font-size:.75rem; padding:.28em .7em; border-radius:20px; font-weight:600;
  }
  .table td, .table th { vertical-align:middle; }
  .empty-state { text-align:center; padding:3rem 1rem; color:#a08060; }
  .empty-state i { font-size:3rem; opacity:.4; }
  .table-card  { background:#fff; border:1px solid #e8d5c0; border-radius:10px; overflow:hidden; box-shadow:0 2px 8px rgba(192,133,82,.08); }
  .page-header { border-bottom:2px solid var(--accent); padding-bottom:.75rem; margin-bottom:1.5rem; }
  .page-header h2 { color:var(--dark); font-weight:700; }
  .summary-card { border-radius:10px; border:none; box-shadow:0 2px 8px rgba(192,133,82,.1); }
  .task-card {
    background:#fff; border:1px solid #e8d5c0; border-radius:12px;
    padding:1.2rem 1.4rem; margin-bottom:1rem;
    box-shadow:0 2px 6px rgba(192,133,82,.07);
    transition: box-shadow .18s;
  }
  .task-card:hover { box-shadow:0 4px 14px rgba(192,133,82,.18); }
  .task-card.status-done { opacity:.65; }
</style>

<div class="container-fluid py-3">

  <!-- Header -->
  <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h2><i class="bi bi-person-check me-2"></i>My Tasks
      <small class="text-muted fs-6 fw-normal ms-2"><?= htmlspecialchars($_SESSION['user_name'] ?? '') ?></small>
    </h2>
    <div class="d-flex align-items-center gap-3">
      <span class="text-muted" style="font-size:.8rem;">
        <i class="bi bi-arrow-repeat me-1" id="syncIcon"></i>
        Auto-refresh in <strong id="hk-countdown">30</strong>s
      </span>
      <a href="<?= APP_URL ?>/index.php?url=housekeeping/index" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-kanban me-1"></i>Full Task Board
      </a>
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
  <div class="row g-3 mb-4">
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

  <!-- Task Cards -->
  <?php if (empty($tasks)): ?>
    <div class="empty-state">
      <i class="bi bi-inbox d-block mb-3"></i>
      <p class="mb-0">You have no tasks assigned to you right now.</p>
    </div>
  <?php else: ?>

    <?php
    // Group by status for visual order
    $groups = ['pending'=>[],'in_progress'=>[],'done'=>[],'skipped'=>[]];
    foreach ($tasks as $t) {
        $groups[$t['status']][] = $t;
    }
    $groupLabels = [
        'pending'     => ['label'=>'⏳ Pending',     'color'=>'#f0a500'],
        'in_progress' => ['label'=>'🔄 In Progress', 'color'=>'#2196a5'],
        'done'        => ['label'=>'✅ Done',         'color'=>'#3a8a3a'],
        'skipped'     => ['label'=>'⏭ Skipped',      'color'=>'#888'],
    ];
    foreach ($groups as $status => $items):
      if (empty($items)) continue;
      $gl = $groupLabels[$status];
    ?>
    <h5 style="color:<?= $gl['color'] ?>;font-weight:700;margin-bottom:.75rem;">
      <?= $gl['label'] ?> <span class="badge" style="background:<?= $gl['color'] ?>;font-size:.8rem;"><?= count($items) ?></span>
    </h5>

    <?php foreach ($items as $t): ?>
    <div class="task-card <?= $t['status']==='done'?'status-done':'' ?>">
      <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
          <span class="type-badge hk-type-<?= $t['task_type'] ?> me-2"><?= ucfirst($t['task_type']) ?></span>
          <strong style="color:var(--dark);font-size:1.05rem;">Room <?= htmlspecialchars($t['room_number']) ?></strong>
          <span class="text-muted ms-2" style="font-size:.82rem;">
            #<?= $t['id'] ?>
            &nbsp;·&nbsp; <i class="bi bi-calendar3"></i> <?= date('d M Y H:i', strtotime($t['created_at'])) ?>
            <?php if ($t['completed_at']): ?>
              &nbsp;·&nbsp; <span style="color:#3a8a3a;font-weight:600;"><i class="bi bi-check-circle"></i> Done <?= date('d M Y H:i', strtotime($t['completed_at'])) ?></span>
            <?php endif; ?>
          </span>
        </div>
        <div class="d-flex gap-2 align-items-center">
          <?php
            $sc = $t['quality_score'];
            if ($sc !== null && $sc !== '') {
                $sc  = (int)$sc;
                $cls = $sc >= 90 ? '#3a8a3a' : ($sc >= 75 ? '#2196a5' : ($sc >= 60 ? '#f0a500' : '#c04040'));
                $lbl = $sc >= 90 ? '★ Excellent' : ($sc >= 75 ? '✓ Good' : ($sc >= 60 ? '~ Average' : '✗ Low'));
                echo "<span class='badge' style='background:$cls;'>Score: $sc/100 &mdash; $lbl</span>";
            }
          ?>

          <?php if ($t['status'] === 'pending'): ?>
            <form method="POST" action="<?= APP_URL ?>/index.php?url=housekeeping/updateStatus/<?= $t['id'] ?>" class="d-inline">
              <input type="hidden" name="status" value="in_progress">
              <button type="submit" class="btn btn-sm" style="background:#2196a5;color:#fff;font-size:.8rem;">
                <i class="bi bi-play-fill me-1"></i>Start
              </button>
            </form>
            <form method="POST" action="<?= APP_URL ?>/index.php?url=housekeeping/updateStatus/<?= $t['id'] ?>" class="d-inline">
              <input type="hidden" name="status" value="skipped">
              <button type="submit" class="btn btn-sm btn-outline-secondary" style="font-size:.8rem;" onclick="return confirm('Skip this task?')">
                <i class="bi bi-skip-forward"></i> Skip
              </button>
            </form>
          <?php elseif ($t['status'] === 'in_progress'): ?>
            <form method="POST" action="<?= APP_URL ?>/index.php?url=housekeeping/updateStatus/<?= $t['id'] ?>" class="d-inline">
              <input type="hidden" name="status" value="done">
              <button type="submit" class="btn btn-sm" style="background:#3a8a3a;color:#fff;font-size:.8rem;">
                <i class="bi bi-check-lg me-1"></i>Mark Done
              </button>
            </form>
          <?php endif; ?>
        </div>
      </div>
      <?php if (!empty($t['notes'])): ?>
        <p class="mt-2 mb-0" style="font-size:.88rem;color:#666;"><?= htmlspecialchars($t['notes']) ?></p>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
    <div class="mb-3"></div>
    <?php endforeach; ?>

  <?php endif; ?>

</div>

<?php $content = ob_get_clean(); ?>
<?php require VIEW_PATH . '/layouts/main.php'; ?>

<script>
// ── My Tasks Live Sync (30s auto-reload) ──
(function() {
    let countdown = 30;
    let lastTs    = '<?= addslashes($lastUpdate ?? '') ?>';
    const countEl = document.getElementById('hk-countdown');
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
                        location.reload(); // new task state — reload
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
