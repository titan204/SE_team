<?php $pageTitle = 'Guest Bill — Res #' . $reservation['id']; ob_start(); ?>

<style>
  .bill-card { background:#fff; border:1px solid #dce8f0; border-radius:10px; padding:1.2rem 1.5rem; margin-bottom:1rem; box-shadow:0 2px 6px rgba(0,0,0,.05); }
  .table thead { background:#2d3a4a; color:#fff; }
  .table tbody tr:hover { background:#f5faff; }
  .voided td { opacity:.45; text-decoration:line-through; }
  .totals-box { background:#2d3a4a; color:#fff; border-radius:10px; padding:1rem 1.5rem; }
  .totals-box .row > div { padding:.25rem 0; border-bottom:1px solid rgba(255,255,255,.1); }
  .totals-box .row > div:last-child { border-bottom:none; }
  .grand { font-size:1.35rem; font-weight:700; color:#a8d8ff; }
  .finalized-banner { background:#d4edda; border:1px solid #c3e6cb; border-radius:8px; padding:.7rem 1rem; margin-bottom:1rem; }
  .btn-sm-icon { font-size:.76rem; padding:.25rem .55rem; }
</style>

<div class="container-fluid py-3">

  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h4 class="mb-0">
      <i class="bi bi-receipt me-2"></i>
      Guest Bill — <?= htmlspecialchars($reservation['guest_name']) ?>
      <small class="text-muted fs-6 ms-2">Res #<?= $reservation['id'] ?></small>
    </h4>
    <a href="<?= APP_URL ?>/index.php?url=billing" class="btn btn-sm btn-outline-secondary">
      <i class="bi bi-arrow-left me-1"></i>Back
    </a>
  </div>

  <!-- Flash messages -->
  <?php foreach (['bill_success'=>'success','bill_error'=>'danger'] as $key=>$cls): ?>
    <?php if (!empty($_SESSION[$key])): ?>
      <div class="alert alert-<?= $cls ?> alert-dismissible fade show py-2">
        <?= htmlspecialchars($_SESSION[$key]) ?>
        <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
      </div>
      <?php unset($_SESSION[$key]); ?>
    <?php endif; ?>
  <?php endforeach; ?>

  <?php if ($isFinalized): ?>
    <div class="finalized-banner">
      <i class="bi bi-lock-fill text-success me-1"></i>
      <strong>Bill Finalized</strong> — Issued <?= htmlspecialchars($finalInvoice['issued_at']) ?>.
      No further edits are permitted. Contact manager to unlock.
    </div>
  <?php endif; ?>

  <!-- Guest & Reservation Info -->
  <div class="bill-card d-flex flex-wrap gap-4">
    <div><small class="text-muted d-block">Guest</small><strong><?= htmlspecialchars($reservation['guest_name']) ?></strong><br><small><?= htmlspecialchars($reservation['guest_email']) ?></small></div>
    <div><small class="text-muted d-block">Room</small><?= htmlspecialchars($reservation['room_number']) ?> <small class="text-muted">(<?= htmlspecialchars($reservation['room_type_name']) ?>)</small></div>
    <div><small class="text-muted d-block">Check-in / Check-out</small><?= $reservation['check_in_date'] ?> → <?= $reservation['check_out_date'] ?></div>
    <div><small class="text-muted d-block">Nights</small><?= $reservation['nights'] ?></div>
    <div><small class="text-muted d-block">Daily Rate</small>$<?= number_format((float)$reservation['daily_rate'], 2) ?></div>
    <div><small class="text-muted d-block">Loyalty Points</small><?= number_format((int)$reservation['loyalty_points']) ?> pts</div>
  </div>

  <!-- Charges Table -->
  <div class="bill-card">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <strong>Charges</strong>
      <?php if (!$isFinalized): ?>
        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addChargeModal" id="btn-add-charge">
          <i class="bi bi-plus-circle me-1"></i>Add Charge
        </button>
      <?php endif; ?>
    </div>

    <!-- Room charge (always shown, derived) -->
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0" id="charges-table">
        <thead>
          <tr><th>Type</th><th>Description</th><th>Qty</th><th>Unit</th><th>Total</th><th>Added By</th><th>Date</th><?php if (!$isFinalized): ?><th></th><?php endif; ?></tr>
        </thead>
        <tbody>
          <!-- Room charge row -->
          <tr>
            <td><span class="badge bg-primary">room_rate</span></td>
            <td><?= (int)$reservation['nights'] ?> nights × $<?= number_format((float)$reservation['daily_rate'],2) ?>/night</td>
            <td>1</td>
            <td>$<?= number_format($totals['roomTotal'],2) ?></td>
            <td><strong>$<?= number_format($totals['roomTotal'],2) ?></strong></td>
            <td>System</td>
            <td><?= $reservation['check_in_date'] ?></td>
            <?php if (!$isFinalized): ?><td></td><?php endif; ?>
          </tr>
          <!-- Billing items -->
          <?php foreach ($items as $item): ?>
          <tr class="<?= $item['is_voided'] ? 'voided' : '' ?>">
            <td><span class="badge bg-secondary"><?= htmlspecialchars($item['item_type']) ?></span></td>
            <td><?= htmlspecialchars($item['description']) ?><?php if ($item['is_voided']): ?> <span class="badge bg-danger ms-1">Voided</span><?php endif; ?></td>
            <td><?= $item['quantity'] ?></td>
            <td>$<?= number_format((float)$item['amount'],2) ?></td>
            <td><strong>$<?= number_format((float)$item['amount'] * $item['quantity'],2) ?></strong></td>
            <td><?= htmlspecialchars($item['added_by_name'] ?? '—') ?></td>
            <td><?= substr($item['added_at'],0,10) ?></td>
            <?php if (!$isFinalized): ?>
            <td>
              <?php if (!$item['is_voided']): ?>
              <button class="btn btn-outline-danger btn-sm btn-sm-icon"
                      onclick="openVoidModal(<?= $item['id'] ?>, <?= $reservation['id'] ?>, '<?= addslashes($item['description']) ?>')"
                      id="btn-void-<?= $item['id'] ?>">
                <i class="bi bi-x-circle"></i>
              </button>
              <?php endif; ?>
            </td>
            <?php endif; ?>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Adjustments -->
  <div class="bill-card">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <strong>Adjustments & Discounts</strong>
      <?php if (!$isFinalized): ?>
      <div class="d-flex gap-2">
        <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#adjustModal" id="btn-add-adjustment">
          <i class="bi bi-percent me-1"></i>Discount/Surcharge
        </button>
        <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#loyaltyModal" id="btn-redeem-points">
          <i class="bi bi-star me-1"></i>Redeem Points
        </button>
      </div>
      <?php endif; ?>
    </div>
    <?php if (empty($adjustments)): ?>
      <p class="text-muted small mb-0">No adjustments applied.</p>
    <?php else: ?>
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead><tr><th>Type</th><th>Value</th><th>Reason</th><th>Applied By</th><th>Date</th></tr></thead>
        <tbody>
        <?php foreach ($adjustments as $adj): ?>
          <tr>
            <td><span class="badge bg-<?= $adj['type']==='surcharge'?'warning text-dark':'success' ?>"><?= ucfirst($adj['type']) ?></span></td>
            <td><?= $adj['type']==='surcharge' ? '+' : '−' ?>$<?= number_format((float)$adj['value'],2) ?></td>
            <td><?= htmlspecialchars($adj['reason']) ?></td>
            <td><?= htmlspecialchars($adj['applied_by_name'] ?? '—') ?></td>
            <td><?= substr($adj['created_at'],0,10) ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>

  <!-- Totals -->
  <div class="totals-box mb-3">
    <div class="row"><div class="col-8 text-end pe-3">Room Charges</div><div class="col-4 text-end">$<?= number_format($totals['roomTotal'],2) ?></div></div>
    <div class="row"><div class="col-8 text-end pe-3">Other Items</div><div class="col-4 text-end">$<?= number_format($totals['itemsTotal'],2) ?></div></div>
    <div class="row"><div class="col-8 text-end pe-3">Subtotal</div><div class="col-4 text-end">$<?= number_format($totals['subtotal'],2) ?></div></div>
    <div class="row"><div class="col-8 text-end pe-3 text-success">Discounts / Loyalty</div><div class="col-4 text-end text-success">−$<?= number_format($totals['discountTotal'],2) ?></div></div>
    <div class="row"><div class="col-8 text-end pe-3">Tax (10%)</div><div class="col-4 text-end">$<?= number_format($totals['taxAmount'],2) ?></div></div>
    <div class="row mt-1"><div class="col-8 text-end pe-3"><span class="grand">Grand Total</span></div><div class="col-4 text-end"><span class="grand">$<?= number_format($totals['grandTotal'],2) ?></span></div></div>
  </div>

  <!-- Action Buttons -->
  <?php if (!$isFinalized): ?>
  <div class="d-flex flex-wrap gap-2 mb-4">
    <form method="POST" action="<?= APP_URL ?>/index.php?url=billing/finalizeBill/<?= $reservation['id'] ?>"
          onsubmit="return confirm('Finalize and lock this bill? No further edits will be allowed.')" style="display:inline;">
      <button type="submit" class="btn btn-success px-4" id="btn-finalize-bill">
        <i class="bi bi-lock me-1"></i>Finalize Bill
      </button>
    </form>
    <button class="btn btn-primary px-4" data-bs-toggle="modal" data-bs-target="#payModal" id="btn-pay-bill">
      <i class="bi bi-credit-card me-1"></i>Record Payment
    </button>
  </div>
  <?php endif; ?>

</div>

<!-- Add Charge Modal -->
<div class="modal fade" id="addChargeModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Add Charge</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="POST" action="<?= APP_URL ?>/index.php?url=billing/addBillingItem/<?= $reservation['id'] ?>">
      <div class="modal-body">
        <div class="mb-2">
          <label class="form-label fw-semibold">Type</label>
          <select name="type" class="form-select" required>
            <option value="minibar">Minibar</option>
            <option value="external_service">External Service</option>
            <option value="manual">Manual</option>
            <option value="other">Other</option>
          </select>
        </div>
        <div class="mb-2">
          <label class="form-label fw-semibold">Description</label>
          <input type="text" name="description" class="form-control" required>
        </div>
        <div class="row g-2">
          <div class="col-6"><label class="form-label fw-semibold">Unit Amount ($)</label><input type="number" name="amount" step="0.01" min="0.01" class="form-control" required></div>
          <div class="col-6"><label class="form-label fw-semibold">Quantity</label><input type="number" name="quantity" min="1" value="1" class="form-control" required></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary" id="btn-save-charge">Add Charge</button>
      </div>
    </form>
  </div></div>
</div>

<!-- Void Modal -->
<div class="modal fade" id="voidModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content border-danger">
    <div class="modal-header bg-danger text-white"><h5 class="modal-title">Void Charge</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
    <form method="POST" id="void-form">
      <input type="hidden" name="reservation_id" value="<?= $reservation['id'] ?>">
      <div class="modal-body">
        <p>Voiding: <strong id="void_desc"></strong></p>
        <label class="form-label fw-semibold">Reason <span class="text-danger">*</span></label>
        <input type="text" name="reason" class="form-control" required placeholder="Required">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-danger" id="btn-confirm-void">Void Charge</button>
      </div>
    </form>
  </div></div>
</div>

<!-- Adjustment Modal -->
<div class="modal fade" id="adjustModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Apply Discount / Surcharge</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="POST" action="<?= APP_URL ?>/index.php?url=billing/applyAdjustment/<?= $reservation['id'] ?>">
      <div class="modal-body">
        <div class="mb-2">
          <label class="form-label fw-semibold">Type</label>
          <select name="type" class="form-select">
            <option value="discount">Discount</option>
            <option value="surcharge">Surcharge</option>
          </select>
        </div>
        <div class="mb-2"><label class="form-label fw-semibold">Amount ($)</label><input type="number" name="value" step="0.01" min="0.01" class="form-control" required></div>
        <div class="mb-2"><label class="form-label fw-semibold">Reason</label><input type="text" name="reason" class="form-control" required></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-success" id="btn-save-adjustment">Apply</button>
      </div>
    </form>
  </div></div>
</div>

<!-- Loyalty Redemption Modal -->
<div class="modal fade" id="loyaltyModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header bg-warning"><h5 class="modal-title">Redeem Loyalty Points</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="POST" action="<?= APP_URL ?>/index.php?url=billing/redeemPoints/<?= $reservation['id'] ?>">
      <div class="modal-body">
        <p>Available: <strong><?= number_format((int)$reservation['loyalty_points']) ?> pts</strong> (1pt = $0.01)</p>
        <label class="form-label fw-semibold">Points to Redeem</label>
        <input type="number" name="points" min="1" max="<?= (int)$reservation['loyalty_points'] ?>" class="form-control" required>
        <small class="text-muted">Max discount: $<?= number_format($reservation['loyalty_points'] * 0.01, 2) ?></small>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-warning" id="btn-redeem-submit">Redeem</button>
      </div>
    </form>
  </div></div>
</div>

<!-- Pay Modal -->
<div class="modal fade" id="payModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Record Payment — Grand Total: $<?= number_format($totals['grandTotal'],2) ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="POST" action="<?= APP_URL ?>/index.php?url=billing/payBill/<?= $reservation['id'] ?>" id="pay-form">
      <div class="modal-body" id="payment-rows">
        <div class="row g-2 mb-2 payment-row">
          <div class="col-5">
            <select name="method[]" class="form-select form-select-sm">
              <option value="card">Card</option>
              <option value="cash">Cash</option>
              <option value="bank_transfer">Bank Transfer</option>
            </select>
          </div>
          <div class="col-5"><input type="number" name="amount[]" step="0.01" min="0.01" class="form-control form-control-sm pay-amount" placeholder="Amount" value="<?= $totals['grandTotal'] ?>" required></div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addPaymentRow()">+ Add Split</button>
        <div>
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="btn-submit-payment" onclick="buildPaymentsJson(event)">Pay</button>
        </div>
      </div>
      <input type="hidden" name="payments" id="payments-json">
    </form>
  </div></div>
</div>

<script>
function openVoidModal(itemId, resId, desc) {
  document.getElementById('void_desc').textContent = desc;
  document.getElementById('void-form').action =
    '<?= APP_URL ?>/index.php?url=billing/voidBillingItem/' + itemId;
  new bootstrap.Modal(document.getElementById('voidModal')).show();
}
function addPaymentRow() {
  const row = document.querySelector('.payment-row').cloneNode(true);
  row.querySelectorAll('input').forEach(i => i.value = '');
  document.getElementById('payment-rows').appendChild(row);
}
function buildPaymentsJson(e) {
  e.preventDefault();
  const methods = [...document.querySelectorAll('[name="method[]"]')].map(s => s.value);
  const amounts = [...document.querySelectorAll('[name="amount[]"]')].map(i => parseFloat(i.value)||0);
  const payments = methods.map((m,i) => ({ method:m, amount:amounts[i] }));
  document.getElementById('payments-json').value = JSON.stringify(payments);
  document.getElementById('pay-form').submit();
}
</script>

<?php $content = ob_get_clean(); require VIEW_PATH . '/layouts/main.php'; ?>
