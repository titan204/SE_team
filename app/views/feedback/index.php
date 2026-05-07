<?php
$pageTitle    = $pageTitle    ?? 'Guest Feedback';
$feedbackList = $feedbackList ?? [];
$averages     = $averages     ?? [];
$filters      = $filters      ?? [];
$guests       = $guests       ?? [];
$success      = $success      ?? null;
$h = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');

$stars = function(float $n): string {
    $o = '';
    for ($i = 1; $i <= 5; $i++) {
        if ($n >= $i)        $o .= '<i class="bi bi-star-fill"  style="color:#F5A623;font-size:.8rem;"></i>';
        elseif ($n >= $i-.5) $o .= '<i class="bi bi-star-half"  style="color:#F5A623;font-size:.8rem;"></i>';
        else                 $o .= '<i class="bi bi-star"        style="color:#ddd;font-size:.8rem;"></i>';
    }
    return $o;
};
ob_start();
?>
<style>
/* ── Admin Feedback Panel ── */
.afb{padding:1.5rem;}
.afb-heading{font-size:1.45rem;font-weight:800;color:#2d1f1f;margin:0 0 .2rem;}
.afb-sub{color:#7a6055;font-size:.88rem;margin:0 0 1.5rem;}
/* Alert */
.afb-ok{display:flex;align-items:center;gap:.5rem;padding:.65rem 1rem;border-radius:8px;
  background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;font-size:.875rem;margin-bottom:1.2rem;}
/* Stats */
.afb-stats{display:grid;grid-template-columns:repeat(auto-fill,minmax(145px,1fr));gap:.85rem;margin-bottom:1.5rem;}
.afb-stat{background:#fff;border-radius:12px;padding:1rem 1.1rem;border:1px solid #ede0d4;
  box-shadow:0 2px 10px rgba(62,35,40,.05);text-align:center;}
.afb-stat-val{font-size:1.65rem;font-weight:800;color:#C4874D;line-height:1;}
.afb-stat-lbl{font-size:.68rem;text-transform:uppercase;letter-spacing:.8px;color:#8B6B5E;margin-top:.3rem;}
/* Filter bar */
.afb-filter{background:#fff;border-radius:12px;border:1px solid #ede0d4;padding:1rem 1.2rem;margin-bottom:1.3rem;}
.afb-filter-title{font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#C4874D;margin-bottom:.6rem;}
.afb-fg{display:flex;flex-wrap:wrap;gap:.65rem;align-items:flex-end;}
.afb-ff{display:flex;flex-direction:column;gap:.2rem;}
.afb-ff label{font-size:.68rem;text-transform:uppercase;letter-spacing:.07em;color:#8B6B5E;font-weight:600;}
.afb-sel,.afb-inp{padding:.42rem .7rem;border:1.5px solid #e0cfc4;border-radius:7px;
  font-size:.82rem;background:#fafaf8;color:#3E2328;outline:none;min-width:130px;}
.afb-sel:focus,.afb-inp:focus{border-color:#C4874D;}
.afb-apply{padding:.44rem 1rem;border:none;border-radius:7px;background:#3E2328;color:#fff;
  font-size:.82rem;font-weight:600;cursor:pointer;transition:background .2s;}
.afb-apply:hover{background:#6a3a20;}
.afb-clear{padding:.44rem .9rem;border:1.5px solid #e0cfc4;border-radius:7px;background:#fff;
  color:#8B6B5E;font-size:.82rem;cursor:pointer;text-decoration:none;}
.afb-clear:hover{border-color:#C4874D;color:#C4874D;}
/* Table */
.afb-table-wrap{background:#fff;border-radius:12px;border:1px solid #ede0d4;overflow:hidden;
  box-shadow:0 2px 10px rgba(62,35,40,.05);}
.afb-tbl{width:100%;border-collapse:collapse;}
.afb-tbl thead tr{background:linear-gradient(135deg,#3E2328,#6a3a20);}
.afb-tbl thead th{padding:.65rem .85rem;font-size:.68rem;font-weight:700;text-transform:uppercase;
  letter-spacing:.08em;color:rgba(255,255,255,.85);text-align:left;white-space:nowrap;}
.afb-tbl tbody tr{border-bottom:1px solid #f5ede6;transition:background .12s;}
.afb-tbl tbody tr:hover{background:#fdf8f4;}
.afb-tbl td{padding:.6rem .85rem;font-size:.82rem;color:#5A3828;vertical-align:middle;}
.bres{display:inline-block;font-size:.65rem;font-weight:700;padding:.18rem .55rem;border-radius:99px;
  text-transform:uppercase;background:#dcfce7;color:#15803d;}
.bpend{display:inline-block;font-size:.65rem;font-weight:700;padding:.18rem .55rem;border-radius:99px;
  text-transform:uppercase;background:#fef3c7;color:#92400e;}
.afb-cmnt{max-width:220px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
  font-size:.78rem;color:#8B6B5E;font-style:italic;}
.resolve-btn{padding:.28rem .75rem;font-size:.74rem;font-weight:600;border:none;border-radius:6px;
  background:linear-gradient(135deg,#22c55e,#15803d);color:#fff;cursor:pointer;white-space:nowrap;}
.resolve-btn:hover{opacity:.85;} .resolve-btn:disabled{opacity:.4;cursor:not-allowed;}
.afb-empty{padding:3rem;text-align:center;color:#8B6B5E;font-size:.9rem;}
</style>

<div class="afb">

  <h1 class="afb-heading"><i class="bi bi-chat-square-quote me-2"></i>Guest Feedback</h1>
  <p class="afb-sub">Monitor, filter, and respond to guest reviews.</p>

  <?php if ($success): ?>
  <div class="afb-ok"><i class="bi bi-check-circle-fill"></i><?= $h($success) ?></div>
  <?php endif; ?>

  <!-- Stats -->
  <?php $tot = (int)($averages['total'] ?? 0); ?>
  <div class="afb-stats">
    <div class="afb-stat">
      <div class="afb-stat-val"><?= $tot ?></div>
      <div class="afb-stat-lbl">Total Reviews</div>
    </div>
    <div class="afb-stat">
      <div class="afb-stat-val"><?= number_format((float)($averages['avg_overall'] ?? 0), 1) ?></div>
      <div class="afb-stat-lbl">Avg Overall</div>
      <div style="margin-top:.3rem;"><?= $stars((float)($averages['avg_overall'] ?? 0)) ?></div>
    </div>
    <div class="afb-stat">
      <div class="afb-stat-val"><?= number_format((float)($averages['avg_cleanliness'] ?? 0), 1) ?></div>
      <div class="afb-stat-lbl">Cleanliness</div>
    </div>
    <div class="afb-stat">
      <div class="afb-stat-val"><?= number_format((float)($averages['avg_staff'] ?? 0), 1) ?></div>
      <div class="afb-stat-lbl">Staff</div>
    </div>
    <div class="afb-stat">
      <div class="afb-stat-val"><?= number_format((float)($averages['avg_food'] ?? 0), 1) ?></div>
      <div class="afb-stat-lbl">Food</div>
    </div>
    <div class="afb-stat">
      <div class="afb-stat-val"><?= number_format((float)($averages['avg_facilities'] ?? 0), 1) ?></div>
      <div class="afb-stat-lbl">Facilities</div>
    </div>
    <div class="afb-stat">
      <div class="afb-stat-val"><?= (int)($averages['recommend_pct'] ?? 0) ?>%</div>
      <div class="afb-stat-lbl">Recommend</div>
    </div>
  </div>

  <!-- Filter -->
  <div class="afb-filter">
    <div class="afb-filter-title"><i class="bi bi-funnel me-1"></i>Filter</div>
    <form method="GET" action="<?= APP_URL ?>/?url=feedback/index">
      <div class="afb-fg">
        <div class="afb-ff">
          <label>Rating</label>
          <select name="rating" class="afb-sel">
            <option value="">Any</option>
            <?php for ($r = 5; $r >= 1; $r--): ?>
              <option value="<?= $r ?>" <?= ($filters['rating'] ?? '') == $r ? 'selected' : '' ?>>
                <?= $r ?> ★
              </option>
            <?php endfor; ?>
          </select>
        </div>
        <div class="afb-ff">
          <label>Guest</label>
          <select name="guest_id" class="afb-sel">
            <option value="">All guests</option>
            <?php foreach ($guests as $g): ?>
              <option value="<?= (int)$g['id'] ?>"
                <?= ($filters['guest_id'] ?? '') == $g['id'] ? 'selected' : '' ?>>
                <?= $h($g['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="afb-ff">
          <label>From</label>
          <input type="date" name="date_from" class="afb-inp" value="<?= $h($filters['date_from'] ?? '') ?>">
        </div>
        <div class="afb-ff">
          <label>To</label>
          <input type="date" name="date_to" class="afb-inp" value="<?= $h($filters['date_to'] ?? '') ?>">
        </div>
        <div class="afb-ff">
          <label>Status</label>
          <select name="is_resolved" class="afb-sel">
            <option value="">All</option>
            <option value="0" <?= isset($filters['is_resolved']) && $filters['is_resolved'] == '0' ? 'selected' : '' ?>>Pending</option>
            <option value="1" <?= ($filters['is_resolved'] ?? '') == '1' ? 'selected' : '' ?>>Resolved</option>
          </select>
        </div>
        <button type="submit" class="afb-apply"><i class="bi bi-search me-1"></i>Apply</button>
        <a href="<?= APP_URL ?>/?url=feedback/index" class="afb-clear">Clear</a>
      </div>
    </form>
  </div>

  <!-- Table -->
  <div class="afb-table-wrap">
    <?php if (empty($feedbackList)): ?>
      <div class="afb-empty"><i class="bi bi-inbox" style="font-size:2rem;opacity:.3;display:block;margin-bottom:.5rem;"></i>No feedback found.</div>
    <?php else: ?>
    <div style="overflow-x:auto;">
    <table class="afb-tbl">
      <thead>
        <tr>
          <th>#</th><th>Guest</th><th>Room</th><th>Stay</th>
          <th>Overall</th><th>Clean</th><th>Staff</th><th>Food</th><th>Facilities</th>
          <th>Rec?</th><th>Comment</th><th>Date</th><th>Status</th><th></th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($feedbackList as $fb): $fid = (int)$fb['id']; ?>
        <tr id="fbr-<?= $fid ?>">
          <td><?= $fid ?></td>
          <td>
            <strong><?= $h($fb['guest_name'] ?? '') ?></strong><br>
            <small style="color:#aaa;"><?= $h($fb['guest_email'] ?? '') ?></small>
          </td>
          <td>
            <?= $h($fb['room_number'] ?? '—') ?>
            <?php if (!empty($fb['room_type_name'])): ?><br><small style="color:#aaa;"><?= $h($fb['room_type_name']) ?></small><?php endif; ?>
          </td>
          <td style="white-space:nowrap;font-size:.75rem;">
            <?= $h($fb['check_in_date'] ?? '') ?><br>→ <?= $h($fb['check_out_date'] ?? '') ?>
          </td>
          <td><?= $stars((int)$fb['overall_rating']) ?></td>
          <td><?= $stars((int)$fb['cleanliness_rating']) ?></td>
          <td><?= $stars((int)$fb['staff_rating']) ?></td>
          <td><?= $stars((int)$fb['food_rating']) ?></td>
          <td><?= $stars((int)$fb['facilities_rating']) ?></td>
          <td style="text-align:center;">
            <?= $fb['recommend_hotel']
              ? '<i class="bi bi-hand-thumbs-up-fill" style="color:#22c55e;font-size:1rem;"></i>'
              : '<i class="bi bi-hand-thumbs-down-fill" style="color:#ef4444;font-size:1rem;"></i>' ?>
          </td>
          <td><div class="afb-cmnt" title="<?= $h($fb['comment'] ?? '') ?>"><?= $h($fb['comment'] ?? '—') ?></div></td>
          <td style="white-space:nowrap;font-size:.75rem;"><?= $h(date('M j, Y', strtotime($fb['created_at']))) ?></td>
          <td id="fbs-<?= $fid ?>">
            <?php if ($fb['is_resolved']): ?>
              <span class="bres">✓ Resolved</span>
              <?php if (!empty($fb['resolved_by_name'])): ?>
                <div style="font-size:.65rem;color:#aaa;margin-top:.15rem;">by <?= $h($fb['resolved_by_name']) ?></div>
              <?php endif; ?>
            <?php else: ?>
              <span class="bpend">Pending</span>
            <?php endif; ?>
          </td>
          <td id="fba-<?= $fid ?>">
            <?php if (!$fb['is_resolved']): ?>
              <button class="resolve-btn" onclick="resolveFb(<?= $fid ?>)" id="rbtn-<?= $fid ?>">
                <i class="bi bi-check-lg me-1"></i>Resolve
              </button>
            <?php else: ?>
              <span style="font-size:.72rem;color:#ccc;">Done</span>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    </div>
    <?php endif; ?>
  </div>
</div>

<script>
async function resolveFb(id) {
  if (!confirm('Mark feedback #' + id + ' as resolved?')) return;
  const btn = document.getElementById('rbtn-' + id);
  if (btn) { btn.disabled = true; btn.textContent = '…'; }

  const fd = new FormData();
  const res = await fetch('<?= APP_URL ?>/?url=feedback/resolve/' + id, {
    method: 'POST', body: fd, headers: {'X-Requested-With': 'XMLHttpRequest'}
  });
  const data = await res.json();
  if (data.ok) {
    document.getElementById('fbs-' + id).innerHTML = '<span class="bres">✓ Resolved</span>';
    document.getElementById('fba-' + id).innerHTML = '<span style="font-size:.72rem;color:#ccc;">Done</span>';
  } else {
    alert(data.message || 'Error resolving feedback.');
    if (btn) { btn.disabled = false; btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Resolve'; }
  }
}
</script>

<?php
$content = ob_get_clean();
require VIEW_PATH . '/layouts/main.php';
?>
