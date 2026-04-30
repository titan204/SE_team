<?php $pageTitle = 'New Reservation'; ?>
<?php ob_start(); ?>

<?php
// Safe defaults — the controller always passes these via extract(),
// but static analysers cannot trace through extract() so we define
// fallbacks here to prevent undefined-variable warnings.
$guests       = $guests       ?? [];
$rooms        = $rooms        ?? [];
$isGuestUser  = $isGuestUser  ?? false;
$currentGuest = $currentGuest ?? null;
?>

<style>
  :root { --bg:#FFF8F0; --accent:#C08552; --accent2:#8C5A3C; --dark:#4B2E2B; }
  body { background-color: var(--bg) !important; }
  .page-header { border-bottom: 2px solid var(--accent); padding-bottom:.75rem; margin-bottom:1.5rem; }
  .page-header h2 { color: var(--dark); font-weight:700; }
  .form-card { background:#fff; border:1px solid #e8d5c0; border-radius:12px; padding:2rem; box-shadow:0 4px 16px rgba(192,133,82,.1); }
  .section-title { color:var(--dark); font-weight:700; font-size:1rem; border-bottom:1px solid #e8d5c0; padding-bottom:.5rem; margin-bottom:1rem; }
  label { color:var(--dark); font-weight:600; font-size:.88rem; margin-bottom:.25rem; }
  .form-control, .form-select { border-color:#d0b090; border-radius:7px; }
  .form-control:focus, .form-select:focus { border-color:var(--accent); box-shadow:0 0 0 .2rem rgba(192,133,82,.25); }
  .btn-accent { background-color:var(--accent); color:#fff; border:none; border-radius:8px; padding:.5rem 1.5rem; font-weight:600; }
  .btn-accent:hover { background-color:var(--accent2); color:#fff; }
  .price-preview { background:linear-gradient(135deg,var(--accent),var(--accent2)); color:#fff; border-radius:10px; padding:1rem 1.5rem; text-align:center; }
  .price-preview .amount { font-size:2rem; font-weight:700; }
  .price-preview .label  { font-size:.85rem; opacity:.85; }
  #room-suggestions .room-card { border:2px solid #e8d5c0; border-radius:8px; padding:.75rem; cursor:pointer; transition:all .2s; margin-bottom:.5rem; }
  #room-suggestions .room-card:hover, #room-suggestions .room-card.selected { border-color:var(--accent); background:#fff8f0; }
  #room-suggestions .room-card .score { float:right; background:var(--accent); color:#fff; border-radius:20px; padding:.1rem .6rem; font-size:.75rem; }
</style>

<div class="container py-3" style="max-width:900px;">

  <div class="page-header d-flex justify-content-between align-items-center">
    <h2><i class="bi bi-calendar-plus me-2"></i>New Reservation</h2>
    <a href="<?= APP_URL ?>/index.php?url=reservations"
       class="btn btn-outline-secondary" style="border-radius:8px;">
      <i class="bi bi-arrow-left me-1"></i> Back
    </a>
  </div>

  <?php if (!empty($_SESSION['reservation_error'])): ?>
  <div style="background:#fff0f0;border:1px solid #e08080;border-radius:8px;
              padding:.85rem 1.2rem;margin-bottom:1rem;color:#7a1a1a;font-weight:600;">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>
    <?= htmlspecialchars($_SESSION['reservation_error']) ?>
  </div>
  <?php unset($_SESSION['reservation_error']); ?>
  <?php endif; ?>

  <form id="reservationForm" method="POST"
        action="<?= APP_URL ?>/index.php?url=reservations/store">

    <div class="form-card mb-4">
      <!-- ── Section: Guest ── -->
      <div class="section-title"><i class="bi bi-person me-2"></i>Guest Information</div>
      <div class="row g-3 mb-3">
        <div class="col-md-6">
          <?php if (!empty($isGuestUser)): ?>
            <!-- GUEST: auto-assigned from session — no dropdown, no selector -->
            <input type="hidden" name="guest_id" value="<?= (int)($currentGuest['id'] ?? 0) ?>">
            <label>Guest</label>
            <div style="
                 background:#f8f0e8;
                 border:1px solid #d0b090;
                 border-radius:7px;
                 padding:.5rem .85rem;
                 display:flex;
                 align-items:center;
                 gap:.6rem;
                 color:#4B2E2B;
                 font-weight:600;
                 font-size:.95rem;">
              <i class="bi bi-person-circle" style="color:#C08552;font-size:1.15rem;"></i>
              <?= htmlspecialchars($currentGuest['name'] ?? $_SESSION['user_name'] ?? 'Guest') ?>
              <?php if (!empty($currentGuest['is_vip'])): ?>
                <span style="background:#c09800;color:#fff;border-radius:20px;
                             padding:.1em .6em;font-size:.72rem;font-weight:700;">★ VIP</span>
              <?php endif; ?>
            </div>
            <small style="color:#888;font-size:.8rem;">Automatically linked to your account.</small>
          <?php else: ?>
            <!-- STAFF: guest dropdown to select any guest -->
            <label for="guest_id">Guest <span class="text-danger">*</span></label>
            <select id="guest_id" name="guest_id" class="form-select" required>
              <option value="">— Select Guest —</option>
              <?php foreach ($guests as $g): ?>
              <option value="<?= $g['id'] ?>">
                <?= htmlspecialchars($g['name']) ?>
                <?php if ($g['is_vip']): ?> ★ VIP<?php endif; ?>
                (<?= htmlspecialchars($g['loyalty_tier']) ?>)
              </option>
              <?php endforeach; ?>
            </select>
          <?php endif; ?>
        </div>
        <div class="col-md-3">
          <label for="adults">Adults <span class="text-danger">*</span></label>
          <input id="adults" type="number" name="adults" class="form-control"
                 value="1" min="1" max="10" required>
        </div>
        <div class="col-md-3">
          <label for="children">Children</label>
          <input id="children" type="number" name="children" class="form-control"
                 value="0" min="0" max="10">
        </div>
      </div>


      <!-- ── Section: Dates ── -->
      <div class="section-title mt-3"><i class="bi bi-calendar-range me-2"></i>Stay Dates</div>
      <div class="row g-3 mb-3">
        <div class="col-md-6">
          <label for="check_in_date">Check-In Date <span class="text-danger">*</span></label>
          <input id="check_in_date" type="date" name="check_in_date" class="form-control"
                 min="<?= date('Y-m-d') ?>" required>
        </div>
        <div class="col-md-6">
          <label for="check_out_date">Check-Out Date <span class="text-danger">*</span></label>
          <input id="check_out_date" type="date" name="check_out_date" class="form-control"
                 min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required>
        </div>
      </div>

      <!-- ── Room Selection ── -->
      <div class="row g-3 mb-3">
        <div class="col-md-6">
          <label for="room_id">Room <span class="text-danger">*</span></label>
          <select id="room_id" name="room_id" class="form-select" required>
            <option value="">— Select a Room —</option>
            <?php foreach ($rooms as $rm): ?>
            <?php $isOOO = ($rm['status'] === 'out_of_order'); ?>
            <option value="<?= $rm['id'] ?>"
                    data-price="<?= $rm['base_price'] ?>"
                    data-status="<?= $rm['status'] ?>"
                    data-ooo="<?= $isOOO ? '1' : '0' ?>"
                    <?= $isOOO ? 'disabled style="color:#c00;background:#fff0f0;"' : ($rm['status'] !== 'available' ? 'style="color:#aaa;"' : '') ?>>
              Room <?= htmlspecialchars($rm['room_number']) ?>
              — <?= htmlspecialchars($rm['type_name']) ?>
              ($<?= number_format($rm['base_price'],2) ?>/night)
              [<?= $isOOO ? '⛔ Out of Order' : $rm['status'] ?>]
            </option>
            <?php endforeach; ?>
          </select>
          <div id="ooo-error" style="display:none;color:#9e3030;font-size:.85rem;margin-top:.35rem;font-weight:600;">
            ⚠ This room is out of order and cannot be reserved.
          </div>
        </div>
        <div class="col-md-6">
          <label>Estimated Total</label>
          <div class="price-preview mt-1">
            <div class="amount" id="priceAmount">$0.00</div>
            <div class="label" id="priceLabel">Select room &amp; dates</div>
          </div>
        </div>
      </div>

      <!-- ── Section: Special Requirements ── -->
      <div class="section-title mt-3"><i class="bi bi-chat-left-text me-2"></i>Special Requirements</div>
      <div class="row g-3 mb-3">
        <div class="col-12">
          <label for="special_requests">Special Requests</label>
          <textarea id="special_requests" name="special_requests" class="form-control"
                    rows="3" placeholder="e.g. extra pillows, late check-in, dietary requirements…"></textarea>
        </div>
        <div class="col-md-4">
          <label for="deposit_amount">Deposit Amount ($)</label>
          <input id="deposit_amount" type="number" name="deposit_amount" class="form-control"
                 value="0" min="0" step="0.01">
        </div>
        <div class="col-md-4 d-flex align-items-end">
          <div class="form-check ms-2">
            <input class="form-check-input" type="checkbox" id="deposit_paid"
                   name="deposit_paid" value="1" style="border-color:var(--accent);">
            <label class="form-check-label" for="deposit_paid">Deposit Paid</label>
          </div>
        </div>
      </div>

      <!-- ── Section: Group Booking ── -->
      <div class="section-title mt-3"><i class="bi bi-people me-2"></i>Group Booking</div>
      <div class="row g-3 mb-1">
        <div class="col-md-4 d-flex align-items-center">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="is_group"
                   name="is_group" value="1" style="border-color:var(--accent);">
            <label class="form-check-label" for="is_group">
              This is a group booking
            </label>
          </div>
        </div>
        <div class="col-md-8" id="groupIdWrap" style="display:none;">
          <label for="group_id">Existing Group ID (leave blank to start new group)</label>
          <input id="group_id" type="number" name="group_id" class="form-control"
                 placeholder="e.g. 6" min="1">
        </div>
      </div>
    </div>

    <!-- ── Submit Buttons ── -->
    <div class="d-flex gap-3 justify-content-end">
      <a href="<?= APP_URL ?>/index.php?url=reservations"
         class="btn btn-outline-secondary px-4" style="border-radius:8px;">
        Cancel
      </a>
      <button type="submit" class="btn btn-accent px-5">
        <i class="bi bi-check-circle me-1"></i> Create Reservation
      </button>
    </div>

  </form>
</div>

<script>
(function() {
  const checkInEl  = document.getElementById('check_in_date');
  const checkOutEl = document.getElementById('check_out_date');
  const roomEl     = document.getElementById('room_id');
  const priceEl    = document.getElementById('priceAmount');
  const labelEl    = document.getElementById('priceLabel');
  const isGroupEl  = document.getElementById('is_group');
  const groupWrap  = document.getElementById('groupIdWrap');

  isGroupEl.addEventListener('change', function() {
    groupWrap.style.display = this.checked ? '' : 'none';
  });

  function updatePrice() {
    const checkIn  = checkInEl.value;
    const checkOut = checkOutEl.value;
    const roomOpt  = roomEl.options[roomEl.selectedIndex];
    const price    = parseFloat(roomOpt?.dataset?.price || 0);

    if (checkIn && checkOut && price > 0) {
      const nights = Math.round((new Date(checkOut) - new Date(checkIn)) / 86400000);
      if (nights > 0) {
        priceEl.textContent = '$' + (price * nights).toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2});
        labelEl.textContent = nights + ' night' + (nights > 1 ? 's' : '') + ' × $' + price.toFixed(2);
        return;
      }
    }
    priceEl.textContent = '$0.00';
    labelEl.textContent = 'Select room & dates';
  }

  roomEl.addEventListener('change', updatePrice);
  checkInEl.addEventListener('change', updatePrice);
  checkOutEl.addEventListener('change', function() {
    if (checkInEl.value && this.value <= checkInEl.value) {
      const next = new Date(checkInEl.value);
      next.setDate(next.getDate() + 1);
      this.value = next.toISOString().split('T')[0];
    }
    updatePrice();
  });

  // ── OOO room guard (frontend) ───────────────────────────────
  const oooError  = document.getElementById('ooo-error');
  const submitBtn = document.querySelector('#reservationForm [type="submit"]');

  function checkOOO() {
    const selected = roomEl.options[roomEl.selectedIndex];
    const isOOO = selected && selected.dataset.ooo === '1';
    if (oooError)  oooError.style.display  = isOOO ? '' : 'none';
    if (submitBtn) submitBtn.disabled       = isOOO;
    return isOOO;
  }

  roomEl.addEventListener('change', checkOOO);

  document.getElementById('reservationForm').addEventListener('submit', function(e) {
    if (checkOOO()) {
      e.preventDefault();
      if (oooError) oooError.style.display = '';
      return false;
    }
  });

  window.selectRoom = function(roomId) {
    const opt = roomEl.querySelector(`option[value="${roomId}"]`);
    if (opt) { roomEl.value = roomId; checkOOO(); updatePrice(); }
  };
})();
</script>

<?php $content = ob_get_clean(); ?>
<?php require VIEW_PATH . '/layouts/main.php'; ?>
