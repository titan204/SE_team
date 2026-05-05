<?php $pageTitle = 'Submit Quality Score — Inspection #' . $inspection['id']; ob_start(); ?>

<style>
  .score-card { background:#fff; border:1px solid #e0d0c0; border-radius:10px; padding:1.5rem; box-shadow:0 2px 8px rgba(0,0,0,.06); }
  .dim-row    { border-bottom:1px solid #f0e8e0; padding:.7rem 0; }
  .dim-row:last-child { border-bottom:none; }
  .overall-display { font-size:2.5rem; font-weight:700; color:#4B2E2B; }
</style>

<div class="container-fluid py-3" style="max-width:720px">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0"><i class="bi bi-star-half me-2"></i>Submit Quality Score</h4>
    <a href="<?= APP_URL ?>/?url=housekeeping/qa" class="btn btn-sm btn-outline-secondary">
      <i class="bi bi-arrow-left me-1"></i>Back
    </a>
  </div>

  <?php if (!empty($_SESSION['hk_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show py-2">
      <?= htmlspecialchars($_SESSION['hk_error']) ?>
      <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['hk_error']); ?>
  <?php endif; ?>

  <!-- Inspection summary -->
  <div class="alert alert-info py-2 small mb-3">
    <strong>Inspection #<?= $inspection['id'] ?></strong> —
    Room <?= htmlspecialchars($inspection['room_number']) ?> •
    Inspected by <?= htmlspecialchars($inspection['inspector_name']) ?> •
    Result: <strong><?= strtoupper($inspection['overall_result']) ?></strong> •
    <?= htmlspecialchars($inspection['inspection_date']) ?>
  </div>

  <div class="score-card">
    <form method="POST" action="<?= APP_URL ?>/?url=housekeeping/qaScoreSubmit" id="qa-score-form">
      <input type="hidden" name="inspection_id" value="<?= (int)$inspection['id'] ?>">

      <!-- Housekeeper being scored -->
      <div class="mb-3">
        <label for="qs_hk" class="form-label fw-semibold">
          Housekeeper Being Scored <span class="text-danger">*</span>
        </label>
        <select id="qs_hk" name="housekeeper_id" class="form-select" required>
          <option value="">— Select housekeeper —</option>
          <?php foreach ($housekeepers as $hk): ?>
            <option value="<?= $hk['id'] ?>"><?= htmlspecialchars($hk['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Score dimensions -->
      <h6 class="fw-semibold mb-1">Score Dimensions <small class="text-muted">(0–100 each)</small></h6>

      <?php
      $dims = [
          'cleanliness'  => ['label' => 'Cleanliness',   'icon' => 'bi-droplet'],
          'presentation' => ['label' => 'Presentation',  'icon' => 'bi-eye'],
          'completeness' => ['label' => 'Completeness',  'icon' => 'bi-check-all'],
          'speed'        => ['label' => 'Speed',          'icon' => 'bi-lightning'],
      ];
      ?>

      <?php foreach ($dims as $key => $meta): ?>
      <div class="dim-row">
        <div class="row align-items-center">
          <div class="col-md-4 fw-semibold">
            <i class="bi <?= $meta['icon'] ?> me-1"></i><?= $meta['label'] ?>
          </div>
          <div class="col-md-5">
            <input type="range" name="<?= $key ?>" id="dim_<?= $key ?>"
                   min="0" max="100" value="80" step="1"
                   class="form-range score-range"
                   oninput="updateDisplay('<?= $key ?>', this.value)">
          </div>
          <div class="col-md-3">
            <span id="val_<?= $key ?>" class="badge bg-primary fs-6 px-3">80</span>
          </div>
        </div>
      </div>
      <?php endforeach; ?>

      <!-- Auto-calculated overall -->
      <div class="mt-3 text-center py-3 bg-light rounded">
        <div class="text-muted small">Overall Score (auto-calculated average)</div>
        <div class="overall-display" id="overall-display">80.0</div>
        <div class="text-muted small">/ 100</div>
      </div>

      <!-- Notes -->
      <div class="mt-3">
        <label for="qs_notes" class="form-label fw-semibold">Notes <small class="text-muted">(optional)</small></label>
        <textarea id="qs_notes" name="notes" class="form-control" rows="2"
                  placeholder="Additional observations…"></textarea>
      </div>

      <div class="mt-4 d-flex gap-2 flex-wrap">
        <button type="submit" class="btn btn-success px-4" id="btn-score-submit">
          <i class="bi bi-check-lg me-1"></i>Submit Score
        </button>
        <button type="button" class="btn btn-outline-secondary" id="btn-restore-draft" style="display:none">
          <i class="bi bi-arrow-counterclockwise me-1"></i>Restore Draft
        </button>
        <a href="<?= APP_URL ?>/?url=housekeeping/qa" class="btn btn-outline-secondary">Cancel</a>
      </div>
    </form>
  </div>

  <?php
    $role = $_SESSION['user_role'] ?? '';
    if (in_array($role, ['supervisor','manager'])):
  ?>
  <!-- Dispute section (supervisor/manager) -->
  <div class="score-card mt-3">
    <h6 class="fw-semibold mb-2"><i class="bi bi-flag me-1 text-danger"></i>Dispute a Score</h6>
    <p class="text-muted small mb-2">
      Flag an existing quality score as disputed. The score ID must be known (visible in the QA log).
      Manager can then resolve with override code.
    </p>
    <button type="button" class="btn btn-sm btn-outline-danger" id="btn-open-dispute"
            onclick="document.getElementById('dispute-panel').style.display='block';this.style.display='none'">
      <i class="bi bi-flag me-1"></i>Flag a Score as Disputed
    </button>
    <div id="dispute-panel" style="display:none">
      <form method="POST"
            action="<?= APP_URL ?>/?url=housekeeping/disputeScore/<?= (int)$inspection['id'] ?>"
            id="dispute-score-form" class="mt-2">
        <div class="mb-2">
          <label class="form-label fw-semibold small">Score ID <span class="text-danger">*</span></label>
          <input type="number" name="score_id_override" class="form-control form-control-sm"
                 placeholder="Enter quality_score id" style="max-width:160px" required
                 oninput="document.getElementById('dispute-score-form').action=
                   '<?= APP_URL ?>/?url=housekeeping/disputeScore/'+this.value">
        </div>
        <div class="mb-2">
          <label class="form-label fw-semibold small">Dispute Note <span class="text-danger">*</span></label>
          <textarea name="dispute_note" class="form-control form-control-sm" rows="2"
                    placeholder="Reason for dispute…" required></textarea>
        </div>
        <button type="submit" class="btn btn-danger btn-sm" id="btn-submit-dispute">
          <i class="bi bi-flag me-1"></i>Submit Dispute
        </button>
      </form>
    </div>
  </div>
  <?php endif; ?>

  <?php if ($role === 'manager'): ?>
  <!-- Resolve dispute (manager only) -->
  <div class="score-card mt-3">
    <h6 class="fw-semibold mb-2"><i class="bi bi-unlock me-1 text-success"></i>Resolve Dispute</h6>
    <p class="text-muted small mb-2">
      Requires <strong>Manager Override Code</strong>. Resolution reason is logged to audit trail.
    </p>
    <button type="button" class="btn btn-sm btn-outline-success"
            onclick="document.getElementById('resolve-panel').style.display='block';this.style.display='none'">
      <i class="bi bi-check2-circle me-1"></i>Resolve a Disputed Score
    </button>
    <div id="resolve-panel" style="display:none">
      <form method="POST"
            action="<?= APP_URL ?>/?url=housekeeping/resolveDispute/0"
            id="resolve-dispute-form" class="mt-2">
        <div class="row g-2">
          <div class="col-md-3">
            <label class="form-label fw-semibold small">Score ID <span class="text-danger">*</span></label>
            <input type="number" name="score_id_resolve" class="form-control form-control-sm"
                   placeholder="Score ID" required
                   oninput="document.getElementById('resolve-dispute-form').action=
                     '<?= APP_URL ?>/?url=housekeeping/resolveDispute/'+this.value">
          </div>
          <div class="col-md-3">
            <label class="form-label fw-semibold small">Override Code <span class="text-danger">*</span></label>
            <input type="password" name="manager_override_code" class="form-control form-control-sm"
                   placeholder="MGR-OVERRIDE" required>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold small">Resolution Reason <span class="text-danger">*</span></label>
            <input type="text" name="resolution_reason" class="form-control form-control-sm"
                   placeholder="Explain the adjustment…" required>
          </div>
        </div>
        <button type="submit" class="btn btn-success btn-sm mt-2" id="btn-confirm-resolve">
          <i class="bi bi-check-lg me-1"></i>Confirm Resolution
        </button>
      </form>
    </div>
  </div>
  <?php endif; ?>

</div>

<script>
const dims      = ['cleanliness','presentation','completeness','speed'];
const DRAFT_KEY = 'qa_score_draft_<?= (int)$inspection['id'] ?>';

function updateDisplay(key, val) {
  document.getElementById('val_' + key).textContent = val;
  recalcOverall();
  saveDraft();
}

function recalcOverall() {
  let sum = 0;
  dims.forEach(d => { sum += parseInt(document.getElementById('dim_' + d).value, 10); });
  const avg = (sum / dims.length).toFixed(1);
  const el  = document.getElementById('overall-display');
  el.textContent = avg;
  el.style.color = avg < 60 ? '#dc3545' : avg < 75 ? '#fd7e14' : '#198754';
}

// ── sessionStorage draft save / restore ──────────────────────
function saveDraft() {
  const draft = {};
  dims.forEach(d => { draft[d] = document.getElementById('dim_' + d).value; });
  draft.notes = document.getElementById('qs_notes').value;
  sessionStorage.setItem(DRAFT_KEY, JSON.stringify(draft));
}

function restoreDraft() {
  const raw = sessionStorage.getItem(DRAFT_KEY);
  if (!raw) return;
  try {
    const draft = JSON.parse(raw);
    dims.forEach(d => {
      if (draft[d] !== undefined) {
        const el = document.getElementById('dim_' + d);
        el.value = draft[d];
        document.getElementById('val_' + d).textContent = draft[d];
      }
    });
    if (draft.notes) document.getElementById('qs_notes').value = draft.notes;
    recalcOverall();
  } catch(e) {}
}

// Show restore button if draft exists
if (sessionStorage.getItem(DRAFT_KEY)) {
  document.getElementById('btn-restore-draft').style.display = '';
}
document.getElementById('btn-restore-draft').addEventListener('click', function() {
  restoreDraft();
  this.style.display = 'none';
});

// Auto-save on notes change
document.getElementById('qs_notes').addEventListener('input', saveDraft);

// Clear draft on successful submit; on failure the draft stays
document.getElementById('qa-score-form').addEventListener('submit', function(e) {
  let valid = true;
  dims.forEach(d => {
    const v = parseInt(document.getElementById('dim_' + d).value, 10);
    if (v < 0 || v > 100) { valid = false; }
  });
  if (!valid) {
    e.preventDefault();
    alert('Score must be 0–100. Please correct the values.');
    return;
  }
  // Clear draft on submit (success path)
  sessionStorage.removeItem(DRAFT_KEY);
});

recalcOverall();
</script>

<?php $content = ob_get_clean(); require VIEW_PATH . '/layouts/main.php'; ?>
