<?php $pageTitle = 'Staff Management'; ?>
<?php ob_start(); ?>

<style>
  .page-header { border-bottom:2px solid var(--accent); padding-bottom:.75rem; margin-bottom:1.5rem; }
  .page-header h2 { color:var(--dark); font-weight:700; }
  .stat-card { background:#fff; border:1px solid #e8d5c0; border-radius:12px; padding:1.1rem 1rem; text-align:center; box-shadow:0 2px 8px rgba(192,133,82,.09); }
  .stat-card .stat-val { font-size:1.8rem; font-weight:700; color:var(--dark); }
  .stat-card .stat-lbl { font-size:.8rem; color:#888; margin-top:.1rem; }
  .filter-card { background:#fff; border:1px solid #e8d5c0; border-radius:10px; padding:1rem 1.2rem; margin-bottom:1.2rem; box-shadow:0 1px 4px rgba(192,133,82,.07); }
  .table-card  { background:#fff; border:1px solid #e8d5c0; border-radius:10px; overflow:hidden; box-shadow:0 2px 8px rgba(192,133,82,.08); }
  .table th    { background-color:var(--dark); color:#fff; font-weight:600; font-size:.85rem; }
  .table tbody tr:hover { background:#fff3e8; }
  .table td, .table th { vertical-align:middle; }
  label { color:var(--dark); font-weight:600; font-size:.88rem; }
  .form-control:focus, .form-select:focus { border-color:var(--accent); box-shadow:0 0 0 .2rem rgba(192,133,82,.25); }
  /* Role badges */
  .role-badge { font-size:.73rem; padding:.25em .7em; border-radius:20px; font-weight:700; white-space:nowrap; }
  .role-manager         { background:#4b2e2b; color:#fff; }
  .role-front_desk      { background:#c08552; color:#fff; }
  .role-housekeeper     { background:#6a9a6a; color:#fff; }
  .role-revenue_manager { background:#2196a5; color:#fff; }
  .role-guest           { background:#aaa;    color:#fff; }
  /* Active badge */
  .active-yes { background:#3a8a3a; color:#fff; font-size:.73rem; padding:.2em .6em; border-radius:20px; font-weight:600; }
  .active-no  { background:#c04040; color:#fff; font-size:.73rem; padding:.2em .6em; border-radius:20px; font-weight:600; }
  /* Avatar */
  .staff-avatar {
    width:36px; height:36px; border-radius:50%;
    display:inline-flex; align-items:center; justify-content:center;
    font-size:14px; font-weight:700; color:#fff; flex-shrink:0;
    border:2px solid rgba(255,255,255,.3);
  }
  .action-btn { font-size:.76rem; padding:.22em .55em; border-radius:6px; margin:1px; }
  .empty-state { text-align:center; padding:2.5rem 1rem; color:#a08060; }
  .empty-state i { font-size:2.5rem; opacity:.4; }
</style>

<div class="container-fluid py-3">

  <!-- Header -->
  <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h2><i class="bi bi-people-fill me-2"></i>Staff Management</h2>
    <a href="<?= APP_URL ?>/index.php?url=users/create" class="btn btn-primary">
      <i class="bi bi-person-plus me-1"></i>Add Staff Member
    </a>
  </div>

  <!-- Flash Message -->
  <?php if ($message): ?>
    <div class="alert alert-success alert-dismissible fade show py-2">
      <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($message) ?>
      <button class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <!-- Summary Cards -->
  <div class="row g-3 mb-4">
    <?php
    $roleCards = [
      ['key'=>'manager',         'label'=>'Managers',         'icon'=>'bi-person-gear',     'color'=>'#4b2e2b'],
      ['key'=>'front_desk',      'label'=>'Front Desk',        'icon'=>'bi-door-open',       'color'=>'#c08552'],
      ['key'=>'housekeeper',     'label'=>'Housekeepers',      'icon'=>'bi-brush',           'color'=>'#6a9a6a'],
      ['key'=>'revenue_manager', 'label'=>'Revenue Managers',  'icon'=>'bi-graph-up',        'color'=>'#2196a5'],
    ];
    foreach ($roleCards as $rc):
      $cnt = $roleCounts[$rc['key']] ?? 0;
    ?>
    <div class="col-6 col-md-2">
      <a href="<?= APP_URL ?>/index.php?url=users/index&role=<?= $rc['key'] ?>" class="text-decoration-none">
        <div class="stat-card h-100">
          <i class="bi <?= $rc['icon'] ?>" style="font-size:1.4rem;color:<?= $rc['color'] ?>;"></i>
          <div class="stat-val" style="color:<?= $rc['color'] ?>;"><?= $cnt ?></div>
          <div class="stat-lbl"><?= $rc['label'] ?></div>
        </div>
      </a>
    </div>
    <?php endforeach; ?>
    <!-- Total -->
    <div class="col-6 col-md-2">
      <div class="stat-card h-100">
        <i class="bi bi-people" style="font-size:1.4rem;color:var(--dark);"></i>
        <div class="stat-val"><?= array_sum($roleCounts) ?></div>
        <div class="stat-lbl">Total Accounts</div>
      </div>
    </div>
  </div>

  <!-- Filter -->
  <div class="filter-card">
    <form method="GET" action="<?= APP_URL ?>/index.php" class="row g-2 align-items-end">
      <input type="hidden" name="url" value="users/index">
      <div class="col-md-3">
        <label>Filter by Role</label>
        <select name="role" class="form-select form-select-sm">
          <option value="">All Roles</option>
          <?php foreach (['manager','front_desk','housekeeper','revenue_manager'] as $rl): ?>
          <option value="<?= $rl ?>" <?= ($roleFilter===$rl)?'selected':'' ?>><?= ucwords(str_replace('_',' ',$rl)) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3 d-flex gap-2">
        <button class="btn btn-sm btn-primary"><i class="bi bi-funnel me-1"></i>Filter</button>
        <a href="<?= APP_URL ?>/index.php?url=users/index" class="btn btn-sm btn-outline-secondary"><i class="bi bi-x me-1"></i>Clear</a>
      </div>
      <?php if ($roleFilter): ?>
      <div class="col-md-6 d-flex align-items-end">
        <span class="text-muted" style="font-size:.85rem;">
          Showing <strong><?= count($users) ?></strong> <?= ucwords(str_replace('_',' ',$roleFilter)) ?>(s)
        </span>
      </div>
      <?php endif; ?>
    </form>
  </div>

  <!-- Staff Table -->
  <div class="table-card">
    <?php if (empty($users)): ?>
      <div class="empty-state">
        <i class="bi bi-people d-block mb-2"></i>
        <p class="mb-0">No staff found<?= $roleFilter ? ' for role "'.ucwords(str_replace('_',' ',$roleFilter)).'"' : '' ?>.</p>
      </div>
    <?php else: ?>
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead>
          <tr>
            <th>#</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role / Position</th>
            <th>Status</th>
            <th>Member Since</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php
        // Avatar background colors per role
        $avatarColors = [
          'manager'         => '#4b2e2b',
          'front_desk'      => '#c08552',
          'housekeeper'     => '#6a9a6a',
          'revenue_manager' => '#2196a5',
          'guest'           => '#888',
        ];
        foreach ($users as $u):
          $initials = implode('', array_map(fn($w) => strtoupper($w[0]), array_slice(explode(' ', $u['name']), 0, 2)));
          $avatarBg = $avatarColors[$u['role']] ?? '#888';
          $roleLabel = ucwords(str_replace('_', ' ', $u['role']));
        ?>
          <tr>
            <td><strong style="color:var(--dark);">#<?= $u['id'] ?></strong></td>
            <td>
              <div class="d-flex align-items-center gap-2">
                <span class="staff-avatar" style="background:<?= $avatarBg ?>;"><?= $initials ?></span>
                <span style="font-weight:600;color:var(--dark);"><?= htmlspecialchars($u['name']) ?></span>
              </div>
            </td>
            <td style="font-size:.88rem;"><?= htmlspecialchars($u['email']) ?></td>
            <td>
              <span class="role-badge role-<?= $u['role'] ?>"><?= $roleLabel ?></span>
            </td>
            <td>
              <?php if ($u['is_active']): ?>
                <span class="active-yes"><i class="bi bi-check-circle me-1"></i>Active</span>
              <?php else: ?>
                <span class="active-no"><i class="bi bi-x-circle me-1"></i>Inactive</span>
              <?php endif; ?>
            </td>
            <td style="font-size:.83rem;color:#888;"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
            <td>
              <a href="<?= APP_URL ?>/index.php?url=users/edit/<?= $u['id'] ?>"
                 class="btn btn-sm action-btn" style="background:#e8d5c0;color:var(--dark);"
                 title="Edit">
                <i class="bi bi-pencil"></i>
              </a>
              <?php if ($u['is_active'] && $u['id'] != ($_SESSION['user_id'] ?? 0)): ?>
                <a href="<?= APP_URL ?>/index.php?url=users/delete/<?= $u['id'] ?>"
                   class="btn btn-sm action-btn" style="background:#e8c0c0;color:#7a1a1a;"
                   onclick="return confirm('Deactivate <?= htmlspecialchars(addslashes($u['name'])) ?>?')"
                   title="Deactivate">
                  <i class="bi bi-person-dash"></i>
                </a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <div class="px-3 py-2" style="color:var(--accent2);font-size:.85rem;">
      Showing <?= count($users) ?> account(s)
      <?php if ($roleFilter): ?> &nbsp;·&nbsp; Role: <strong><?= ucwords(str_replace('_',' ',$roleFilter)) ?></strong><?php endif; ?>
    </div>
    <?php endif; ?>
  </div>

</div>

<?php $content = ob_get_clean(); ?>
<?php require VIEW_PATH . '/layouts/main.php'; ?>
