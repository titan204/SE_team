<?php if (!empty($checkinDate)): ?>
<div id="checkin-toast" style="
    position:fixed;
    top:20px;
    left:50%;
    transform:translateX(-50%);
    width:calc(100% - 40px);
    max-width:560px;
    background:#9A3F3F;
    border:1px solid #C1856D;
    border-radius:14px;
    padding:14px 18px;
    display:flex;
    align-items:center;
    gap:14px;
    box-shadow:0 8px 32px rgba(154,63,63,0.3);
    z-index:9999;
    animation:toastSlideDown .5s cubic-bezier(.34,1.56,.64,1) both;
">
    <div style="
        width:38px;height:38px;border-radius:50%;flex-shrink:0;
        background:linear-gradient(135deg,#C1856D,#E6CFA9);
        display:flex;align-items:center;justify-content:center;font-size:16px;
    ">🔔</div>
    <div style="flex:1">
        <div style="font-size:10px;letter-spacing:2px;text-transform:uppercase;color:#FBF9D1;font-weight:500;margin-bottom:3px">
            Check-in Reminder
        </div>
        <div style="font-size:13px;color:rgba(251,249,209,0.85);font-family:'DM Sans',sans-serif">
            <?= date('D d M · g:i A', strtotime($checkinDate)) ?>
            <?= $roomNumber ? " &nbsp;·&nbsp; Room $roomNumber" : '' ?>
        </div>
    </div>
    <span
        onclick="(function(){var t=document.getElementById('checkin-toast');t.style.transition='all .3s ease';t.style.opacity='0';t.style.transform='translateX(-50%) translateY(-16px)';setTimeout(function(){t.remove()},300);})()"
        style="cursor:pointer;font-size:20px;color:rgba(251,249,209,0.5);flex-shrink:0;line-height:1"
    >×</span>
</div>
<style>
@keyframes toastSlideDown {
    from { opacity:0; transform:translateX(-50%) translateY(-24px); }
    to   { opacity:1; transform:translateX(-50%) translateY(0); }
}
</style>
<script>
setTimeout(function(){
    var t = document.getElementById('checkin-toast');
    if(t){
        t.style.transition = 'all .4s ease';
        t.style.opacity = '0';
        t.style.transform = 'translateX(-50%) translateY(-16px)';
        setTimeout(function(){ t.remove(); }, 400);
    }
}, 8000);
</script>
<?php endif; ?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600&family=DM+Sans:wght@300;400;500&display=swap');

:root {
    --primary:      #9A3F3F;
    --primary-light:#C1856D;
    --sand:         #E6CFA9;
    --cream:        #FBF9D1;
    --dark:         #3B1F1F;
    --muted:        #8B6B5E;
    --border:       rgba(193,133,109,0.25);
}

.hotel-hero {
    position:relative; height:480px;
    border-radius:24px; overflow:hidden; margin-bottom:2rem;
}
.hotel-hero img { width:100%;height:100%;object-fit:cover;display:block; }
.hotel-hero-overlay {
    position:absolute;inset:0;
    background:linear-gradient(to top,rgba(58,20,20,.88) 0%,rgba(58,20,20,.2) 50%,transparent 100%);
}
.hotel-hero-content { position:absolute;bottom:0;left:0;right:0;padding:2.5rem; }
.hotel-badge {
    display:inline-flex;align-items:center;gap:6px;
    border:1px solid #C1856D; color:#E6CFA9;
    font-size:10px;letter-spacing:2.5px;text-transform:uppercase;
    font-family:'DM Sans',sans-serif;
    padding:6px 16px;border-radius:100px;margin-bottom:16px;
    background:rgba(154,63,63,0.3);
}
.hotel-hero h1 {
    font-family:'Cormorant Garamond',serif;
    font-size:48px;font-weight:300;color:#FBF9D1;
    line-height:1.1;margin-bottom:8px;
}
.hotel-hero-sub {
    font-size:13px;color:rgba(230,207,169,0.8);
    margin-bottom:22px;letter-spacing:0.5px;font-family:'DM Sans',sans-serif;
}
.hotel-pills { display:flex;gap:8px;flex-wrap:wrap; }
.hotel-pill {
    background:rgba(154,63,63,0.25);
    border:1px solid rgba(193,133,109,0.4);
    border-radius:8px;padding:7px 14px;
    color:#E6CFA9;font-size:11px;
    display:flex;align-items:center;gap:6px;font-family:'DM Sans',sans-serif;
}

.hotel-section-lbl {
    font-size:10px;letter-spacing:3px;text-transform:uppercase;
    color:var(--primary);font-weight:500;margin-bottom:16px;
    display:flex;align-items:center;gap:10px;font-family:'DM Sans',sans-serif;
}
.hotel-section-lbl::after { content:'';flex:1;height:0.5px;background:var(--border); }

.hotel-explore-grid {
    display:grid;grid-template-columns:repeat(4,1fr);
    gap:12px;margin-bottom:2.5rem;
}
.hotel-ex-card {
    border-radius:18px;overflow:hidden;position:relative;
    height:200px;cursor:pointer;text-decoration:none;display:block;
}
.hotel-ex-card img {
    width:100%;height:100%;object-fit:cover;display:block;transition:transform .5s ease;
}
.hotel-ex-card:hover img { transform:scale(1.08); }
.hotel-ex-overlay {
    position:absolute;inset:0;
    background:linear-gradient(to top,rgba(58,20,20,.75) 0%,transparent 55%);
    transition:background .3s ease;
}
.hotel-ex-card:hover .hotel-ex-overlay {
    background:linear-gradient(to top,rgba(58,20,20,.88) 0%,rgba(58,20,20,.1) 65%);
}
.hotel-ex-label { position:absolute;bottom:0;left:0;right:0;padding:14px 16px; }
.hotel-ex-name { font-size:13px;font-weight:500;color:#FBF9D1;margin-bottom:2px;font-family:'DM Sans',sans-serif; }
.hotel-ex-sub  { font-size:10px;color:rgba(230,207,169,0.7);letter-spacing:0.5px;font-family:'DM Sans',sans-serif; }
.hotel-ex-arrow {
    position:absolute;top:12px;right:12px;
    width:30px;height:30px;border-radius:50%;
    background:rgba(193,133,109,0.25);
    border:1px solid rgba(193,133,109,0.5);
    display:flex;align-items:center;justify-content:center;
    color:#E6CFA9;font-size:13px;
    opacity:0;transform:translateY(-4px);transition:all .3s ease;
}
.hotel-ex-card:hover .hotel-ex-arrow { opacity:1;transform:translateY(0); }

.hotel-info-grid {
    display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:2.5rem;
}
.hotel-info-card {
    background:#fff;border-radius:16px;padding:1.2rem;
    border:0.5px solid rgba(193,133,109,0.2);
    position:relative;overflow:hidden;
}
.hotel-info-card::before {
    content:'';position:absolute;top:0;left:0;right:0;height:2px;
    background:linear-gradient(90deg,#9A3F3F,#C1856D);
}
.hotel-info-icon { font-size:20px;margin-bottom:10px; }
.hotel-info-lbl {
    font-size:10px;color:var(--muted);letter-spacing:1px;
    text-transform:uppercase;margin-bottom:4px;font-family:'DM Sans',sans-serif;
}
.hotel-info-val { font-size:15px;font-weight:500;color:var(--dark);font-family:'DM Sans',sans-serif; }

.hotel-reviews-grid {
    display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:2.5rem;
}
.hotel-rev {
    background:#fff;border-radius:18px;padding:1.5rem;
    border:0.5px solid rgba(193,133,109,0.2);position:relative;
}
.hotel-rev-quote {
    font-size:48px;line-height:1;
    font-family:'Cormorant Garamond',serif;
    color:#C1856D;opacity:.3;
    position:absolute;top:12px;right:20px;
}
.hotel-rev-stars  { color:#9A3F3F;font-size:12px;letter-spacing:3px;margin-bottom:10px; }
.hotel-rev-text   { font-size:15px;color:#666;line-height:1.7;font-style:italic;font-family:'Cormorant Garamond',serif; }
.hotel-rev-author {
    margin-top:16px;display:flex;align-items:center;gap:10px;
    padding-top:14px;border-top:0.5px solid rgba(193,133,109,0.15);
}
.hotel-rev-avatar {
    width:34px;height:34px;border-radius:50%;
    background:linear-gradient(135deg,#9A3F3F,#C1856D);
    display:flex;align-items:center;justify-content:center;
    color:#FBF9D1;font-size:11px;font-weight:500;flex-shrink:0;font-family:'DM Sans',sans-serif;
}
.hotel-rev-name { font-size:12px;font-weight:500;color:var(--dark);font-family:'DM Sans',sans-serif; }
.hotel-rev-from { font-size:11px;color:var(--muted);margin-top:1px;font-family:'DM Sans',sans-serif; }

.hotel-footer {
    border-top:0.5px solid var(--border);padding-top:1.5rem;
    display:flex;justify-content:space-between;align-items:center;
}
.hotel-footer-name  { font-family:'Cormorant Garamond',serif;font-size:16px;font-weight:500;color:var(--dark); }
.hotel-footer-addr  { font-size:11px;color:var(--muted);margin-top:2px;font-family:'DM Sans',sans-serif; }
.hotel-footer-phone { font-size:12px;color:var(--muted);letter-spacing:0.5px;font-family:'DM Sans',sans-serif; }

.disabled-link {
    pointer-events: none;
    cursor: default;
    opacity: 1;
}

@media(max-width:768px){
    .hotel-hero { height:340px; }
    .hotel-hero h1 { font-size:32px; }
    .hotel-explore-grid { grid-template-columns:repeat(2,1fr); }
    .hotel-info-grid    { grid-template-columns:repeat(2,1fr); }
    .hotel-reviews-grid { grid-template-columns:1fr; }
}
@media(max-width:480px){
    .hotel-explore-grid { grid-template-columns:1fr 1fr; }
    .hotel-hero-content { padding:1.5rem; }
}
</style>

<!-- HERO -->
<div class="hotel-hero">
    <img src="<?= APP_URL ?>/public/assets/images/hotel.jpg" alt="<?= htmlspecialchars(APP_NAME) ?>">
    <div class="hotel-hero-overlay"></div>
    <div class="hotel-hero-content">
        <div class="hotel-badge">★ ★ ★ ★ ★ &nbsp; 5-Star Luxury</div>
        <h1><?= htmlspecialchars(APP_NAME) ?></h1>
        <div class="hotel-hero-sub">Cairo, Egypt · Timeless luxury since 1998</div>
        <div class="hotel-pills">
            <div class="hotel-pill">📍 Nile Corniche</div>
            <div class="hotel-pill">🕐 Open 24/7</div>
            <div class="hotel-pill">✨ Free Wi-Fi</div>
        </div>
    </div>
</div>

<!-- EXPLORE -->
<p class="hotel-section-lbl">Explore the hotel</p>
<div class="hotel-explore-grid">
    <?php
    $sections = [
        ['Rooms',      'Browse available rooms', APP_URL . '/?url=rooms/guest', 'room.jpg'],
        ['Restaurant', 'Fine dining',            '',                           'restaurant.jpg'],
        ['Pool & Spa', 'Relax daily',            '',                           'pool.jpg'],
        ['Wellness',   'Gym & yoga',             '',                           'wellness.jpg'],
    ];
    foreach ($sections as $s): ?>
    <a href="<?= $s[2] !== '' ? $s[2] : 'javascript:void(0)' ?>" class="hotel-ex-card <?= $s[2] === '' ? 'disabled-link' : '' ?>">
        <img src="<?= APP_URL ?>/public/assets/images/<?= $s[3] ?>" alt="<?= htmlspecialchars($s[0]) ?>">
        <div class="hotel-ex-overlay"></div>
        <div class="hotel-ex-arrow">↗</div>
        <div class="hotel-ex-label">
            <div class="hotel-ex-name"><?= htmlspecialchars($s[0]) ?></div>
            <div class="hotel-ex-sub"><?= htmlspecialchars($s[1]) ?></div>
        </div>
    </a>
    <?php endforeach; ?>
</div>

<!-- INFO -->
<p class="hotel-section-lbl">Hotel info</p>
<div class="hotel-info-grid">
    <?php
    $info = [
        ['🛬', 'Check-in',    '3:00 PM'],
        ['🛫', 'Check-out',   '12:00 PM'],
        ['📶', 'Wi-Fi',       APP_NAME . '_Guest'],
        ['⭐', 'Guest rating', '4.9 / 5'],
    ];
    foreach ($info as $i): ?>
    <div class="hotel-info-card">
        <div class="hotel-info-icon"><?= $i[0] ?></div>
        <div class="hotel-info-lbl"><?= htmlspecialchars($i[1]) ?></div>
        <div class="hotel-info-val" <?= $i[1]==='Wi-Fi' ? 'style="font-size:13px"' : '' ?>>
            <?= htmlspecialchars($i[2]) ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- REVIEWS -->
<p class="hotel-section-lbl">What guests say</p>
<div class="hotel-reviews-grid">
    <div class="hotel-rev">
        <div class="hotel-rev-quote">"</div>
        <div class="hotel-rev-stars">★★★★★</div>
        <div class="hotel-rev-text">Absolutely breathtaking. From the moment we arrived, the staff made us feel like royalty.</div>
        <div class="hotel-rev-author">
            <div class="hotel-rev-avatar">SM</div>
            <div>
                <div class="hotel-rev-name">Sarah M.</div>
                <div class="hotel-rev-from">London, UK</div>
            </div>
        </div>
    </div>
    <div class="hotel-rev">
        <div class="hotel-rev-quote">"</div>
        <div class="hotel-rev-stars">★★★★★</div>
        <div class="hotel-rev-text">Best hotel experience I've had across all of Africa and the Middle East.</div>
        <div class="hotel-rev-author">
            <div class="hotel-rev-avatar" style="background:linear-gradient(135deg,#C1856D,#E6CFA9);color:#3B1F1F">JK</div>
            <div>
                <div class="hotel-rev-name">James K.</div>
                <div class="hotel-rev-from">New York, USA</div>
            </div>
        </div>
    </div>
</div>

<!-- FOOTER -->
<div class="hotel-footer">
    <div>
        <div class="hotel-footer-name"><?= htmlspecialchars(APP_NAME) ?></div>
        <div class="hotel-footer-addr">Nile Corniche, Cairo · info@hotel.com</div>
    </div>
    <div class="hotel-footer-phone">+20 100 000 0000</div>
</div>
