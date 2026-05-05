<?php $pageTitle = 'Split Group Billing — ' . htmlspecialchars($group['group_name']); ob_start(); ?>

<style>
  .split-card { background:#fff; border:1px solid #e0d0c0; border-radius:10px; padding:1.5rem; margin-bottom:1.2rem; box-shadow:0 2px 8px rgba(0,0,0,.06); }
  .table thead { background:#4B2E2B; color:#fff; }
  .table tbody tr:hover { background:#fff8f0; }
  .col-check { width:44px; text-align:center; }
  .dispute-btn { font-size:.75rem; }
  .blocked-banner { background:#fff3cd; border:1px solid #ffc107; border-radius:8px; padding:.7rem 1rem; margin-bottom:1rem; }
  .dispute-banner { background:#f8d7da; border:1px solid #f5c2c7; border-radius:8px; padding:.7rem 1rem; margin-bottom:1rem; }
</style>

<div class="container-fluid py-3">

  <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h4 class="mb-0"><i class="bi bi-scissors me-2"></i>Split Billing — <?= htmlspecialchars($group['group_name']) ?></h4>
    <a href="<?= APP_URL ?>/index.php?url=billing/group/<?= (int)$group['id'] ?>"
       class="btn btn-sm btn-outline-secondary">
      <i class="bi bi-arrow-left me-1"></i>Back to Group
    </a>
  </div>

  <?php if (!empty($_SESSION['flash_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show py-2">
      <?= htmlspecialchars($_SESSION['flash_error']) ?>
      <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['flash_error']); ?>
  <?php endif; ?>

  <?php if (!empty($disputes)): ?>
    <div class="dispute-banner">
      <i class="bi bi-exclamation-circle-fill text-danger me-1"></i>
      <strong>Split paused:</strong> <?= count($disputes) ?> open dispute(s) must be resolved by the coordinator before splitting can proceed.
      <ul class="mb-0 mt-1 small">
        <?php foreach ($disputes as $d): ?>
          <li>Reservation #<?= $d['reservation_id'] ?> — <?= htmlspecialchars($d['description']) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <?php if ($invoiceBlocked): ?>
    <div class="blocked-banner">
      <i class="bi bi-lock-fill text-warning me-1"></i>
      <?= $groupInvoice
          ? '<strong>Invoice already finalized.</strong> Generate a new draft invoice first.'
          : '<strong>No invoice exists yet.</strong> Generate a group invoice before splitting.' ?>
    </div>
  <?php endif; ?>

  <!-- Info row -->
  <div class="split-card d-flex flex-wrap gap-4">
    <div><small class="text-muted d-block">Group</small><strong><?= htmlspecialchars($group['group_name']) ?></strong></div>
    <div><small class="text-muted d-block">Coordinator</small><?= htmlspecialchars($group['coordinator_name']) ?></div>
    <div><small class="text-muted d-block">Group Discount</small><?= number_format((float)$group['discount_percentage'], 1) ?>%</div>
    <div><small class="text-muted d-block">Invoice Status</small>
      <?php if ($groupInvoice): ?>
        <span class="badge bg-<?= $groupInvoice['status'] === 'draft' ? 'secondary' : ($groupInvoice['status'] === 'finalized' ? 'success' : 'danger') ?>">
          <?= ucfirst($groupInvoice['status']) ?>
        </span>
      <?php else: ?><span class="badge bg-warning text-dark">None</span><?php endif; ?>
    </div>
  </div>

  <!-- Split form -->
  <form method="POST" action="<?= APP_URL ?>/index.php?url=billing/splitProcess/<?= (int)$group['id'] ?>"
        id="split-form"
        onsubmit="return confirmSplit()">

    <div class="split-card">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <strong>Select Members for Individual Billing</strong>
        <div class="form-check form-switch mb-0">
          <input class="form-check-input" type="checkbox" id="selectAll" onchange="toggleAll(this)">
          <label class="form-check-label" for="selectAll">Select All</label>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="split-members-table">
          <thead>
            <tr>
              <th class="col-check"><i class="bi bi-check2-square"></i></th>
              <th>Guest</th>
              <th>Room</th>
              <th>Room Charges</th>
              <th>Services</th>
              <th>Minibar</th>
              <th>Other</th>
              <th>Tax (prop.)</th>
              <th>Member Total</th>
              <th>Dispute</th>
            </tr>
          </thead>
          <tbody>
          <?php
            // Pre-compute group subtotal for proportional tax display
            $grpSubtotal = 0;
            $grpTax = 0;
            foreach ($members as $m) {
                $grpSubtotal += (float)($m['member_subtotal'] ?? 0);
                $grpTax      += (float)$m['tax_charges'];
            }
          ?>
          <?php foreach ($members as $m):
            $memberSubtotal = (float)($m['member_subtotal'] ?? 0);
            $proportionalTax = $grpSubtotal > 0
                ? round(($memberSubtotal / $grpSubtotal) * $grpTax, 2)
                : (float)$m['tax_charges'];
            $memberTotal = round($memberSubtotal + $proportionalTax, 2);
          ?>
            <tr>
              <td class="col-check">
                <input type="checkbox" name="member_ids[]"
                       value="<?= (int)$m['reservation_id'] ?>"
                       class="form-check-input member-check"
                       id="mem_<?= $m['reservation_id'] ?>"
                       <?= $invoiceBlocked || !empty($disputes) ? 'disabled' : '' ?>>
              </td>
              <td>
                <label for="mem_<?= $m['reservation_id'] ?>">
                  <strong><?= htmlspecialchars($m['guest_name']) ?></strong>
                  <?php if (empty($m['guest_email'])): ?>
                    <span class="badge bg-warning text-dark ms-1" title="No email — manual delivery">No email</span>
                  <?php endif; ?>
                </label>
              </td>
              <td><?= htmlspecialchars($m['room_number']) ?></td>
              <td>$<?= number_format((float)$m['room_charges'], 2) ?></td>
              <td>$<?= number_format((float)$m['service_charges'], 2) ?></td>
              <td>$<?= number_format((float)$m['minibar_charges'], 2) ?></td>
              <td>$<?= number_format((float)($m['other_charges'] ?? 0), 2) ?></td>
              <td>$<?= number_format($proportionalTax, 2) ?></td>
              <td><strong>$<?= number_format($memberTotal, 2) ?></strong></td>
              <td>
                <?php if (!$invoiceBlocked): ?>
                <button type="button" class="btn btn-outline-danger btn-sm dispute-btn"
                        onclick="openDisputeModal(<?= (int)$m['reservation_id'] ?>, '<?= addslashes($m['guest_name']) ?>')"
                        id="btn-dispute-<?= $m['reservation_id'] ?>">
                  <i class="bi bi-flag"></i> Dispute
                </button>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div class="mt-3 d-flex gap-2">
        <button type="submit" class="btn btn-primary px-4"
                id="btn-execute-split"
                <?= $invoiceBlocked || !empty($disputes) ? 'disabled' : '' ?>>
          <i class="bi bi-scissors me-1"></i>Execute Split
        </button>
        <a href="<?= APP_URL ?>/index.php?url=billing/group/<?= (int)$group['id'] ?>"
           class="btn btn-outline-secondary">Cancel</a>
      </div>
    </div>
  </form>
</div>

<!-- Dispute Modal -->
<div class="modal fade" id="disputeModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content border-danger">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title"><i class="bi bi-flag me-2"></i>Raise Dispute</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="<?= APP_URL ?>/index.php?url=billing/raiseDispute/<?= (int)$group['id'] ?>"
            id="dispute-form">
        <input type="hidden" name="reservation_id" id="dispute_res_id">
        <div class="modal-body">
          <p>Raise a dispute for: <strong id="dispute_guest_name"></strong></p>
          <div class="mb-3">
            <label for="dispute_description" class="form-label fw-semibold">
              Description <span class="text-danger">*</span>
            </label>
            <textarea id="dispute_description" name="description" class="form-control" rows="3"
                      placeholder="Describe the billing discrepancy..." required></textarea>
          </div>
          <div class="alert alert-warning py-2 small mb-0">
            Raising a dispute will <strong>pause the split</strong> until the coordinator resolves it.
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger" id="btn-submit-dispute">Raise Dispute</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function toggleAll(cb) {
  document.querySelectorAll('.member-check:not([disabled])').forEach(c => c.checked = cb.checked);
}
function confirmSplit() {
  const checked = document.querySelectorAll('.member-check:checked').length;
  if (!checked) { alert('Select at least one member to split.'); return false; }
  return confirm(`Execute split for ${checked} member(s)? Individual invoices will be generated and emailed.`);
}
function openDisputeModal(resId, guestName) {
  document.getElementById('dispute_res_id').value    = resId;
  document.getElementById('dispute_guest_name').textContent = guestName;
  document.getElementById('dispute_description').value = '';
  new bootstrap.Modal(document.getElementById('disputeModal')).show();
}
</script>

<?php $content = ob_get_clean(); require VIEW_PATH . '/layouts/main.php'; ?>
