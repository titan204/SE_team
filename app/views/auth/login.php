<?php
$pageTitle = 'Sign In — Grand Hotel';
$errors  = $errors  ?? [];
$old     = $old     ?? [];
$message = $message ?? null;
$email   = htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8');
ob_start();
?>
<link rel="stylesheet" href="<?= APP_URL ?>/public/assets/css/home.css">
<style>.auth-shell{min-height:calc(100vh - 56px);}</style>

<!-- Luxury Navbar -->
<nav class="hm-nav scrolled" id="authNav">
  <div class="inner">
    <a href="<?= APP_URL ?>/?url=home/index" class="nav-brand"><i class="bi bi-building-fill"></i>Grand Hotel</a>
    <ul class="nav-links">
      <li><a href="<?= APP_URL ?>/?url=home/index">Home</a></li>
      <li><a href="<?= APP_URL ?>/?url=rooms/guest">Rooms</a></li>
      <li><a href="<?= APP_URL ?>/?url=home/index#services">Services</a></li>
      <li><a href="<?= APP_URL ?>/?url=home/index#contact">Contact</a></li>
    </ul>
    <div class="nav-auth">
      <a href="<?= APP_URL ?>/?url=auth/login"    class="btn-nav-login" style="border-color:var(--gold);color:var(--gold)">Sign In</a>
      <a href="<?= APP_URL ?>/?url=auth/register" class="btn-nav-reg">Register</a>
    </div>
    <button class="nav-toggle" id="authNavToggle"><i class="bi bi-list"></i></button>
  </div>
  <div id="authMobileMenu" style="display:none;" class="mobile-menu">
    <a href="<?= APP_URL ?>/?url=home/index">Home</a>
    <a href="<?= APP_URL ?>/?url=rooms/guest">Rooms</a>
    <a href="<?= APP_URL ?>/?url=auth/register">Register</a>
  </div>
</nav>
<script>document.getElementById('authNavToggle').addEventListener('click',function(){var m=document.getElementById('authMobileMenu');m.style.display=m.style.display==='none'?'flex':'none';});</script>


<style>
/* ── Auth Page Shell ────────────────────────────────────────── */
.auth-shell {
  min-height: calc(100vh - 120px);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem 1rem;
  background:
    radial-gradient(ellipse at 20% 50%, rgba(196,135,77,.10) 0%, transparent 60%),
    radial-gradient(ellipse at 80% 20%, rgba(62,35,40,.08)  0%, transparent 50%),
    var(--hms-cream);
}

/* ── Card ───────────────────────────────────────────────────── */
.auth-card {
  width: 100%;
  max-width: 440px;
  background: #fff;
  border-radius: 20px;
  box-shadow:
    0 4px 6px  rgba(62,35,40,.04),
    0 20px 60px rgba(62,35,40,.12);
  overflow: hidden;
  animation: authFadeUp .45s cubic-bezier(.22,.68,0,1.2) both;
}

@keyframes authFadeUp {
  from { opacity:0; transform:translateY(28px) scale(.97); }
  to   { opacity:1; transform:translateY(0)    scale(1);   }
}

/* ── Card Header ────────────────────────────────────────────── */
.auth-header {
  background: linear-gradient(135deg, var(--hms-dark) 0%, #6a3a20 100%);
  padding: 2.2rem 2rem 1.8rem;
  text-align: center;
  position: relative;
  overflow: hidden;
}
.auth-header::before {
  content: '';
  position: absolute; inset: 0;
  background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}
.auth-logo {
  width: 56px; height: 56px;
  background: rgba(196,135,77,.18);
  border: 2px solid rgba(196,135,77,.4);
  border-radius: 16px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 1.6rem;
  color: var(--hms-caramel);
  margin-bottom: 1rem;
  position: relative;
}
.auth-header h1 {
  color: #fff;
  font-size: 1.45rem;
  font-weight: 700;
  margin: 0 0 .3rem;
  letter-spacing: -.02em;
}
.auth-header p {
  color: rgba(255,248,242,.65);
  font-size: .875rem;
  margin: 0;
}

/* ── Card Body ──────────────────────────────────────────────── */
.auth-body {
  padding: 2rem;
}

/* ── Input Groups ───────────────────────────────────────────── */
.auth-field {
  margin-bottom: 1.1rem;
}
.auth-field label {
  display: block;
  font-size: .8rem;
  font-weight: 600;
  color: var(--hms-dark);
  margin-bottom: .35rem;
  letter-spacing: .04em;
  text-transform: uppercase;
}
.input-icon-wrap {
  position: relative;
}
.input-icon-wrap .field-icon {
  position: absolute;
  left: .9rem;
  top: 50%;
  transform: translateY(-50%);
  color: #b0a090;
  font-size: 1rem;
  pointer-events: none;
  transition: color .2s;
}
.input-icon-wrap .field-icon-right {
  position: absolute;
  right: .9rem;
  top: 50%;
  transform: translateY(-50%);
  color: #b0a090;
  font-size: 1rem;
  cursor: pointer;
  transition: color .2s;
  background: none;
  border: none;
  padding: 0;
  line-height: 1;
}
.input-icon-wrap .field-icon-right:hover { color: var(--hms-caramel); }

.auth-input {
  width: 100%;
  padding: .7rem .9rem .7rem 2.6rem;
  border: 1.5px solid #e8ddd4;
  border-radius: 10px;
  font-size: .95rem;
  background: #fdfaf7;
  color: var(--hms-dark);
  transition: border-color .2s, box-shadow .2s, background .2s;
  outline: none;
  -webkit-appearance: none;
}
.auth-input:focus {
  border-color: var(--hms-caramel);
  background: #fff;
  box-shadow: 0 0 0 3px rgba(196,135,77,.15);
}
.auth-input.has-right-icon { padding-right: 2.6rem; }
.auth-input.is-invalid {
  border-color: #dc3545;
  background: #fff8f8;
}
.auth-input.is-invalid:focus {
  box-shadow: 0 0 0 3px rgba(220,53,69,.15);
}
.input-icon-wrap:focus-within .field-icon { color: var(--hms-caramel); }

.field-error {
  font-size: .78rem;
  color: #dc3545;
  margin-top: .3rem;
  display: flex;
  align-items: center;
  gap: .3rem;
}

/* ── Remember + Forgot row ──────────────────────────────────── */
.auth-meta {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 1.4rem;
  font-size: .85rem;
}
.auth-check {
  display: flex;
  align-items: center;
  gap: .45rem;
  cursor: pointer;
}
.auth-check input[type=checkbox] {
  width: 15px; height: 15px;
  accent-color: var(--hms-caramel);
  cursor: pointer;
}
.auth-check span { color: #6b5548; font-weight: 500; }
.auth-forgot {
  color: var(--hms-caramel);
  font-weight: 600;
  text-decoration: none;
  transition: color .18s;
}
.auth-forgot:hover { color: var(--hms-brown); text-decoration: underline; }

/* ── Submit Button ──────────────────────────────────────────── */
.auth-btn {
  width: 100%;
  padding: .8rem;
  border: none;
  border-radius: 10px;
  background: linear-gradient(135deg, var(--hms-caramel) 0%, #a0682e 100%);
  color: #fff;
  font-size: 1rem;
  font-weight: 700;
  letter-spacing: .02em;
  cursor: pointer;
  transition: transform .15s, box-shadow .15s, filter .15s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: .5rem;
  position: relative;
  overflow: hidden;
}
.auth-btn::after {
  content: '';
  position: absolute; inset: 0;
  background: linear-gradient(rgba(255,255,255,.12), transparent);
  pointer-events: none;
}
.auth-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(196,135,77,.4);
  filter: brightness(1.05);
}
.auth-btn:active { transform: translateY(0); }
.auth-btn .spinner {
  display: none;
  width: 18px; height: 18px;
  border: 2.5px solid rgba(255,255,255,.4);
  border-top-color: #fff;
  border-radius: 50%;
  animation: spin .7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }
.auth-btn.loading .spinner    { display: block; }
.auth-btn.loading .btn-text   { display: none; }
.auth-btn.loading { pointer-events: none; filter: brightness(.9); }

/* ── Divider ────────────────────────────────────────────────── */
.auth-divider {
  display: flex;
  align-items: center;
  gap: .75rem;
  margin: 1.3rem 0;
  color: #c4b09a;
  font-size: .8rem;
  font-weight: 500;
}
.auth-divider::before,
.auth-divider::after {
  content: '';
  flex: 1;
  height: 1px;
  background: #eddecf;
}

/* ── Secondary Button ───────────────────────────────────────── */
.auth-btn-outline {
  width: 100%;
  padding: .75rem;
  border: 1.5px solid var(--hms-caramel);
  border-radius: 10px;
  background: transparent;
  color: var(--hms-caramel);
  font-size: .95rem;
  font-weight: 600;
  cursor: pointer;
  text-align: center;
  text-decoration: none;
  display: block;
  transition: background .18s, color .18s, transform .15s;
}
.auth-btn-outline:hover {
  background: var(--hms-caramel);
  color: #fff;
  transform: translateY(-1px);
}

/* ── Alert Banner ───────────────────────────────────────────── */
.auth-alert {
  display: flex;
  align-items: center;
  gap: .6rem;
  padding: .75rem 1rem;
  border-radius: 10px;
  margin-bottom: 1.2rem;
  font-size: .875rem;
  font-weight: 500;
  animation: alertSlide .3s ease both;
}
@keyframes alertSlide {
  from { opacity:0; transform: translateY(-8px); }
  to   { opacity:1; transform: translateY(0); }
}
.auth-alert.danger {
  background: #fff2f2;
  border: 1px solid #f5c2c2;
  color: #9b1c1c;
}
.auth-alert.success {
  background: #f0fdf4;
  border: 1px solid #bbf7d0;
  color: #14532d;
}

/* ── Footer ─────────────────────────────────────────────────── */
.auth-footer {
  padding: 1rem 2rem 1.5rem;
  text-align: center;
  font-size: .85rem;
  color: #7a6055;
}
.auth-footer a {
  color: var(--hms-caramel);
  font-weight: 600;
  text-decoration: none;
}
.auth-footer a:hover { text-decoration: underline; }

.auth-note {
  padding: .6rem 2rem 1.2rem;
  text-align: center;
  font-size: .75rem;
  color: #aaa;
  border-top: 1px solid #f0e8df;
}
</style>

<div class="auth-shell">
  <div class="auth-card">

    <!-- Header -->
    <div class="auth-header">
      <div class="auth-logo"><i class="bi bi-building-fill"></i></div>
      <h1>Welcome back</h1>
      <p>Sign in to Grand Hotel Management</p>
    </div>

    <!-- Body -->
    <div class="auth-body">

      <?php if (!empty($message)): ?>
      <div class="auth-alert danger" role="alert">
        <i class="bi bi-exclamation-circle-fill"></i>
        <span><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></span>
      </div>
      <?php endif; ?>

      <form id="loginForm"
            action="<?= APP_URL ?>/?url=auth/doLogin"
            method="POST"
            novalidate>

        <!-- Email -->
        <div class="auth-field">
          <label for="login-email">Email Address</label>
          <div class="input-icon-wrap">
            <i class="bi bi-envelope field-icon"></i>
            <input
              type="email"
              id="login-email"
              name="email"
              class="auth-input<?= !empty($errors['email']) ? ' is-invalid' : '' ?>"
              placeholder="name@example.com"
              value="<?= $email ?>"
              autocomplete="username"
              autofocus
              required>
          </div>
          <?php if (!empty($errors['email'])): ?>
          <div class="field-error">
            <i class="bi bi-x-circle-fill"></i>
            <?= htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8') ?>
          </div>
          <?php endif; ?>
        </div>

        <!-- Password -->
        <div class="auth-field">
          <label for="login-password">Password</label>
          <div class="input-icon-wrap">
            <i class="bi bi-lock field-icon"></i>
            <input
              type="password"
              id="login-password"
              name="password"
              class="auth-input has-right-icon<?= !empty($errors['password']) ? ' is-invalid' : '' ?>"
              placeholder="Enter your password"
              autocomplete="current-password"
              required>
            <button type="button"
                    class="field-icon-right"
                    id="toggleLoginPwd"
                    aria-label="Toggle password visibility"
                    title="Show/hide password">
              <i class="bi bi-eye" id="loginPwdIcon"></i>
            </button>
          </div>
          <?php if (!empty($errors['password'])): ?>
          <div class="field-error">
            <i class="bi bi-x-circle-fill"></i>
            <?= htmlspecialchars($errors['password'], ENT_QUOTES, 'UTF-8') ?>
          </div>
          <?php endif; ?>
        </div>

        <!-- Remember + Forgot -->
        <div class="auth-meta">
          <label class="auth-check">
            <input type="checkbox" name="remember_me" id="remember_me">
            <span>Remember me</span>
          </label>
          <a href="<?= APP_URL ?>/?url=auth/forgotPassword" class="auth-forgot">Forgot password?</a>
        </div>

        <!-- Submit -->
        <button type="submit" class="auth-btn" id="loginSubmitBtn">
          <span class="spinner"></span>
          <span class="btn-text"><i class="bi bi-box-arrow-in-right"></i> Sign In</span>
        </button>

      </form>


    </div>

    <!-- Footer -->
    <div class="auth-footer">
      New guest?
      <a href="<?= APP_URL ?>/?url=auth/register">Register here</a>
    </div>
    <div class="auth-note">
      Staff accounts are created by the hotel administrator only.
    </div>

  </div>
</div>

<script>
(function () {
  /* ── Password toggle ── */
  const toggleBtn = document.getElementById('toggleLoginPwd');
  const pwdInput  = document.getElementById('login-password');
  const pwdIcon   = document.getElementById('loginPwdIcon');
  if (toggleBtn) {
    toggleBtn.addEventListener('click', () => {
      const visible = pwdInput.type === 'text';
      pwdInput.type  = visible ? 'password' : 'text';
      pwdIcon.className = visible ? 'bi bi-eye' : 'bi bi-eye-slash';
    });
  }

  /* ── Loading state on submit ── */
  const form      = document.getElementById('loginForm');
  const submitBtn = document.getElementById('loginSubmitBtn');
  if (form) {
    form.addEventListener('submit', (e) => {
      // Basic client-side check before loading state
      const email = document.getElementById('login-email').value.trim();
      const pwd   = pwdInput.value;
      if (!email || !pwd) return; // let browser handle native validation
      submitBtn.classList.add('loading');
    });
  }
})();
</script>

<?php
$content = ob_get_clean();
require VIEW_PATH . '/layouts/main.php';
?>
