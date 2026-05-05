<?php $pageTitle = 'Log Emergency Repair'; ob_start(); ?>

<style>
  .em-card { background:#fff; border:1px solid #f5c2c7; border-radius:10px; padding:1.5rem; box-shadow:0 2px 8px rgba(0,0,0,.06); }
  .print-form { display:none; }
  @media print { body > *:not(.print-form) { display:none; } .print-form { display:block !important; } }
</style>

<div class="container-fluid py-3" style="max-width:800px">
  <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h4 class="mb-0 text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Log Emergency Repair</h4>
    <div class="d-flex gap-2">
      <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.print()"
              title="Print paper form if system is unavailable">
        <i class="bi bi-printer me-1"></i>Print Form
      </button>
      <a href="<?= APP_URL ?>/?url=maintenance/index" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back
      </a>
    </div>
  </div>

  <?php if (!empty($_SESSION['maint_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show py-2">
      <?= htmlspecialchars($_SESSION['maint_error']) ?>
      <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['maint_error']); ?>
  <?php endif; ?>

  <!-- Offline notice (shown when server cannot be reached) -->
  <div class="alert alert-warning py-2 small mb-3" id="offline-notice" style="display:none">
    <i class="bi bi-wifi-off me-1"></i>
    <strong>System unavailable.</strong> Print this form and submit manually on reconnect.
    <button type="button" class="btn btn-sm btn-warning ms-2" onclick="window.print()">Print Now</button>
  </div>

  <div class="em-card">
    <form method="POST" action="<?= APP_URL ?>/?url=maintenance/emergency" id="emergency-form">

      <!-- Description -->
      <div class="mb-3">
        <label for="em_desc" class="form-label fw-semibold">Fault Description <span class="text-danger">*</span></label>
        <textarea id="em_desc" name="description" class="form-control" rows="4"
                  placeholder="Describe the fault clearly â€” what failed, when noticed, visible symptomsâ€¦" required></textarea>
      </div>

      <!-- Location -->
      <div class="row g-2 mb-3">
        <div class="col-md-6">
          <label class="form-label fw-semibold small">Room (if room-specific)</label>
          <select name="room_id" class="form-select" id="em_room">
            <option value="">â€” No specific room â€”</option>
            <?php foreach ($rooms as $r): ?>
              <option value="<?= $r['id'] ?>">Room <?= htmlspecialchars($r['room_number']) ?> (<?= ucfirst($r['status']) ?>)</option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold small">Asset (if asset-related)</label>
          <select name="asset_id" class="form-select">
            <option value="">â€” No specific asset â€”</option>
            <?php foreach ($assets as $a): ?>
              <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['name']) ?> (<?= htmlspecialchars($a['location']) ?>)</option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <!-- Failure type (UC35 required field) -->
      <div class="mb-3">
        <label for="em_failure_type" class="form-label fw-semibold">Failure Type <span class="text-danger">*</span></label>
        <select name="failure_type" id="em_failure_type" class="form-select" required>
          <option value="">â€” Select failure type â€”</option>
          <option value="electrical">Electrical</option>
          <option value="plumbing">Plumbing</option>
          <option value="hvac">HVAC</option>
          <option value="structural">Structural</option>
          <option value="safety_hazard">Safety Hazard</option>
          <option value="equipment">Equipment</option>
          <option value="other">Other</option>
        </select>
      </div>

      <!-- Severity -->
      <div class="mb-3">
        <label class="form-label fw-semibold">Severity <span class="text-danger">*</span></label>
        <div class="row g-2">
          <?php foreach (['low'=>'Low â€” Minor issue','medium'=>'Medium â€” Inconvenient','high'=>'High â€” Significant disruption','safety_critical'=>'Safety Critical âš  â€” Property-wide alert'] as $val=>$lbl): ?>
          <div class="col-md-3">
            <div class="form-check border rounded p-2">
              <input class="form-check-input" type="radio" name="severity" value="<?= $val ?>"
                     id="sev_<?= $val ?>" <?= $val === 'high' ? 'checked' : '' ?>>
              <label class="form-check-label small fw-semibold" for="sev_<?= $val ?>"><?= $lbl ?></label>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Immediate safety risk (UC35) -->
      <div class="mb-3">
        <div class="form-check form-switch">
          <input class="form-check-input" type="checkbox" name="immediate_safety_risk"
                 value="1" id="em_safety_risk">
          <label class="form-check-label fw-semibold text-danger" for="em_safety_risk">
            <i class="bi bi-person-exclamation me-1"></i>
            Immediate Safety Risk to Guests
            <small class="text-muted fw-normal">(triggers property-wide alert regardless of severity)</small>
          </label>
        </div>
      </div>

      <!-- Contractor required (UC35 error handling) -->
      <div class="mb-3">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="contractor_required"
                 value="1" id="em_contractor">
          <label class="form-check-label small text-muted" for="em_contractor">
            External contractor required (marks WO for contractor assignment)
          </label>
        </div>
      </div>

      <!-- Assign Technician -->
      <div class="mb-3">
        <label class="form-label fw-semibold">Assign Technician</label>
        <select name="assigned_to" class="form-select">
          <option value="">â€” Assign later â€”</option>
          <?php foreach ($technicians as $t): ?>
            <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="alert alert-warning py-2 small mb-3">
        <i class="bi bi-info-circle me-1"></i>
        Affected room set to <strong>Out of Order</strong> immediately.
        Safety Critical + Immediate Safety Risk also triggers a <strong>property-wide alert</strong>.
        Maintenance Supervisor and Front Desk are notified instantly.
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-danger px-4" id="btn-log-emergency">
          <i class="bi bi-exclamation-triangle me-1"></i>Log Emergency
        </button>
        <a href="<?= APP_URL ?>/?url=maintenance/index" class="btn btn-outline-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>

<!-- â”€â”€ Printable paper form (shown only when printing) â”€â”€â”€â”€â”€â”€â”€ -->
<div class="print-form p-4">
  <h2>EMERGENCY REPAIR LOG â€” PAPER FORM</h2>
  <p><small>Complete manually if system is unavailable. Enter into system on reconnect.</small></p>
  <table border="1" cellpadding="6" cellspacing="0" width="100%">
    <tr><td width="30%"><strong>Date / Time</strong></td><td></td></tr>
    <tr><td><strong>Logged By (Name)</strong></td><td></td></tr>
    <tr><td><strong>Room / Asset</strong></td><td></td></tr>
    <tr><td><strong>Failure Type</strong></td><td>â˜ Electrical â˜ Plumbing â˜ HVAC â˜ Structural â˜ Safety Hazard â˜ Equipment â˜ Other</td></tr>
    <tr><td><strong>Severity</strong></td><td>â˜ Low â˜ Medium â˜ High â˜ Safety Critical</td></tr>
    <tr><td><strong>Immediate Guest Safety Risk</strong></td><td>â˜ YES â˜ NO</td></tr>
    <tr><td><strong>Fault Description</strong></td><td style="height:80px"></td></tr>
    <tr><td><strong>Assigned Technician</strong></td><td></td></tr>
    <tr><td><strong>Supervisor Notified</strong></td><td>â˜ YES â€” Name: _______________</td></tr>
    <tr><td><strong>Front Desk Notified</strong></td><td>â˜ YES â€” Time: _______________</td></tr>
  </table>
</div>

<script>
// Show offline notice when navigator.onLine is false
if (!navigator.onLine) {
  document.getElementById('offline-notice').style.display = '';
}
window.addEventListener('offline', () => {
  document.getElementById('offline-notice').style.display = '';
});
window.addEventListener('online', () => {
  document.getElementById('offline-notice').style.display = 'none';
});
</script>

<?php $content = ob_get_clean(); require VIEW_PATH . '/layouts/main.php'; ?>
