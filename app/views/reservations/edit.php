<?php $pageTitle = 'Edit Reservation'; ?>
<?php ob_start(); ?>

<?php $r = $reservation; $rid = $r['id']; ?>

<style>
  :root { --bg:#FFF8F0; --accent:#C08552; --accent2:#8C5A3C; --dark:#4B2E2B; }
  body { background-color: var(--bg) !important; }
  .page-header { border-bottom:2px solid var(--accent); padding-bottom:.75rem; margin-bottom:1.5rem; }
  .page-header h2 { color:var(--dark); font-weight:700; }
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
</style>

<div class="container py-3" style="max-width:900px;">

  <div class="page-header d-flex justify-content-between align-items-center">
    <h2><i class="bi bi-pencil-square me-2"></i>Edit Reservation #<?= $rid ?></h2>
    <a href="<?= APP_URL ?>/index.php?url=reservations/show/<?= $rid ?>"
       class="btn btn-outline-secondary" style="border-radius:8px;">
      <i class="bi bi-arrow-left me-1"></i> Back
    </a>
  </div>

  <form method="POST" action="<?= APP_URL ?>/index.php?url=reservations/update/<?= $rid ?>">

    <div class="form-card mb-4">

      <!-- ── Guest ── -->
      <div class="section-title"><i class="bi bi-person me-2"></i>Guest Information</div>
      <div class="row g-3 mb-3">
        <div class="col-md-6">
          <label for="guest_id">Guest <span class="text-danger">*</span></label>
          <select id="guest_id" name="guest_id" class="form-select" required>
            <?php foreach ($guests as $g): ?>
            <option value="<?= $g['id'] ?>" <?= $g['id'] == $r['guest_id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($g['name']) ?>
              <?php if ($g['is_vip']): ?> ★ VIP<?php endif; ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-3">
          <label for="adults">Adults <span class="text-danger">*</span></label>
          <input id="adults" type="number" name="adults" class="form-control"
                 value="<?= (int)$r['adults'] ?>" min="1" max="10" required>
        </div>
        <div class="col-md-3">
          <label for="children">Children</label>
          <input id="children" type="number" name="children" class="form-control"
                 value="<?= (int)$r['children'] ?>" min="0" max="10">
        </div>
      </div>

      <!-- ── Dates ── -->
      <div class="section-title mt-3"><i class="bi bi-calendar-range me-2"></i>Stay Dates</div>
      <div class="row g-3 mb-3">
        <div class="col-md-4">
          <label for="check_in_date">Check-In Date <span class="text-danger">*</span></label>
          <input id="check_in_date" type="date" name="check_in_date" class="form-control"
                 value="<?= htmlspecialchars($r['check_in_date']) ?>" required>
        </div>
        <div class="col-md-4">
          <label for="check_out_date">Check-Out Date <span class="text-danger">*</span></label>
          <input id="check_out_date" type="date" name="check_out_date" class="form-control"
                 value="<?= htmlspecialchars($r['check_out_date']) ?>" required>
        </div>
        <div class="col-md-4">
          <label>Nights</label>
          <input id="nights_display" type="text" class="form-control" readonly
                 style="background:#f8f0e8;color:var(--dark);font-weight:700;">
        </div>
      </div>

      <!-- ── Room + Price ── -->
      <div class="row g-3 mb-3">
        <div class="col-md-6">
          <label for="room_id">Room <span class="text-danger">*</span></label>
          <select id="room_id" name="room_id" class="form-select" required>
            <?php foreach ($rooms as $rm): ?>
            <option value="<?= $rm['id'] ?>"
                    data-price="<?= $rm['base_price'] ?>"
                    <?= $rm['id'] == $r['room_id'] ? 'selected' : '' ?>>
              Room <?= htmlspecialchars($rm['room_number']) ?>
              — <?= htmlspecialchars($rm['type_name']) ?>
              ($<?= number_format($rm['base_price'],2) ?>/night)
              [<?= $rm['status'] ?>]
            </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6">
          <label>Estimated Total</label>
          <div class="price-preview mt-1">
            <div class="amount" id="priceAmount">$0.00</div>
            <div class="label"  id="priceLabel">—</div>
          </div>
        </div>
      </div>

      <!-- ── Special Requests ── -->
      <div class="section-title mt-3"><i class="bi bi-chat-left-text me-2"></i>Special Requirements</div>
      <div class="row g-3 mb-3">
        <div class="col-12">
          <label for="special_requests">Special Requests</label>
          <textarea id="special_requests" name="special_requests" class="form-control" rows="3"
          ><?= htmlspecialchars($r['special_requests'] ?? '') ?></textarea>
        </div>
        <div class="col-md-4">
          <label for="deposit_amount">Deposit Amount ($)</label>
          <input id="deposit_amount" type="number" name="deposit_amount" class="form-control"
                 value="<?= htmlspecialchars($r['deposit_amount']) ?>" min="0" step="0.01">
        </div>
        <div class="col-md-4 d-flex align-items-end">
          <div class="form-check ms-2">
            <input class="form-check-input" type="checkbox" id="deposit_paid"
                   name="deposit_paid" value="1"
                   <?= $r['deposit_paid'] ? 'checked' : '' ?>
                   style="border-color:var(--accent);">
            <label class="form-check-label" for="deposit_paid">Deposit Paid</label>
          </div>
        </div>
      </div>

      <!-- ── Group Booking ── -->
      <div class="section-title mt-3"><i class="bi bi-people me-2"></i>Group Booking</div>
      <div class="row g-3 mb-1">
        <div class="col-md-4 d-flex align-items-center">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="is_group"
                   name="is_group" value="1"
                   <?= !empty($r['is_group']) ? 'checked' : '' ?>
                   style="border-color:var(--accent);">
            <label class="form-check-label" for="is_group">Group Booking</label>
          </div>
        </div>
        <div class="col-md-8" id="groupIdWrap"
             style="display:<?= !empty($r['is_group']) ? '' : 'none' ?>;">
          <label for="group_id">Group ID</label>
          <input id="group_id" type="number" name="group_id" class="form-control"
                 value="<?= htmlspecialchars($r['group_id'] ?? '') ?>" min="1">
        </div>
      </div>
    </div>

    <!-- ── Submit ── -->
    <div class="d-flex gap-3 justify-content-end">
      <a href="<?= APP_URL ?>/index.php?url=reservations/show/<?= $rid ?>"
         class="btn btn-outline-secondary px-4" style="border-radius:8px;">Cancel</a>
      <button type="submit" class="btn btn-accent px-5">
        <i class="bi bi-check-circle me-1"></i> Update Reservation
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
  const nightsEl   = document.getElementById('nights_display');
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
        nightsEl.value    = nights + ' night' + (nights > 1 ? 's' : '');
        priceEl.textContent = '$' + (price * nights).toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2});
        labelEl.textContent = nights + ' night' + (nights>1?'s':'') + ' × $' + price.toFixed(2);
        return;
      }
    }
    nightsEl.value      = '—';
    priceEl.textContent = '$0.00';
    labelEl.textContent = 'Select room & dates';
  }

  roomEl.addEventListener('change', updatePrice);
  checkInEl.addEventListener('change', updatePrice);
  checkOutEl.addEventListener('change', updatePrice);

  // Run on load
  updatePrice();
})();
</script>

<?php $content = ob_get_clean(); ?>
<?php require VIEW_PATH . '/layouts/main.php'; ?>
