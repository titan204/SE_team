<?php $pageTitle = 'Revenue Report'; ?>
<?php ob_start(); ?>

<style>
  .page-header { border-bottom:2px solid var(--accent); padding-bottom:.75rem; margin-bottom:1.5rem; }
  .page-header h2 { color:var(--dark); font-weight:700; }
  .stat-card { background:#fff; border:1px solid #e8d5c0; border-radius:12px; padding:1.4rem 1.2rem; box-shadow:0 2px 8px rgba(192,133,82,.09); text-align:center; }
  .stat-card .val { font-size:2rem; font-weight:700; }
  .stat-card .lbl { font-size:.82rem; color:#888; margin-top:.2rem; }
  .stat-card .sub { font-size:.75rem; color:#bbb; margin-top:.2rem; }
  .section-card { background:#fff; border:1px solid #e8d5c0; border-radius:10px; margin-bottom:1.5rem; box-shadow:0 2px 8px rgba(192,133,82,.08); overflow:hidden; }
  .section-card .sec-head { background:var(--dark); color:#fff; padding:.75rem 1.2rem; font-weight:600; font-size:.93rem; display:flex; justify-content:space-between; align-items:center; }
  .section-card .sec-body { padding:1.2rem; }
  .table th { background:var(--dark); color:#fff; font-size:.84rem; font-weight:600; }
  .table tbody tr:hover { background:#fff3e8; }
  .table td, .table th { vertical-align:middle; }
  .method-bar-bg  { background:#f0e4d0; border-radius:8px; height:10px; overflow:hidden; }
  .method-bar-fill{ height:10px; border-radius:8px; background:var(--accent); }
  .filter-card { background:#fff; border:1px solid #e8d5c0; border-radius:10px; padding:1rem 1.2rem; margin-bottom:1.2rem; box-shadow:0 1px 4px rgba(192,133,82,.07); }
  label { color:var(--dark); font-weight:600; font-size:.88rem; }
  .form-control:focus { border-color:var(--accent); box-shadow:0 0 0 .2rem rgba(192,133,82,.25); }
  .growth-up   { color:#3a8a3a; font-weight:700; }
  .growth-down { color:#c04040; font-weight:700; }
  .badge-open     { background:#f0a500; color:#fff; font-size:.73rem; padding:.22em .6em; border-radius:20px; }
  .badge-settled  { background:#3a8a3a; color:#fff; font-size:.73rem; padding:.22em .6em; border-radius:20px; }
  .badge-refunded { background:#2196a5; color:#fff; font-size:.73rem; padding:.22em .6em; border-radius:20px; }
  .empty-state { text-align:center; padding:2.5rem 1rem; color:#a08060; }
  .day-bar-bg  { background:#f0e4d0; border-radius:4px; height:8px; }
  .day-bar-fill{ height:8px; border-radius:4px; background:var(--accent); }
  .method-icons { cash:'bi-cash-coin', credit_card:'bi-credit-card-2-front', bank_transfer:'bi-bank', online:'bi-globe', debit_card:'bi-credit-card' }
</style>

<div class="container-fluid py-3">

  <!-- Header -->
  <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h2><i class="bi bi-graph-up-arrow me-2"></i>Revenue Report</h2>
    <a href="<?= APP_URL ?>/index.php?url=reports/index" class="btn btn-sm btn-outline-secondary">
      <i class="bi bi-arrow-left me-1"></i>Reports
    </a>
  </div>

  <!-- Date Range Filter -->
  <div class="filter-card">
    <form method="GET" action="<?= APP_URL ?>/index.php" class="row g-2 align-items-end">
      <input type="hidden" name="url" value="reports/revenue">
      <div class="col-md-3">
        <label>From</label>
        <input type="date" name="start" class="form-control form-control-sm"
               value="<?= htmlspecialchars($start) ?>" max="<?= date('Y-m-d') ?>">
      </div>
      <div class="col-md-3">
        <label>To</label>
        <input type="date" name="end" class="form-control form-control-sm"
               value="<?= htmlspecialchars($end) ?>">
      </div>
      <div class="col-md-3 d-flex gap-2">
        <button class="btn btn-sm btn-primary"><i class="bi bi-funnel me-1"></i>Apply</button>
        <a href="<?= APP_URL ?>/index.php?url=reports/revenue" class="btn btn-sm btn-outline-secondary"><i class="bi bi-x me-1"></i>Reset</a>
      </div>
      <div class="col-md-3 text-end" style="font-size:.82rem;color:#888;">
        Period: <strong><?= date('d M Y', strtotime($start)) ?></strong> → <strong><?= date('d M Y', strtotime($end)) ?></strong>
      </div>
    </form>
  </div>

  <!-- KPI Cards -->
  <div class="row g-3 mb-4">

    <!-- Total Revenue -->
    <div class="col-6 col-md-3">
      <div class="stat-card">
        <i class="bi bi-currency-dollar" style="font-size:1.6rem;color:var(--accent);"></i>
        <div class="val" style="color:var(--dark);">$<?= number_format($totalRevenue, 2) ?></div>
        <div class="lbl">Total Revenue</div>
        <div class="sub">Actual payments received</div>
      </div>
    </div>

    <!-- Avg Daily -->
    <div class="col-6 col-md-3">
      <div class="stat-card">
        <i class="bi bi-calendar-day" style="font-size:1.6rem;color:var(--accent2);"></i>
        <div class="val" style="color:var(--dark);">$<?= number_format($avgDaily, 2) ?></div>
        <div class="lbl">Avg. Daily Revenue</div>
        <div class="sub"><?= count($dailyData) ?> active day(s) in period</div>
      </div>
    </div>

    <!-- Transactions -->
    <div class="col-6 col-md-3">
      <div class="stat-card">
        <i class="bi bi-receipt" style="font-size:1.6rem;color:#2196a5;"></i>
        <div class="val" style="color:#2196a5;"><?= array_sum(array_column($dailyData,'transactions')) ?></div>
        <div class="lbl">Total Transactions</div>
        <div class="sub"><?= count($byMethod) ?> payment method(s)</div>
      </div>
    </div>

    <!-- vs Previous Period -->
    <div class="col-6 col-md-3">
      <div class="stat-card">
        <i class="bi bi-arrow-left-right" style="font-size:1.6rem;color:#888;"></i>
        <div class="val" style="color:var(--dark);">$<?= number_format($prevRevenue, 2) ?></div>
        <div class="lbl">Previous Period</div>
        <div class="sub">
          <?php if ($growthPct !== null): ?>
            <span class="<?= $growthPct >= 0 ? 'growth-up' : 'growth-down' ?>">
              <?= $growthPct >= 0 ? '▲' : '▼' ?> <?= abs($growthPct) ?>%
              <?= $growthPct >= 0 ? 'growth' : 'decline' ?>
            </span>
          <?php else: ?>
            <span style="color:#bbb;">No prior data</span>
          <?php endif; ?>
        </div>
      </div>
    </div>

  </div>

  <div class="row g-3">

    <!-- Daily Revenue -->
    <div class="col-md-7">
      <div class="section-card h-100">
        <div class="sec-head">
          <span><i class="bi bi-bar-chart me-2"></i>Daily Revenue</span>
          <span style="font-size:.82rem;font-weight:400;"><?= date('d M', strtotime($start)) ?> – <?= date('d M Y', strtotime($end)) ?></span>
        </div>
        <div class="sec-body">
          <?php if (empty($dailyData)): ?>
            <div class="empty-state">
              <i class="bi bi-bar-chart d-block mb-2" style="font-size:2rem;opacity:.3;"></i>
              <p class="mb-0">No payments recorded in this period.</p>
            </div>
          <?php else: ?>
            <!-- Bar chart -->
            <canvas id="revChart" height="90" class="mb-3"></canvas>
            <?php
            $maxDay = max(array_column($dailyData, 'daily_total')) ?: 1;
            ?>
            <div class="table-responsive">
              <table class="table table-hover mb-0" style="font-size:.87rem;">
                <thead>
                  <tr>
                    <th>Date</th>
                    <th>Transactions</th>
                    <th>Revenue</th>
                    <th style="min-width:140px;">Bar</th>
                  </tr>
                </thead>
                <tbody>
                <?php foreach ($dailyData as $d): ?>
                  <?php $pct = round((float)$d['daily_total'] / $maxDay * 100); ?>
                  <tr>
                    <td><strong><?= date('d M Y', strtotime($d['day'])) ?></strong></td>
                    <td><?= $d['transactions'] ?> tx</td>
                    <td style="font-weight:700;color:var(--dark);">$<?= number_format((float)$d['daily_total'], 2) ?></td>
                    <td>
                      <div class="day-bar-bg">
                        <div class="day-bar-fill" style="width:<?= $pct ?>%;"></div>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Payment Method Breakdown -->
    <div class="col-md-5">
      <div class="section-card h-100">
        <div class="sec-head">
          <span><i class="bi bi-credit-card me-2"></i>Payment Methods</span>
        </div>
        <div class="sec-body">
          <?php
          $methodIcons = [
            'cash'          => 'bi-cash-coin',
            'credit_card'   => 'bi-credit-card-2-front',
            'debit_card'    => 'bi-credit-card',
            'bank_transfer' => 'bi-bank',
            'online'        => 'bi-globe',
          ];
          $grandTotal = array_sum(array_column($byMethod, 'subtotal')) ?: 1;
          if (empty($byMethod)): ?>
            <div class="empty-state">
              <i class="bi bi-credit-card d-block mb-2" style="font-size:2rem;opacity:.3;"></i>
              <p class="mb-0">No payment data.</p>
            </div>
          <?php else:
            foreach ($byMethod as $m):
              $pct = round((float)$m['subtotal'] / $grandTotal * 100);
          ?>
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span style="font-size:.88rem;">
                <i class="bi <?= $methodIcons[$m['method']] ?? 'bi-credit-card' ?> me-1" style="color:var(--accent);"></i>
                <?= ucwords(str_replace('_', ' ', $m['method'])) ?>
                <span class="text-muted" style="font-size:.78rem;">(<?= $m['tx_count'] ?> tx)</span>
              </span>
              <strong style="color:var(--dark);">$<?= number_format((float)$m['subtotal'], 2) ?></strong>
            </div>
            <div class="method-bar-bg mb-3">
              <div class="method-bar-fill" style="width:<?= $pct ?>%;"></div>
            </div>
          <?php endforeach; endif; ?>
        </div>
      </div>
    </div>

  </div>

  <!-- Top Folios by Revenue -->
  <?php if (!empty($topFolios)): ?>
  <div class="section-card mt-3">
    <div class="sec-head">
      <span><i class="bi bi-trophy me-2"></i>Top Accounts by Revenue <span style="font-weight:400;font-size:.82rem;">in this period</span></span>
      <span style="font-size:.82rem;font-weight:400;"><?= count($topFolios) ?> folio(s)</span>
    </div>
    <div class="sec-body pb-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0" style="font-size:.87rem;">
          <thead>
            <tr>
              <th>Folio #</th>
              <th>Guest</th>
              <th>Room</th>
              <th>Period Paid</th>
              <th>Total Folio</th>
              <th>Balance</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($topFolios as $i => $f): ?>
            <tr>
              <td>
                <?php if ($i === 0): ?>
                  <i class="bi bi-trophy-fill" style="color:#f0a500;"></i>
                <?php elseif ($i === 1): ?>
                  <i class="bi bi-trophy-fill" style="color:#aaa;"></i>
                <?php elseif ($i === 2): ?>
                  <i class="bi bi-trophy-fill" style="color:#cd7f32;"></i>
                <?php endif; ?>
                <strong style="color:var(--dark);">#<?= $f['folio_id'] ?></strong>
              </td>
              <td><?= htmlspecialchars($f['guest_name'] ?? '—') ?></td>
              <td><?= htmlspecialchars($f['room_number'] ?? '—') ?></td>
              <td style="font-weight:700;color:var(--dark);">$<?= number_format((float)$f['period_paid'], 2) ?></td>
              <td>$<?= number_format((float)$f['total_amount'], 2) ?></td>
              <td>
                <span style="color:<?= (float)$f['balance_due'] > 0 ? '#e07020' : '#3a8a3a' ?>;font-weight:700;">
                  $<?= number_format((float)$f['balance_due'], 2) ?>
                </span>
              </td>
              <td><span class="badge-<?= $f['status'] ?>"><?= ucfirst($f['status']) ?></span></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div class="px-3 py-2" style="font-size:.83rem;color:var(--accent2);">
        Total revenue in period: <strong>$<?= number_format($totalRevenue, 2) ?></strong>
        &nbsp;·&nbsp; Avg/day: <strong>$<?= number_format($avgDaily, 2) ?></strong>
        <?php if ($growthPct !== null): ?>
          &nbsp;·&nbsp; vs prev period:
          <span class="<?= $growthPct >= 0 ? 'growth-up' : 'growth-down' ?>">
            <?= $growthPct >= 0 ? '+' : '' ?><?= $growthPct ?>%
          </span>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <?php endif; ?>

</div>

<?php if (!empty($dailyData)): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
(function(){
  var labels = <?= json_encode(array_map(fn($d)=>date('d M',strtotime($d['day'])), $dailyData)) ?>;
  var values = <?= json_encode(array_map(fn($d)=>(float)$d['daily_total'], $dailyData)) ?>;
  var el = document.getElementById('revChart');
  if(!el) return;
  new Chart(el, {
    type: 'bar',
    data: {
      labels: labels,
      datasets:[{
        label: 'Revenue ($)',
        data: values,
        backgroundColor: 'rgba(192,133,82,.75)',
        borderColor: '#8C5A3C',
        borderWidth: 1.5,
        borderRadius: 5
      }]
    },
    options:{
      responsive:true,
      plugins:{ legend:{display:false}, tooltip:{callbacks:{label:function(c){return '$'+c.parsed.y.toLocaleString('en-US',{minimumFractionDigits:2});}}} },
      scales:{
        y:{ ticks:{ callback:function(v){return '$'+v.toLocaleString();} }, grid:{color:'rgba(0,0,0,.05)'} },
        x:{ grid:{display:false} }
      }
    }
  });
})();
</script>
<?php endif; ?>
<?php $content = ob_get_clean(); ?>
<?php require VIEW_PATH . '/layouts/main.php'; ?>
