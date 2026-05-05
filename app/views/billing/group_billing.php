<?php $pageTitle = 'Group Billing — ' . htmlspecialchars($group['group_name']); ?>
<?php ob_start(); ?>

<style>
  :root {
    --bg:      #FFF8F0;
    --accent:  #C08552;
    --accent2: #8C5A3C;
    --dark:    #4B2E2B;
  }
  body { background-color: var(--bg) !important; }
  .page-header { border-bottom: 2px solid var(--accent); padding-bottom: .75rem; margin-bottom: 1.5rem; }
  .page-header h2 { color: var(--dark); font-weight: 700; }
  .info-card  { background:#fff; border:1px solid #e8d5c0; border-radius:10px; padding:1.2rem 1.5rem; box-shadow:0 2px 8px rgba(192,133,82,.08); margin-bottom:1.5rem; }
  .table-card { background:#fff; border:1px solid #e8d5c0; border-radius:10px; overflow:hidden; box-shadow:0 2px 8px rgba(192,133,82,.08); margin-bottom:1.5rem; }
  .table thead { background-color: var(--dark); color:#fff; }
  .table tbody tr:hover { background-color:#fff3e8; }
  .table td, .table th { vertical-align:middle; }
  .total-bar { background: var(--dark); color:#fff; border-radius:10px; padding:1rem 1.5rem; margin-bottom:1.5rem; }
  .total-bar .amount { font-size:1.8rem; font-weight:700; color:#f5d08a; }
  .badge-group      { background:#2196a5; color:#fff; }
  .badge-individual { background:#c08552; color:#fff; }
  .badge-cancelled  { background:#9e3030; color:#fff; }
  .badge-active     { background:#3a8a3a; color:#fff; }
  .status-badge { font-size:.76rem; padding:.3em .7em; border-radius:20px; font-weight:600; }
  .btn-accent { background-color:var(--accent); color:#fff; border:none; }
  .btn-accent:hover { background-color:var(--accent2); color:#fff; }
  .alert-flagged { background:#fff3cd; border:1px solid #ffc107; border-radius:8px; padding:.8rem 1.2rem; margin-bottom:1rem; }
  label { color:var(--dark); font-weight:600; font-size:.88rem; }
</style>

<div class="container-fluid py-3">

  <!-- Page Header -->
  <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h2><i class="bi bi-people me-2"></i>Group Billing: <?= htmlspecialchars($group['group_name']) ?></h2>
    <a href="<?= APP_URL ?>/index.php?url=billing" class="btn btn-outline-secondary" style="border-radius:8px;">
      <i class="bi bi-arrow-left me-1"></i> Back to Billing
    </a>
  </div>

  <!-- Flash message -->
  <?php if (!empty($_SESSION['flash_success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="bi bi-check-circle me-1"></i> <?= htmlspecialchars($_SESSION['flash_success']) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['flash_success']); ?>
  <?php endif; ?>

  <?php if (!empty($_SESSION['flash_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="bi bi-exclamation-triangle me-1"></i> <?= htmlspecialchars($_SESSION['flash_error']) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['flash_error']); ?>
  <?php endif; ?>

  <!-- Group Info Card -->
  <div class="info-card">
    <div class="row g-3">
      <div class="col-md-3">
        <label>Group Name</label>
        <div><?= htmlspecialchars($group['group_name']) ?></div>
      </div>
      <div class="col-md-3">
        <label>Coordinator</label>
        <div><?= htmlspecialchars($group['coordinator_name']) ?></div>
        <small class="text-muted"><?= htmlspecialchars($group['coordinator_email']) ?></small>
      </div>
      <div class="col-md-2">
        <label>Group Discount</label>
        <div><strong><?= number_format((float)$group['discount_percentage'], 1) ?>%</strong></div>
      </div>
      <div class="col-md-2">
        <label>Created</label>
        <div><?= htmlspecialchars($group['created_at']) ?></div>
      </div>
      <div class="col-md-2 d-flex align-items-end">
        <?php if ($existingInvoice): ?>
          <span class="badge bg-success">Invoice #<?= $existingInvoice['id'] ?> — <?= ucfirst($existingInvoice['status']) ?></span>
        <?php else: ?>
          <span class="badge bg-secondary">No invoice yet</span>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Active Members Table -->
  <div class="table-card">
    <div class="px-3 pt-3 pb-1" style="border-bottom:1px solid #e8d5c0;">
      <strong style="color:var(--dark);">Active Members (<?= count($activeMembers) ?>)</strong>
    </div>
    <?php if (empty($activeMembers)): ?>
      <div class="text-center py-4 text-muted"><i class="bi bi-person-slash d-block mb-2" style="font-size:2rem;opacity:.4;"></i>No active members.</div>
    <?php else: ?>
    <div class="table-responsive">
      <table class="table table-hover mb-0" id="active-members-table">
        <thead>
          <tr>
            <th>Guest</th>
            <th>Room</th>
            <th>Nights</th>
            <th>Check-In</th>
            <th>Check-Out</th>
            <th>Room Charges</th>
            <th>Services</th>
            <th>Minibar</th>
            <th>Other</th>
            <th>Taxes</th>
            <th>Member Total</th>
            <th>Billing Type</th>
            <th>Balance Due</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($activeMembers as $m): ?>
          <tr>
            <td>
              <strong><?= htmlspecialchars($m['guest_name']) ?></strong>
              <small class="text-muted d-block"><?= htmlspecialchars($m['guest_email']) ?></small>
            </td>
            <td>
              <span style="font-weight:600;color:var(--dark);"><?= htmlspecialchars($m['room_number']) ?></span>
              <small class="text-muted d-block"><?= htmlspecialchars($m['room_type_name']) ?></small>
            </td>
            <td><?= (int)$m['room_nights'] ?></td>
            <td><?= htmlspecialchars($m['check_in_date']) ?></td>
            <td><?= htmlspecialchars($m['check_out_date']) ?></td>
            <td>$<?= number_format((float)$m['room_charges'], 2) ?></td>
            <td>$<?= number_format((float)$m['service_charges'], 2) ?></td>
            <td>$<?= number_format((float)$m['minibar_charges'], 2) ?></td>
            <td>$<?= number_format((float)($m['other_charges'] ?? 0), 2) ?></td>
            <td>$<?= number_format((float)$m['tax_charges'], 2) ?></td>
            <td><strong>$<?= number_format((float)$m['member_total'], 2) ?></strong></td>
            <td>
              <span class="status-badge badge-<?= htmlspecialchars($m['billing_type']) ?>">
                <?= ucfirst($m['billing_type']) ?>
              </span>
            </td>
            <td>
              <?php $balance = (float)($m['balance_due'] ?? ($m['member_total'] - $m['amount_paid'])); ?>
              <span class="<?= $balance > 0 ? 'text-danger fw-bold' : 'text-success fw-bold' ?>">
                $<?= number_format($balance, 2) ?>
              </span>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>

  <!-- Cancelled Members -->
  <?php if (!empty($cancelledMembers)): ?>
  <div class="table-card">
    <div class="px-3 pt-3 pb-1" style="border-bottom:1px solid #e8d5c0;">
      <strong style="color:#9e3030;">Cancelled Members (<?= count($cancelledMembers) ?>) — Excluded from Invoice</strong>
    </div>
    <div class="table-responsive">
      <table class="table table-hover mb-0" id="cancelled-members-table">
        <thead>
          <tr>
            <th>Guest</th>
            <th>Room</th>
            <th>Check-In</th>
            <th>Check-Out</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($cancelledMembers as $cm): ?>
          <tr class="table-danger">
            <td><?= htmlspecialchars($cm['guest_name']) ?></td>
            <td><?= htmlspecialchars($cm['room_number']) ?></td>
            <td><?= htmlspecialchars($cm['check_in_date']) ?></td>
            <td><?= htmlspecialchars($cm['check_out_date']) ?></td>
            <td><span class="status-badge badge-cancelled">Cancelled</span></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php endif; ?>

  <!-- Consolidated Total Bar -->
  <div class="total-bar d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
      <div style="font-size:.9rem;opacity:.8;">Consolidated Subtotal (before discount)</div>
      <div class="amount">$<?= number_format($consolidatedSubtotal, 2) ?></div>
      <div style="font-size:.8rem;opacity:.7;">Tax: $<?= number_format($consolidatedTax, 2) ?></div>
    </div>
    <div style="font-size:.88rem;opacity:.7;">
      Group discount: <strong><?= number_format((float)$group['discount_percentage'], 1) ?>%</strong>
      = &minus;$<?= number_format($discountAmount, 2) ?>
    </div>
    <div>
      <div style="font-size:.9rem;opacity:.8;">Estimated Grand Total</div>
      <div class="amount">$<?= number_format($consolidatedTotal, 2) ?></div>
    </div>
  </div>

  <!-- Action Buttons -->
  <div class="d-flex flex-wrap gap-3 mb-4">
    <!-- Generate / View Invoice -->
    <a href="<?= APP_URL ?>/index.php?url=billing/group/<?= (int)$group['id'] ?>/invoice"
       class="btn btn-accent px-4" style="border-radius:8px;" id="btn-generate-invoice">
      <i class="bi bi-file-earmark-text me-1"></i>
      <?= $existingInvoice ? 'Regenerate Invoice' : 'Generate Invoice' ?>
    </a>

    <!-- Split Billing → UC13 -->
    <a href="<?= APP_URL ?>/index.php?url=billing/splitBill/<?= (int)$group['id'] ?>"
       class="btn btn-outline-secondary px-4" style="border-radius:8px;" id="btn-split-billing">
      <i class="bi bi-scissors me-1"></i> Split Billing
    </a>

    <!-- Finalize (POST) — only show if a draft invoice exists -->
    <?php if ($existingInvoice && $existingInvoice['status'] === 'draft'): ?>
    <form method="POST"
          action="<?= APP_URL ?>/index.php?url=billing/group/<?= (int)$group['id'] ?>/finalize"
          onsubmit="return confirm('Finalize group invoice and queue coordinator/member notifications?')"
          style="display:inline;">
      <button type="submit" class="btn px-4" id="btn-finalize-invoice"
              style="background:var(--dark);color:#fff;border:none;border-radius:8px;">
        <i class="bi bi-send-check me-1"></i> Finalize
      </button>
    </form>
    <?php endif; ?>

    <form method="POST"
          action="<?= APP_URL ?>/index.php?url=billing/group/<?= (int)$group['id'] ?>/cancel"
          onsubmit="return confirm('Cancel all eligible reservations in this group and queue cancellation notifications?')"
          style="display:inline;">
      <button type="submit" class="btn btn-outline-danger px-4" id="btn-cancel-group" style="border-radius:8px;">
        <i class="bi bi-x-circle me-1"></i> Cancel Group
      </button>
    </form>
  </div>

</div>

<?php $content = ob_get_clean(); ?>
<?php require VIEW_PATH . '/layouts/main.php'; ?>
