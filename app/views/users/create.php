<?php $pageTitle = 'Add Staff Member'; ?>
<?php ob_start(); ?>

<style>
  .page-header { border-bottom:2px solid var(--accent); padding-bottom:.75rem; margin-bottom:1.5rem; }
  .page-header h2 { color:var(--dark); font-weight:700; }
  .form-card { background:#fff; border:1px solid #e8d5c0; border-radius:12px; box-shadow:0 2px 8px rgba(192,133,82,.09); overflow:hidden; }
  .form-card .card-head { background:var(--dark); color:#fff; padding:1rem 1.4rem; font-weight:600; font-size:.95rem; }
  .form-card .card-body { padding:1.8rem 1.4rem; }
  label.form-label { color:var(--dark); font-weight:600; font-size:.88rem; }
  .form-control:focus, .form-select:focus { border-color:var(--accent); box-shadow:0 0 0 .2rem rgba(192,133,82,.25); }
  .form-text { font-size:.8rem; color:#aaa; }
  .required-star { color:#c04040; }
  /* Role option preview badge */
  .role-preview { display:inline-flex; align-items:center; gap:.5rem; margin-top:.4rem; min-height:1.4rem; }
  .role-dot { width:10px; height:10px; border-radius:50%; flex-shrink:0; }
  /* Info card */
  .info-card { background:#fff8f0; border:1px solid #e8d5c0; border-radius:10px; padding:1.1rem 1.2rem; }
  .info-card h6 { color:var(--dark); font-weight:700; margin-bottom:.6rem; }
  .info-card li { font-size:.84rem; color:#666; margin-bottom:.25rem; }
  .role-icon { width:28px; height:28px; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; color:#fff; font-size:.75rem; flex-shrink:0; }
</style>

<div class="container py-3" style="max-width:800px;">

  <!-- Header -->
  <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h2><i class="bi bi-person-plus-fill me-2"></i>Add Staff Member</h2>
    <a href="<?= APP_URL ?>/index.php?url=users/index" class="btn btn-sm btn-outline-secondary">
      <i class="bi bi-arrow-left me-1"></i>Back to Staff
    </a>
  </div>

  <!-- Error Alert -->
  <?php if (!empty($errors)): ?>
    <div class="alert alert-danger py-2 mb-3">
      <i class="bi bi-exclamation-triangle me-2"></i>
      <strong>Please fix the following:</strong>
      <ul class="mb-0 mt-1">
        <?php foreach ($errors as $err): ?>
          <li><?= htmlspecialchars($err) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <div class="row g-4">

    <!-- Main Form -->
    <div class="col-md-8">
      <div class="form-card">
        <div class="card-head"><i class="bi bi-person-badge me-2"></i>Staff Information</div>
        <div class="card-body">
          <form method="POST" action="<?= APP_URL ?>/index.php?url=users/store" id="createStaffForm">

            <div class="row g-3">

              <!-- Name -->
              <div class="col-12">
                <label for="name" class="form-label">
                  Full Name <span class="required-star">*</span>
                </label>
                <div class="input-group">
                  <span class="input-group-text" style="background:#f8f0e8;border-color:#e8d5c0;">
                    <i class="bi bi-person" style="color:var(--accent);"></i>
                  </span>
                  <input type="text" id="name" name="name"
                         class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                         value="<?= htmlspecialchars($old['name'] ?? '') ?>"
                         placeholder="e.g. Ahmed Hassan"
                         required autofocus>
                  <?php if (isset($errors['name'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['name']) ?></div>
                  <?php endif; ?>
                </div>
              </div>

              <!-- Email -->
              <div class="col-12">
                <label for="email" class="form-label">
                  Email Address <span class="required-star">*</span>
                </label>
                <div class="input-group">
                  <span class="input-group-text" style="background:#f8f0e8;border-color:#e8d5c0;">
                    <i class="bi bi-envelope" style="color:var(--accent);"></i>
                  </span>
                  <input type="email" id="email" name="email"
                         class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                         value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                         placeholder="e.g. ahmed.hassan@grandhotel.com"
                         required>
                  <?php if (isset($errors['email'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['email']) ?></div>
                  <?php endif; ?>
                </div>
              </div>

              <!-- Password -->
              <div class="col-md-6">
                <label for="password" class="form-label">
                  Password <span class="required-star">*</span>
                </label>
                <div class="input-group">
                  <span class="input-group-text" style="background:#f8f0e8;border-color:#e8d5c0;">
                    <i class="bi bi-lock" style="color:var(--accent);"></i>
                  </span>
                  <input type="password" id="password" name="password"
                         class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                         placeholder="Min. 6 characters" required
                         autocomplete="new-password">
                  <button class="btn" type="button" id="togglePwd"
                          style="background:#f8f0e8;border-color:#e8d5c0;"
                          title="Show/hide password">
                    <i class="bi bi-eye" id="pwdIcon" style="color:var(--accent);"></i>
                  </button>
                  <?php if (isset($errors['password'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['password']) ?></div>
                  <?php endif; ?>
                </div>
                <div class="form-text">Minimum 6 characters.</div>
              </div>

              <!-- Role -->
              <div class="col-md-6">
                <label for="role_id" class="form-label">
                  Role / Position <span class="required-star">*</span>
                </label>
                <div class="input-group">
                  <span class="input-group-text" style="background:#f8f0e8;border-color:#e8d5c0;">
                    <i class="bi bi-briefcase" style="color:var(--accent);"></i>
                  </span>
                  <select id="role_id" name="role_id"
                          class="form-select <?= isset($errors['role_id']) ? 'is-invalid' : '' ?>"
                          required>
                    <option value="">-- Select Role --</option>
                    <?php
                    $roleColors = [
                      'manager'         => '#4b2e2b',
                      'front_desk'      => '#c08552',
                      'housekeeper'     => '#6a9a6a',
                      'revenue_manager' => '#2196a5',
                    ];
                    foreach ($roles as $role):
                      if ($role['name'] === 'guest') continue;
                      $selected = (isset($old['role_id']) && (int)$old['role_id'] === (int)$role['id']) ? 'selected' : '';
                    ?>
                      <option value="<?= $role['id'] ?>" <?= $selected ?>>
                        <?= ucwords(str_replace('_', ' ', $role['name'])) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                  <?php if (isset($errors['role_id'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['role_id']) ?></div>
                  <?php endif; ?>
                </div>
                <!-- Live role colour preview -->
                <div class="role-preview" id="rolePreview"></div>
              </div>

            </div>

            <!-- Submit Buttons -->
            <div class="d-flex gap-3 mt-4 pt-2" style="border-top:1px solid #f0e4d0;">
              <button type="submit" class="btn btn-primary px-4">
                <i class="bi bi-person-plus me-1"></i>Create Staff Member
              </button>
              <a href="<?= APP_URL ?>/index.php?url=users/index" class="btn btn-outline-secondary">
                <i class="bi bi-x me-1"></i>Cancel
              </a>
            </div>

          </form>
        </div>
      </div>
    </div>

    <!-- Sidebar Info -->
    <div class="col-md-4">
      <div class="info-card">
        <h6><i class="bi bi-info-circle me-2" style="color:var(--accent);"></i>Role Guide</h6>
        <ul class="list-unstyled">
          <?php
          $roleGuide = [
            ['name'=>'manager',         'label'=>'Manager',          'color'=>'#4b2e2b', 'desc'=>'Full system access, staff & reports'],
            ['name'=>'front_desk',      'label'=>'Front Desk',       'color'=>'#c08552', 'desc'=>'Reservations, check-in/out, billing'],
            ['name'=>'housekeeper',     'label'=>'Housekeeper',      'color'=>'#6a9a6a', 'desc'=>'Cleaning tasks, room status'],
            ['name'=>'revenue_manager', 'label'=>'Revenue Manager',  'color'=>'#2196a5', 'desc'=>'Pricing, forecasting, analytics'],
          ];
          foreach ($roleGuide as $rg):
          ?>
          <li class="d-flex align-items-start gap-2 mb-2">
            <span class="role-icon" style="background:<?= $rg['color'] ?>; margin-top:.1rem;">
              <i class="bi bi-briefcase"></i>
            </span>
            <div>
              <div style="font-weight:600;color:var(--dark);font-size:.84rem;"><?= $rg['label'] ?></div>
              <div style="font-size:.78rem;color:#888;"><?= $rg['desc'] ?></div>
            </div>
          </li>
          <?php endforeach; ?>
        </ul>

        <div style="margin-top:1rem;padding-top:.8rem;border-top:1px solid #e8d5c0;">
          <div style="font-size:.82rem;color:#888;">
            <i class="bi bi-shield-check me-1" style="color:var(--accent);"></i>
            Password is stored securely (SHA-256 hashed).
          </div>
        </div>
      </div>
    </div>

  </div><!-- /row -->

</div>

<script>
// ── Show/hide password ──
document.getElementById('togglePwd')?.addEventListener('click', function() {
    const pwd  = document.getElementById('password');
    const icon = document.getElementById('pwdIcon');
    if (pwd.type === 'password') {
        pwd.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        pwd.type = 'password';
        icon.className = 'bi bi-eye';
    }
});

// ── Live role colour preview ──
const roleColors = {
    <?php foreach ($roles as $role): if ($role['name'] === 'guest') continue; ?>
    '<?= $role['id'] ?>': { label:'<?= ucwords(str_replace('_',' ',$role['name'])) ?>', color:'<?= $roleColors[$role['name']] ?? '#888' ?>' },
    <?php endforeach; ?>
};

document.getElementById('role_id')?.addEventListener('change', function() {
    const preview = document.getElementById('rolePreview');
    const val = this.value;
    if (val && roleColors[val]) {
        const r = roleColors[val];
        preview.innerHTML = `<span class="role-dot" style="background:${r.color};"></span>
                             <span style="font-size:.8rem;color:${r.color};font-weight:600;">${r.label}</span>`;
    } else {
        preview.innerHTML = '';
    }
});
</script>

<?php $content = ob_get_clean(); ?>
<?php require VIEW_PATH . '/layouts/main.php'; ?>
