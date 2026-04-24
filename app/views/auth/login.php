<?php
$pageTitle = 'Login';
$errors = $errors ?? [];
$old = $old ?? [];
$message = $message ?? null;
$email = htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8');

ob_start();
?>

<div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
  <div class="card shadow-lg" style="width: 100%; max-width: 420px; border-radius: 15px;">
    <div class="card-body p-5">
      <h2 class="card-title text-center fw-bold mb-4">Login</h2>
      <p class="text-muted text-center mb-4">Please enter your credentials</p>

      <?php if (!empty($message)): ?>
        <div class="alert alert-danger d-flex align-items-center" role="alert">
          <i class="bi bi-exclamation-triangle-fill me-2"></i>
          <div><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
        </div>
      <?php endif; ?>

      <form action="<?= APP_URL ?>/?url=auth/doLogin" method="POST">
        <div class="mb-3">
          <label for="email" class="form-label fw-semibold">Email Address</label>
          <input
            type="email"
            name="email"
            id="email"
            class="form-control <?= !empty($errors['email']) ? 'is-invalid' : '' ?>"
            placeholder="name@example.com"
            value="<?= $email ?>"
            autocomplete="username"
            required
            autofocus>
          <?php if (!empty($errors['email'])): ?>
            <div class="invalid-feedback"><?= htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8') ?></div>
          <?php endif; ?>
        </div>

        <div class="mb-4">
          <label for="password" class="form-label fw-semibold">Password</label>
          <input
            type="password"
            name="password"
            id="password"
            class="form-control <?= !empty($errors['password']) ? 'is-invalid' : '' ?>"
            placeholder="..........."
            required>
          <?php if (!empty($errors['password'])): ?>
            <div class="invalid-feedback"><?= htmlspecialchars($errors['password'], ENT_QUOTES, 'UTF-8') ?></div>
          <?php endif; ?>
        </div>

        <div class="d-grid gap-2">
          <button type="submit" class="btn btn-primary btn-lg shadow-sm">Sign In</button>
        </div>
      </form>
    </div>
    <div class="card-footer text-center py-3 bg-light" style="border-bottom-left-radius: 15px; border-bottom-right-radius: 15px;">
      <small class="text-muted">Please contact the administrator if you need a staff account.</small>
    </div>
  </div>
</div>

<?php
$content = ob_get_clean();
require VIEW_PATH . '/layouts/main.php';
?>