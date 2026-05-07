<?php

$pageTitle    = 'My Profile';
$guest        = $guest        ?? [];
$reservations = $reservations ?? [];
$preferences  = $preferences  ?? [];
$errors       = $errors       ?? [];
$old          = $old          ?? [];
$success      = $success      ?? null;
$appUrl       = APP_URL;

$h = fn(string $v): string => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
$v = fn(string $key): string => $h((string)($old[$key] ?? $guest[$key] ?? ''));

ob_start();
?>
<style>
/* ── Self-Service Profile Styles ─────────────────────────── */
:root{--pr:#9A3F3F;--pr2:#C1856D;--bg:#FBF9D1;--light:#FFF8F0;}
.sp-wrap{max-width:860px;margin:0 auto;}
/* avatar header */
.sp-header{background:linear-gradient(135deg,var(--pr),var(--pr2));border-radius:16px 16px 0 0;padding:2rem;text-align:center;color:#fff;}
.sp-avatar{width:80px;height:80px;border-radius:50%;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;font-size:32px;font-weight:700;margin:0 auto .75rem;border:3px solid rgba(255,255,255,.4);}
.sp-name{font-size:1.25rem;font-weight:700;}
.sp-tier{font-size:.75rem;letter-spacing:2px;text-transform:uppercase;opacity:.8;}
/* tab nav */
.sp-tabs{display:flex;background:#fff;border-bottom:2px solid #e8d5c0;}
.sp-tab{flex:1;padding:.85rem;text-align:center;cursor:pointer;font-size:.85rem;font-weight:600;color:#8B6B5E;border:none;background:none;transition:color .2s,border-bottom .2s;border-bottom:2px solid transparent;margin-bottom:-2px;}
.sp-tab:hover{color:var(--pr);}
.sp-tab.active{color:var(--pr);border-bottom-color:var(--pr);}
.sp-tab i{display:block;font-size:1.1rem;margin-bottom:2px;}
/* panels */
.sp-panel{display:none;padding:1.5rem 2rem;background:#fff;border-radius:0 0 16px 16px;}
.sp-panel.active{display:block;}
/* alerts */
.sp-alert{padding:.75rem 1rem;border-radius:8px;margin-bottom:1rem;font-size:.88rem;}
.sp-alert-success{background:#d4edda;color:#155724;border:1px solid #c3e6cb;}
.sp-alert-danger{background:#f8d7da;color:#721c24;border:1px solid #f5c6cb;}
/* form */
.sp-row{display:grid;grid-template-columns:1fr 1fr;gap:1rem;}
@media(max-width:560px){.sp-row{grid-template-columns:1fr;}}
.sp-group{margin-bottom:1rem;}
.sp-label{font-size:.78rem;text-transform:uppercase;letter-spacing:1px;color:#8B6B5E;margin-bottom:.3rem;display:block;}
.sp-input{width:100%;padding:.5rem .75rem;border:1px solid #d5b8a8;border-radius:8px;font-size:.9rem;background:#fafafa;transition:border-color .2s;}
.sp-input:focus{outline:none;border-color:var(--pr);background:#fff;}
.sp-input.is-invalid{border-color:#dc3545;}
.sp-err{color:#dc3545;font-size:.78rem;margin-top:.2rem;}
.btn-pr{background:var(--pr);color:#fff;border:none;padding:.55rem 1.5rem;border-radius:8px;font-weight:600;cursor:pointer;transition:background .2s;}
.btn-pr:hover{background:var(--pr2);}
.btn-sm-pr{background:var(--pr);color:#fff;border:none;padding:.3rem .8rem;border-radius:6px;font-size:.8rem;cursor:pointer;transition:background .2s;}
.btn-sm-pr:hover{background:var(--pr2);}
/* reservation table */
.sp-table{width:100%;border-collapse:collapse;font-size:.88rem;}
.sp-table th{background:var(--pr);color:#fff;padding:.6rem .8rem;text-align:left;}
.sp-table td{padding:.6rem .8rem;border-bottom:1px solid #e8d5c0;}
.sp-table tr:last-child td{border-bottom:none;}
.sp-table tr:hover td{background:#fff3e8;}
.badge-confirmed{background:#d4edda;color:#155724;padding:.2rem .55rem;border-radius:20px;font-size:.75rem;}
.badge-pending{background:#fff3cd;color:#856404;padding:.2rem .55rem;border-radius:20px;font-size:.75rem;}
.badge-cancelled{background:#f8d7da;color:#721c24;padding:.2rem .55rem;border-radius:20px;font-size:.75rem;}
.badge-checked_in{background:#cce5ff;color:#004085;padding:.2rem .55rem;border-radius:20px;font-size:.75rem;}
.badge-checked_out{background:#e2e3e5;color:#383d41;padding:.2rem .55rem;border-radius:20px;font-size:.75rem;}
/* preferences */
.pref-list{list-style:none;padding:0;margin:0 0 1.5rem;}
.pref-item{display:flex;align-items:center;gap:.75rem;padding:.55rem .75rem;border-radius:8px;border:1px solid #e8d5c0;margin-bottom:.5rem;background:#fafafa;}
.pref-key{font-size:.75rem;text-transform:uppercase;letter-spacing:1px;color:#8B6B5E;min-width:110px;}
.pref-val{flex:1;font-size:.9rem;color:#3B1F1F;}
.pref-actions{display:flex;gap:.4rem;}
.pref-edit-form{display:none;padding:.75rem;background:#fff3e8;border-radius:8px;border:1px solid #e8d5c0;margin-bottom:.5rem;}
.pref-edit-row{display:flex;gap:.5rem;align-items:flex-end;}
/* add preference form */
.pref-add-form{padding:1rem;background:var(--light);border-radius:10px;border:1px dashed var(--pr2);}
.pref-add-row{display:grid;grid-template-columns:1fr 1fr auto;gap:.75rem;align-items:end;}
@media(max-width:560px){.pref-add-row{grid-template-columns:1fr;}}
/* empty state */
.sp-empty{text-align:center;padding:2rem;color:#8B6B5E;font-size:.9rem;}
.sp-empty i{font-size:2rem;display:block;margin-bottom:.5rem;opacity:.5;}
</style>

<div class="sp-wrap">

  <!-- ── Header ─────────────────────────────────────────── -->
  <div class="sp-header">
    <div class="sp-avatar"><?= strtoupper(substr($guest['name'] ?? 'G', 0, 1)) ?></div>
    <div class="sp-name"><?= $h($guest['name'] ?? '') ?></div>
    <div class="sp-tier">
      <?= $h(ucfirst($guest['loyalty_tier'] ?? 'standard')) ?> Member
      <?php if (!empty($guest['is_vip'])): ?> &nbsp;⭐ VIP<?php endif; ?>
    </div>
  </div>

  <!-- ── Tab Nav ────────────────────────────────────────── -->
  <div class="sp-tabs">
    <button class="sp-tab active" data-tab="profile" id="tab-profile">
      <i class="bi bi-person"></i>Profile
    </button>
    <button class="sp-tab" data-tab="reservations" id="tab-reservations">
      <i class="bi bi-calendar-check"></i>Reservations
    </button>
    <button class="sp-tab" data-tab="preferences" id="tab-preferences">
      <i class="bi bi-sliders"></i>Preferences
    </button>
  </div>

  <!-- ═══════════════════════════════════════════════════════
       PANEL 1 — Profile
       ═══════════════════════════════════════════════════════ -->
  <div class="sp-panel active" id="panel-profile">

    <?php if ($success): ?>
      <div class="sp-alert sp-alert-success"><i class="bi bi-check-circle me-1"></i><?= $h($success) ?></div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
      <div class="sp-alert sp-alert-danger">
        <i class="bi bi-exclamation-circle me-1"></i>Please correct the errors below.
      </div>
    <?php endif; ?>

    <form id="profileForm"
          action="<?= $appUrl ?>/?url=guestProfile/updateProfile"
          method="POST">

      <div class="sp-row">
        <div class="sp-group">
          <label class="sp-label" for="pf-name">Full Name <span style="color:#dc3545">*</span></label>
          <input id="pf-name" name="name" class="sp-input <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                 value="<?= $v('name') ?>" maxlength="150" required>
          <?php if (isset($errors['name'])): ?><div class="sp-err"><?= $h($errors['name']) ?></div><?php endif; ?>
        </div>
        <div class="sp-group">
          <label class="sp-label" for="pf-email">Email <span style="color:#dc3545">*</span></label>
          <input id="pf-email" name="email" type="email"
                 class="sp-input <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                 value="<?= $v('email') ?>" maxlength="150" required>
          <?php if (isset($errors['email'])): ?><div class="sp-err"><?= $h($errors['email']) ?></div><?php endif; ?>
        </div>
      </div>

      <div class="sp-row">
        <div class="sp-group">
          <label class="sp-label" for="pf-phone">Phone</label>
          <input id="pf-phone" name="phone" class="sp-input <?= isset($errors['phone']) ? 'is-invalid' : '' ?>"
                 value="<?= $v('phone') ?>" maxlength="30">
          <?php if (isset($errors['phone'])): ?><div class="sp-err"><?= $h($errors['phone']) ?></div><?php endif; ?>
        </div>
        <div class="sp-group">
          <label class="sp-label" for="pf-nat">Nationality</label>
          <input id="pf-nat" name="nationality"
                 class="sp-input <?= isset($errors['nationality']) ? 'is-invalid' : '' ?>"
                 value="<?= $v('nationality') ?>" maxlength="80">
          <?php if (isset($errors['nationality'])): ?><div class="sp-err"><?= $h($errors['nationality']) ?></div><?php endif; ?>
        </div>
      </div>

      <div class="sp-group" style="max-width:260px">
        <label class="sp-label" for="pf-dob">Date of Birth</label>
        <input id="pf-dob" name="date_of_birth" type="date"
               class="sp-input <?= isset($errors['date_of_birth']) ? 'is-invalid' : '' ?>"
               value="<?= $v('date_of_birth') ?>">
        <?php if (isset($errors['date_of_birth'])): ?><div class="sp-err"><?= $h($errors['date_of_birth']) ?></div><?php endif; ?>
      </div>

      <button type="submit" class="btn-pr" id="btn-save-profile">
        <i class="bi bi-check-lg me-1"></i>Save Changes
      </button>
      <span id="profile-saving" style="display:none;margin-left:.75rem;font-size:.85rem;color:#8B6B5E;">Saving…</span>
      <div id="profile-ajax-msg" style="margin-top:.5rem;display:none;" class="sp-alert"></div>
    </form>
  </div>

  <!-- ═══════════════════════════════════════════════════════
       PANEL 2 — Reservations
       ═══════════════════════════════════════════════════════ -->
  <div class="sp-panel" id="panel-reservations">

    <?php if (empty($reservations)): ?>
      <div class="sp-empty"><i class="bi bi-calendar-x"></i>No reservations found.</div>
    <?php else: ?>
      <table class="sp-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Room #</th>
            <th>Room Type</th>
            <th>Check-In</th>
            <th>Check-Out</th>
            <th>Status</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($reservations as $res): ?>
          <tr id="res-row-<?= (int)$res['id'] ?>">
            <td><?= (int)$res['id'] ?></td>
            <td><?= $h($res['room_number'] ?? '—') ?></td>
            <td><?= $h($res['room_type_name'] ?? $res['room_type'] ?? '—') ?></td>
            <td><?= $h($res['check_in_date']  ?? '—') ?></td>
            <td><?= $h($res['check_out_date'] ?? '—') ?></td>
            <td>
              <?php $st = strtolower($res['status'] ?? ''); ?>
              <span class="badge-<?= $h($st) ?>"><?= $h(ucfirst(str_replace('_',' ',$st))) ?></span>
            </td>
            <td>
              <?php if ($st === 'confirmed'): ?>
                <button class="btn-sm-pr"
                        style="background:#dc3545;"
                        onclick="cancelReservation(<?= (int)$res['id'] ?>)"
                        id="cancel-btn-<?= (int)$res['id'] ?>">
                  Cancel
                </button>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
    <div id="res-msg" style="display:none;margin-top:.75rem;" class="sp-alert"></div>
  </div>

  <!-- ═══════════════════════════════════════════════════════
       PANEL 3 — Preferences (structured form)
       ═══════════════════════════════════════════════════════ -->
  <div class="sp-panel" id="panel-preferences">
    <?php
    // Build a flat key→value map from the DB rows for easy lookup
    $prefMap = [];
    foreach ($preferences as $p) {
        $prefMap[$p['pref_key']] = $p['pref_value'];
    }
    $pv = fn(string $key): string => $h($prefMap[$key] ?? '');
    $pc = fn(string $key): string => !empty($prefMap[$key]) ? 'checked' : '';
    $roomTypes = $roomTypes ?? [];
    ?>

    <style>
    .pf-section{margin-bottom:1.5rem;}
    .pf-section-title{font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--pr);border-bottom:1.5px solid rgba(154,63,63,.15);padding-bottom:.4rem;margin-bottom:1rem;display:flex;align-items:center;gap:.4rem;}
    .pf-grid2{display:grid;grid-template-columns:1fr 1fr;gap:.75rem;}
    .pf-grid3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:.75rem;}
    @media(max-width:600px){.pf-grid2,.pf-grid3{grid-template-columns:1fr;}}
    .pf-field{display:flex;flex-direction:column;gap:.3rem;}
    .pf-label{font-size:.75rem;text-transform:uppercase;letter-spacing:1px;color:#8B6B5E;font-weight:600;}
    .pf-select,.pf-textarea{width:100%;padding:.5rem .75rem;border:1.5px solid #e0cfc4;border-radius:8px;font-size:.875rem;background:#fafaf8;color:#3B1F1F;transition:border-color .2s;}
    .pf-select:focus,.pf-textarea:focus{outline:none;border-color:var(--pr);background:#fff;}
    .pf-textarea{resize:vertical;min-height:70px;}
    /* Checkbox extras */
    .pf-extras{display:grid;grid-template-columns:repeat(auto-fill,minmax(170px,1fr));gap:.5rem;}
    .pf-check{display:flex;align-items:center;gap:.45rem;font-size:.85rem;color:#4a2f2f;cursor:pointer;padding:.35rem .5rem;border-radius:7px;transition:background .15s;}
    .pf-check:hover{background:#fff3e8;}
    .pf-check input{accent-color:var(--pr);width:16px;height:16px;cursor:pointer;flex-shrink:0;}
    /* Save bar */
    .pf-save-bar{position:sticky;bottom:0;background:linear-gradient(to top,#fff 80%,transparent);padding:1rem 0 .5rem;margin-top:1rem;display:flex;align-items:center;gap:1rem;}
    </style>

    <form id="prefForm">

      <!-- ── 1. Room Preferences ─────────────────────────── -->
      <div class="pf-section">
        <div class="pf-section-title"><i class="bi bi-door-open"></i>Room Preferences</div>
        <div class="pf-grid3">

          <div class="pf-field">
            <label class="pf-label">Room Type</label>
            <select name="room_type" class="pf-select">
              <option value="">No preference</option>
              <?php foreach ($roomTypes as $rt): ?>
                <option value="<?= $h($rt['name']) ?>"
                  <?= ($prefMap['room_type'] ?? '') === $rt['name'] ? 'selected' : '' ?>>
                  <?= $h($rt['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="pf-field">
            <label class="pf-label">Bed Type</label>
            <select name="bed_type" class="pf-select">
              <option value="">No preference</option>
              <?php foreach (['King','Queen','Twin','Single','Double'] as $bt): ?>
                <option value="<?= $bt ?>" <?= ($prefMap['bed_type'] ?? '') === $bt ? 'selected' : '' ?>><?= $bt ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="pf-field">
            <label class="pf-label">Smoking</label>
            <select name="smoking" class="pf-select">
              <option value="">No preference</option>
              <option value="Non-Smoking" <?= ($prefMap['smoking'] ?? '') === 'Non-Smoking' ? 'selected' : '' ?>>Non-Smoking</option>
              <option value="Smoking"     <?= ($prefMap['smoking'] ?? '') === 'Smoking'     ? 'selected' : '' ?>>Smoking</option>
            </select>
          </div>

          <div class="pf-field">
            <label class="pf-label">Floor Level</label>
            <select name="floor_level" class="pf-select">
              <option value="">No preference</option>
              <option value="High Floor" <?= ($prefMap['floor_level'] ?? '') === 'High Floor' ? 'selected' : '' ?>>High Floor</option>
              <option value="Low Floor"  <?= ($prefMap['floor_level'] ?? '') === 'Low Floor'  ? 'selected' : '' ?>>Low Floor</option>
            </select>
          </div>

          <div class="pf-field">
            <label class="pf-label">View</label>
            <select name="view" class="pf-select">
              <option value="">No preference</option>
              <option value="Sea View"    <?= ($prefMap['view'] ?? '') === 'Sea View'    ? 'selected' : '' ?>>Sea View</option>
              <option value="City View"   <?= ($prefMap['view'] ?? '') === 'City View'   ? 'selected' : '' ?>>City View</option>
              <option value="Garden View" <?= ($prefMap['view'] ?? '') === 'Garden View' ? 'selected' : '' ?>>Garden View</option>
            </select>
          </div>

          <div class="pf-field">
            <label class="pf-label">Dietary Needs</label>
            <select name="dietary" class="pf-select">
              <option value="">None</option>
              <option value="Vegetarian" <?= ($prefMap['dietary'] ?? '') === 'Vegetarian' ? 'selected' : '' ?>>Vegetarian</option>
              <option value="Vegan"      <?= ($prefMap['dietary'] ?? '') === 'Vegan'      ? 'selected' : '' ?>>Vegan</option>
              <option value="Halal"      <?= ($prefMap['dietary'] ?? '') === 'Halal'      ? 'selected' : '' ?>>Halal</option>
              <option value="Kosher"     <?= ($prefMap['dietary'] ?? '') === 'Kosher'     ? 'selected' : '' ?>>Kosher</option>
              <option value="Gluten-Free"<?= ($prefMap['dietary'] ?? '') === 'Gluten-Free'? 'selected' : '' ?>>Gluten-Free</option>
            </select>
          </div>

        </div>
      </div>

      <!-- ── 2. Room Extras ──────────────────────────────── -->
      <div class="pf-section">
        <div class="pf-section-title"><i class="bi bi-stars"></i>Room Extras</div>
        <div class="pf-extras">
          <?php
          $extras = [
            'quiet_room'            => 'Quiet Room',
            'near_elevator'         => 'Near Elevator',
            'extra_pillow'          => 'Extra Pillow',
            'extra_blanket'         => 'Extra Blanket',
            'baby_crib'             => 'Baby Crib',
            'accessible_room'       => 'Accessible Room',
            'connecting_room'       => 'Connecting Room',
            'allergy_free_room'     => 'Allergy-Free Room',
            'non_smoking_guarantee' => 'Non-Smoking Guarantee',
            'work_desk_needed'      => 'Work Desk',
            'balcony_preferred'     => 'Balcony Preferred',
          ];
          foreach ($extras as $key => $label): ?>
            <label class="pf-check">
              <input type="checkbox" name="<?= $key ?>" value="Yes" <?= $pc($key) ?>>
              <?= $label ?>
            </label>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- ── 3. Check-in Requests ────────────────────────── -->
      <div class="pf-section">
        <div class="pf-section-title"><i class="bi bi-calendar-check"></i>Check-in Requests</div>
        <div class="pf-extras">
          <label class="pf-check">
            <input type="checkbox" name="early_check_in_request" value="Yes" <?= $pc('early_check_in_request') ?>>
            Early Check-In Request
          </label>
          <label class="pf-check">
            <input type="checkbox" name="late_check_in_request" value="Yes" <?= $pc('late_check_in_request') ?>>
            Late Check-In Request
          </label>
        </div>
      </div>

      <!-- ── 4. Special Requests ─────────────────────────── -->
      <div class="pf-section">
        <div class="pf-section-title"><i class="bi bi-chat-dots"></i>Special Requests</div>
        <div class="pf-field">
          <label class="pf-label">Any special requests for your stay?</label>
          <textarea name="special_requests" class="pf-textarea"
                    placeholder="e.g. Honeymoon setup, extra towels, specific pillow type…"
                    maxlength="500"><?= $pv('special_requests') ?></textarea>
        </div>
      </div>

      <!-- ── Save Bar ──────────────────────────────────────── -->
      <div class="pf-save-bar">
        <button type="button" class="btn-pr" onclick="savePreferences()" id="btn-save-prefs">
          <i class="bi bi-check-circle me-1"></i>Save Preferences
        </button>
        <span id="pref-saving" style="display:none;font-size:.85rem;color:#8B6B5E;">Saving…</span>
        <div id="pref-msg" style="display:none;" class="sp-alert"></div>
      </div>

    </form>
  </div>

</div><!-- /.sp-wrap -->



<script>
const BASE = '<?= $appUrl ?>';

/* ── Tabs ──────────────────────────────────────────────── */
document.querySelectorAll('.sp-tab').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.sp-tab').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.sp-panel').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('panel-' + btn.dataset.tab).classList.add('active');
  });
});

/* ── Profile form via AJAX ─────────────────────────────── */
document.getElementById('profileForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const btn = document.getElementById('btn-save-profile');
  const spin = document.getElementById('profile-saving');
  const msg  = document.getElementById('profile-ajax-msg');
  btn.disabled = true; spin.style.display = 'inline'; msg.style.display = 'none';

  try {
    const res  = await fetch(this.action, {method:'POST', body: new FormData(this), headers:{'X-Requested-With':'XMLHttpRequest'}});
    const data = await res.json();
    msg.style.display = 'block';
    if (data.ok) {
      msg.className = 'sp-alert sp-alert-success';
      msg.textContent = '✓ Profile saved successfully.';
    } else {
      msg.className = 'sp-alert sp-alert-danger';
      msg.textContent = Object.values(data.errors || {}).join(' • ') || 'Validation failed.';
    }
  } catch {
    msg.style.display = 'block';
    msg.className = 'sp-alert sp-alert-danger';
    msg.textContent = 'Network error. Please try again.';
  } finally {
    btn.disabled = false; spin.style.display = 'none';
  }
});

/* ── Cancel Reservation ────────────────────────────────── */
async function cancelReservation(id) {
  if (!confirm('Cancel reservation #' + id + '? This cannot be undone.')) return;
  const btn = document.getElementById('cancel-btn-' + id);
  const msg = document.getElementById('res-msg');
  btn.disabled = true; btn.textContent = '…';

  const fd = new FormData();
  fd.append('reservation_id', id);
  try {
    const res  = await fetch(BASE + '/?url=guestProfile/cancelReservation', {method:'POST', body:fd, headers:{'X-Requested-With':'XMLHttpRequest'}});
    const data = await res.json();
    msg.style.display = 'block';
    if (data.ok) {
      msg.className = 'sp-alert sp-alert-success';
      msg.textContent = '✓ ' + data.message;
      btn.closest('tr').querySelector('.badge-confirmed').textContent = 'Cancelled';
      btn.closest('tr').querySelector('.badge-confirmed').className = 'badge-cancelled';
      btn.remove();
    } else {
      msg.className = 'sp-alert sp-alert-danger';
      msg.textContent = data.message;
      btn.disabled = false; btn.textContent = 'Cancel';
    }
  } catch {
    msg.style.display = 'block';
    msg.className = 'sp-alert sp-alert-danger';
    msg.textContent = 'Network error.';
    btn.disabled = false; btn.textContent = 'Cancel';
  }
}

/* ── Save Preferences (bulk) ────────────────────────── */
async function savePreferences() {
  const btn  = document.getElementById('btn-save-prefs');
  const spin = document.getElementById('pref-saving');
  const msg  = document.getElementById('pref-msg');
  btn.disabled = true; spin.style.display = 'inline'; msg.style.display = 'none';

  const form = document.getElementById('prefForm');
  const fd   = new FormData(form);

  // Unchecked checkboxes don't appear in FormData — explicitly add them as empty
  const checkboxNames = [
    'quiet_room','near_elevator','extra_pillow','extra_blanket','baby_crib',
    'accessible_room','connecting_room','allergy_free_room','non_smoking_guarantee',
    'work_desk_needed','balcony_preferred','early_check_in_request','late_check_in_request'
  ];
  checkboxNames.forEach(name => { if (!fd.has(name)) fd.append(name, ''); });

  try {
    const res  = await fetch(BASE + '/?url=guestProfile/savePreferences', {
      method:'POST', body:fd, headers:{'X-Requested-With':'XMLHttpRequest'}
    });
    const data = await res.json();
    msg.style.display = 'block';
    if (data.ok) {
      msg.className = 'sp-alert sp-alert-success';
      msg.textContent = '✓ ' + data.message;
    } else {
      msg.className = 'sp-alert sp-alert-danger';
      msg.textContent = data.message || 'Could not save preferences.';
    }
  } catch {
    msg.style.display = 'block';
    msg.className = 'sp-alert sp-alert-danger';
    msg.textContent = 'Network error. Please try again.';
  } finally {
    btn.disabled = false; spin.style.display = 'none';
  }
}

let _editingId = null;

function openEditOverlay(id, key, val) {
  _editingId = id;
  document.getElementById('ov-pref-id').value = id;
  document.getElementById('ov-val').value = val;
  const sel = document.getElementById('ov-key');
  let matched = false;
  for (const opt of sel.options) { if (opt.value === key) { opt.selected = true; matched = true; break; } }
  if (!matched) sel.options[sel.options.length - 1].selected = true;
  const ov = document.getElementById('pref-edit-overlay');
  if (ov) { ov.style.display = 'block'; document.getElementById('ov-val').focus(); }
}

function closeEditOverlay() {
  const ov = document.getElementById('pref-edit-overlay');
  if (ov) ov.style.display = 'none';
  _editingId = null;
}

async function saveOverlayPref() {
  const id  = document.getElementById('ov-pref-id').value;
  const key = document.getElementById('ov-key').value;
  const val = document.getElementById('ov-val').value.trim();
  if (!val) { alert('Value cannot be empty.'); return; }
  const fd = new FormData();
  fd.append('pref_id', id); fd.append('pref_key', key); fd.append('pref_value', val);
  const res  = await fetch(BASE + '/?url=guestProfile/updatePreference', {method:'POST', body:fd, headers:{'X-Requested-With':'XMLHttpRequest'}});
  const data = await res.json();
  if (data.ok) {
    const el = document.getElementById('pref-val-' + id);
    if (el) { el.textContent = val; el.title = val; }
    closeEditOverlay();
  } else { alert(data.message || 'Update failed.'); }
}

async function deletePref(id) {
  if (!confirm('Delete this preference?')) return;
  const fd = new FormData(); fd.append('pref_id', id);
  const res  = await fetch(BASE + '/?url=guestProfile/deletePreference', {method:'POST', body:fd, headers:{'X-Requested-With':'XMLHttpRequest'}});
  const data = await res.json();
  if (data.ok) {
    const card = document.getElementById('pref-card-' + id);
    if (card) card.remove();
    if (_editingId == id) closeEditOverlay();
  } else { alert(data.message || 'Delete failed.'); }
}

function updateAddPlaceholder() {
  const hints = {
    room_type: 'e.g. Deluxe, Suite…',
    bed_type:  'e.g. King, Twin…',
    smoking:   'e.g. Non-Smoking…',
    floor:     'e.g. 3, High floor…',
    view:      'e.g. Sea view, Garden…',
    pillow:    'e.g. Soft, Extra…',
    dietary:   'e.g. Vegetarian, Halal…',
    special_requests: 'e.g. Late check-in…',
    other:     'Describe your preference…'
  };
  const key = document.getElementById('new-pref-key').value;
  document.getElementById('new-pref-val').placeholder = hints[key] || 'Enter value…';
}

async function addPref() {
  const key = document.getElementById('new-pref-key').value;
  const val = document.getElementById('new-pref-val').value.trim();
  const msg = document.getElementById('add-pref-msg');
  msg.style.display = 'none';
  if (!val) { msg.style.display='block'; msg.className='sp-alert sp-alert-danger'; msg.textContent='Value is required.'; return; }
  const fd = new FormData(); fd.append('pref_key', key); fd.append('pref_value', val);
  const res  = await fetch(BASE + '/?url=guestProfile/addPreference', {method:'POST', body:fd, headers:{'X-Requested-With':'XMLHttpRequest'}});
  const data = await res.json();
  if (data.ok) {
    msg.style.display='block'; msg.className='sp-alert sp-alert-success'; msg.textContent='Preference added. Refreshing…';
    setTimeout(() => location.reload(), 800);
  } else {
    msg.style.display='block'; msg.className='sp-alert sp-alert-danger'; msg.textContent=data.message||'Failed.';
  }
}

/* ── Hash-based tab auto-activation ───────────────────── */
(function activateTabFromHash() {
  const validTabs = ['profile', 'reservations', 'preferences'];
  const hash = (location.hash || '').replace('#', '').toLowerCase();
  if (validTabs.includes(hash)) {
    document.querySelectorAll('.sp-tab').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.sp-panel').forEach(p => p.classList.remove('active'));
    const btn = document.querySelector('[data-tab="' + hash + '"]');
    const panel = document.getElementById('panel-' + hash);
    if (btn) btn.classList.add('active');
    if (panel) panel.classList.add('active');
  }
})();

</script>

<?php
$content = ob_get_clean();
require VIEW_PATH . '/layouts/main.php';
?>