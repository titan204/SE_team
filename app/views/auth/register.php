<?php
$pageTitle = 'Register — Grand Hotel';
$errors = $errors ?? [];
$old    = $old    ?? [];
$message = $message ?? null;
$roomTypes    = $roomTypes    ?? [];
$floorOptions = $floorOptions ?? [];

$name  = htmlspecialchars($old['name']  ?? '', ENT_QUOTES, 'UTF-8');
$email = htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8');
$phone = htmlspecialchars($old['phone'] ?? '', ENT_QUOTES, 'UTF-8');
$roomTypeId        = $old['room_type_id']        ?? '';
$smokingPref       = $old['smoking_preference']  ?? '';
$floorPref         = $old['floor_preference']    ?? '';
$specialRequests   = htmlspecialchars($old['special_requests'] ?? '', ENT_QUOTES, 'UTF-8');
$specialNotes      = htmlspecialchars($old['special_notes']    ?? '', ENT_QUOTES, 'UTF-8');
$floorLevelPref    = $old['floor_level_preference'] ?? '';
$viewPref          = $old['view_preference']        ?? '';

$chk = fn($k) => ($old[$k] ?? '') === '1' ? 'checked' : '';
ob_start();
?>
<link rel="stylesheet" href="<?= APP_URL ?>/public/assets/css/home.css">
<style>.auth-shell{min-height:calc(100vh - 56px);padding-top:5rem;}</style>

<!-- Luxury Navbar -->
<nav class="hm-nav scrolled" id="regNav">
  <div class="inner">
    <a href="<?= APP_URL ?>/?url=home/index" class="nav-brand"><i class="bi bi-building-fill"></i>Grand Hotel</a>
    <ul class="nav-links">
      <li><a href="<?= APP_URL ?>/?url=home/index">Home</a></li>
      <li><a href="<?= APP_URL ?>/?url=rooms/guest">Rooms</a></li>
      <li><a href="<?= APP_URL ?>/?url=home/index#services">Services</a></li>
      <li><a href="<?= APP_URL ?>/?url=home/index#contact">Contact</a></li>
    </ul>
    <div class="nav-auth">
      <a href="<?= APP_URL ?>/?url=auth/login"    class="btn-nav-login">Sign In</a>
      <a href="<?= APP_URL ?>/?url=auth/register" class="btn-nav-reg" style="background:var(--brown)">Register</a>
    </div>
    <button class="nav-toggle" id="regNavToggle"><i class="bi bi-list"></i></button>
  </div>
  <div id="regMobileMenu" style="display:none;" class="mobile-menu">
    <a href="<?= APP_URL ?>/?url=home/index">Home</a>
    <a href="<?= APP_URL ?>/?url=rooms/guest">Rooms</a>
    <a href="<?= APP_URL ?>/?url=auth/login">Sign In</a>
  </div>
</nav>
<script>document.getElementById('regNavToggle').addEventListener('click',function(){var m=document.getElementById('regMobileMenu');m.style.display=m.style.display==='none'?'flex':'none';});</script>

<style>
.auth-shell{min-height:calc(100vh - 120px);display:flex;align-items:center;justify-content:center;padding:2rem 1rem;background:radial-gradient(ellipse at 20% 50%,rgba(196,135,77,.10) 0%,transparent 60%),radial-gradient(ellipse at 80% 20%,rgba(62,35,40,.08) 0%,transparent 50%),var(--hms-cream);}
.auth-card{width:100%;max-width:660px;background:#fff;border-radius:20px;box-shadow:0 4px 6px rgba(62,35,40,.04),0 20px 60px rgba(62,35,40,.12);overflow:hidden;animation:authFadeUp .45s cubic-bezier(.22,.68,0,1.2) both;}
@keyframes authFadeUp{from{opacity:0;transform:translateY(28px) scale(.97)}to{opacity:1;transform:none}}
.auth-header{background:linear-gradient(135deg,var(--hms-dark) 0%,#6a3a20 100%);padding:1.8rem 2rem;text-align:center;}
.auth-logo{width:52px;height:52px;background:rgba(196,135,77,.18);border:2px solid rgba(196,135,77,.4);border-radius:14px;display:inline-flex;align-items:center;justify-content:center;font-size:1.5rem;color:var(--hms-caramel);margin-bottom:.8rem;}
.auth-header h1{color:#fff;font-size:1.4rem;font-weight:700;margin:0 0 .25rem;letter-spacing:-.02em;}
.auth-header p{color:rgba(255,248,242,.65);font-size:.875rem;margin:0;}
.auth-body{padding:1.8rem 2rem;}
.auth-section-title{font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.09em;color:var(--hms-caramel);border-bottom:1px solid rgba(196,135,77,.2);padding-bottom:.4rem;margin:1.4rem 0 1rem;}
.auth-field{margin-bottom:.9rem;}
.auth-field label{display:block;font-size:.78rem;font-weight:600;color:var(--hms-dark);margin-bottom:.3rem;letter-spacing:.04em;text-transform:uppercase;}
.input-icon-wrap{position:relative;}
.input-icon-wrap .fi{position:absolute;left:.85rem;top:50%;transform:translateY(-50%);color:#b0a090;font-size:.95rem;pointer-events:none;transition:color .2s;}
.input-icon-wrap .fir{position:absolute;right:.85rem;top:50%;transform:translateY(-50%);color:#b0a090;font-size:.95rem;cursor:pointer;background:none;border:none;padding:0;transition:color .2s;}
.input-icon-wrap .fir:hover{color:var(--hms-caramel);}
.input-icon-wrap:focus-within .fi{color:var(--hms-caramel);}
.auth-input,.auth-select,.auth-textarea{width:100%;padding:.65rem .85rem .65rem 2.45rem;border:1.5px solid #e8ddd4;border-radius:10px;font-size:.9rem;background:#fdfaf7;color:var(--hms-dark);transition:border-color .2s,box-shadow .2s,background .2s;outline:none;-webkit-appearance:none;}
.auth-input:focus,.auth-select:focus,.auth-textarea:focus{border-color:var(--hms-caramel);background:#fff;box-shadow:0 0 0 3px rgba(196,135,77,.15);}
.auth-input.is-invalid,.auth-select.is-invalid,.auth-textarea.is-invalid{border-color:#dc3545;background:#fff8f8;}
.auth-select,.auth-textarea{padding-left:.85rem;}
.auth-textarea{resize:vertical;min-height:72px;}
.has-right-icon{padding-right:2.45rem;}
.field-error{font-size:.76rem;color:#dc3545;margin-top:.25rem;display:flex;align-items:center;gap:.3rem;}
.pwd-strength{height:4px;border-radius:4px;margin-top:.4rem;background:#eee;overflow:hidden;}
.pwd-strength-bar{height:100%;border-radius:4px;transition:width .3s,background .3s;width:0;}
.pwd-hint{font-size:.73rem;color:#9e7a60;margin-top:.25rem;}
.checks-grid{display:grid;grid-template-columns:1fr 1fr;gap:.4rem .8rem;}
.auth-check-item{display:flex;align-items:center;gap:.4rem;font-size:.85rem;color:#5a4035;cursor:pointer;}
.auth-check-item input{accent-color:var(--hms-caramel);width:15px;height:15px;cursor:pointer;}
.auth-alert{display:flex;align-items:center;gap:.6rem;padding:.75rem 1rem;border-radius:10px;margin-bottom:1.1rem;font-size:.875rem;font-weight:500;animation:alertSlide .3s ease both;}
@keyframes alertSlide{from{opacity:0;transform:translateY(-8px)}to{opacity:1;transform:none}}
.auth-alert.danger{background:#fff2f2;border:1px solid #f5c2c2;color:#9b1c1c;}
.auth-btn{width:100%;padding:.8rem;border:none;border-radius:10px;background:linear-gradient(135deg,var(--hms-caramel) 0%,#a0682e 100%);color:#fff;font-size:1rem;font-weight:700;cursor:pointer;transition:transform .15s,box-shadow .15s,filter .15s;display:flex;align-items:center;justify-content:center;gap:.5rem;position:relative;overflow:hidden;}
.auth-btn::after{content:'';position:absolute;inset:0;background:linear-gradient(rgba(255,255,255,.12),transparent);pointer-events:none;}
.auth-btn:hover{transform:translateY(-2px);box-shadow:0 8px 20px rgba(196,135,77,.4);filter:brightness(1.05);}
.auth-btn:active{transform:none;}
.auth-btn .spinner{display:none;width:18px;height:18px;border:2.5px solid rgba(255,255,255,.4);border-top-color:#fff;border-radius:50%;animation:spin .7s linear infinite;}
@keyframes spin{to{transform:rotate(360deg)}}
.auth-btn.loading .spinner{display:block;}.auth-btn.loading .btn-text{display:none;}.auth-btn.loading{pointer-events:none;filter:brightness(.9);}
.auth-footer{padding:.8rem 2rem 1.3rem;text-align:center;font-size:.85rem;color:#7a6055;border-top:1px solid #f0e8df;}
.auth-footer a{color:var(--hms-caramel);font-weight:600;text-decoration:none;}
.auth-footer a:hover{text-decoration:underline;}
.radio-group{display:flex;flex-wrap:wrap;gap:.5rem;}
.radio-opt{display:flex;align-items:center;gap:.35rem;font-size:.85rem;color:#5a4035;cursor:pointer;}
.radio-opt input{accent-color:var(--hms-caramel);width:15px;height:15px;}
@media(max-width:540px){.auth-card{border-radius:14px;}.auth-body{padding:1.4rem 1.2rem;}.checks-grid{grid-template-columns:1fr;}}
</style>

<div class="auth-shell">
 <div class="auth-card">

  <!-- Header -->
  <div class="auth-header">
   <div class="auth-logo"><i class="bi bi-person-plus-fill"></i></div>
   <h1>Create Guest Account</h1>
   <p>Register to enjoy personalised hotel services</p>
  </div>

  <div class="auth-body">

   <?php if (!empty($message)): ?>
   <div class="auth-alert danger">
    <i class="bi bi-exclamation-circle-fill"></i>
    <span><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></span>
   </div>
   <?php endif; ?>

   <form id="regForm" action="<?= APP_URL ?>/?url=auth/doRegister" method="POST" novalidate>

    <!-- ── Account Info ─────────────────────────────── -->
    <div class="auth-section-title"><i class="bi bi-person me-1"></i>Account Information</div>
    <div class="row g-2">
     <div class="col-md-6">
      <div class="auth-field">
       <label for="reg-name">Full Name <span style="color:#dc3545">*</span></label>
       <div class="input-icon-wrap">
        <i class="bi bi-person fi"></i>
        <input type="text" id="reg-name" name="name"
               class="auth-input<?= !empty($errors['name']) ? ' is-invalid' : '' ?>"
               placeholder="Your full name" value="<?= $name ?>"
               autocomplete="name" autofocus required>
       </div>
       <?php if (!empty($errors['name'])): ?>
       <div class="field-error"><i class="bi bi-x-circle-fill"></i><?= htmlspecialchars($errors['name'], ENT_QUOTES, 'UTF-8') ?></div>
       <?php endif; ?>
      </div>
     </div>
     <div class="col-md-6">
      <div class="auth-field">
       <label for="reg-phone">Phone Number <span style="color:#dc3545">*</span></label>
       <div class="input-icon-wrap">
        <i class="bi bi-telephone fi"></i>
        <input type="tel" id="reg-phone" name="phone"
               class="auth-input<?= !empty($errors['phone']) ? ' is-invalid' : '' ?>"
               placeholder="+1 (555) 000-0000" value="<?= $phone ?>"
               autocomplete="tel" required>
       </div>
       <?php if (!empty($errors['phone'])): ?>
       <div class="field-error"><i class="bi bi-x-circle-fill"></i><?= htmlspecialchars($errors['phone'], ENT_QUOTES, 'UTF-8') ?></div>
       <?php endif; ?>
      </div>
     </div>
    </div>

    <div class="auth-field">
     <label for="reg-email">Email Address <span style="color:#dc3545">*</span></label>
     <div class="input-icon-wrap">
      <i class="bi bi-envelope fi"></i>
      <input type="email" id="reg-email" name="email"
             class="auth-input<?= !empty($errors['email']) ? ' is-invalid' : '' ?>"
             placeholder="name@example.com" value="<?= $email ?>"
             autocomplete="email" required>
     </div>
     <?php if (!empty($errors['email'])): ?>
     <div class="field-error"><i class="bi bi-x-circle-fill"></i><?= htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8') ?></div>
     <?php endif; ?>
    </div>

    <div class="row g-2">
     <div class="col-md-6">
      <div class="auth-field">
       <label for="reg-password">Password <span style="color:#dc3545">*</span></label>
       <div class="input-icon-wrap">
        <i class="bi bi-lock fi"></i>
        <input type="password" id="reg-password" name="password"
               class="auth-input has-right-icon<?= !empty($errors['password']) ? ' is-invalid' : '' ?>"
               placeholder="Min 6 characters"
               autocomplete="new-password" required>
        <button type="button" class="fir" id="toggleRegPwd" title="Show/hide">
         <i class="bi bi-eye" id="regPwdIcon"></i>
        </button>
       </div>
       <div class="pwd-strength"><div class="pwd-strength-bar" id="pwdBar"></div></div>
       <div class="pwd-hint" id="pwdHint">Enter a password</div>
       <?php if (!empty($errors['password'])): ?>
       <div class="field-error"><i class="bi bi-x-circle-fill"></i><?= htmlspecialchars($errors['password'], ENT_QUOTES, 'UTF-8') ?></div>
       <?php endif; ?>
      </div>
     </div>
     <div class="col-md-6">
      <div class="auth-field">
       <label for="reg-confirm">Confirm Password <span style="color:#dc3545">*</span></label>
       <div class="input-icon-wrap">
        <i class="bi bi-lock-fill fi"></i>
        <input type="password" id="reg-confirm" name="confirm_password"
               class="auth-input has-right-icon<?= !empty($errors['confirm_password']) ? ' is-invalid' : '' ?>"
               placeholder="Repeat password"
               autocomplete="new-password" required>
        <button type="button" class="fir" id="toggleRegConf" title="Show/hide">
         <i class="bi bi-eye" id="regConfIcon"></i>
        </button>
       </div>
       <div class="pwd-hint" id="matchHint">&nbsp;</div>
       <?php if (!empty($errors['confirm_password'])): ?>
       <div class="field-error"><i class="bi bi-x-circle-fill"></i><?= htmlspecialchars($errors['confirm_password'], ENT_QUOTES, 'UTF-8') ?></div>
       <?php endif; ?>
      </div>
     </div>
    </div>

    <!-- ── Room Preferences ──────────────────────────── -->
    <div class="auth-section-title"><i class="bi bi-house me-1"></i>Room Preferences <small style="font-weight:400;text-transform:none;letter-spacing:0;color:#b09080">(optional)</small></div>

    <div class="row g-2">
     <div class="col-md-4">
      <div class="auth-field">
       <label for="room_type_id">Room Type</label>
       <select name="room_type_id" id="room_type_id" class="auth-select<?= !empty($errors['room_type_id']) ? ' is-invalid' : '' ?>">
        <option value="">No preference</option>
        <?php foreach ($roomTypes as $rt): ?>
        <option value="<?= (int)$rt['id'] ?>" <?= $roomTypeId == $rt['id'] ? 'selected' : '' ?>>
         <?= htmlspecialchars($rt['name'], ENT_QUOTES, 'UTF-8') ?>
        </option>
        <?php endforeach; ?>
       </select>
       <?php if (!empty($errors['room_type_id'])): ?>
       <div class="field-error"><i class="bi bi-x-circle-fill"></i><?= htmlspecialchars($errors['room_type_id'], ENT_QUOTES, 'UTF-8') ?></div>
       <?php endif; ?>
      </div>
     </div>
     <div class="col-md-4">
      <div class="auth-field">
       <label for="smoking_preference">Smoking</label>
       <select name="smoking_preference" id="smoking_preference" class="auth-select">
        <option value="">No preference</option>
        <option value="non_smoking" <?= $smokingPref==='non_smoking'?'selected':'' ?>>Non-Smoking</option>
        <option value="smoking"     <?= $smokingPref==='smoking'    ?'selected':'' ?>>Smoking</option>
       </select>
      </div>
     </div>
     <div class="col-md-4">
      <div class="auth-field">
       <label for="floor_preference">Floor</label>
       <select name="floor_preference" id="floor_preference" class="auth-select">
        <option value="">No preference</option>
        <?php foreach ($floorOptions as $f): ?>
        <option value="<?= (int)$f ?>" <?= $floorPref==(string)$f?'selected':'' ?>>Floor <?= (int)$f ?></option>
        <?php endforeach; ?>
       </select>
      </div>
     </div>
    </div>

    <div class="row g-2">
     <div class="col-md-6">
      <div class="auth-field">
       <label for="view_preference">View</label>
       <select name="view_preference" id="view_preference" class="auth-select">
        <option value="">No preference</option>
        <option value="sea_view"    <?= $viewPref==='sea_view'    ?'selected':'' ?>>Sea View</option>
        <option value="city_view"   <?= $viewPref==='city_view'   ?'selected':'' ?>>City View</option>
        <option value="garden_view" <?= $viewPref==='garden_view' ?'selected':'' ?>>Garden View</option>
       </select>
      </div>
     </div>
     <div class="col-md-6">
      <div class="auth-field">
       <label>Floor Level</label>
       <div class="radio-group mt-1">
        <label class="radio-opt"><input type="radio" name="floor_level_preference" value=""         <?= $floorLevelPref===''          ?'checked':'' ?>> Any</label>
        <label class="radio-opt"><input type="radio" name="floor_level_preference" value="high_floor" <?= $floorLevelPref==='high_floor' ?'checked':'' ?>> High Floor</label>
        <label class="radio-opt"><input type="radio" name="floor_level_preference" value="low_floor"  <?= $floorLevelPref==='low_floor'  ?'checked':'' ?>> Low Floor</label>
       </div>
      </div>
     </div>
    </div>

    <!-- Checkboxes -->
    <div class="auth-field">
     <label>Room Extras</label>
     <div class="checks-grid">
      <?php
      $boxes = [
       ['quiet_room','Quiet Room'],['near_elevator','Near Elevator'],
       ['extra_pillow','Extra Pillow'],['extra_blanket','Extra Blanket'],
       ['baby_crib','Baby Crib'],['accessible_room','Accessible Room'],
       ['connecting_room','Connecting Room'],['allergy_free_room','Allergy-Free'],
       ['work_desk_needed','Work Desk'],['balcony_preferred','Balcony'],
       ['early_check_in_request','Early Check-in'],['late_check_in_request','Late Check-in'],
       ['non_smoking_guarantee','Non-Smoking Guarantee'],
      ];
      foreach ($boxes as [$key,$label]):
      ?>
      <label class="auth-check-item">
       <input type="checkbox" name="<?= $key ?>" value="1" <?= $chk($key) ?>>
       <?= $label ?>
      </label>
      <?php endforeach; ?>
     </div>
    </div>

    <div class="row g-2">
     <div class="col-md-6">
      <div class="auth-field">
       <label for="special_requests">Special Requests</label>
       <textarea name="special_requests" id="special_requests" class="auth-textarea" maxlength="255" placeholder="Any requests for your stay..."><?= $specialRequests ?></textarea>
       <?php if (!empty($errors['special_requests'])): ?>
       <div class="field-error"><i class="bi bi-x-circle-fill"></i><?= htmlspecialchars($errors['special_requests'], ENT_QUOTES, 'UTF-8') ?></div>
       <?php endif; ?>
      </div>
     </div>
     <div class="col-md-6">
      <div class="auth-field">
       <label for="special_notes">Special Notes</label>
       <textarea name="special_notes" id="special_notes" class="auth-textarea" maxlength="255" placeholder="Additional room preference notes..."><?= $specialNotes ?></textarea>
       <?php if (!empty($errors['special_notes'])): ?>
       <div class="field-error"><i class="bi bi-x-circle-fill"></i><?= htmlspecialchars($errors['special_notes'], ENT_QUOTES, 'UTF-8') ?></div>
       <?php endif; ?>
      </div>
     </div>
    </div>

    <!-- Submit -->
    <div class="mt-3">
     <button type="submit" class="auth-btn" id="regSubmitBtn">
      <span class="spinner"></span>
      <span class="btn-text"><i class="bi bi-person-check me-1"></i>Create Account</span>
     </button>
    </div>

   </form>
  </div><!-- /.auth-body -->

  <div class="auth-footer">
   Already have an account? <a href="<?= APP_URL ?>/?url=auth/login">Sign in</a>
  </div>

 </div><!-- /.auth-card -->
</div>

<script>
(function () {
 /* ── Password toggles ── */
 function mkToggle(btnId, inputId, iconId) {
  const btn = document.getElementById(btnId);
  const inp = document.getElementById(inputId);
  const ico = document.getElementById(iconId);
  if (!btn) return;
  btn.addEventListener('click', () => {
   const vis = inp.type === 'text';
   inp.type = vis ? 'password' : 'text';
   ico.className = vis ? 'bi bi-eye' : 'bi bi-eye-slash';
  });
 }
 mkToggle('toggleRegPwd',  'reg-password', 'regPwdIcon');
 mkToggle('toggleRegConf', 'reg-confirm',  'regConfIcon');

 /* ── Password strength ── */
 const pwdInp  = document.getElementById('reg-password');
 const bar     = document.getElementById('pwdBar');
 const hint    = document.getElementById('pwdHint');
 const levels  = [
  { re: /.{6}/,   pct: 25, color: '#ef4444', label: 'Too short' },
  { re: /.{8}/,   pct: 50, color: '#f97316', label: 'Weak' },
  { re: /(?=.*[A-Z])(?=.*\d).{8}/, pct: 75, color: '#eab308', label: 'Fair' },
  { re: /(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8}/, pct: 100, color: '#22c55e', label: 'Strong' },
 ];
 pwdInp && pwdInp.addEventListener('input', () => {
  const v = pwdInp.value;
  if (!v) { bar.style.width='0'; hint.textContent='Enter a password'; return; }
  let chosen = levels[0];
  for (const l of levels) { if (l.re.test(v)) chosen = l; }
  bar.style.width = chosen.pct + '%';
  bar.style.background = chosen.color;
  hint.textContent = chosen.label;
  hint.style.color = chosen.color;
 });

 /* ── Password match ── */
 const conf     = document.getElementById('reg-confirm');
 const matchHnt = document.getElementById('matchHint');
 function checkMatch() {
  if (!conf.value) { matchHnt.innerHTML = '&nbsp;'; return; }
  const ok = pwdInp.value === conf.value;
  matchHnt.textContent = ok ? '✓ Passwords match' : '✗ Passwords do not match';
  matchHnt.style.color = ok ? '#22c55e' : '#ef4444';
 }
 conf    && conf.addEventListener('input', checkMatch);
 pwdInp  && pwdInp.addEventListener('input', checkMatch);

 /* ── Loading state ── */
 const form = document.getElementById('regForm');
 const btn  = document.getElementById('regSubmitBtn');
 form && form.addEventListener('submit', () => btn.classList.add('loading'));
})();
</script>

<?php
$content = ob_get_clean();
require VIEW_PATH . '/layouts/main.php';
?>
