<?php $pageTitle = 'Billing Overview'; ?>
<?php ob_start(); ?>

<style>
  :root { --bg:#FFF8F0; --accent:#C08552; --accent2:#8C5A3C; --dark:#4B2E2B; }
  .page-header { border-bottom: 2px solid var(--accent); padding-bottom: .75rem; margin-bottom: 1.5rem; }
  .page-header h2 { color: var(--dark); font-weight: 700; }
  .stat-card { background:#fff; border:1px solid #e8d5c0; border-radius:12px; padding:1.4rem 1.2rem; box-shadow:0 2px 8px rgba(192,133,82,.09); text-align:center; }
  .stat-card .stat-value { font-size:1.9rem; font-weight:700; }
  .stat-card .stat-label { font-size:.82rem; color:#888; margin-top:.2rem; }
  .section-card { background:#fff; border:1px solid #e8d5c0; border-radius:10px; margin-bottom:1.5rem; box-shadow:0 2px 8px rgba(192,133,82,.08); overflow:hidden; }
  .section-card .section-header { background:var(--dark); color:#fff; padding:.75rem 1.2rem; font-weight:600; font-size:.95rem; display:flex; justify-content:space-between; align-items:center; }
  .section-card .section-body { padding:1.2rem; }
  .table th { background-color: var(--dark); color:#fff; font-weight:600; font-size:.85rem; }
  .table tbody tr:hover { background-color:#fff3e8; }
  .table td, .table th { vertical-align:middle; }
  .badge-open     { background:#f0a500; color:#fff; }
  .badge-settled  { background:#3a8a3a; color:#fff; }
  .badge-refunded { background:#2196a5; color:#fff; }
  .folio-badge { font-size:.75rem; padding:.25em .65em; border-radius:20px; font-weight:600; }
  .method-bar-bg { background:#f0e4d0; border-radius:8px; height:10px; overflow:hidden; }
  .method-bar-fill { height:10px; border-radius:8px; background:var(--accent); transition:width .6s; }
  .filter-card { background:#fff; border:1px solid #e8d5c0; border-radius:10px; padding:1rem 1.2rem; margin-bottom:1.2rem; box-shadow:0 1px 4px rgba(192,133,82,.07); }
  label { color:var(--dark); font-weight:600; font-size:.88rem; }
  .form-control:focus, .form-select:focus { border-color:var(--accent); box-shadow:0 0 0 .2rem rgba(192,133,82,.25); }
  .action-btn { font-size:.76rem; padding:.2em .55em; border-radius:6px; margin:1px; }
  .empty-state { text-align:center; padding:2.5rem 1rem; color:#a08060; }
  .empty-state i { font-size:2.5rem; opacity:.4; }
</style>

<div class="container-fluid py-3">

  <!-- Header -->
  <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h2><i class="bi bi-receipt me-2"></i>Billing Overview</h2>
    <span class="text-muted" style="font-size:.85rem;">
      <i class="bi bi-clock me-1"></i><?= date('l, d F Y — H:i') ?>
    </span>
  </div>

  <!-- Flash -->
  <?php if (!empty($_SESSION['billing_success'])): ?>
    <div class="alert alert-success alert-dismissible fade show py-2"><i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($_SESSION['billing_success']) ?><button class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php unset($_SESSION['billing_success']); ?>
  <?php endif; ?>

  <!-- ── KPI Row ── -->
  <div class="row g-3 mb-4">

    <!-- Revenue Today -->
    <div class="col-6 col-md-3">
      <div class="stat-card">
        <i class="bi bi-currency-dollar" style="font-size:1.6rem;color:var(--accent);"></i>
        <div class="stat-value" style="color:var(--dark);">$<?= number_format($revenueToday, 2) ?></div>
        <div class="stat-label">Revenue Today</div>
        <div style="font-size:.75rem;color:#aaa;margin-top:.3rem;">Actual payments · <?= date('d M Y') ?></div>
      </div>
    </div>

    <!-- Revenue This Month -->
    <div class="col-6 col-md-3">
      <div class="stat-card">
        <i class="bi bi-graph-up" style="font-size:1.6rem;color:var(--accent2);"></i>
        <div class="stat-value" style="color:var(--dark);">$<?= number_format($revenueMonth, 2) ?></div>
        <div class="stat-label">Revenue This Month</div>
        <div style="font-size:.75rem;color:#aaa;margin-top:.3rem;"><?= date('F Y') ?></div>
      </div>
    </div>

    <!-- Outstanding Balance -->
    <div class="col-6 col-md-3">
      <div class="stat-card">
        <i class="bi bi-exclamation-circle" style="font-size:1.6rem;color:#e07020;"></i>
        <div class="stat-value" style="color:#e07020;">$<?= number_format($folioStats['total_outstanding'], 2) ?></div>
        <div class="stat-label">Outstanding Balance</div>
        <div style="font-size:.75rem;color:#aaa;margin-top:.3rem;"><?= $folioStats['open'] ?> open folio(s)</div>
      </div>
    </div>

    <!-- Open Disputes -->
    <div class="col-6 col-md-3">
      <div class="stat-card">
        <i class="bi bi-flag" style="font-size:1.6rem;color:<?= $openDisputes > 0 ? '#c04040' : '#3a8a3a' ?>;"></i>
        <div class="stat-value" style="color:<?= $openDisputes > 0 ? '#c04040' : '#3a8a3a' ?>;"><?= $openDisputes ?></div>
        <div class="stat-label">Open Disputes</div>
        <div style="font-size:.75rem;color:#aaa;margin-top:.3rem;">Require resolution</div>
      </div>
    </div>

  </div>

  <div class="row g-3 mb-4">

    <!-- Folio Status Summary -->
    <div class="col-md-5">
      <div class="section-card h-100">
        <div class="section-header"><span><i class="bi bi-folder2-open me-2"></i>Folio Status</span><span><?= $folioStats['total'] ?> total</span></div>
        <div class="section-body">
          <?php
          $statusItems = [
            ['key'=>'open',     'label'=>'Open',     'color'=>'#f0a500', 'icon'=>'bi-folder2-open'],
            ['key'=>'settled',  'label'=>'Settled',  'color'=>'#3a8a3a', 'icon'=>'bi-check-circle'],
            ['key'=>'refunded', 'label'=>'Refunded', 'color'=>'#2196a5', 'icon'=>'bi-arrow-counterclockwise'],
          ];
          foreach ($statusItems as $si):
            $cnt = (int)($folioStats[$si['key']] ?? 0);
            $pct = $folioStats['total'] > 0 ? round($cnt / $folioStats['total'] * 100) : 0;
          ?>
          <div class="d-flex justify-content-between align-items-center mb-2">
            <span><i class="bi <?= $si['icon'] ?> me-2" style="color:<?= $si['color'] ?>;"></i><?= $si['label'] ?></span>
            <span style="font-weight:700;color:<?= $si['color'] ?>;"><?= $cnt ?></span>
          </div>
          <div class="method-bar-bg mb-3">
            <div class="method-bar-fill" style="width:<?= $pct ?>%;background:<?= $si['color'] ?>;"></div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <!-- Payment Method Breakdown -->
    <div class="col-md-7">
      <div class="section-card h-100">
        <div class="section-header"><span><i class="bi bi-credit-card me-2"></i>Payment Methods</span><span>All time</span></div>
        <div class="section-body">
          <?php
          $grandTotal = array_sum(array_column($paymentMethods, 'total')) ?: 1;
          $methodIcons = ['cash'=>'bi-cash-coin','credit_card'=>'bi-credit-card-2-front','debit_card'=>'bi-credit-card','bank_transfer'=>'bi-bank','online'=>'bi-globe'];
          foreach ($paymentMethods as $m):
            $pct = round((float)$m['total'] / $grandTotal * 100);
          ?>
          <div class="d-flex justify-content-between align-items-center mb-1">
            <span style="font-size:.88rem;">
              <i class="bi <?= $methodIcons[$m['method']] ?? 'bi-credit-card' ?> me-2" style="color:var(--accent);"></i>
              <?= ucwords(str_replace('_',' ', $m['method'])) ?>
              <span class="text-muted ms-1" style="font-size:.78rem;">(<?= $m['cnt'] ?> tx)</span>
            </span>
            <span style="font-weight:700;color:var(--dark);">$<?= number_format((float)$m['total'], 2) ?></span>
          </div>
          <div class="method-bar-bg mb-3">
            <div class="method-bar-fill" style="width:<?= $pct ?>%;"></div>
          </div>
          <?php endforeach; ?>
          <?php if (empty($paymentMethods)): ?>
            <p class="text-muted text-center mb-0">No payments recorded.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- ── Folios Table ── -->
  <div class="section-card">
    <div class="section-header">
      <span><i class="bi bi-table me-2"></i>Guest Folios</span>
      <span><?= count($folios) ?> folio(s) shown</span>
    </div>
    <div class="section-body pb-0">

      <!-- Filter -->
      <div class="filter-card">
        <form method="GET" action="<?= APP_URL ?>/index.php" class="row g-2 align-items-end">
          <input type="hidden" name="url" value="billing/index">
          <div class="col-md-3">
            <label>Filter by Status</label>
            <select name="status" class="form-select form-select-sm">
              <option value="">All Statuses</option>
              <?php foreach (['open','settled','refunded'] as $s): ?>
              <option value="<?= $s ?>" <?= ($statusFilter===$s)?'selected':'' ?>><?= ucfirst($s) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-3 d-flex gap-2">
            <button class="btn btn-sm btn-primary"><i class="bi bi-funnel me-1"></i>Filter</button>
            <a href="<?= APP_URL ?>/index.php?url=billing/index" class="btn btn-sm btn-outline-secondary"><i class="bi bi-x me-1"></i>Clear</a>
          </div>
        </form>
      </div>

      <?php if (empty($folios)): ?>
        <div class="empty-state">
          <i class="bi bi-receipt d-block mb-2"></i>
          <p class="mb-0">No folios found<?= $statusFilter ? ' for status "'.htmlspecialchars($statusFilter).'"' : '' ?>.</p>
        </div>
      <?php else: ?>
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th>Folio #</th>
              <th>Guest</th>
              <th>Room</th>
              <th>Check-in</th>
              <th>Check-out</th>
              <th>Total</th>
              <th>Paid</th>
              <th>Balance</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($folios as $f): ?>
            <tr>
              <td><strong style="color:var(--dark);">#<?= $f['id'] ?></strong></td>
              <td>
                <?php if (!empty($f['guest_id'])): ?>
                  <a href="<?= APP_URL ?>/index.php?url=guests/show/<?= $f['guest_id'] ?>"
                     style="color:var(--dark);font-weight:600;text-decoration:none;"
                     title="View guest profile">
                    <i class="bi bi-person-circle me-1" style="color:var(--accent);"></i><?= htmlspecialchars($f['guest_name'] ?? '—') ?>
                  </a>
                <?php else: ?>
                  <?= htmlspecialchars($f['guest_name'] ?? '—') ?>
                <?php endif; ?>
              </td>
              <td>
                <span style="font-weight:600;color:var(--dark);"><?= htmlspecialchars($f['room_number'] ?? '—') ?></span>
                <?php if ($f['room_type']): ?>
                <small class="text-muted d-block"><?= htmlspecialchars($f['room_type']) ?></small>
                <?php endif; ?>
              </td>
              <td style="font-size:.85rem;"><?= $f['check_in_date']  ? date('d M Y', strtotime($f['check_in_date']))  : '—' ?></td>
              <td style="font-size:.85rem;"><?= $f['check_out_date'] ? date('d M Y', strtotime($f['check_out_date'])) : '—' ?></td>
              <td><strong>$<?= number_format((float)$f['total_amount'], 2) ?></strong></td>
              <td style="color:#3a8a3a;">$<?= number_format((float)$f['amount_paid'], 2) ?></td>
              <td>
                <?php $bal = (float)$f['balance_due']; ?>
                <span style="font-weight:700;color:<?= $bal > 0 ? '#e07020' : '#3a8a3a'; ?>">
                  $<?= number_format($bal, 2) ?>
                </span>
              </td>
              <td>
                <span class="folio-badge badge-<?= $f['status'] ?>">
                  <?= ucfirst($f['status']) ?>
                </span>
              </td>
              <td>
                <?php if (!empty($f['guest_id'])): ?>
                  <a href="<?= APP_URL ?>/index.php?url=guests/show/<?= $f['guest_id'] ?>"
                     class="btn btn-sm action-btn" style="background:#e8d5c0;color:var(--dark);"
                     title="View guest profile">
                    <i class="bi bi-eye"></i>
                  </a>
                <?php else: ?>
                  <a href="<?= APP_URL ?>/index.php?url=billing/show/<?= $f['id'] ?>"
                     class="btn btn-sm action-btn" style="background:#e8d5c0;color:var(--dark);">
                    <i class="bi bi-eye"></i>
                  </a>
                <?php endif; ?>
                <?php if ($f['status'] === 'open'): ?>
                  <a href="<?= APP_URL ?>/index.php?url=billing/guestBill/<?= $f['id'] ?>"
                     class="btn btn-sm action-btn" style="background:var(--accent);color:#fff;">
                    <i class="bi bi-receipt"></i> Manage
                  </a>
                <?php endif; ?>
                <?php if ($f['balance_due'] <= 0 && $f['status'] === 'open'): ?>
                  <a href="<?= APP_URL ?>/index.php?url=billing/invoice/<?= $f['id'] ?>"
                     class="btn btn-sm action-btn" style="background:#3a8a3a;color:#fff;">
                    <i class="bi bi-file-earmark-check"></i>
                  </a>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div class="px-3 py-2" style="color:var(--accent2);font-size:.85rem;">
        Showing <?= count($folios) ?> folio(s) &nbsp;·&nbsp;
        Revenue Today: <strong>$<?= number_format($revenueToday, 2) ?></strong> (<?= date('d M Y') ?>) &nbsp;·&nbsp;
        Outstanding: <strong>$<?= number_format($folioStats['total_outstanding'], 2) ?></strong>
      </div>
      <?php endif; ?>
    </div>
  </div>

</div>

<?php $content = ob_get_clean(); ?>
<?php require VIEW_PATH . '/layouts/main.php'; ?>
