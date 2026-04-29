<?php $pageTitle = 'Reservation Details'; ?>
<?php ob_start(); ?>

<?php
$r       = $reservation;
$rid     = $r['id'];
$status  = $r['status'];
$nights  = (new DateTime($r['check_in_date']))->diff(new DateTime($r['check_out_date']))->days;
$statusColors = [
    'pending'     => ['bg'=>'#f0a500','label'=>'Pending'],
    'confirmed'   => ['bg'=>'#2196a5','label'=>'Confirmed'],
    'checked_in'  => ['bg'=>'#3a8a3a','label'=>'Checked In'],
    'checked_out' => ['bg'=>'#8C5A3C','label'=>'Checked Out'],
    'cancelled'   => ['bg'=>'#9e3030','label'=>'Cancelled'],
    'no_show'     => ['bg'=>'#555',   'label'=>'No Show'],
];
$sc  = $statusColors[$status] ?? ['bg'=>'#888','label'=>ucfirst($status)];
?>

<style>
  :root { --bg:#FFF8F0; --accent:#C08552; --accent2:#8C5A3C; --dark:#4B2E2B; }
  body { background-color: var(--bg) !important; }
  .page-header { border-bottom: 2px solid var(--accent); padding-bottom:.75rem; margin-bottom:1.5rem; }
  .page-header h2 { color:var(--dark); font-weight:700; }
  .info-card { background:#fff; border:1px solid #e8d5c0; border-radius:12px; padding:1.5rem; margin-bottom:1.25rem; box-shadow:0 2px 10px rgba(192,133,82,.08); }
  .card-title { color:var(--dark); font-weight:700; font-size:.95rem; border-bottom:1px solid #f0e0cc; padding-bottom:.5rem; margin-bottom:1rem; }
  .detail-row { display:flex; justify-content:space-between; padding:.4rem 0; border-bottom:1px dashed #f0e0cc; font-size:.9rem; }
  .detail-row:last-child { border-bottom:none; }
  .detail-label { color:#888; font-weight:600; }
  .detail-value { color:var(--dark); font-weight:500; text-align:right; }
  .btn-accent { background-color:var(--accent); color:#fff; border:none; border-radius:7px; }
  .btn-accent:hover { background-color:var(--accent2); color:#fff; }
  .status-pill { display:inline-block; padding:.35em 1em; border-radius:30px; font-size:.85rem; font-weight:700; color:#fff; }
  .action-card { background:#fff; border:1px solid #e8d5c0; border-radius:12px; padding:1.5rem; box-shadow:0 2px 10px rgba(192,133,82,.08); }
  .action-card .section-label { color:var(--dark); font-weight:700; font-size:.9rem; margin-bottom:.75rem; }
  .alert-early { background:linear-gradient(135deg,#e8f5e8,#c8e8c8); border:1px solid #4a8a4a; border-radius:8px; padding:.75rem 1rem; }
  .upgrade-card { background:linear-gradient(135deg,#fff8e0,#ffe8b0); border:1px solid #c09800; border-radius:8px; padding:.75rem 1rem; }
  .folio-total { font-size:1.4rem; font-weight:700; color:var(--dark); }
  .group-badge { background:var(--accent); color:#fff; border-radius:20px; padding:.2em .8em; font-size:.75rem; }
</style>

<div class="container py-3" style="max-width:1000px;">

  <!-- Header -->
  <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div class="d-flex align-items-center gap-3">
      <h2 class="mb-0"><i class="bi bi-calendar-event me-2"></i>Reservation #<?= $rid ?></h2>
      <span class="status-pill" style="background:<?= $sc['bg'] ?>;"><?= $sc['label'] ?></span>
      <?php if (!empty($r['is_vip'])): ?>
        <span class="status-pill" style="background:#c09800;">★ VIP</span>
      <?php endif; ?>
      <?php if (!empty($r['is_group'])): ?>
        <span class="group-badge">Group #<?= $r['group_id'] ?></span>
      <?php endif; ?>
    </div>
    <?php if (!empty($isGuestUser)): ?>
      <a href="<?= APP_URL ?>/index.php?url=rooms/guest"
         class="btn btn-outline-secondary" style="border-radius:8px;">
        <i class="bi bi-arrow-left me-1"></i> Rooms
      </a>
    <?php else: ?>
      <a href="<?= APP_URL ?>/index.php?url=reservations"
         class="btn btn-outline-secondary" style="border-radius:8px;">
        <i class="bi bi-arrow-left me-1"></i> Back to Reservations
      </a>
    <?php endif; ?>
  </div>

  <!-- Early Check-In Alert -->
  <?php if ($earlyCheckIn && in_array($status, ['pending','confirmed'])): ?>
  <div class="alert-early mb-3 d-flex align-items-center gap-2">
    <i class="bi bi-check-circle-fill text-success fs-5"></i>
    <div>
      <strong style="color:#2a6a2a;">Early Check-In Available</strong>
      <span class="text-muted ms-2">Room is clean and ready for early arrival.</span>
    </div>
    <a href="<?= APP_URL ?>/index.php?url=reservations/earlycheckin/<?= $rid ?>"
       class="btn btn-sm ms-auto" style="background:#3a8a3a;color:#fff;border-radius:6px;"
       onclick="return confirm('Process early check-in for reservation #<?= $rid ?>?')">
      <i class="bi bi-box-arrow-in-right me-1"></i> Early Check-In
    </a>
  </div>
  <?php endif; ?>

  <!-- Upgrade Suggestion -->
  <?php if (!empty($upgradeRoom)): ?>
  <div class="upgrade-card mb-3 d-flex align-items-center gap-2">
    <i class="bi bi-stars fs-5" style="color:#c09800;"></i>
    <div>
      <strong style="color:#7a6000;">Room Upgrade Available</strong>
      <span class="text-muted ms-2">
        Room <?= htmlspecialchars($upgradeRoom['room_number']) ?>
        (<?= htmlspecialchars($upgradeRoom['type_name'] ?? '') ?>)
        at $<?= number_format($upgradeRoom['base_price'] ?? 0, 2) ?>/night
      </span>
    </div>
  </div>
  <?php endif; ?>

  <div class="row g-3">

    <!-- Left column: reservation + guest + room info -->
    <div class="col-lg-8">

      <!-- Reservation Info -->
      <div class="info-card">
        <div class="card-title"><i class="bi bi-calendar-range me-2"></i>Reservation Details</div>
        <div class="detail-row">
          <span class="detail-label">Check-In</span>
          <span class="detail-value"><?= htmlspecialchars($r['check_in_date']) ?></span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Check-Out</span>
          <span class="detail-value"><?= htmlspecialchars($r['check_out_date']) ?></span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Duration</span>
          <span class="detail-value"><?= $nights ?> night<?= $nights != 1 ? 's' : '' ?></span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Adults / Children</span>
          <span class="detail-value"><?= $r['adults'] ?> / <?= $r['children'] ?></span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Total Price</span>
          <span class="detail-value"><strong>$<?= number_format($r['total_price'],2) ?></strong></span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Deposit</span>
          <span class="detail-value">
            $<?= number_format($r['deposit_amount'],2) ?>
            <?php if ($r['deposit_paid']): ?>
              <span class="badge ms-1" style="background:#3a8a3a;">Paid</span>
            <?php else: ?>
              <span class="badge ms-1" style="background:#9e3030;">Unpaid</span>
            <?php endif; ?>
          </span>
        </div>
        <?php if (!empty($r['actual_check_in'])): ?>
        <div class="detail-row">
          <span class="detail-label">Actual Check-In</span>
          <span class="detail-value"><?= htmlspecialchars($r['actual_check_in']) ?></span>
        </div>
        <?php endif; ?>
        <?php if (!empty($r['actual_check_out'])): ?>
        <div class="detail-row">
          <span class="detail-label">Actual Check-Out</span>
          <span class="detail-value"><?= htmlspecialchars($r['actual_check_out']) ?></span>
        </div>
        <?php endif; ?>
        <?php if (!empty($r['special_requests'])): ?>
        <div class="detail-row flex-column">
          <span class="detail-label mb-1">Special Requests</span>
          <span style="color:var(--dark);font-style:italic;"><?= htmlspecialchars($r['special_requests']) ?></span>
        </div>
        <?php endif; ?>
        <div class="detail-row">
          <span class="detail-label">Created At</span>
          <span class="detail-value text-muted"><?= htmlspecialchars($r['created_at']) ?></span>
        </div>
      </div>

      <!-- Guest Info -->
      <div class="info-card">
        <div class="card-title"><i class="bi bi-person me-2"></i>Guest Information</div>
        <div class="detail-row">
          <span class="detail-label">Name</span>
          <span class="detail-value">
            <?= htmlspecialchars($r['guest_name']) ?>
            <?php if (!empty($r['is_vip'])): ?>
              <span class="badge ms-1" style="background:#c09800;">VIP</span>
            <?php endif; ?>
          </span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Email</span>
          <span class="detail-value"><?= htmlspecialchars($r['guest_email'] ?? '—') ?></span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Phone</span>
          <span class="detail-value"><?= htmlspecialchars($r['guest_phone'] ?? '—') ?></span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Loyalty Tier</span>
          <span class="detail-value" style="text-transform:capitalize;">
            <?= htmlspecialchars($r['loyalty_tier'] ?? '—') ?>
          </span>
        </div>
        <div class="mt-2">
          <a href="<?= APP_URL ?>/index.php?url=guests/show/<?= $r['guest_id'] ?>"
             class="btn btn-sm" style="background:#e8d5c0;color:var(--dark);border-radius:6px;">
            <i class="bi bi-person-lines-fill me-1"></i> View Guest Profile
          </a>
        </div>
      </div>

      <!-- Room Info -->
      <div class="info-card">
        <div class="card-title"><i class="bi bi-door-closed me-2"></i>Room Information</div>
        <div class="detail-row">
          <span class="detail-label">Room Number</span>
          <span class="detail-value"><strong><?= htmlspecialchars($r['room_number']) ?></strong></span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Type</span>
          <span class="detail-value"><?= htmlspecialchars($r['room_type_name']) ?></span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Floor</span>
          <span class="detail-value"><?= htmlspecialchars($r['floor']) ?></span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Base Price</span>
          <span class="detail-value">$<?= number_format($r['base_price'], 2) ?>/night</span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Room Status</span>
          <span class="detail-value" style="text-transform:capitalize;">
            <?= htmlspecialchars($r['room_status'] ?? '—') ?>
          </span>
        </div>
        <div class="mt-2">
          <a href="<?= APP_URL ?>/index.php?url=rooms/show/<?= $r['room_id'] ?>"
             class="btn btn-sm" style="background:#e8d5c0;color:var(--dark);border-radius:6px;">
            <i class="bi bi-building me-1"></i> View Room Details
          </a>
        </div>
      </div>

      <!-- Group Reservations -->
      <?php if (!empty($groupReservations) && count($groupReservations) > 1): ?>
      <div class="info-card">
        <div class="card-title"><i class="bi bi-people me-2"></i>Group Reservations (Group #<?= $r['group_id'] ?>)</div>
        <div class="table-responsive">
          <table class="table table-sm mb-0">
            <thead style="background:var(--dark);color:#fff;">
              <tr><th>#</th><th>Guest</th><th>Room</th><th>Status</th><th></th></tr>
            </thead>
            <tbody>
            <?php foreach ($groupReservations as $gr): ?>
              <tr <?= $gr['id'] == $rid ? 'style="background:#fff8f0;"' : '' ?>>
                <td>#<?= $gr['id'] ?></td>
                <td><?= htmlspecialchars($gr['guest_name']) ?></td>
                <td><?= htmlspecialchars($gr['room_number']) ?></td>
                <td><?= ucwords(str_replace('_',' ',$gr['status'])) ?></td>
                <td>
                  <?php if ($gr['id'] != $rid): ?>
                  <a href="<?= APP_URL ?>/index.php?url=reservations/show/<?= $gr['id'] ?>"
                     class="btn btn-sm" style="background:#e8d5c0;color:var(--dark);font-size:.75rem;border-radius:5px;">
                    View
                  </a>
                  <?php else: ?>
                  <span class="text-muted" style="font-size:.8rem;">This</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
      <?php endif; ?>
    </div>

    <!-- Right column: actions + folio -->
    <div class="col-lg-4">

      <!-- Actions Card — same workflow for ALL roles -->
      <div class="action-card mb-3">
        <div class="section-label"><i class="bi bi-lightning me-2"></i>Actions</div>

        <?php if ($status === 'pending'): ?>
        <a href="<?= APP_URL ?>/index.php?url=reservations/confirm/<?= $rid ?>"
           class="btn btn-accent w-100 mb-2"
           onclick="return confirm('Confirm this reservation?')">
          <i class="bi bi-check-circle me-1"></i> Confirm Reservation
        </a>
        <?php endif; ?>

        <?php if (in_array($status, ['pending','confirmed'])): ?>
        <a href="<?= APP_URL ?>/index.php?url=reservations/checkin/<?= $rid ?>"
           class="btn w-100 mb-2" style="background:#3a8a3a;color:#fff;border-radius:7px;"
           onclick="return confirm('Proceed with check-in?')">
          <i class="bi bi-box-arrow-in-right me-1"></i> Check In
        </a>
        <?php endif; ?>

        <?php if ($status === 'checked_in'): ?>
        <a href="<?= APP_URL ?>/index.php?url=reservations/checkout/<?= $rid ?>"
           class="btn btn-accent w-100 mb-2"
           onclick="return confirm('Proceed with check-out?')">
          <i class="bi bi-box-arrow-right me-1"></i> Check Out
        </a>
        <?php endif; ?>

        <?php if (in_array($status, ['pending','confirmed'])): ?>
        <a href="<?= APP_URL ?>/index.php?url=reservations/noshow/<?= $rid ?>"
           class="btn w-100 mb-2" style="background:#777;color:#fff;border-radius:7px;"
           onclick="return confirm('Mark as No-Show?')">
          <i class="bi bi-person-slash me-1"></i> Mark No-Show
        </a>
        <?php endif; ?>

        <?php if (!in_array($status, ['checked_in','checked_out','cancelled','no_show'])): ?>
        <a href="<?= APP_URL ?>/index.php?url=reservations/delete/<?= $rid ?>"
           class="btn w-100 mb-2" style="background:#9e3030;color:#fff;border-radius:7px;"
           onclick="return confirm('Cancel this reservation?')">
          <i class="bi bi-x-circle me-1"></i> Cancel Reservation
        </a>
        <?php endif; ?>

        <?php if (in_array($status, ['pending','confirmed'])): ?>
        <a href="<?= APP_URL ?>/index.php?url=reservations/edit/<?= $rid ?>"
           class="btn w-100 mb-2" style="background:#e8d5c0;color:var(--dark);border-radius:7px;">
          <i class="bi bi-pencil me-1"></i> Edit Reservation
        </a>
        <?php endif; ?>

      </div>


      <!-- Folio Summary -->
      <div class="info-card">
        <div class="card-title"><i class="bi bi-receipt me-2"></i>Folio Summary</div>
        <?php if (!empty($folio)): ?>
        <div class="detail-row">
          <span class="detail-label">Total Charges</span>
          <span class="detail-value">$<?= number_format($folio['total_amount'],2) ?></span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Amount Paid</span>
          <span class="detail-value" style="color:#3a8a3a;">$<?= number_format($folio['amount_paid'],2) ?></span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Balance Due</span>
          <span class="detail-value folio-total"
                style="color:<?= $folio['balance_due'] > 0 ? '#9e3030' : '#3a8a3a' ?>;">
            $<?= number_format($folio['balance_due'] ?? ($folio['total_amount'] - $folio['amount_paid']),2) ?>
          </span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Status</span>
          <span class="detail-value" style="text-transform:capitalize;">
            <?= htmlspecialchars($folio['status']) ?>
          </span>
        </div>
        <?php else: ?>
        <p class="text-muted mb-0" style="font-size:.88rem;">No folio created yet.</p>
        <?php endif; ?>
      </div>

    </div>
  </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php require VIEW_PATH . '/layouts/main.php'; ?>
