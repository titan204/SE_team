<?php
$pageTitle    = $pageTitle    ?? 'My Feedback';
$feedbackList = $feedbackList ?? [];
$h = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$stars = function(int $n): string {
    $o = '';
    for ($i = 1; $i <= 5; $i++)
        $o .= $i <= $n
            ? '<i class="bi bi-star-fill" style="color:#F5A623;font-size:.95rem;"></i>'
            : '<i class="bi bi-star"      style="color:#ddd;font-size:.95rem;"></i>';
    return $o;
};
ob_start();
?>
<link rel="stylesheet" href="<?= APP_URL ?>/public/assets/css/home.css">
<style>
.mfb-shell{min-height:calc(100vh - 56px);padding:4.5rem 1rem 3rem;
  background:radial-gradient(ellipse at 20% 50%,rgba(196,135,77,.10) 0%,transparent 60%),#FDF8F2;}
.mfb-wrap{max-width:860px;margin:0 auto;}
.mfb-hero{text-align:center;margin-bottom:2rem;}
.mfb-hero h1{font-size:1.8rem;font-weight:800;color:#3E2328;margin:0 0 .3rem;}
.mfb-hero p{color:#8B6B5E;font-size:.95rem;margin:0;}
.btn-new-fb{display:inline-flex;align-items:center;gap:.4rem;padding:.6rem 1.3rem;
  border-radius:10px;background:linear-gradient(135deg,#C4874D,#a0682e);
  color:#fff;font-weight:700;font-size:.875rem;text-decoration:none;margin-top:1rem;
  transition:transform .15s,box-shadow .15s;}
.btn-new-fb:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(196,135,77,.35);color:#fff;}
.mfb-card{background:#fff;border-radius:16px;box-shadow:0 2px 20px rgba(62,35,40,.07);
  margin-bottom:1.2rem;padding:1.4rem 1.6rem;border-left:4px solid #C4874D;
  animation:fbUp .35s ease both;}
@keyframes fbUp{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:none}}
.mfb-card:hover{box-shadow:0 6px 28px rgba(62,35,40,.12);}
.mfb-top{display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:.5rem;margin-bottom:.75rem;}
.mfb-title{font-weight:700;color:#3E2328;font-size:1rem;}
.mfb-meta{font-size:.78rem;color:#8B6B5E;margin-top:.15rem;}
.badge-resolved{font-size:.68rem;font-weight:700;padding:.22rem .6rem;border-radius:99px;
  background:#dcfce7;color:#15803d;text-transform:uppercase;white-space:nowrap;}
.badge-pending{font-size:.68rem;font-weight:700;padding:.22rem .6rem;border-radius:99px;
  background:#fef3c7;color:#92400e;text-transform:uppercase;white-space:nowrap;}
.mfb-ratings{display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:.5rem;margin-bottom:.75rem;}
.mfb-rating-box{background:#fdf8f4;border-radius:8px;padding:.4rem .65rem;}
.mfb-rating-lbl{font-size:.64rem;text-transform:uppercase;letter-spacing:.8px;color:#8B6B5E;margin-bottom:.2rem;}
.mfb-comment{font-size:.875rem;color:#5A3828;background:#fdf8f4;padding:.7rem .9rem;
  border-radius:8px;border-left:3px solid #C4874D;margin-top:.6rem;white-space:pre-line;line-height:1.5;}
.mfb-rec{font-size:.82rem;margin-top:.5rem;display:flex;align-items:center;gap:.4rem;}
.mfb-empty{background:#fff;border-radius:16px;padding:3rem;text-align:center;color:#8B6B5E;
  box-shadow:0 2px 20px rgba(62,35,40,.07);}
.mfb-empty i{font-size:3rem;display:block;margin-bottom:.75rem;opacity:.35;}
</style>

<nav class="hm-nav scrolled">
  <div class="inner">
    <a href="<?= APP_URL ?>/?url=home/index" class="nav-brand"><i class="bi bi-building-fill"></i>Grand Hotel</a>
    <ul class="nav-links">
      <li><a href="<?= APP_URL ?>/?url=home/index">Home</a></li>
      <li><a href="<?= APP_URL ?>/?url=guestProfile/index">My Profile</a></li>
      <li><a href="<?= APP_URL ?>/?url=feedback/create">Leave Feedback</a></li>
    </ul>
  </div>
</nav>

<div class="mfb-shell">
 <div class="mfb-wrap">

  <div class="mfb-hero">
    <h1><i class="bi bi-chat-quote me-2" style="color:#C4874D;"></i>My Feedback History</h1>
    <p>Reviews you've submitted for past stays at Grand Hotel</p>
    <a href="<?= APP_URL ?>/?url=feedback/create" class="btn-new-fb">
      <i class="bi bi-plus-circle-fill"></i>Leave New Feedback
    </a>
  </div>

  <?php if (empty($feedbackList)): ?>
    <div class="mfb-empty">
      <i class="bi bi-chat-square-dots"></i>
      <p style="font-weight:600;margin-bottom:.5rem;">No feedback submitted yet.</p>
      <p style="font-size:.85rem;">After checkout, share your experience to help us improve.</p>
      <a href="<?= APP_URL ?>/?url=feedback/create" class="btn-new-fb" style="margin-top:.75rem;">
        <i class="bi bi-send"></i>Submit Your First Review
      </a>
    </div>
  <?php else: ?>

    <?php foreach ($feedbackList as $fb): ?>
    <div class="mfb-card">
      <div class="mfb-top">
        <div>
          <div class="mfb-title">
            Reservation #<?= (int)$fb['reservation_id'] ?>
            <?php if (!empty($fb['room_number'])): ?>
              — Room <?= $h($fb['room_number']) ?>
            <?php endif; ?>
          </div>
          <div class="mfb-meta">
            Stay: <strong><?= $h($fb['check_in_date'] ?? '') ?></strong>
            → <strong><?= $h($fb['check_out_date'] ?? '') ?></strong>
            &nbsp;·&nbsp; Submitted: <?= $h(date('M j, Y', strtotime($fb['created_at']))) ?>
          </div>
        </div>
        <span class="<?= $fb['is_resolved'] ? 'badge-resolved' : 'badge-pending' ?>">
          <?= $fb['is_resolved'] ? '✓ Resolved' : 'Pending Review' ?>
        </span>
      </div>

      <div class="mfb-ratings">
        <?php foreach ([
          'overall_rating'     => 'Overall',
          'cleanliness_rating' => 'Cleanliness',
          'staff_rating'       => 'Staff',
          'food_rating'        => 'Food',
          'facilities_rating'  => 'Facilities',
        ] as $col => $lbl): ?>
          <div class="mfb-rating-box">
            <div class="mfb-rating-lbl"><?= $lbl ?></div>
            <?= $stars((int)$fb[$col]) ?>
          </div>
        <?php endforeach; ?>
      </div>

      <?php if (!empty(trim($fb['comment'] ?? ''))): ?>
        <div class="mfb-comment"><?= $h($fb['comment']) ?></div>
      <?php endif; ?>

      <div class="mfb-rec">
        <?php if ($fb['recommend_hotel']): ?>
          <i class="bi bi-hand-thumbs-up-fill" style="color:#22c55e;font-size:1.1rem;"></i>
          <span style="color:#15803d;font-weight:600;">Would recommend Grand Hotel</span>
        <?php else: ?>
          <i class="bi bi-hand-thumbs-down-fill" style="color:#ef4444;font-size:1.1rem;"></i>
          <span style="color:#b91c1c;font-weight:600;">Would not recommend</span>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>

  <?php endif; ?>

 </div>
</div>

<?php
$content = ob_get_clean();
require VIEW_PATH . '/layouts/main.php';
?>
