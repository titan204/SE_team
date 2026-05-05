<?php $pageTitle = 'Schedule Preventative Maintenance'; ob_start(); ?>

<style>
  .pm-card { background:#fff; border:1px solid #dee2e6; border-radius:10px; padding:1.5rem; box-shadow:0 2px 8px rgba(0,0,0,.06); }
</style>

<div class="container-fluid py-3" style="max-width:820px">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0"><i class="bi bi-calendar-check me-2"></i>Schedule Preventative Maintenance</h4>
    <a href="<?= APP_URL ?>/?url=maintenance/index" class="btn btn-sm btn-outline-secondary">
      <i class="bi bi-arrow-left me-1"></i>Back
    </a>
  </div>

  <?php if (!empty($_SESSION['maint_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show py-2">
      <?= htmlspecialchars($_SESSION['maint_error']) ?>
      <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['maint_error']); ?>
  <?php endif; ?>

  <?php if (!empty($conflict)): ?>
    <div class="alert alert-warning">
      <i class="bi bi-exclamation-triangle me-1"></i>
      <strong>Scheduling conflict detected</strong> — the selected room is unavailable on that date.
      <?php if (!empty($alternatives)): ?>
        <br>Suggested alternatives:
        <?php foreach ($alternatives as $alt): ?>
          <span class="badge bg-secondary ms-1"><?= htmlspecialchars($alt) ?></span>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <div class="pm-card">
    <form method="POST" action="<?= APP_URL ?>/?url=maintenance/preventative" id="pm-form">

      <?php if (!empty($conflict)): ?>
        <input type="hidden" name="override_conflict" value="1">
        <div class="alert alert-info py-2 small mb-3">
          <i class="bi bi-info-circle me-1"></i>Override active — re-submitting will force-schedule despite conflict.
        </div>
        <?php foreach ($formData as $k => $v): ?>
          <?php if (is_array($v)): foreach ($v as $vv): ?>
            <input type="hidden" name="<?= htmlspecialchars($k) ?>[]" value="<?= htmlspecialchars($vv) ?>">
          <?php endforeach; else: ?>
            <input type="hidden" name="<?= htmlspecialchars($k) ?>" value="<?= htmlspecialchars($v) ?>">
          <?php endif; ?>
        <?php endforeach; ?>
      <?php endif; ?>

      <div class="mb-3">
        <label for="pm_desc" class="form-label fw-semibold">
          Work Description <span class="text-danger">*</span>
        </label>
        <textarea id="pm_desc" name="description" class="form-control" rows="3"
                  placeholder="Describe the maintenance work to be performed…"
                  required><?= htmlspecialchars($formData['description'] ?? '') ?></textarea>
      </div>

      <div class="mb-3">
        <label class="form-label fw-semibold">Maintenance Type</label>
        <select name="maintenance_type" class="form-select">
          <?php foreach (['hvac','elevator','plumbing','electrical','deep_cleaning','other'] as $t): ?>
            <option value="<?= $t ?>" <?= ($formData['maintenance_type'] ?? '') === $t ? 'selected' : '' ?>>
              <?= ucfirst(str_replace('_', ' ', $t)) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="row g-2 mb-3">
        <div class="col-md-6">
          <label class="form-label fw-semibold small">Room (optional)</label>
          <select name="room_id" id="pm_room" class="form-select" onchange="checkConflict()">
            <option value="">— No specific room —</option>
            <?php foreach ($rooms as $r): ?>
              <option value="<?= $r['id'] ?>"
                      data-num="<?= htmlspecialchars($r['room_number']) ?>"
                      <?= ($formData['room_id'] ?? '') == $r['id'] ? 'selected' : '' ?>>
                Room <?= htmlspecialchars($r['room_number']) ?> (<?= ucfirst($r['status']) ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold small">Asset (optional)</label>
          <select name="asset_id" class="form-select">
            <option value="">— No specific asset —</option>
            <?php foreach ($assets as $a): ?>
              <option value="<?= $a['id'] ?>" <?= ($formData['asset_id'] ?? '') == $a['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($a['name']) ?> (<?= htmlspecialchars($a['location']) ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="row g-2 mb-3">
        <div class="col-md-4">
          <label for="pm_date" class="form-label fw-semibold small">
            Scheduled Date <span class="text-danger">*</span>
          </label>
          <input type="date" id="pm_date" name="scheduled_date" class="form-control"
                 min="<?= date('Y-m-d') ?>"
                 value="<?= htmlspecialchars($formData['scheduled_date'] ?? date('Y-m-d', strtotime('+1 day'))) ?>"
                 onchange="checkConflict()" required>
          <div id="conflict-indicator" class="form-text"></div>
        </div>
        <div class="col-md-2">
          <label for="pm_start_time" class="form-label fw-semibold small">Start Time</label>
          <input type="time" id="pm_start_time" name="start_time" class="form-control"
                 value="<?= htmlspecialchars($formData['start_time'] ?? '09:00') ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold small">Estimated Duration</label>
          <div class="input-group">
            <input type="number" name="est_hours" class="form-control" min="0" max="48"
                   value="<?= htmlspecialchars($formData['est_hours'] ?? '2') ?>" placeholder="Hrs">
            <span class="input-group-text">h</span>
            <input type="number" name="est_minutes" class="form-control" min="0" max="59" step="15"
                   value="<?= htmlspecialchars($formData['est_minutes'] ?? '0') ?>" placeholder="Min">
            <span class="input-group-text">m</span>
          </div>
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold small">Assign Technician</label>
          <select name="assigned_to" class="form-select">
            <option value="">— Assign later —</option>
            <?php foreach ($technicians as $t): ?>
              <option value="<?= $t['id'] ?>" <?= ($formData['assigned_to'] ?? '') == $t['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($t['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <!-- Recurring -->
      <div class="mb-3">
        <div class="form-check form-switch mb-2">
          <input class="form-check-input" type="checkbox" name="is_recurring" value="1"
                 id="pm_recurring" onchange="toggleRecurring(this.checked)"
                 <?= !empty($formData['is_recurring']) ? 'checked' : '' ?>>
          <label class="form-check-label fw-semibold" for="pm_recurring">Recurring Schedule</label>
        </div>
        <div id="recurrence-block" style="display:<?= !empty($formData['is_recurring']) ? 'block' : 'none' ?>">
          <select name="recurrence_frequency" class="form-select form-select-sm" style="max-width:200px">
            <?php foreach (['weekly','monthly','quarterly','yearly'] as $f): ?>
              <option value="<?= $f ?>" <?= ($formData['recurrence_frequency'] ?? '') === $f ? 'selected' : '' ?>>
                <?= ucfirst($f) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <div class="form-text">Next schedule auto-created when this work order is closed.</div>
        </div>
      </div>

      <div class="alert alert-info py-2 small mb-3">
        <i class="bi bi-door-closed me-1"></i>
        If a room is selected, it will be set to <strong>Out of Order</strong> for the scheduled date.
        A conflict check runs automatically.
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary px-4" id="btn-schedule-pm">
          <i class="bi bi-calendar-plus me-1"></i>Schedule
        </button>
        <a href="<?= APP_URL ?>/?url=maintenance/index" class="btn btn-outline-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>

<script>
function toggleRecurring(on) {
  document.getElementById('recurrence-block').style.display = on ? 'block' : 'none';
}

function checkConflict() {
  const roomId = document.getElementById('pm_room').value;
  const date   = document.getElementById('pm_date').value;
  const el     = document.getElementById('conflict-indicator');
  if (!roomId || !date) { el.textContent = ''; return; }
  el.textContent = 'Checking availability…';
  el.className   = 'form-text text-muted';
  fetch(`<?= APP_URL ?>/?url=maintenance/availability&room_id=${roomId}&date=${date}`)
    .then(r => r.json())
    .then(data => {
      if (data.conflict) {
        el.textContent = '⚠ Conflict — room unavailable on this date.';
        el.className   = 'form-text text-danger fw-semibold';
      } else {
        el.textContent = '✓ Date is available.';
        el.className   = 'form-text text-success fw-semibold';
      }
    })
    .catch(() => { el.textContent = ''; });
}
</script>

<?php $content = ob_get_clean(); require VIEW_PATH . '/layouts/main.php'; ?>
