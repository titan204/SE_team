<?php $pageTitle = 'Group Invoice — ' . htmlspecialchars($group['group_name']); ?>
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
  .invoice-card {
    background:#fff;
    border:1px solid #e8d5c0;
    border-radius:12px;
    padding:2rem;
    box-shadow:0 4px 16px rgba(192,133,82,.12);
    margin-bottom:1.5rem;
    max-width:860px;
    margin-left:auto;
    margin-right:auto;
  }
  .invoice-header { border-bottom:2px solid var(--dark); padding-bottom:1rem; margin-bottom:1.5rem; }
  .invoice-title  { font-size:1.6rem; font-weight:800; color:var(--dark); }
  .invoice-meta   { font-size:.88rem; color:#888; }
  .table-card     { overflow:hidden; border-radius:8px; border:1px solid #e8d5c0; }
  .table thead    { background-color:var(--dark); color:#fff; }
  .table td, .table th { vertical-align:middle; }
  .totals-box {
    background:var(--dark);
    color:#fff;
    border-radius:10px;
    padding:1.2rem 1.5rem;
    margin-top:1rem;
  }
  .totals-box .row > div { padding:.3rem 0; border-bottom:1px solid rgba(255,255,255,.1); }
  .totals-box .row > div:last-child { border-bottom:none; }
  .grand-total { font-size:1.5rem; font-weight:700; color:#f5d08a; }
  .badge-group { background:#2196a5; color:#fff; }
  .badge-individual { background:#c08552; color:#fff; }
  .status-badge { font-size:.76rem; padding:.3em .7em; border-radius:20px; font-weight:600; }
  .btn-accent { background-color:var(--accent); color:#fff; border:none; }
  .btn-accent:hover { background-color:var(--accent2); color:#fff; }
  .flagged-alert { background:#fff3cd; border:1px solid #ffc107; border-radius:8px;
                   padding:.8rem 1.2rem; margin-bottom:1rem; font-size:.9rem; }

  /* PDF print optimisation */
  @media print {
    body { background:#fff !important; }
    .no-print { display:none !important; }
    .invoice-card { box-shadow:none; border:none; max-width:100%; }
  }
</style>

<div class="container-fluid py-3">

  <!-- Page Header -->
  <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2 no-print">
    <h2><i class="bi bi-file-earmark-text me-2"></i>Group Invoice</h2>
    <div class="d-flex gap-2">
      <a href="<?= APP_URL ?>/index.php?url=billing/group/<?= (int)$group['id'] ?>"
         class="btn btn-outline-secondary" style="border-radius:8px;" id="btn-back-to-group">
        <i class="bi bi-arrow-left me-1"></i> Back to Group
      </a>
      <a href="<?= APP_URL ?>/index.php?url=billing/group/<?= (int)$group['id'] ?>/invoice/pdf"
         class="btn btn-accent" style="border-radius:8px;" id="btn-download-invoice-pdf">
        <i class="bi bi-download me-1"></i> Download PDF
      </a>
    </div>
  </div>

  <!-- Flagged Members Warning -->
  <?php if (!empty($flaggedMembers)): ?>
  <div class="flagged-alert no-print" style="max-width:860px;margin:0 auto 1rem;">
    <i class="bi bi-exclamation-triangle-fill text-warning me-1"></i>
    <strong><?= count($flaggedMembers) ?> member(s) flagged</strong> — incomplete payment info.
    These members were excluded from the invoice total; a coordinator notification was queued.
    <ul class="mb-0 mt-1">
      <?php foreach ($flaggedMembers as $fm): ?>
        <li><?= htmlspecialchars($fm['guest_name']) ?> — Reservation #<?= $fm['reservation_id'] ?></li>
        <?php if (!empty($fm['flag_reason'])): ?>
          <li style="list-style:none;margin-left:1rem;color:#7a5a00;"><?= htmlspecialchars($fm['flag_reason']) ?></li>
        <?php endif; ?>
      <?php endforeach; ?>
    </ul>
  </div>
  <?php endif; ?>

  <!-- Invoice Card -->
  <div class="invoice-card" id="invoice-printable">

    <!-- Invoice Header -->
    <div class="invoice-header d-flex justify-content-between align-items-start flex-wrap gap-2">
      <div>
        <div class="invoice-title"><i class="bi bi-building me-2"></i>Grand Hotel</div>
        <div class="invoice-meta">Hotel Management System</div>
      </div>
      <div class="text-end">
        <div class="invoice-title" style="font-size:1.2rem;">CONSOLIDATED GROUP INVOICE</div>
        <div class="invoice-meta">Invoice #<?= $invoiceId ?> &nbsp;|&nbsp; Generated: <?= date('Y-m-d H:i') ?></div>
        <div class="invoice-meta">Status: <strong>Draft</strong></div>
      </div>
    </div>

    <!-- Group & Coordinator Details -->
    <div class="row g-3 mb-3">
      <div class="col-md-6">
        <div style="background:#fdf5eb;border-radius:8px;padding:.9rem 1.1rem;">
          <strong style="color:var(--dark);">Group Details</strong>
          <div class="mt-1"><strong><?= htmlspecialchars($group['group_name']) ?></strong></div>
          <div class="invoice-meta">Group ID: #<?= $group['id'] ?></div>
          <div class="invoice-meta">Created: <?= htmlspecialchars($group['created_at']) ?></div>
        </div>
      </div>
      <div class="col-md-6">
        <div style="background:#fdf5eb;border-radius:8px;padding:.9rem 1.1rem;">
          <strong style="color:var(--dark);">Group Coordinator</strong>
          <div class="mt-1"><strong><?= htmlspecialchars($group['coordinator_name']) ?></strong></div>
          <div class="invoice-meta"><?= htmlspecialchars($group['coordinator_email']) ?></div>
        </div>
      </div>
    </div>

    <!-- Member Breakdown Table -->
    <div class="table-card mb-3">
      <table class="table mb-0" id="invoice-members-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Guest</th>
            <th>Room</th>
            <th>Nights</th>
            <th>Dates</th>
            <th>Room Charges</th>
            <th>Services</th>
            <th>Minibar</th>
            <th>Other</th>
            <th>Tax</th>
            <th>Billing</th>
            <th class="text-end">Member Total</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($activeMembers as $i => $m): ?>
          <tr>
            <td><?= $i + 1 ?></td>
            <td>
              <strong><?= htmlspecialchars($m['guest_name']) ?></strong>
              <small class="d-block text-muted"><?= htmlspecialchars($m['guest_email']) ?></small>
            </td>
            <td><?= htmlspecialchars($m['room_number']) ?> <small class="text-muted">(<?= htmlspecialchars($m['room_type_name']) ?>)</small></td>
            <td><?= (int)$m['room_nights'] ?></td>
            <td style="white-space:nowrap;">
              <?= htmlspecialchars($m['check_in_date']) ?><br>
              <small class="text-muted">→ <?= htmlspecialchars($m['check_out_date']) ?></small>
            </td>
            <td>$<?= number_format((float)$m['room_charges'], 2) ?></td>
            <td>$<?= number_format((float)$m['service_charges'], 2) ?></td>
            <td>$<?= number_format((float)$m['minibar_charges'], 2) ?></td>
            <td>$<?= number_format((float)($m['other_charges'] ?? 0), 2) ?></td>
            <td>$<?= number_format((float)$m['tax_charges'], 2) ?></td>
            <td>
              <span class="status-badge badge-<?= htmlspecialchars($m['billing_type']) ?>">
                <?= ucfirst($m['billing_type']) ?>
              </span>
            </td>
            <td class="text-end"><strong>$<?= number_format((float)$m['member_total'], 2) ?></strong></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- Totals Box -->
    <div class="totals-box">
      <div class="row">
        <div class="col-6 col-md-8 text-end" style="padding-right:2rem;">Subtotal</div>
        <div class="col-6 col-md-4 text-end">$<?= number_format($subtotal, 2) ?></div>
      </div>
      <div class="row">
        <div class="col-6 col-md-8 text-end" style="padding-right:2rem;">Tax Amount</div>
        <div class="col-6 col-md-4 text-end">$<?= number_format($taxAmount, 2) ?></div>
      </div>
      <div class="row">
        <div class="col-6 col-md-8 text-end" style="padding-right:2rem;">
          Group Discount (<?= number_format($discountPercentage, 1) ?>%)
        </div>
        <div class="col-6 col-md-4 text-end text-warning">&minus;$<?= number_format($discountAmount, 2) ?></div>
      </div>
      <div class="row mt-1">
        <div class="col-6 col-md-8 text-end" style="padding-right:2rem;">
          <span class="grand-total">Grand Total</span>
        </div>
        <div class="col-6 col-md-4 text-end">
          <span class="grand-total">$<?= number_format($totalAfterDiscount, 2) ?></span>
        </div>
      </div>
    </div>

    <!-- Footer note -->
    <div class="mt-3" style="font-size:.8rem; color:#888;">
      This is a <strong>draft</strong> invoice. To finalize and queue coordinator/member notifications, go back to the group billing page and click <em>Finalize</em>.
      Members with <strong>Individual</strong> billing will receive their own separate invoice notification upon finalization.
    </div>

  </div><!-- /.invoice-card -->

  <!-- Action Buttons -->
  <div class="d-flex flex-wrap gap-3 mb-4 justify-content-center no-print">
    <a href="<?= APP_URL ?>/index.php?url=billing/group/<?= (int)$group['id'] ?>"
       class="btn btn-outline-secondary px-4" style="border-radius:8px;">
      <i class="bi bi-arrow-left me-1"></i> Back to Group
    </a>

    <form method="POST"
          action="<?= APP_URL ?>/index.php?url=billing/group/<?= (int)$group['id'] ?>/finalize"
          onsubmit="return confirm('Finalize group invoice #<?= $invoiceId ?> and queue coordinator/member notifications?')"
          style="display:inline;">
      <button type="submit" class="btn px-4" id="btn-finalize-from-invoice"
              style="background:var(--dark);color:#fff;border:none;border-radius:8px;">
        <i class="bi bi-send-check me-1"></i> Finalize
      </button>
    </form>

    <a href="<?= APP_URL ?>/index.php?url=billing/group/<?= (int)$group['id'] ?>/invoice/pdf"
       class="btn btn-accent px-4" style="border-radius:8px;" id="btn-pdf-download">
      <i class="bi bi-download me-1"></i> Download PDF
    </a>
  </div>

</div>

<?php $content = ob_get_clean(); ?>
<?php require VIEW_PATH . '/layouts/main.php'; ?>
