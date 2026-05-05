<?php
$pageTitle = 'Occupancy Report';
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
  <div>
    <h1 class="h3 mb-0"><i class="bi bi-bar-chart-fill me-2"></i>Occupancy Report</h1>
    <small class="text-muted"><?= htmlspecialchars($start) ?> — <?= htmlspecialchars($end) ?></small>
  </div>
  <a href="<?= APP_URL ?>/?url=reports/index" class="btn btn-sm btn-outline-secondary">← Reports</a>
</div>

<!-- Date filter -->
<form method="GET" action="<?= APP_URL ?>/?url=reports/occupancy" class="rpt-card p-3 mb-4">
  <div class="row g-2 align-items-end">
    <div class="col-auto">
      <label class="form-label small mb-1 fw-semibold">From</label>
      <input type="date" name="start" class="form-control form-control-sm" value="<?= htmlspecialchars($start) ?>">
    </div>
    <div class="col-auto">
      <label class="form-label small mb-1 fw-semibold">To</label>
      <input type="date" name="end" class="form-control form-control-sm" value="<?= htmlspecialchars($end) ?>">
    </div>
    <div class="col-auto">
      <button class="btn btn-primary btn-sm px-4">Apply</button>
    </div>
  </div>
</form>

<!-- Summary stat cards -->
<div class="row g-3 mb-4">
  <div class="col-6 col-md-4">
    <div class="rpt-stat-card c-blue">
      <div class="rpt-num"><?= (int)$totalRooms ?></div>
      <div class="rpt-lbl"><i class="bi bi-building me-1"></i>Total Rooms</div>
      <i class="bi bi-building rpt-icon"></i>
    </div>
  </div>
  <div class="col-6 col-md-4">
    <div class="rpt-stat-card c-green">
      <div class="rpt-num"><?= $avgOccupied ?>%</div>
      <div class="rpt-lbl"><i class="bi bi-graph-up me-1"></i>Avg Occupancy Rate</div>
      <i class="bi bi-graph-up rpt-icon"></i>
    </div>
  </div>
  <div class="col-6 col-md-4">
    <div class="rpt-stat-card c-brown">
      <div class="rpt-num"><?= count($dailyData) ?></div>
      <div class="rpt-lbl"><i class="bi bi-calendar3 me-1"></i>Days with Data</div>
      <i class="bi bi-calendar3 rpt-icon"></i>
    </div>
  </div>
</div>

<!-- Line chart -->
<?php if (!empty($dailyData)): ?>
<div class="rpt-card mb-4">
  <div class="rpt-hd"><i class="bi bi-graph-up me-2"></i>Daily Occupancy % — Line Chart</div>
  <div class="p-3"><canvas id="occChart" height="80"></canvas></div>
</div>
<?php endif; ?>

<!-- Room-type breakdown -->
<?php if (!empty($typeData)): ?>
<div class="rpt-card mb-4">
  <div class="rpt-hd"><i class="bi bi-grid me-2"></i>Breakdown by Room Type</div>
  <div class="table-responsive">
    <table class="table table-striped table-hover mb-0">
      <thead><tr>
        <th>Room Type</th><th>Nights Available</th><th>Nights Occupied</th><th>Occupancy %</th>
      </tr></thead>
      <tbody>
      <?php foreach ($typeData as $t):
        $pct = $t['nights_available'] > 0 ? round($t['nights_occupied'] / $t['nights_available'] * 100, 1) : 0;
        $barCol = $pct >= 80 ? '#43A047' : ($pct >= 50 ? '#FFA726' : '#1a6fc4');
      ?>
        <tr>
          <td class="fw-semibold"><?= htmlspecialchars($t['room_type']) ?></td>
          <td><?= (int)$t['nights_available'] ?></td>
          <td><?= (int)$t['nights_occupied'] ?></td>
          <td>
            <div style="display:flex;align-items:center;gap:.5rem">
              <div style="background:#e8d5c0;border-radius:5px;height:8px;width:90px;overflow:hidden;flex-shrink:0">
                <div style="height:100%;width:<?= $pct ?>%;background:<?= $barCol ?>;border-radius:5px"></div>
              </div>
              <span class="fw-semibold" style="color:<?= $barCol ?>"><?= $pct ?>%</span>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; ?>

<!-- Daily table -->
<?php if (!empty($dailyData)): ?>
<div class="rpt-card">
  <div class="rpt-hd"><i class="bi bi-table me-2"></i>Daily Occupancy Data</div>
  <div class="table-responsive">
    <table class="table table-striped table-hover table-sm mb-0">
      <thead><tr><th>Date</th><th>Rooms Occupied</th><th>Occupancy %</th></tr></thead>
      <tbody>
      <?php foreach ($dailyData as $d):
        $pct = $totalRooms > 0 ? round($d['occupied'] / $totalRooms * 100, 1) : 0;
        $badgeCls = $pct >= 80 ? 'bg-success' : ($pct >= 50 ? 'bg-warning text-dark' : 'bg-secondary');
      ?>
        <tr>
          <td><?= htmlspecialchars($d['day']) ?></td>
          <td><?= (int)$d['occupied'] ?> / <?= $totalRooms ?></td>
          <td><span class="badge <?= $badgeCls ?>"><?= $pct ?>%</span></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php else: ?>
  <p class="text-muted text-center py-5"><i class="bi bi-inbox fs-1 d-block mb-2"></i>No occupancy data found for the selected period.</p>
<?php endif; ?>

<?php if (!empty($dailyData)): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
(function(){
  var labels = <?= json_encode(array_column($dailyData, 'day')) ?>;
  var total  = <?= (int)$totalRooms ?: 1 ?>;
  var values = <?= json_encode(array_map(function($d) use ($totalRooms) {
    return $totalRooms > 0 ? round($d['occupied'] / $totalRooms * 100, 1) : 0;
  }, $dailyData)) ?>;
  new Chart(document.getElementById('occChart'), {
    type: 'line',
    data: {
      labels: labels,
      datasets: [{
        label: 'Occupancy %',
        data: values,
        borderColor: '#C08552',
        backgroundColor: 'rgba(192,133,82,.12)',
        borderWidth: 2.5,
        pointBackgroundColor: '#C08552',
        pointRadius: 4,
        tension: 0.35,
        fill: true
      }]
    },
    options: {
      responsive: true,
      plugins: { legend: { display: false }, tooltip: { callbacks: { label: function(c){ return c.parsed.y + '%'; } } } },
      scales: {
        y: { min: 0, max: 100, ticks: { callback: function(v){ return v + '%'; } }, grid: { color: 'rgba(0,0,0,.05)' } },
        x: { grid: { display: false } }
      }
    }
  });
})();
</script>
<?php endif; ?>
<?php $content = ob_get_clean(); require VIEW_PATH . '/layouts/main.php'; ?>
