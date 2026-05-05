<?php $pageTitle = 'Edit Staff Member'; ?>
<?php ob_start(); ?>

<style>
  .page-header { border-bottom:2px solid var(--accent); padding-bottom:.75rem; margin-bottom:1.5rem; }
  .page-header h2 { color:var(--dark); font-weight:700; }
  .form-card { background:#fff; border:1px solid #e8d5c0; border-radius:12px; box-shadow:0 2px 8px rgba(192,133,82,.09); overflow:hidden; }
  .form-card .card-head { background:var(--dark); color:#fff; padding:1rem 1.4rem; font-weight:600; font-size:.95rem; }
  .form-card .card-body { padding:1.6rem 1.4rem; }
  label { color:var(--dark); font-weight:600; font-size:.88rem; }
  .form-control:focus, .form-select:focus { border-color:var(--accent); box-shadow:0 0 0 .2rem rgba(192,133,82,.25); }
  .form-text { font-size:.8rem; color:#aaa; }
  /* Role badges in dropdown */
  .role-badge { font-size:.73rem; padding:.25em .7em; border-radius:20px; font-weight:700; white-space:nowrap; }
  .role-manager         { background:#4b2e2b; color:#fff; }
  .role-front_desk      { background:#c08552; color:#fff; }
  .role-housekeeper     { background:#6a9a6a; color:#fff; }
  .role-revenue_manager { background:#2196a5; color:#fff; }
  /* Staff avatar big */
  .staff-avatar-lg {
    width:64px; height:64px; border-radius:50%;
    display:inline-flex; align-items:center; justify-content:center;
    font-size:22px; font-weight:700; color:#fff;
    border:3px solid #e8d5c0;
  }
  .toggle-active { cursor:pointer; }
</style>

<?php
// Build initials + color
$initials = implode('', array_map(fn($w) => strtoupper($w[0]), array_slice(explode(' ', $user['name']), 0, 2)));
$roleColors = ['manager'=>'#4b2e2b','front_desk'=>'#c08552','housekeeper'=>'#6a9a6a','revenue_manager'=>'#2196a5','guest'=>'#888'];
// Get current role name from the roles list
$currentRoleName = '';
foreach ($roles as $rl) {
    if ((int)$rl['id'] === (int)$user['role_id']) { $currentRoleName = $rl['name']; break; }
}
$avatarBg = $roleColors[$currentRoleName] ?? '#888';
?>

<div class="container py-3" style="max-width:680px;">

  <!-- Header -->
  <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h2><i class="bi bi-person-gear me-2"></i>Edit Staff Member</h2>
    <a href="<?= APP_URL ?>/index.php?url=users/index" class="btn btn-sm btn-outline-secondary">
      <i class="bi bi-arrow-left me-1"></i>Back to Staff
    </a>
  </div>

  <!-- Staff Identity Card -->
  <div class="d-flex align-items-center gap-3 mb-4 p-3" style="background:#fff;border:1px solid #e8d5c0;border-radius:12px;">
    <span class="staff-avatar-lg" style="background:<?= $avatarBg ?>;"><?= $initials ?></span>
    <div>
      <div style="font-size:1.1rem;font-weight:700;color:var(--dark);"><?= htmlspecialchars($user['name']) ?></div>
      <div style="font-size:.85rem;color:#888;"><?= htmlspecialchars($user['email']) ?></div>
      <span class="role-badge role-<?= $currentRoleName ?>"><?= ucwords(str_replace('_',' ',$currentRoleName)) ?></span>
      <span class="ms-2" style="font-size:.8rem;color:<?= $user['is_active'] ? '#3a8a3a' : '#c04040' ?>;">
        <i class="bi bi-<?= $user['is_active'] ? 'check-circle' : 'x-circle' ?> me-1"></i>
        <?= $user['is_active'] ? 'Active' : 'Inactive' ?>
      </span>
    </div>
  </div>

  <!-- Errors -->
  <?php if (!empty($errors)): ?>
    <div class="alert alert-danger py-2">
      <i class="bi bi-exclamation-triangle me-2"></i>
      <?php foreach ($errors as $err): ?><?= htmlspecialchars($err) ?> <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <!-- Edit Form -->
  <div class="form-card">
    <div class="card-head"><i class="bi bi-pencil-square me-2"></i>Update Information</div>
    <div class="card-body">
      <form method="POST" action="<?= APP_URL ?>/index.php?url=users/update/<?= $user['id'] ?>">

        <div class="row g-3">

          <!-- Name -->
          <div class="col-md-6">
            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
            <input type="text" id="name" name="name" class="form-control <?= isset($errors['name'])?'is-invalid':'' ?>"
                   value="<?= htmlspecialchars($user['name']) ?>" required>
            <?php if (isset($errors['name'])): ?>
              <div class="invalid-feedback"><?= htmlspecialchars($errors['name']) ?></div>
            <?php endif; ?>
          </div>

          <!-- Email -->
          <div class="col-md-6">
            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
            <input type="email" id="email" name="email" class="form-control <?= isset($errors['email'])?'is-invalid':'' ?>"
                   value="<?= htmlspecialchars($user['email']) ?>" required>
            <?php if (isset($errors['email'])): ?>
              <div class="invalid-feedback"><?= htmlspecialchars($errors['email']) ?></div>
            <?php endif; ?>
          </div>

          <!-- Role -->
          <div class="col-md-6">
            <label for="role_id" class="form-label">Role / Position <span class="text-danger">*</span></label>
            <select id="role_id" name="role_id" class="form-select <?= isset($errors['role_id'])?'is-invalid':'' ?>" required>
              <?php foreach ($roles as $role): ?>
                <?php if ($role['name'] === 'guest') continue; // staff only ?>
                <option value="<?= $role['id'] ?>" <?= ((int)$role['id'] === (int)$user['role_id']) ? 'selected' : '' ?>>
                  <?= ucwords(str_replace('_', ' ', $role['name'])) ?>
                </option>
              <?php endforeach; ?>
            </select>
            <?php if (isset($errors['role_id'])): ?>
              <div class="invalid-feedback"><?= htmlspecialchars($errors['role_id']) ?></div>
            <?php endif; ?>
          </div>

          <!-- Active Status -->
          <div class="col-md-6">
            <label class="form-label d-block">Account Status</label>
            <div class="form-check form-switch mt-2">
              <input class="form-check-input toggle-active" type="checkbox" id="is_active"
                     name="is_active" style="width:2.5em;height:1.3em;"
                     <?= $user['is_active'] ? 'checked' : '' ?>
                     <?= ($user['id'] == ($_SESSION['user_id'] ?? 0)) ? 'disabled title="Cannot deactivate your own account"' : '' ?>>
              <label class="form-check-label ms-2" for="is_active" id="activeLabel">
                <span style="color:<?= $user['is_active'] ? '#3a8a3a' : '#c04040' ?>;font-weight:600;">
                  <?= $user['is_active'] ? 'Active' : 'Inactive' ?>
                </span>
              </label>
            </div>
          </div>

          <!-- New Password (optional) -->
          <div class="col-12">
            <label for="password" class="form-label">New Password <span class="text-muted fw-normal">(leave blank to keep current)</span></label>
            <input type="password" id="password" name="password"
                   class="form-control <?= isset($errors['password'])?'is-invalid':'' ?>"
                   placeholder="Min. 6 characters" autocomplete="new-password">
            <?php if (isset($errors['password'])): ?>
              <div class="invalid-feedback"><?= htmlspecialchars($errors['password']) ?></div>
            <?php else: ?>
              <div class="form-text">Only fill this if you want to change the password.</div>
            <?php endif; ?>
          </div>

        </div>

        <!-- Buttons -->
        <div class="d-flex gap-3 mt-4">
          <button type="submit" class="btn btn-primary px-4">
            <i class="bi bi-check-lg me-1"></i>Save Changes
          </button>
          <a href="<?= APP_URL ?>/index.php?url=users/index" class="btn btn-outline-secondary">
            <i class="bi bi-x me-1"></i>Cancel
          </a>
        </div>

      </form>
    </div>
  </div>

</div>

<script>
// Live-update the status label when toggle changes
document.getElementById('is_active')?.addEventListener('change', function() {
    const label = document.getElementById('activeLabel');
    if (this.checked) {
        label.innerHTML = '<span style="color:#3a8a3a;font-weight:600;"><i class="bi bi-check-circle me-1"></i>Active</span>';
    } else {
        label.innerHTML = '<span style="color:#c04040;font-weight:600;"><i class="bi bi-x-circle me-1"></i>Inactive</span>';
    }
});
</script>

<?php $content = ob_get_clean(); ?>
<?php require VIEW_PATH . '/layouts/main.php'; ?>
