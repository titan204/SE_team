<?php $pageTitle = 'All Reservations'; ?>
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
  .btn-accent { background-color: var(--accent); color: #fff; border: none; }
  .btn-accent:hover { background-color: var(--accent2); color: #fff; }
  .btn-dark-custom { background-color: var(--dark); color: #fff; border: none; }
  .btn-dark-custom:hover { background-color: var(--accent2); color: #fff; }
  .filter-card { background: #fff; border: 1px solid #e8d5c0; border-radius: 10px; padding: 1.2rem 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 2px 8px rgba(192,133,82,.08); }
  .table-card  { background: #fff; border: 1px solid #e8d5c0; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 8px rgba(192,133,82,.08); }
  .table thead { background-color: var(--dark); color: #fff; }
  .table tbody tr:hover { background-color: #fff3e8; }
  .badge-pending     { background-color: #f0a500; color:#fff; }
  .badge-confirmed   { background-color: #2196a5; color:#fff; }
  .badge-checked_in  { background-color: #3a8a3a; color:#fff; }
  .badge-checked_out { background-color: var(--accent2); color:#fff; }
  .badge-cancelled   { background-color: #9e3030; color:#fff; }
  .badge-no_show     { background-color: #555; color:#fff; }
  .status-badge { font-size:.78rem; padding:.3em .7em; border-radius:20px; font-weight:600; }
  .action-btn { font-size:.78rem; padding:.25em .6em; border-radius:6px; margin:1px; }
  .table td, .table th { vertical-align: middle; }
  label { color: var(--dark); font-weight: 600; font-size: .88rem; }
  .form-control:focus, .form-select:focus { border-color: var(--accent); box-shadow: 0 0 0 .2rem rgba(192,133,82,.25); }
  .empty-state { text-align:center; padding: 3rem 1rem; color: #a08060; }
  .empty-state i { font-size: 3rem; opacity:.4; }
</style>

<div class="container-fluid py-3">

  <!-- Page Header -->
  <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h2><i class="bi bi-calendar-check me-2"></i>Reservations</h2>
    <a href="<?= APP_URL ?>/index.php?url=reservations/create"
       class="btn btn-accent px-4 py-2" style="border-radius:8px;">
      <i class="bi bi-plus-circle me-1"></i> New Reservation
    </a>
  </div>

  <!-- Filter Card -->
  <div class="filter-card">
    <form method="GET" action="<?= APP_URL ?>/index.php" class="row g-3 align-items-end">
      <input type="hidden" name="url" value="reservations">

      <div class="col-md-3">
        <label for="guest_name">Guest Name</label>
        <input id="guest_name" type="text" name="guest_name" class="form-control"
               placeholder="Search guest…"
               value="<?= htmlspecialchars($filters['guest_name'] ?? '') ?>">
      </div>

      <div class="col-md-2">
        <label for="status_filter">Status</label>
        <select id="status_filter" name="status" class="form-select">
          <option value="">All Statuses</option>
          <?php
          $statuses = ['pending','confirmed','checked_in','checked_out','cancelled','no_show'];
          foreach ($statuses as $s):
            $sel = (($filters['status'] ?? '') === $s) ? 'selected' : '';
          ?>
          <option value="<?= $s ?>" <?= $sel ?>><?= ucwords(str_replace('_',' ',$s)) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-md-2">
        <label for="date_from">Check-in From</label>
        <input id="date_from" type="date" name="date_from" class="form-control"
               value="<?= htmlspecialchars($filters['date_from'] ?? '') ?>">
      </div>

      <div class="col-md-2">
        <label for="date_to">Check-out To</label>
        <input id="date_to" type="date" name="date_to" class="form-control"
               value="<?= htmlspecialchars($filters['date_to'] ?? '') ?>">
      </div>

      <div class="col-md-3 d-flex gap-2">
        <button type="submit" class="btn btn-accent w-100" style="border-radius:7px;">
          <i class="bi bi-search me-1"></i> Filter
        </button>
        <a href="<?= APP_URL ?>/index.php?url=reservations"
           class="btn btn-outline-secondary w-100" style="border-radius:7px;">
          <i class="bi bi-x-circle me-1"></i> Clear
        </a>
      </div>
    </form>
  </div>

  <!-- Reservations Table -->
  <div class="table-card">
    <?php if (empty($reservations)): ?>
      <div class="empty-state">
        <i class="bi bi-calendar-x d-block mb-3"></i>
        <p class="mb-0">No reservations found.</p>
      </div>
    <?php else: ?>
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead>
          <tr>
            <th>#</th>
            <th>Guest</th>
            <th>Room</th>
            <th>Check-In</th>
            <th>Check-Out</th>
            <th>Nights</th>
            <th>Total</th>
            <th>Status</th>
            <th style="min-width:200px;">Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($reservations as $r):
          $nights = (new DateTime($r['check_in_date']))->diff(new DateTime($r['check_out_date']))->days;
          $statusClass = 'badge-' . $r['status'];
          $statusLabel = ucwords(str_replace('_',' ', $r['status']));
          $rid = $r['id'];
        ?>
          <tr>
            <td><strong style="color:var(--dark);">#<?= $rid ?></strong>
              <?php if (!empty($r['is_group'])): ?>
                <span class="badge bg-secondary ms-1" style="font-size:.7rem;">Group</span>
              <?php endif; ?>
              <?php if (!empty($r['is_vip'])): ?>
                <span class="badge ms-1" style="font-size:.7rem;background:#c09800;">VIP</span>
              <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($r['guest_name']) ?></td>
            <td>
              <span style="font-weight:600;color:var(--dark);"><?= htmlspecialchars($r['room_number']) ?></span>
              <small class="text-muted d-block"><?= htmlspecialchars($r['room_type_name']) ?></small>
            </td>
            <td><?= htmlspecialchars($r['check_in_date']) ?></td>
            <td><?= htmlspecialchars($r['check_out_date']) ?></td>
            <td><?= $nights ?></td>
            <td><strong>$<?= number_format($r['total_price'], 2) ?></strong></td>
            <td>
              <span class="status-badge <?= $statusClass ?>">
                <?= $statusLabel ?>
              </span>
            </td>
            <td>
              <!-- View -->
              <a href="<?= APP_URL ?>/index.php?url=reservations/show/<?= $rid ?>"
                 class="btn btn-sm action-btn" style="background:#e8d5c0;color:var(--dark);">
                <i class="bi bi-eye"></i>
              </a>

              <!-- Edit (pending/confirmed only) -->
              <?php if (in_array($r['status'], ['pending','confirmed'])): ?>
              <a href="<?= APP_URL ?>/index.php?url=reservations/edit/<?= $rid ?>"
                 class="btn btn-sm action-btn" style="background:#d4e8c0;color:#2a5a10;">
                <i class="bi bi-pencil"></i>
              </a>
              <?php endif; ?>

              <!-- Confirm (pending only) -->
              <?php if ($r['status'] === 'pending'): ?>
              <a href="<?= APP_URL ?>/index.php?url=reservations/confirm/<?= $rid ?>"
                 class="btn btn-sm action-btn" style="background:#c0d8e8;color:#1a4a6a;"
                 onclick="return confirm('Confirm reservation #<?= $rid ?>?')">
                <i class="bi bi-check-circle"></i>
              </a>
              <?php endif; ?>

              <!-- Check-In (pending/confirmed) -->
              <?php if (in_array($r['status'], ['pending','confirmed'])): ?>
              <a href="<?= APP_URL ?>/index.php?url=reservations/checkin/<?= $rid ?>"
                 class="btn btn-sm action-btn btn-accent"
                 onclick="return confirm('Check in reservation #<?= $rid ?>?')">
                <i class="bi bi-box-arrow-in-right"></i>
              </a>
              <?php endif; ?>

              <!-- Check-Out (checked_in only) -->
              <?php if ($r['status'] === 'checked_in'): ?>
              <a href="<?= APP_URL ?>/index.php?url=reservations/checkout/<?= $rid ?>"
                 class="btn btn-sm action-btn" style="background:var(--accent2);color:#fff;"
                 onclick="return confirm('Check out reservation #<?= $rid ?>?')">
                <i class="bi bi-box-arrow-right"></i>
              </a>
              <?php endif; ?>

              <!-- Cancel (not checked_in/out) -->
              <?php if (!in_array($r['status'], ['checked_in','checked_out','cancelled','no_show'])): ?>
              <a href="<?= APP_URL ?>/index.php?url=reservations/delete/<?= $rid ?>"
                 class="btn btn-sm action-btn" style="background:#e8c0c0;color:#7a1a1a;"
                 onclick="return confirm('Cancel reservation #<?= $rid ?>?')">
                <i class="bi bi-x-circle"></i>
              </a>
              <?php endif; ?>

              <!-- No-Show (pending/confirmed) -->
              <?php if (in_array($r['status'], ['pending','confirmed'])): ?>
              <a href="<?= APP_URL ?>/index.php?url=reservations/noshow/<?= $rid ?>"
                 class="btn btn-sm action-btn" style="background:#ddd;color:#444;"
                 onclick="return confirm('Mark reservation #<?= $rid ?> as No-Show?')">
                <i class="bi bi-person-slash"></i>
              </a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <div class="px-3 py-2" style="color:var(--accent2);font-size:.85rem;">
      Showing <?= count($reservations) ?> reservation(s)
    </div>
    <?php endif; ?>
  </div>

</div>

<?php $content = ob_get_clean(); ?>
<?php require VIEW_PATH . '/layouts/main.php'; ?>
