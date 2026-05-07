<?php
$pageTitle = $pageTitle ?? 'Leave Feedback';
$guest     = $guest     ?? [];
$eligible  = $eligible  ?? [];
$errors    = $errors    ?? [];
$old       = $old       ?? [];
$success   = $success   ?? null;
$h  = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$o  = fn($k) => htmlspecialchars((string)($old[$k] ?? ''), ENT_QUOTES, 'UTF-8');
$e  = fn($k) => $errors[$k] ?? null;
ob_start();
?>
<link rel="stylesheet" href="<?= APP_URL ?>/public/assets/css/home.css">

<style>
.fb-shell{min-height:calc(100vh - 56px);padding:4.5rem 1rem 3rem;
  background:radial-gradient(ellipse at 20% 50%,rgba(196,135,77,.10) 0%,transparent 60%),
             radial-gradient(ellipse at 80% 20%,rgba(62,35,40,.08) 0%,transparent 50%),
             #FDF8F2;}
.fb-card{max-width:780px;margin:0 auto;background:#fff;border-radius:20px;
  box-shadow:0 4px 6px rgba(62,35,40,.04),0 20px 60px rgba(62,35,40,.12);
  overflow:hidden;animation:fbUp .4s cubic-bezier(.22,.68,0,1.2) both;}
@keyframes fbUp{from{opacity:0;transform:translateY(24px)}to{opacity:1;transform:none}}
.fb-head{background:linear-gradient(135deg,#3E2328 0%,#6a3a20 100%);padding:2rem;text-align:center;}
.fb-head h1{color:#fff;font-size:1.5rem;font-weight:700;margin:0 0 .25rem;}
.fb-head p{color:rgba(255,248,242,.65);font-size:.875rem;margin:0;}
.fb-body{padding:2rem;}
/* Section */
.fb-sec{margin-bottom:1.75rem;}
.fb-sec-title{font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;
  color:#C4874D;border-bottom:1.5px solid rgba(196,135,77,.2);padding-bottom:.4rem;
  margin-bottom:1rem;display:flex;align-items:center;gap:.4rem;}
/* Field */
.fb-field{margin-bottom:.9rem;}
.fb-label{display:block;font-size:.78rem;font-weight:600;text-transform:uppercase;
  letter-spacing:.04em;color:#3E2328;margin-bottom:.35rem;}
.fb-select,.fb-textarea{width:100%;padding:.6rem .85rem;border:1.5px solid #e8ddd4;
  border-radius:10px;font-size:.9rem;background:#fdfaf7;color:#3E2328;
  transition:border-color .2s,box-shadow .2s;outline:none;}
.fb-select:focus,.fb-textarea:focus{border-color:#C4874D;background:#fff;
  box-shadow:0 0 0 3px rgba(196,135,77,.15);}
.fb-select.is-invalid,.fb-textarea.is-invalid{border-color:#dc3545;background:#fff8f8;}
.fb-textarea{resize:vertical;min-height:100px;}
.field-error{font-size:.75rem;color:#dc3545;margin-top:.3rem;display:flex;align-items:center;gap:.3rem;}
.eligible-hint{font-size:.74rem;color:#8B6B5E;margin-top:.25rem;}
/* Stars */
.star-group{display:flex;flex-direction:row-reverse;justify-content:flex-end;gap:.15rem;}
.star-group input{display:none;}
.star-group label{font-size:1.7rem;color:#ddd;cursor:pointer;transition:color .12s,transform .12s;line-height:1;}
.star-group input:checked ~ label,
.star-group label:hover,
.star-group label:hover ~ label{color:#F5A623;}
.star-group label:hover{transform:scale(1.18);}
.star-row{display:flex;align-items:center;gap:.9rem;flex-wrap:wrap;margin-bottom:.6rem;}
.star-row-lbl{width:170px;font-size:.82rem;font-weight:600;color:#5A3828;flex-shrink:0;}
/* Radio */
.radio-group{display:flex;gap:1.5rem;}
.radio-opt{display:flex;align-items:center;gap:.4rem;font-size:.9rem;color:#5A3828;cursor:pointer;}
.radio-opt input{accent-color:#C4874D;width:16px;height:16px;}
/* Alerts */
.fb-alert{display:flex;align-items:center;gap:.6rem;padding:.75rem 1rem;border-radius:10px;
  margin-bottom:1.2rem;font-size:.875rem;font-weight:500;}
.fb-alert.success{background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;}
.fb-alert.danger {background:#fff2f2;border:1px solid #f5c2c2;color:#9b1c1c;}
/* Button */
.fb-btn{width:100%;padding:.85rem;border:none;border-radius:10px;
  background:linear-gradient(135deg,#C4874D 0%,#a0682e 100%);
  color:#fff;font-size:1rem;font-weight:700;cursor:pointer;
  transition:transform .15s,box-shadow .15s;
  display:flex;align-items:center;justify-content:center;gap:.5rem;margin-top:.5rem;}
.fb-btn:hover{transform:translateY(-2px);box-shadow:0 8px 20px rgba(196,135,77,.4);}
.fb-btn:disabled{opacity:.6;pointer-events:none;}
@media(max-width:540px){.fb-body{padding:1.4rem 1rem;}.star-row{flex-direction:column;align-items:flex-start;}}
</style>

<nav class="hm-nav scrolled">
  <div class="inner">
    <a href="<?= APP_URL ?>/?url=home/index" class="nav-brand"><i class="bi bi-building-fill"></i>Grand Hotel</a>
    <ul class="nav-links">
      <li><a href="<?= APP_URL ?>/?url=home/index">Home</a></li>
      <li><a href="<?= APP_URL ?>/?url=guestProfile/index">My Profile</a></li>
      <li><a href="<?= APP_URL ?>/?url=feedback/myFeedback">My Reviews</a></li>
    </ul>
  </div>
</nav>

<div class="fb-shell">
 <div class="fb-card">

  <div class="fb-head">
    <div style="font-size:2.2rem;margin-bottom:.5rem;">⭐</div>
    <h1>Share Your Experience</h1>
    <p>Your feedback helps us deliver a better stay for every guest</p>
  </div>

  <div class="fb-body">

    <?php if ($success): ?>
    <div class="fb-alert success"><i class="bi bi-check-circle-fill"></i><?= $h($success) ?></div>
    <?php endif; ?>

    <?php if (!empty($errors['general'])): ?>
    <div class="fb-alert danger"><i class="bi bi-exclamation-circle-fill"></i><?= $h($errors['general']) ?></div>
    <?php endif; ?>

    <?php if (empty($eligible) && !$success): ?>
      <div class="fb-alert danger" style="margin-bottom:0;">
        <i class="bi bi-info-circle-fill"></i>
        You have no completed reservations awaiting feedback. Feedback can only be submitted after checkout.
      </div>
    <?php else: ?>

    <form method="POST" action="<?= APP_URL ?>/?url=feedback/store" id="fbForm" novalidate>

      <!-- ── Select Reservation ── -->
      <div class="fb-sec">
        <div class="fb-sec-title"><i class="bi bi-calendar-check"></i>Select Your Reservation</div>
        <div class="fb-field">
          <label class="fb-label" for="reservation_id">Reservation <span style="color:#dc3545">*</span></label>
          <select name="reservation_id" id="reservation_id"
                  class="fb-select<?= $e('reservation_id') ? ' is-invalid' : '' ?>" required>
            <option value="">— Choose a completed reservation —</option>
            <?php foreach ($eligible as $res): ?>
              <option value="<?= (int)$res['id'] ?>"
                <?= ((int)($old['reservation_id'] ?? 0)) === (int)$res['id'] ? 'selected' : '' ?>>
                Reservation #<?= (int)$res['id'] ?> —
                Room <?= $h($res['room_number'] ?? '?') ?>
                <?= !empty($res['room_type_name']) ? '(' . $h($res['room_type_name']) . ')' : '' ?> |
                <?= $h($res['check_in_date'] ?? '') ?> → <?= $h($res['check_out_date'] ?? '') ?>
              </option>
            <?php endforeach; ?>
          </select>
          <?php if ($e('reservation_id')): ?>
            <div class="field-error"><i class="bi bi-x-circle-fill"></i><?= $h($e('reservation_id')) ?></div>
          <?php endif; ?>
          <div class="eligible-hint"><i class="bi bi-info-circle me-1"></i>Only checked-out reservations without existing feedback appear here.</div>
        </div>
      </div>

      <!-- ── Star Ratings ── -->
      <div class="fb-sec">
        <div class="fb-sec-title"><i class="bi bi-star-half"></i>Rate Your Stay</div>
        <?php
        $ratings = [
          'overall_rating'     => ['label' => 'Overall Experience', 'icon' => 'bi-trophy-fill'],
          'cleanliness_rating' => ['label' => 'Room Cleanliness',   'icon' => 'bi-stars'],
          'staff_rating'       => ['label' => 'Staff & Service',    'icon' => 'bi-person-heart'],
          'food_rating'        => ['label' => 'Food Quality',       'icon' => 'bi-egg-fried'],
          'facilities_rating'  => ['label' => 'Facilities',         'icon' => 'bi-building-check'],
        ];
        foreach ($ratings as $field => $meta):
          $cur = (int)($old[$field] ?? 0);
        ?>
        <div class="star-row">
          <span class="star-row-lbl"><i class="bi <?= $meta['icon'] ?> me-1"></i><?= $meta['label'] ?> <span style="color:#dc3545">*</span></span>
          <div class="star-group" role="radiogroup">
            <?php for ($s = 5; $s >= 1; $s--): ?>
              <input type="radio" name="<?= $field ?>" id="<?= $field ?>_<?= $s ?>"
                     value="<?= $s ?>" <?= $cur === $s ? 'checked' : '' ?> required>
              <label for="<?= $field ?>_<?= $s ?>" title="<?= $s ?> star<?= $s > 1 ? 's' : '' ?>">★</label>
            <?php endfor; ?>
          </div>
          <?php if ($e($field)): ?>
            <div class="field-error"><?= $h($e($field)) ?></div>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- ── Comment ── -->
      <div class="fb-sec">
        <div class="fb-sec-title"><i class="bi bi-chat-quote"></i>Your Review</div>
        <div class="fb-field">
          <label class="fb-label" for="comment">Comments &amp; Suggestions <small style="text-transform:none;font-weight:400;color:#aaa;">(optional)</small></label>
          <textarea name="comment" id="comment" maxlength="2000"
                    class="fb-textarea<?= $e('comment') ? ' is-invalid' : '' ?>"
                    placeholder="Tell us what you loved, and what we can improve…"><?= $o('comment') ?></textarea>
          <div style="font-size:.72rem;color:#bbb;margin-top:.2rem;text-align:right;" id="charCount">0 / 2000</div>
          <?php if ($e('comment')): ?>
            <div class="field-error"><i class="bi bi-x-circle-fill"></i><?= $h($e('comment')) ?></div>
          <?php endif; ?>
        </div>
      </div>

      <!-- ── Recommend ── -->
      <div class="fb-sec">
        <div class="fb-sec-title"><i class="bi bi-hand-thumbs-up"></i>Would You Recommend Us?</div>
        <div class="radio-group">
          <label class="radio-opt">
            <input type="radio" name="recommend_hotel" value="yes"
              <?= ($old['recommend_hotel'] ?? 'yes') === 'yes' ? 'checked' : '' ?>>
            <i class="bi bi-emoji-smile-fill" style="color:#22c55e;font-size:1.2rem;"></i>
            Yes, absolutely!
          </label>
          <label class="radio-opt">
            <input type="radio" name="recommend_hotel" value="no"
              <?= ($old['recommend_hotel'] ?? '') === 'no' ? 'checked' : '' ?>>
            <i class="bi bi-emoji-frown-fill" style="color:#ef4444;font-size:1.2rem;"></i>
            Not this time
          </label>
        </div>
      </div>

      <!-- ── Submit ── -->
      <button type="submit" class="fb-btn" id="fbSubmit">
        <i class="bi bi-send-fill"></i>Submit Feedback
      </button>

    </form>
    <?php endif; ?>

  </div>
 </div>
</div>

<script>
// Character counter
const ta = document.getElementById('comment');
const cc = document.getElementById('charCount');
if (ta && cc) {
  ta.addEventListener('input', () => cc.textContent = ta.value.length + ' / 2000');
}
// Disable button on submit to prevent double-submit
document.getElementById('fbForm')?.addEventListener('submit', function() {
  document.getElementById('fbSubmit')?.setAttribute('disabled','true');
});
</script>

<?php
$content = ob_get_clean();
require VIEW_PATH . '/layouts/main.php';
?>
