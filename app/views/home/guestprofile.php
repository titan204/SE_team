<?php
$pageTitle = 'My Profile';
$guest     = $guest ?? [];

ob_start();
?>

<style>
.profile-avatar {
    width:90px;height:90px;border-radius:50%;
    background:linear-gradient(135deg,#9A3F3F,#C1856D);
    display:flex;align-items:center;justify-content:center;
    font-size:36px;font-weight:600;color:#FBF9D1;
    margin:0 auto 1rem;
}
.profile-card {
    max-width:560px;margin:2rem auto;
    border-radius:16px;
    border:0.5px solid rgba(193,133,109,0.25);
    overflow:hidden;
}
.profile-header {
    background:#9A3F3F;
    padding:2rem;text-align:center;
}
.profile-name {
    font-size:20px;font-weight:600;color:#FBF9D1;margin-bottom:4px;
}
.profile-role {
    font-size:12px;letter-spacing:2px;text-transform:uppercase;color:#E6CFA9;
}
.profile-body {
    background:#fff;padding:1.5rem 2rem;
}
.profile-row {
    display:flex;justify-content:space-between;align-items:center;
    padding:12px 0;
    border-bottom:0.5px solid rgba(193,133,109,0.15);
}
.profile-row:last-child { border-bottom:none; }
.profile-lbl {
    font-size:11px;text-transform:uppercase;letter-spacing:1px;color:#8B6B5E;
}
.profile-val {
    font-size:14px;font-weight:500;color:#3B1F1F;
}
.profile-card-footer {
    background:#FBF9D1;
    padding:.85rem 2rem;text-align:center;
    border-top:0.5px solid rgba(193,133,109,0.2);
}
.profile-card-footer a {
    font-size:12px;color:#9A3F3F;text-decoration:none;font-weight:500;
}
.profile-card-footer a:hover { text-decoration:underline; }
</style>

<div class="profile-card shadow-sm">

    <!-- Header -->
    <div class="profile-header">
        <div class="profile-avatar">
            <?= strtoupper(substr($guest['name'] ?? 'G', 0, 1)) ?>
        </div>
        <div class="profile-name"><?= htmlspecialchars($guest['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
        <div class="profile-role">Guest</div>
    </div>

    <!-- Body -->
    <div class="profile-body">
        <div class="profile-row">
            <span class="profile-lbl">Email</span>
            <span class="profile-val"><?= htmlspecialchars($guest['email'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span>
        </div>
        <div class="profile-row">
            <span class="profile-lbl">Phone</span>
            <span class="profile-val"><?= htmlspecialchars($guest['phone'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span>
        </div>
        <div class="profile-row">
            <span class="profile-lbl">Nationality</span>
            <span class="profile-val"><?= htmlspecialchars($guest['nationality'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span>
        </div>
        <div class="profile-row">
            <span class="profile-lbl">ID / Passport</span>
            <span class="profile-val"><?= htmlspecialchars($guest['national_id'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span>
        </div>
        <div class="profile-row">
            <span class="profile-lbl">Member since</span>
            <span class="profile-val">
                <?= !empty($guest['created_at']) ? date('d M Y', strtotime($guest['created_at'])) : '—' ?>
            </span>
        </div>
    </div>

    <!-- Footer -->
    <div class="profile-card-footer">
        <a href="<?= APP_URL ?>">← Back to Home</a>
    </div>

</div>

<?php
$content = ob_get_clean();
require VIEW_PATH . '/layouts/main.php';
?>