<?php $pageTitle = 'Pay Deposit'; ?>
<?php ob_start(); ?>

<?php
$reservation   = $reservation   ?? null;
$depositAmount = $depositAmount ?? 0;
$paid          = ($_GET['paid'] ?? '') === '1' || ($reservation['deposit_paid'] ?? 0) == 1;

if (!$reservation) {
    header('Location: ' . (defined('APP_URL') ? APP_URL : '') . '/index.php?url=reservations');
    exit;
}

$rid        = (int) $reservation['id'];
$total      = (float) $reservation['total_price'];
$nights     = (new DateTime($reservation['check_in_date']))->diff(new DateTime($reservation['check_out_date']))->days;
?>

<style>
  :root { --bg:#FFF8F0; --accent:#C08552; --accent2:#8C5A3C; --dark:#4B2E2B; }
  body  { background:var(--bg) !important; }

  .dep-page   { max-width: 780px; margin: 0 auto; padding: 2rem 1rem; }
  .page-header { border-bottom:2px solid var(--accent); padding-bottom:.75rem; margin-bottom:1.75rem; }
  .page-header h2 { color:var(--dark); font-weight:700; }

  /* Step bar */
  .steps { display:flex; gap:0; margin-bottom:2rem; }
  .step  { flex:1; text-align:center; padding:.5rem .25rem; font-size:.78rem; font-weight:600;
           color:#aaa; border-bottom:3px solid #e0d0c0; position:relative; }
  .step.active { color:var(--accent); border-color:var(--accent); }
  .step.done   { color:#3a8a3a; border-color:#3a8a3a; }
  .step .num   { display:inline-flex; align-items:center; justify-content:center;
                 width:1.6rem; height:1.6rem; border-radius:50%; border:2px solid currentColor;
                 margin-bottom:.25rem; font-size:.8rem; }

  /* Summary card */
  .summary-card { background:#fff; border:1px solid #e8d5c0; border-radius:12px;
                  padding:1.25rem 1.5rem; margin-bottom:1.5rem;
                  box-shadow:0 2px 12px rgba(192,133,82,.09); }
  .summary-row  { display:flex; justify-content:space-between; padding:.35rem 0;
                  border-bottom:1px dashed #f0e0cc; font-size:.9rem; }
  .summary-row:last-child { border:none; }
  .summary-label { color:#888; font-weight:600; }
  .summary-value { color:var(--dark); font-weight:500; }
  .deposit-amount { font-size:1.6rem; font-weight:700; color:var(--accent); }

  /* Card form */
  .payment-card { background:#fff; border:1px solid #e8d5c0; border-radius:12px;
                  padding:1.75rem; box-shadow:0 4px 20px rgba(192,133,82,.12); }
  .card-title-bar { color:var(--dark); font-weight:700; font-size:1rem;
                    border-bottom:1px solid #f0e0cc; padding-bottom:.6rem; margin-bottom:1.25rem; }
  label { color:var(--dark); font-weight:600; font-size:.875rem; margin-bottom:.25rem; display:block; }
  .form-control { border-color:#d0b090; border-radius:7px; }
  .form-control:focus { border-color:var(--accent); box-shadow:0 0 0 .2rem rgba(192,133,82,.25); }
  .form-control.is-valid   { border-color:#3a8a3a; }
  .form-control.is-invalid { border-color:#c0392b; }

  /* Credit card visual */
  .cc-visual { background:linear-gradient(135deg,var(--dark) 0%,var(--accent) 100%);
               border-radius:14px; padding:1.4rem 1.5rem 1.1rem; color:#fff; margin-bottom:1.5rem;
               box-shadow:0 8px 24px rgba(75,46,43,.35); position:relative; overflow:hidden; }
  .cc-visual::before { content:''; position:absolute; top:-30px; right:-30px;
                       width:140px; height:140px; background:rgba(255,255,255,.07); border-radius:50%; }
  .cc-visual::after  { content:''; position:absolute; bottom:-50px; left:40px;
                       width:180px; height:180px; background:rgba(255,255,255,.05); border-radius:50%; }
  .cc-chip  { width:38px; height:28px; background:linear-gradient(135deg,#f0d070,#c8a840);
              border-radius:5px; margin-bottom:.75rem; }
  .cc-number { font-size:1.15rem; letter-spacing:.18em; font-family:monospace; margin-bottom:.6rem; }
  .cc-meta   { display:flex; justify-content:space-between; font-size:.78rem; opacity:.85; }

  /* Processing overlay */
  .processing-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.45);
                        z-index:9999; align-items:center; justify-content:center; }
  .processing-overlay.show { display:flex; }
  .processing-box { background:#fff; border-radius:14px; padding:2.5rem 3rem; text-align:center;
                    box-shadow:0 12px 40px rgba(0,0,0,.25); max-width:320px; }
  .spinner { width:52px; height:52px; border:5px solid #f0e0cc;
             border-top-color:var(--accent); border-radius:50%;
             animation:spin .8s linear infinite; margin:0 auto 1rem; }
  @keyframes spin { to { transform:rotate(360deg); } }

  /* Success banner */
  .success-banner { background:linear-gradient(135deg,#e8f5e8,#c8e8c8);
                    border:1.5px solid #3a8a3a; border-radius:12px;
                    padding:1.5rem 1.75rem; margin-bottom:1.5rem; }
  .btn-confirm { background:linear-gradient(135deg,var(--accent),var(--accent2));
                 color:#fff; border:none; border-radius:9px; padding:.7rem 2rem;
                 font-weight:700; font-size:1rem; width:100%;
                 transition:opacity .2s; }
  .btn-confirm:hover { opacity:.88; color:#fff; }
  .btn-pay { background:linear-gradient(135deg,var(--accent),var(--accent2));
             color:#fff; border:none; border-radius:9px; padding:.7rem 2rem;
             font-weight:700; font-size:1rem; width:100%; transition:opacity .2s; }
  .btn-pay:hover { opacity:.88; color:#fff; }
</style>

<div class="dep-page">

  <!-- Header -->
  <div class="page-header d-flex justify-content-between align-items-center">
    <h2><i class="bi bi-credit-card me-2"></i>Secure Deposit Payment</h2>
    <a href="<?= APP_URL ?>/index.php?url=reservations/show/<?= $rid ?>"
       class="btn btn-outline-secondary" style="border-radius:8px;">
      <i class="bi bi-eye me-1"></i> View Reservation
    </a>
  </div>

  <!-- Step progress -->
  <div class="steps mb-4">
    <div class="step done">
      <div class="num">✓</div>
      <div>Reservation Created</div>
    </div>
    <div class="step <?= $paid ? 'done' : 'active' ?>">
      <div class="num"><?= $paid ? '✓' : '2' ?></div>
      <div>Pay Deposit</div>
    </div>
    <div class="step <?= $paid ? 'active' : '' ?>">
      <div class="num"><?= $paid ? '✓' : '3' ?></div>
      <div><?= $paid ? 'Reservation Confirmed' : 'Auto-Confirm' ?></div>
    </div>
  </div>

  <!-- Reservation Summary -->
  <div class="summary-card">
    <div class="summary-row">
      <span class="summary-label">Reservation #</span>
      <span class="summary-value"><?= $rid ?></span>
    </div>
    <div class="summary-row">
      <span class="summary-label">Room</span>
      <span class="summary-value">Room <?= htmlspecialchars($reservation['room_number'] ?? '—') ?> — <?= htmlspecialchars($reservation['room_type_name'] ?? '') ?></span>
    </div>
    <div class="summary-row">
      <span class="summary-label">Stay</span>
      <span class="summary-value"><?= htmlspecialchars($reservation['check_in_date']) ?> → <?= htmlspecialchars($reservation['check_out_date']) ?> (<?= $nights ?> night<?= $nights != 1 ? 's' : '' ?>)</span>
    </div>
    <div class="summary-row">
      <span class="summary-label">Total Price</span>
      <span class="summary-value"><strong>$<?= number_format($total, 2) ?></strong></span>
    </div>
    <div class="summary-row" style="background:#fff8f0;margin:0 -.5rem;padding:.5rem .5rem;border-radius:6px;border:none;">
      <span class="summary-label" style="color:var(--accent);">Deposit Due (20%)</span>
      <span class="deposit-amount">$<?= number_format($depositAmount, 2) ?></span>
    </div>
  </div>

  <?php if ($paid): ?>
  <!-- ══════════  SUCCESS STATE  ══════════ -->
  <div class="success-banner">
    <div class="d-flex align-items-center gap-3 mb-2">
      <span style="font-size:2rem;">✅</span>
      <div>
        <div style="font-size:1.1rem;font-weight:700;color:#2a6a2a;">Deposit Paid — Reservation Confirmed!</div>
        <div style="color:#3a7a3a;font-size:.9rem;">
          $<?= number_format($depositAmount, 2) ?> has been charged to your card.
          Your reservation is now <strong>confirmed</strong>.
        </div>
      </div>
    </div>
    <div style="font-size:.85rem;color:#555;margin-top:.5rem;">
      Remaining balance of <strong>$<?= number_format($total - $depositAmount, 2) ?></strong>
      will be collected at check-in.
    </div>
  </div>

  <a href="<?= APP_URL ?>/index.php?url=reservations/show/<?= $rid ?>"
     class="btn btn-confirm">
    <i class="bi bi-calendar-check me-2"></i> View My Confirmed Reservation
  </a>

  <?php else: ?>
  <!-- ══════════  PAYMENT FORM  ══════════ -->
  <div class="payment-card">
    <div class="card-title-bar"><i class="bi bi-shield-lock me-2"></i>Enter Payment Details</div>

    <!-- Credit card visual -->
    <div class="cc-visual" id="ccVisual">
      <div class="cc-chip"></div>
      <div class="cc-number" id="ccDisplay">•••• •••• •••• ••••</div>
      <div class="cc-meta">
        <div>
          <div style="font-size:.68rem;opacity:.7;margin-bottom:.1rem;">CARD HOLDER</div>
          <div id="ccNameDisplay">YOUR NAME</div>
        </div>
        <div style="text-align:right;">
          <div style="font-size:.68rem;opacity:.7;margin-bottom:.1rem;">EXPIRES</div>
          <div id="ccExpDisplay">MM/YY</div>
        </div>
      </div>
    </div>

    <form id="depositForm" method="POST"
          action="<?= APP_URL ?>/index.php?url=reservations/payDeposit/<?= $rid ?>"
          novalidate>

      <!-- Card Number -->
      <div class="mb-3">
        <label for="card_number">Card Number</label>
        <input id="card_number" type="text" class="form-control"
               placeholder="1234 5678 9012 3456" maxlength="19" autocomplete="cc-number">
        <div class="invalid-feedback">Please enter a valid 16-digit card number.</div>
      </div>

      <!-- Name on Card -->
      <div class="mb-3">
        <label for="card_name">Name on Card</label>
        <input id="card_name" type="text" class="form-control"
               placeholder="John Smith" autocomplete="cc-name">
        <div class="invalid-feedback">Please enter the cardholder name.</div>
      </div>

      <div class="row g-3 mb-4">
        <div class="col-6">
          <label for="card_expiry">Expiry Date</label>
          <input id="card_expiry" type="text" class="form-control"
                 placeholder="MM/YY" maxlength="5" autocomplete="cc-exp">
          <div class="invalid-feedback">Invalid expiry date.</div>
        </div>
        <div class="col-6">
          <label for="card_cvv">CVV</label>
          <input id="card_cvv" type="password" class="form-control"
                 placeholder="•••" maxlength="4" autocomplete="cc-csc">
          <div class="invalid-feedback">Invalid CVV.</div>
        </div>
      </div>

      <div style="background:#f8f0e8;border-radius:8px;padding:.75rem 1rem;font-size:.83rem;color:#7a5030;margin-bottom:1.25rem;">
        <i class="bi bi-lock-fill me-1"></i>
        Your payment is protected by 256-bit SSL encryption.
        This is a <strong>demo simulation</strong> — no real charge will occur.
      </div>

      <button type="submit" class="btn btn-pay" id="payBtn">
        <i class="bi bi-credit-card me-2"></i>
        Pay Deposit — $<?= number_format($depositAmount, 2) ?>
      </button>
    </form>
  </div>
  <?php endif; ?>

</div>

<!-- Processing overlay -->
<div class="processing-overlay" id="processingOverlay">
  <div class="processing-box">
    <div class="spinner"></div>
    <div style="font-weight:700;color:var(--dark);font-size:1.05rem;">Processing Payment…</div>
    <div style="color:#888;font-size:.87rem;margin-top:.35rem;">Please do not close this page</div>
  </div>
</div>

<script>
(function () {
  const numEl    = document.getElementById('card_number');
  const nameEl   = document.getElementById('card_name');
  const expEl    = document.getElementById('card_expiry');
  const cvvEl    = document.getElementById('card_cvv');
  const form     = document.getElementById('depositForm');
  const overlay  = document.getElementById('processingOverlay');
  const ccDisp   = document.getElementById('ccDisplay');
  const ccName   = document.getElementById('ccNameDisplay');
  const ccExp    = document.getElementById('ccExpDisplay');

  if (!form) return; // already paid state — skip

  // ── Live card number formatting + display ─────────────────
  numEl.addEventListener('input', function () {
    let v = this.value.replace(/\D/g, '').substring(0, 16);
    this.value = v.replace(/(.{4})/g, '$1 ').trim();
    const padded = (v + '••••••••••••••••').substring(0, 16);
    ccDisp.textContent = padded.replace(/(.{4})/g, '$1 ').trim();
  });

  nameEl.addEventListener('input', function () {
    ccName.textContent = this.value.toUpperCase() || 'YOUR NAME';
  });

  expEl.addEventListener('input', function () {
    let v = this.value.replace(/\D/g, '').substring(0, 4);
    if (v.length >= 2) v = v.substring(0, 2) + '/' + v.substring(2);
    this.value = v;
    ccExp.textContent = v || 'MM/YY';
  });

  // ── Luhn check ────────────────────────────────────────────
  function luhn(num) {
    let sum = 0, alt = false;
    for (let i = num.length - 1; i >= 0; i--) {
      let n = parseInt(num[i]);
      if (alt) { n *= 2; if (n > 9) n -= 9; }
      sum += n; alt = !alt;
    }
    return sum % 10 === 0;
  }

  // ── Validate expiry MM/YY ─────────────────────────────────
  function validExpiry(val) {
    const parts = val.split('/');
    if (parts.length !== 2) return false;
    const mm = parseInt(parts[0]), yy = parseInt(parts[1]);
    if (mm < 1 || mm > 12) return false;
    const now = new Date();
    const exp = new Date(2000 + yy, mm - 1);
    return exp >= new Date(now.getFullYear(), now.getMonth());
  }

  // ── Field validation helpers ──────────────────────────────
  function setValid(el, ok) {
    el.classList.toggle('is-valid', ok);
    el.classList.toggle('is-invalid', !ok);
  }

  // ── Form submission ───────────────────────────────────────
  form.addEventListener('submit', function (e) {
    e.preventDefault();

    const rawNum = numEl.value.replace(/\s/g, '');
    const name   = nameEl.value.trim();
    const exp    = expEl.value.trim();
    const cvv    = cvvEl.value.trim();

    const numOk  = rawNum.length === 16 && /^\d+$/.test(rawNum);
    const nameOk = name.length >= 2;
    const expOk  = validExpiry(exp);
    const cvvOk  = /^\d{3,4}$/.test(cvv);

    setValid(numEl,  numOk);
    setValid(nameEl, nameOk);
    setValid(expEl,  expOk);
    setValid(cvvEl,  cvvOk);

    if (!numOk || !nameOk || !expOk || !cvvOk) return;

    // Show processing overlay, then submit after 2.2s delay (simulation)
    overlay.classList.add('show');
    setTimeout(function () { form.submit(); }, 2200);
  });
})();
</script>

<?php $content = ob_get_clean(); ?>
<?php require VIEW_PATH . '/layouts/main.php'; ?>
