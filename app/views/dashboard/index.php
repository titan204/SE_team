<?php
$pageTitle = 'Dashboard';
$role = strtolower($_SESSION['user_role'] ?? '');
ob_start();
?>
<style>
:root { --accent:#C08552; --accent2:#8C5A3C; --dark:#4B2E2B; --bg:#FFF8F0; }
.stat-card { border-radius:14px; padding:1.4rem 1.2rem; color:#fff; position:relative; overflow:hidden; transition:transform .2s; }
.stat-card:hover { transform:translateY(-3px); }
.stat-card .stat-icon { font-size:2.8rem; opacity:.18; position:absolute; right:1rem; bottom:.6rem; }
.stat-card .stat-num  { font-size:2.2rem; font-weight:700; line-height:1; }
.stat-card .stat-lbl  { font-size:.8rem; opacity:.88; margin-top:.3rem; }
.stat-card.c-blue   { background:linear-gradient(135deg,#1a6fc4,#2196F3); }
.stat-card.c-green  { background:linear-gradient(135deg,#1b7a3e,#43A047); }
.stat-card.c-amber  { background:linear-gradient(135deg,#b76e00,#FFA726); }
.stat-card.c-brown  { background:linear-gradient(135deg,var(--dark),var(--accent)); }
.stat-card.c-teal   { background:linear-gradient(135deg,#00695c,#26A69A); }
.stat-card.c-purple { background:linear-gradient(135deg,#5c2d91,#9C27B0); }
.dash-card { background:#fff; border:1px solid #e8d5c0; border-radius:12px; box-shadow:0 2px 10px rgba(192,133,82,.1); }
.dash-card .dash-card-hd { background:linear-gradient(90deg,var(--dark),var(--accent2)); color:#fff; border-radius:12px 12px 0 0; padding:.7rem 1rem; font-weight:600; font-size:.92rem; }
.dash-card table thead th { background:var(--dark); color:#fff; font-size:.82rem; padding:.45rem .7rem; }
.dash-card table tbody td { font-size:.83rem; padding:.4rem .7rem; vertical-align:middle; }
.dash-card table tbody tr:hover { background:#fff3e8; }
.role-banner { background:linear-gradient(120deg,var(--dark),var(--accent)); color:#fff; border-radius:14px; padding:1.2rem 1.6rem; margin-bottom:1.5rem; }
.role-banner h4 { margin:0; font-weight:700; }
.role-banner small { opacity:.82; }
.refresh-pill { background:rgba(255,248,240,.15); border:1px solid rgba(255,248,240,.3); border-radius:20px; padding:.25rem .8rem; font-size:.78rem; color:#fff; }
.occ-bar { height:10px; border-radius:5px; background:#e8d5c0; overflow:hidden; }
.occ-fill { height:100%; border-radius:5px; background:linear-gradient(90deg,var(--accent2),var(--accent)); transition:width .6s ease; }
</style>

<!-- ── Role Banner ── -->
<div class="role-banner d-flex justify-content-between align-items-center flex-wrap gap-2">
  <div>
    <h4><i class="bi bi-speedometer2 me-2"></i>
    <?php
    $labels = ['manager'=>'Manager Dashboard','front_desk'=>'Front Desk Dashboard',
               'revenue_manager'=>'Revenue Dashboard','housekeeper'=>'Housekeeping Dashboard'];
    echo $labels[$role] ?? 'Dashboard';
    ?>
    </h4>
    <small>Welcome, <strong><?= htmlspecialchars($_SESSION['user_name'] ?? '') ?></strong>
     &mdash; <?= date('l, d F Y') ?></small>
  </div>
  <span class="refresh-pill"><i class="bi bi-arrow-repeat me-1"></i>Auto-refresh in <span id="countdown">60</span>s</span>
</div>

<?php /* ═══════════ MANAGER ═══════════ */ if ($role === 'manager' || $role === 'supervisor'): ?>

<div class="row g-3 mb-4">
  <div class="col-6 col-md-3">
    <div class="stat-card c-blue">
      <div class="stat-num" id="stat-reservations"><?= (int)($reservations_today??0) ?></div>
      <div class="stat-lbl"><i class="bi bi-calendar-check me-1"></i>Reservations Today</div>
      <i class="bi bi-calendar-check stat-icon"></i>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card c-green">
      <div class="stat-num" id="stat-available"><?= (int)($rooms_by_status['available']??0) ?></div>
      <div class="stat-lbl"><i class="bi bi-door-open me-1"></i>Available Rooms
        <span class="ms-1 badge bg-light text-dark" id="stat-occupied"><?= (int)($rooms_by_status['occupied']??0) ?></span> occ
        <span class="ms-1 badge bg-warning text-dark" id="stat-dirty"><?= (int)($rooms_by_status['dirty']??0) ?></span> dirty
      </div>
      <i class="bi bi-building stat-icon"></i>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card c-amber">
      <div class="stat-num" id="stat-hk"><?= (int)($pending_hk_tasks??0) ?></div>
      <div class="stat-lbl"><i class="bi bi-brush me-1"></i>Pending HK Tasks</div>
      <i class="bi bi-brush stat-icon"></i>
    </div>
  </div>
    <div class="col-6 col-md-3">
    <div class="stat-card c-brown">
      <div class="stat-num" id="stat-revenue">$<?= number_format($revenue_today??0,2) ?></div>
      <div class="stat-lbl"><i class="bi bi-cash-stack me-1"></i>Revenue Today
        <span class="d-block mt-1 small" id="stat-month">Month: $<?= number_format($revenue_month??0,2) ?></span>
      </div>
      <i class="bi bi-cash-stack stat-icon"></i>
    </div>
  </div>
</div>

<div class="row g-3 mb-3">
  <div class="col-6 col-md-3">
    <div class="stat-card c-purple">
      <div class="stat-num" id="stat-wo"><?= (int)($open_work_orders??0) ?></div>
      <div class="stat-lbl"><i class="bi bi-tools me-1"></i>Open Work Orders</div>
      <i class="bi bi-tools stat-icon"></i>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card" style="background:linear-gradient(135deg,#7b2d2d,#c0392b)">
      <div class="stat-num" id="stat-disputes"><?= (int)($open_disputes??0) ?></div>
      <div class="stat-lbl"><i class="bi bi-exclamation-circle me-1"></i>Open Disputes</div>
      <i class="bi bi-exclamation-circle stat-icon"></i>
    </div>
  </div>
</div>

<div class="row g-3 mb-4">
  <div class="col-md-6">
    <div class="dash-card">
      <div class="dash-card-hd"><i class="bi bi-box-arrow-in-right me-2"></i>Check-ins Today <span class="badge bg-light text-dark ms-1"><?= count($upcoming_checkins??[]) ?></span></div>
      <?php if(empty($upcoming_checkins)): ?><p class="text-muted text-center py-3 mb-0 small">No check-ins today.</p><?php else: ?>
      <div class="table-responsive"><table class="table mb-0">
        <thead><tr><th>Guest</th><th>Room</th><th>Pax</th><th></th></tr></thead>
        <tbody><?php foreach($upcoming_checkins as $ci): ?>
          <tr><td><?= htmlspecialchars($ci['guest_name']) ?></td>
          <td><span class="badge bg-secondary"><?= htmlspecialchars($ci['room_number']) ?></span></td>
          <td><?= (int)$ci['adults']+(int)$ci['children'] ?></td>
          <td><a href="<?= APP_URL ?>/?url=reservations/show/<?= $ci['id'] ?>" class="btn btn-sm btn-outline-primary py-0 px-2">View</a></td></tr>
        <?php endforeach; ?></tbody>
      </table></div><?php endif; ?>
    </div>
  </div>
  <div class="col-md-6">
    <div class="dash-card">
      <div class="dash-card-hd"><i class="bi bi-box-arrow-right me-2"></i>Check-outs Today <span class="badge bg-light text-dark ms-1"><?= count($upcoming_checkouts??[]) ?></span></div>
      <?php if(empty($upcoming_checkouts)): ?><p class="text-muted text-center py-3 mb-0 small">No check-outs today.</p><?php else: ?>
      <div class="table-responsive"><table class="table mb-0">
        <thead><tr><th>Guest</th><th>Room</th><th>Total</th><th></th></tr></thead>
        <tbody><?php foreach($upcoming_checkouts as $co): ?>
          <tr><td><?= htmlspecialchars($co['guest_name']) ?></td>
          <td><span class="badge bg-secondary"><?= htmlspecialchars($co['room_number']) ?></span></td>
          <td>$<?= number_format($co['total_price'],2) ?></td>
          <td><a href="<?= APP_URL ?>/?url=reservations/show/<?= $co['id'] ?>" class="btn btn-sm btn-outline-danger py-0 px-2">View</a></td></tr>
        <?php endforeach; ?></tbody>
      </table></div><?php endif; ?>
    </div>
  </div>
</div>

<div class="dash-card mb-3">
  <div class="dash-card-hd" style="background:linear-gradient(90deg,#7d5a00,#FFA726)"><i class="bi bi-star-fill me-2"></i>VIP Arrivals Today <span class="badge bg-light text-dark ms-1"><?= count($vip_arrivals??[]) ?></span></div>
  <?php if(empty($vip_arrivals)): ?><p class="text-muted text-center py-3 mb-0 small">No VIP arrivals today.</p><?php else: ?>
  <div class="table-responsive"><table class="table mb-0">
    <thead><tr><th>Guest</th><th>Room</th><th>Requests</th><th></th></tr></thead>
    <tbody><?php foreach($vip_arrivals as $v): ?>
      <tr class="table-warning"><td><i class="bi bi-star-fill text-warning me-1"></i><?= htmlspecialchars($v['guest_name']) ?></td>
      <td><span class="badge bg-secondary"><?= htmlspecialchars($v['room_number']) ?></span></td>
      <td class="small"><?= htmlspecialchars(substr($v['special_requests']??'',0,80)) ?></td>
      <td><a href="<?= APP_URL ?>/?url=reservations/show/<?= $v['id'] ?>" class="btn btn-sm btn-warning py-0 px-2">View</a></td></tr>
    <?php endforeach; ?></tbody>
  </table></div><?php endif; ?>
</div>

<?php /* ═══════════ FRONT DESK ═══════════ */ elseif ($role === 'front_desk'): ?>

<div class="row g-3 mb-4">
  <div class="col-6 col-md-3">
    <div class="stat-card c-blue">
      <div class="stat-num" id="stat-reservations"><?= (int)($reservations_today??0) ?></div>
      <div class="stat-lbl"><i class="bi bi-calendar-check me-1"></i>Activity Today</div>
      <i class="bi bi-calendar-check stat-icon"></i>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card c-green">
      <div class="stat-num" id="stat-available"><?= (int)($rooms_by_status['available']??0) ?></div>
      <div class="stat-lbl"><i class="bi bi-door-open me-1"></i>Available</div>
      <i class="bi bi-building stat-icon"></i>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card c-teal">
      <div class="stat-num"><?= count($upcoming_checkins??[]) ?></div>
      <div class="stat-lbl"><i class="bi bi-box-arrow-in-right me-1"></i>Check-ins Today</div>
      <i class="bi bi-box-arrow-in-right stat-icon"></i>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card c-amber">
      <div class="stat-num"><?= count($upcoming_checkouts??[]) ?></div>
      <div class="stat-lbl"><i class="bi bi-box-arrow-right me-1"></i>Check-outs Today</div>
      <i class="bi bi-box-arrow-right stat-icon"></i>
    </div>
  </div>
</div>

<div class="row g-3 mb-4">
  <div class="col-md-6">
    <div class="dash-card">
      <div class="dash-card-hd"><i class="bi bi-box-arrow-in-right me-2"></i>Arrivals</div>
      <?php if(empty($upcoming_checkins)): ?><p class="text-muted text-center py-3 mb-0 small">No arrivals today.</p><?php else: ?>
      <div class="table-responsive"><table class="table mb-0">
        <thead><tr><th>Guest</th><th>Room</th><th>Pax</th><th></th></tr></thead>
        <tbody><?php foreach($upcoming_checkins as $ci): ?>
          <tr><td><?= htmlspecialchars($ci['guest_name']) ?></td>
          <td><span class="badge bg-secondary"><?= htmlspecialchars($ci['room_number']) ?></span></td>
          <td><?= (int)$ci['adults']+(int)$ci['children'] ?></td>
          <td><a href="<?= APP_URL ?>/?url=reservations/show/<?= $ci['id'] ?>" class="btn btn-sm btn-outline-primary py-0 px-2">Check-In</a></td></tr>
        <?php endforeach; ?></tbody>
      </table></div><?php endif; ?>
    </div>
  </div>
  <div class="col-md-6">
    <div class="dash-card">
      <div class="dash-card-hd"><i class="bi bi-box-arrow-right me-2"></i>Departures</div>
      <?php if(empty($upcoming_checkouts)): ?><p class="text-muted text-center py-3 mb-0 small">No departures today.</p><?php else: ?>
      <div class="table-responsive"><table class="table mb-0">
        <thead><tr><th>Guest</th><th>Room</th><th>Balance</th><th></th></tr></thead>
        <tbody><?php foreach($upcoming_checkouts as $co): ?>
          <tr><td><?= htmlspecialchars($co['guest_name']) ?></td>
          <td><span class="badge bg-secondary"><?= htmlspecialchars($co['room_number']) ?></span></td>
          <td>$<?= number_format($co['total_price'],2) ?></td>
          <td><a href="<?= APP_URL ?>/?url=reservations/show/<?= $co['id'] ?>" class="btn btn-sm btn-outline-danger py-0 px-2">Check-Out</a></td></tr>
        <?php endforeach; ?></tbody>
      </table></div><?php endif; ?>
    </div>
  </div>
</div>
<?php if(!empty($vip_arrivals)): ?>
<div class="dash-card mb-3">
  <div class="dash-card-hd" style="background:linear-gradient(90deg,#7d5a00,#FFA726)"><i class="bi bi-star-fill me-2"></i>VIP Arrivals <span class="badge bg-light text-dark ms-1"><?= count($vip_arrivals) ?></span></div>
  <div class="table-responsive"><table class="table mb-0">
    <thead><tr><th>Guest</th><th>Room</th><th>Requests</th></tr></thead>
    <tbody><?php foreach($vip_arrivals as $v): ?>
      <tr class="table-warning"><td><i class="bi bi-star-fill text-warning me-1"></i><?= htmlspecialchars($v['guest_name']) ?></td>
      <td><span class="badge bg-secondary"><?= htmlspecialchars($v['room_number']) ?></span></td>
      <td class="small"><?= htmlspecialchars(substr($v['special_requests']??'',0,80)) ?></td></tr>
    <?php endforeach; ?></tbody>
  </table></div>
</div>
<?php endif; ?>

<?php /* ═══════════ REVENUE MANAGER ═══════════ */ elseif ($role === 'revenue_manager'): ?>

<div class="row g-3 mb-4">
  <div class="col-6 col-md-3">
    <div class="stat-card c-brown">
      <div class="stat-num" id="stat-revenue">$<?= number_format($revenue_today??0,2) ?></div>
      <div class="stat-lbl"><i class="bi bi-cash-stack me-1"></i>Revenue Today</div>
      <i class="bi bi-cash-stack stat-icon"></i>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card c-purple">
      <div class="stat-num">$<?= number_format($revenue_month??0,0) ?></div>
      <div class="stat-lbl"><i class="bi bi-graph-up me-1"></i>Revenue This Month</div>
      <i class="bi bi-graph-up stat-icon"></i>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card c-teal">
      <div class="stat-num"><?= $occupancy_rate??0 ?>%</div>
      <div class="stat-lbl"><i class="bi bi-bar-chart me-1"></i>Occupancy Rate</div>
      <i class="bi bi-bar-chart stat-icon"></i>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card c-blue">
      <div class="stat-num" id="stat-reservations"><?= (int)($reservations_today??0) ?></div>
      <div class="stat-lbl"><i class="bi bi-calendar-check me-1"></i>Reservations Today</div>
      <i class="bi bi-calendar-check stat-icon"></i>
    </div>
  </div>
</div>

<div class="row g-3 mb-4">
  <div class="col-md-6">
    <div class="dash-card p-3">
      <h6 class="fw-semibold mb-2" style="color:var(--dark)"><i class="bi bi-building me-2"></i>Room Status Breakdown</h6>
      <?php $rbs = $rooms_by_status ?? ['available'=>0,'occupied'=>0,'dirty'=>0,'out_of_order'=>0,'total'=>1];
      $total = max($rbs['total'],1); ?>
      <?php foreach(['available'=>'#43A047','occupied'=>'#1a6fc4','dirty'=>'#FFA726','out_of_order'=>'#e53935'] as $s=>$col): ?>
      <div class="d-flex align-items-center mb-2">
        <span class="small fw-semibold me-2" style="width:100px;color:var(--dark)"><?= ucfirst(str_replace('_',' ',$s)) ?></span>
        <div class="occ-bar flex-grow-1 me-2">
          <div class="occ-fill" style="width:<?= round(($rbs[$s]??0)/$total*100) ?>%;background:<?= $col ?>;height:10px;"></div>
        </div>
        <span class="small fw-bold" style="color:var(--dark);width:30px;text-align:right"><?= $rbs[$s]??0 ?></span>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <div class="col-md-6">
    <div class="dash-card">
      <div class="dash-card-hd"><i class="bi bi-box-arrow-in-right me-2"></i>Today's Arrivals</div>
      <?php if(empty($upcoming_checkins)): ?><p class="text-muted text-center py-3 mb-0 small">No arrivals today.</p><?php else: ?>
      <div class="table-responsive"><table class="table mb-0">
        <thead><tr><th>Guest</th><th>Room</th><th>Pax</th></tr></thead>
        <tbody><?php foreach(array_slice($upcoming_checkins,0,6) as $ci): ?>
          <tr><td><?= htmlspecialchars($ci['guest_name']) ?></td>
          <td><span class="badge bg-secondary"><?= htmlspecialchars($ci['room_number']) ?></span></td>
          <td><?= (int)$ci['adults']+(int)$ci['children'] ?></td></tr>
        <?php endforeach; ?></tbody>
      </table></div><?php endif; ?>
    </div>
  </div>
</div>

<?php /* ═══════════ HOUSEKEEPER ═══════════ */ elseif ($role === 'housekeeper'): ?>

<div class="row g-3 mb-4">
  <div class="col-6 col-md-4">
    <div class="stat-card c-amber">
      <div class="stat-num" id="stat-hk"><?= (int)($pending_hk_tasks??0) ?></div>
      <div class="stat-lbl"><i class="bi bi-hourglass me-1"></i>Pending Tasks</div>
      <i class="bi bi-brush stat-icon"></i>
    </div>
  </div>
  <div class="col-6 col-md-4">
    <div class="stat-card c-green">
      <div class="stat-num" id="stat-available"><?= (int)($rooms_by_status['available']??0) ?></div>
      <div class="stat-lbl"><i class="bi bi-door-open me-1"></i>Clean & Available</div>
      <i class="bi bi-door-open stat-icon"></i>
    </div>
  </div>
  <div class="col-6 col-md-4">
    <div class="stat-card c-blue">
      <div class="stat-num" id="stat-dirty"><?= (int)($rooms_by_status['dirty']??0) ?></div>
      <div class="stat-lbl"><i class="bi bi-bucket me-1"></i>Rooms Need Cleaning</div>
      <i class="bi bi-bucket stat-icon"></i>
    </div>
  </div>
</div>

<div class="dash-card mb-3">
  <div class="dash-card-hd"><i class="bi bi-person-check me-2"></i>My Assigned Tasks</div>
  <?php if(empty($hk_my_tasks)): ?>
    <p class="text-muted text-center py-3 mb-0">No open tasks assigned to you.</p>
  <?php else: ?>
  <div class="table-responsive"><table class="table mb-0">
    <thead><tr><th>Room</th><th>Type</th><th>Status</th><th>Notes</th><th></th></tr></thead>
    <tbody><?php foreach($hk_my_tasks as $t): ?>
      <tr>
        <td><span class="badge bg-secondary"><?= htmlspecialchars($t['room_number']??'—') ?></span></td>
        <td class="small"><?= ucfirst(str_replace('_',' ',$t['task_type']??'')) ?></td>
        <td><?php $tc=['pending'=>'warning text-dark','in_progress'=>'info text-dark','done'=>'success','skipped'=>'secondary'];
          ?><span class="badge bg-<?= $tc[$t['status']]??'secondary' ?>"><?= ucfirst($t['status']) ?></span></td>
        <td class="small"><?= htmlspecialchars(substr($t['notes']??'',0,50)) ?></td>
        <td><a href="<?= APP_URL ?>/?url=housekeeping/myTasks" class="btn btn-sm btn-outline-primary py-0 px-2">Update</a></td>
      </tr>
    <?php endforeach; ?></tbody>
  </table></div>
  <?php endif; ?>
</div>
<a href="<?= APP_URL ?>/?url=housekeeping/index" class="btn btn-primary"><i class="bi bi-kanban me-1"></i>Open Task Board</a>

<?php endif; ?>

<!-- ── Auto-refresh ── -->
<script>
(function(){
  var s=60, el=document.getElementById('countdown');
  setInterval(function(){
    s--; if(el) el.textContent=s;
    if(s<=0){ s=60; refreshStats(); }
  },1000);
  function refreshStats(){
    fetch('<?= APP_URL ?>/?url=dashboard/stats',{credentials:'same-origin'})
      .then(function(r){return r.json();})
      .then(function(d){
        set('stat-reservations', d.reservations_today);
        if(d.rooms_by_status){
          set('stat-available', d.rooms_by_status.available);
          set('stat-dirty',     d.rooms_by_status.dirty);
          set('stat-occupied',  d.rooms_by_status.occupied);
        }
        set('stat-hk', d.pending_hk_tasks);
        if(d.revenue_today  != null) set('stat-revenue',   '$'+parseFloat(d.revenue_today).toLocaleString('en-US',{minimumFractionDigits:2}));
        if(d.revenue_month  != null) set('stat-month',     'Month: $'+parseFloat(d.revenue_month).toLocaleString('en-US',{minimumFractionDigits:2}));
        if(d.open_work_orders != null) set('stat-wo',      d.open_work_orders);
        if(d.open_disputes    != null) set('stat-disputes',d.open_disputes);
      }).catch(function(){});
  }
  function set(id,v){ var e=document.getElementById(id); if(e&&v!==undefined) e.textContent=v; }
})();
</script>
<?php $content = ob_get_clean(); require VIEW_PATH . '/layouts/main.php'; ?>
