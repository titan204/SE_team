<?php $pageTitle = 'QA Inspection — Room ' . htmlspecialchars($room['room_number']); ob_start();
$criteria = [
    'floors_surfaces' => 'Floors & Surfaces',
    'bathroom'        => 'Bathroom',
    'bed_linen'       => 'Bed & Linen',
    'amenities'       => 'Amenities',
    'minibar'         => 'Minibar',
    'maintenance'     => 'Maintenance Issues Visible',
    'odor_air'        => 'Odor & Air Quality',
];
?>

<style>
  .qa-card  { background:#fff; border:1px solid #e0d0c0; border-radius:10px; padding:1.5rem; box-shadow:0 2px 8px rgba(0,0,0,.06); }
  .qa-row   { border-bottom:1px solid #f0e8e0; padding:.6rem 0; }
  .qa-row:last-child { border-bottom:none; }
  .corrective-block { background:#fff3cd; border:1px solid #ffc107; border-radius:8px; padding:1rem; margin-top:1rem; display:none; }
</style>

<div class="container-fluid py-3" style="max-width:820px">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">
      <i class="bi bi-clipboard2-check me-2"></i>QA Inspection — Room
      <span class="badge bg-secondary"><?= htmlspecialchars($room['room_number']) ?></span>
      <small class="text-muted fs-6"><?= htmlspecialchars($room['room_type']) ?></small>
    </h4>
    <a href="<?= APP_URL ?>/?url=housekeeping/qa" class="btn btn-sm btn-outline-secondary">
      <i class="bi bi-arrow-left me-1"></i>Back to QA
    </a>
  </div>

  <div class="qa-card">
    <form method="POST" action="<?= APP_URL ?>/?url=housekeeping/qaSubmit" id="qa-form">
      <input type="hidden" name="room_id" value="<?= (int)$room['id'] ?>">

      <h6 class="fw-semibold mb-3">Checklist <span class="text-muted small">(PASS / FAIL / N/A each criterion)</span></h6>

      <?php foreach ($criteria as $key => $label): ?>
      <div class="qa-row">
        <div class="row align-items-center">
          <div class="col-md-5 fw-semibold"><?= $label ?></div>
          <div class="col-md-7">
            <div class="d-flex gap-3">
              <?php foreach (['pass','fail','na'] as $val): ?>
              <div class="form-check">
                <input class="form-check-input checklist-radio" type="radio"
                       name="checklist[<?= $key ?>]" value="<?= $val ?>"
                       id="cl_<?= $key ?>_<?= $val ?>"
                       onchange="checkFails()"
                       <?= $val === 'na' ? 'checked' : '' ?>>
                <label class="form-check-label" for="cl_<?= $key ?>_<?= $val ?>">
                  <?php if ($val === 'pass'): ?>
                    <span class="text-success fw-semibold">PASS</span>
                  <?php elseif ($val === 'fail'): ?>
                    <span class="text-danger fw-semibold">FAIL</span>
                  <?php else: ?>
                    <span class="text-muted">N/A</span>
                  <?php endif; ?>
                </label>
              </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>

      <!-- Critical failure flag -->
      <div class="mt-3">
        <div class="form-check form-switch">
          <input class="form-check-input" type="checkbox" name="is_critical" value="1" id="is_critical">
          <label class="form-check-label text-danger fw-semibold" for="is_critical">
            <i class="bi bi-exclamation-octagon me-1"></i>Critical Safety Failure
            <small class="text-muted fw-normal">(sets room to out-of-order, creates maintenance request — MANDATORY)</small>
          </label>
        </div>
      </div>

      <!-- Corrective assignments (shown when FAIL detected) -->
      <div class="corrective-block" id="corrective-block">
        <h6 class="fw-semibold"><i class="bi bi-tools me-1"></i>Corrective Task Assignment</h6>
        <div id="corrective-list">
          <div class="corrective-entry row g-2 mb-2" data-index="0">
            <div class="col-md-4">
              <select name="corrective_hk[0]" class="form-select form-select-sm">
                <option value="">— Assign housekeeper —</option>
                <?php foreach ($housekeepers as $hk): ?>
                  <option value="<?= $hk['id'] ?>"><?= htmlspecialchars($hk['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-5">
              <input type="text" name="corrective_task[0]" class="form-control form-control-sm"
                     placeholder="Task description">
            </div>
            <div class="col-md-3">
              <input type="datetime-local" name="corrective_due[0]" class="form-control form-control-sm">
            </div>
          </div>
        </div>
        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addCorrectiveRow()">
          <i class="bi bi-plus me-1"></i>Add another task
        </button>
      </div>

      <!-- Notes -->
      <div class="mt-3">
        <label for="qa_notes" class="form-label fw-semibold">Inspector Notes</label>
        <textarea id="qa_notes" name="notes" class="form-control" rows="3"
                  placeholder="Optional additional notes…"></textarea>
      </div>

      <div class="mt-4 d-flex gap-2">
        <button type="submit" class="btn btn-primary px-4" id="btn-qa-submit">
          <i class="bi bi-check-lg me-1"></i>Submit Inspection
        </button>
        <a href="<?= APP_URL ?>/?url=housekeeping/qa" class="btn btn-outline-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>

<script>
let correctiveIdx = 0;

function checkFails() {
  const hasFail = document.querySelectorAll('input.checklist-radio[value="fail"]:checked').length > 0;
  document.getElementById('corrective-block').style.display = hasFail ? 'block' : 'none';
}

function addCorrectiveRow() {
  correctiveIdx++;
  const i = correctiveIdx;
  const container = document.getElementById('corrective-list');
  const div = document.createElement('div');
  div.className = 'corrective-entry row g-2 mb-2';
  div.innerHTML = `
    <div class="col-md-4">
      <select name="corrective_hk[${i}]" class="form-select form-select-sm">
        <option value="">— Assign housekeeper —</option>
        <?php foreach ($housekeepers as $hk): ?>
          <option value="<?= $hk['id'] ?>"><?= addslashes(htmlspecialchars($hk['name'])) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-5">
      <input type="text" name="corrective_task[${i}]" class="form-control form-control-sm"
             placeholder="Task description">
    </div>
    <div class="col-md-2">
      <input type="datetime-local" name="corrective_due[${i}]" class="form-control form-control-sm">
    </div>
    <div class="col-md-1">
      <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.corrective-entry').remove()">
        <i class="bi bi-x"></i>
      </button>
    </div>`;
  container.appendChild(div);
}
</script>

<?php $content = ob_get_clean(); require VIEW_PATH . '/layouts/main.php'; ?>
