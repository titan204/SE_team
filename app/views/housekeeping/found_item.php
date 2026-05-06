<?php $pageTitle = 'Log Found Item'; ob_start(); ?>

<style>
  .fi-card  { background:#fff; border:1px solid #e0d0c0; border-radius:10px; padding:1.5rem; box-shadow:0 2px 8px rgba(0,0,0,.06); }
  .hv-badge { background:#dc3545; color:#fff; border-radius:4px; padding:2px 8px; font-size:.78rem; }
</style>

<div class="container-fluid py-3" style="max-width:760px">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0"><i class="bi bi-search-heart me-2"></i>Log Found Item</h4>
    <a href="<?= APP_URL ?>/?url=housekeeping/index" class="btn btn-sm btn-outline-secondary">
      <i class="bi bi-arrow-left me-1"></i>Back
    </a>
  </div>

  <?php if (!empty($_SESSION['hk_success'])): ?>
    <div class="alert alert-success alert-dismissible fade show py-2">
      <?= htmlspecialchars($_SESSION['hk_success']) ?>
      <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['hk_success']); ?>
  <?php endif; ?>

  <?php if (!empty($_SESSION['hk_duplicates'])): ?>
    <div class="alert alert-warning alert-dismissible">
      <i class="bi bi-exclamation-triangle me-2"></i>
      <strong>Possible duplicates found in the last 2 hours for this room:</strong>
      <ul class="mb-1 mt-1 small">
        <?php foreach ($_SESSION['hk_duplicates'] as $d): ?>
          <li><strong><?= htmlspecialchars($d['lf_reference']) ?></strong> — <?= htmlspecialchars($d['description']) ?>
            <span class="text-muted">(<?= htmlspecialchars($d['found_at']) ?>)</span></li>
        <?php endforeach; ?>
      </ul>
      Submit anyway using the form below (check "Override duplicate warning").
      <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['hk_duplicates']); ?>
  <?php endif; ?>

  <div class="fi-card">
    <form method="POST" action="<?= APP_URL ?>/?url=housekeeping/logFoundItem"
          enctype="multipart/form-data" id="found-item-form">

      <!-- Description -->
      <div class="mb-3">
        <label for="fi_description" class="form-label fw-semibold">
          Item Description <span class="text-danger">*</span>
        </label>
        <textarea id="fi_description" name="description" class="form-control" rows="3"
                  placeholder="Describe the item in detail: colour, brand, distinguishing marks…"
                  required><?= htmlspecialchars($_SESSION['hk_form_data']['description'] ?? '') ?></textarea>
      </div>

      <!-- Location -->
      <div class="mb-3">
        <label class="form-label fw-semibold">Found Location <span class="text-danger">*</span></label>
        <div class="d-flex gap-3 mb-2">
          <div class="form-check">
            <input class="form-check-input" type="radio" name="location_type" value="room"
                   id="loc_room" checked onchange="toggleLocation(this.value)">
            <label class="form-check-label" for="loc_room">Room</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="location_type" value="public"
                   id="loc_public" onchange="toggleLocation(this.value)">
            <label class="form-check-label" for="loc_public">Public Area</label>
          </div>
        </div>
        <div id="room-picker">
          <select name="room_number" class="form-select">
            <option value="">— Select room —</option>
            <?php foreach ($rooms as $r): ?>
              <option value="<?= htmlspecialchars($r['room_number']) ?>"><?= htmlspecialchars($r['room_number']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div id="area-picker" style="display:none">
          <select name="public_area" class="form-select">
            <option value="lobby">Lobby</option>
            <option value="pool">Pool</option>
            <option value="restaurant">Restaurant</option>
            <option value="elevator">Elevator</option>
            <option value="parking">Parking</option>
            <option value="other">Other</option>
          </select>
        </div>
      </div>

      <!-- Condition -->
      <div class="mb-3">
        <label class="form-label fw-semibold">Condition</label>
        <div class="d-flex gap-3">
          <?php foreach (['good','damaged','fragile'] as $cond): ?>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="condition"
                     value="<?= $cond ?>" id="cond_<?= $cond ?>" <?= $cond === 'good' ? 'checked' : '' ?>>
              <label class="form-check-label" for="cond_<?= $cond ?>"><?= ucfirst($cond) ?></label>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- High-value flag -->
      <div class="mb-3">
        <div class="form-check form-switch">
          <input class="form-check-input" type="checkbox" name="is_high_value"
                 value="1" id="fi_high_value">
          <label class="form-check-label" for="fi_high_value">
            <span class="hv-badge me-1">HIGH VALUE</span>
            Jewelry, electronics, passport, cash — triggers security escalation
          </label>
        </div>
      </div>

      <!-- Photo upload -->
      <div class="mb-3">
        <label for="fi_photo" class="form-label fw-semibold">Photo <small class="text-muted">(optional)</small></label>
        <input type="file" name="photo" id="fi_photo" class="form-control" accept="image/*">
        <div class="form-text">If upload fails, submit without — text is saved immediately.</div>
      </div>

      <!-- Override duplicate -->
      <div class="mb-3">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="override_duplicate" value="1" id="fi_override">
          <label class="form-check-label text-muted small" for="fi_override">Override duplicate warning</label>
        </div>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-success px-4" id="btn-log-found">
          <i class="bi bi-plus-circle me-1"></i>Log Found Item
        </button>
        <a href="<?= APP_URL ?>/?url=housekeeping/index" class="btn btn-outline-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>

<script>
function toggleLocation(val) {
  document.getElementById('room-picker').style.display = val === 'room'   ? '' : 'none';
  document.getElementById('area-picker').style.display = val === 'public' ? '' : 'none';
}
</script>

<?php $content = ob_get_clean(); require VIEW_PATH . '/layouts/main.php'; ?>
