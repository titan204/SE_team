<?php
/* Home Page — Grand Hotel  |  route: home/index */
$guestName   = $guestName   ?? '';
$checkinDate = $checkinDate ?? null;
$roomNumber  = $roomNumber  ?? null;
$isGuest     = !empty($_SESSION['user_id']) && ($_SESSION['user_role'] ?? '') === 'guest';
$appUrl      = APP_URL;

$rooms = [
  ['name'=>'Standard Room','price'=>500,'desc'=>'Cosy and elegantly furnished with all essential amenities for a comfortable stay.','badge'=>'Most Popular','img'=>'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=600&q=80'],
  ['name'=>'Deluxe Room','price'=>800,'desc'=>'Spacious deluxe interiors with premium bedding, lounge seating, and city views.','badge'=>'Best Value','img'=>'https://images.unsplash.com/photo-1590490360182-c33d57733427?w=600&q=80'],
  ['name'=>'Suite','price'=>1500,'desc'=>'Indulge in our signature suites with a private lounge, jacuzzi, and butler service.','badge'=>'Luxury','img'=>'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?w=600&q=80'],
];
$services=[
  ['icon'=>'bi-wifi','title'=>'Free Wi-Fi','desc'=>'High-speed fibre in every room and public area.'],
  ['icon'=>'bi-flower1','title'=>'Spa & Wellness','desc'=>'World-class spa treatments and a heated indoor pool.'],
  ['icon'=>'bi-airplane','title'=>'Airport Transfer','desc'=>'Luxury vehicle service, available 24/7.'],
  ['icon'=>'bi-headset','title'=>'24/7 Support','desc'=>'Our concierge team is always here for you.'],
  ['icon'=>'bi-cup-hot','title'=>'Fine Dining','desc'=>'Award-winning restaurant with international cuisine.'],
  ['icon'=>'bi-shield-check','title'=>'Secure & Private','desc'=>'Your safety and privacy are our top priority.'],
];
$testimonials=[
  ['stars'=>5,'text'=>'"An unforgettable experience from check-in to check-out. The suite was breathtaking and the staff truly made us feel like royalty."','name'=>'Sarah M.','loc'=>'New York, USA'],
  ['stars'=>5,'text'=>'"The finest hotel I have stayed at in years. Impeccable service, stunning rooms, and the spa is world-class."','name'=>'James K.','loc'=>'London, UK'],
  ['stars'=>5,'text'=>'"Every detail was perfect. The food, the ambience, the personalised service — I will definitely return."','name'=>'Layla H.','loc'=>'Dubai, UAE'],
];
$gallery=[
  'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=900&q=80',
  'https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?w=600&q=80',
  'https://images.unsplash.com/photo-1564501049412-61c2a3083791?w=600&q=80',
  'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=600&q=80',
  'https://images.unsplash.com/photo-1584132967334-10e028bd69f7?w=600&q=80',
];
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Grand Hotel — Experience Luxury</title>
<meta name="description" content="Grand Hotel — a world-class luxury experience with premium rooms, fine dining, and personalised service.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">
<link rel="stylesheet" href="<?= $appUrl ?>/public/assets/css/home.css">
</head>
<body>

<!-- LOADER -->
<div id="loader"><div class="loader-ring"></div></div>

<!-- NAV -->
<nav class="hm-nav" id="mainNav">
  <div class="inner">
    <a href="<?= $appUrl ?>/?url=home/index" class="nav-brand"><i class="bi bi-building-fill"></i>Grand Hotel</a>
    <ul class="nav-links">
      <li><a href="<?= $appUrl ?>/?url=rooms/guest">Rooms</a></li>
      <li><a href="#services">Services</a></li>
      <li><a href="#gallery">Gallery</a></li>
      <li><a href="#contact">Contact</a></li>
    </ul>
    <div class="nav-auth">
      <?php if($isGuest): ?>
        <a href="<?= $appUrl ?>/?url=home/guestprofile" class="btn-nav-login"><i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($guestName) ?></a>
        <a href="<?= $appUrl ?>/?url=auth/logout" class="btn-nav-reg">Sign Out</a>
      <?php else: ?>
        <a href="<?= $appUrl ?>/?url=auth/login"    class="btn-nav-login">Sign In</a>
        <a href="<?= $appUrl ?>/?url=auth/register" class="btn-nav-reg">Register</a>
      <?php endif; ?>
    </div>
    <button class="nav-toggle" id="navToggle" aria-label="Menu"><i class="bi bi-list"></i></button>
  </div>
  <div id="mobileMenu" style="display:none;" class="mobile-menu">
    <a href="<?= $appUrl ?>/?url=rooms/guest">Rooms</a><a href="#services">Services</a>
    <a href="#gallery">Gallery</a><a href="#contact">Contact</a>
    <?php if($isGuest): ?>
      <a href="<?= $appUrl ?>/?url=auth/logout">Sign Out</a>
    <?php else: ?>
      <a href="<?= $appUrl ?>/?url=auth/login">Sign In</a>
      <a href="<?= $appUrl ?>/?url=auth/register">Register</a>
    <?php endif; ?>
  </div>
</nav>

<!-- HERO -->
<section class="hero">
  <div class="hero-bg" id="heroBg"></div>
  <div class="hero-overlay"></div>
  <div class="hero-content">
    <div class="hero-badge">✦ Luxury Redefined</div>
    <h1>Experience <em>Luxury</em><br>Like Never Before</h1>
    <p>Book your perfect stay with comfort, elegance, and world-class service</p>
    <div class="hero-btns">
      <a href="<?= $appUrl ?>/?url=<?= $isGuest ? 'reservations/create' : 'auth/login' ?>" class="btn-gold"><i class="bi bi-calendar-check me-2"></i>Book Now</a>
      <a href="<?= $appUrl ?>/?url=rooms/guest" class="btn-outline-gold"><i class="bi bi-door-open me-2"></i>Explore Rooms</a>
    </div>
  </div>
  <div class="hero-scroll"><span></span>Scroll</div>
</section>

<!-- ROOMS -->
<section class="section" id="rooms">
  <div class="section-inner">
    <div class="section-hd" data-aos="fade-up">
      <div class="section-label">Our Accommodations</div>
      <h2 class="section-title">Rooms &amp; Suites</h2>
      <p class="section-sub">Each room is thoughtfully designed to provide the utmost comfort and style.</p>
    </div>
    <div class="rooms-grid">
      <?php foreach($rooms as $i=>$r): ?>
      <div class="room-card" data-aos="fade-up" data-aos-delay="<?= $i*100 ?>">
        <div class="room-img">
          <img src="<?= $r['img'] ?>" alt="<?= htmlspecialchars($r['name']) ?>" loading="lazy">
          <div class="room-badge"><?= $r['badge'] ?></div>
        </div>
        <div class="room-body">
          <h3><?= htmlspecialchars($r['name']) ?></h3>
          <p><?= htmlspecialchars($r['desc']) ?></p>
          <div class="room-footer">
            <div class="room-price">$<?= number_format($r['price']) ?><span>/night</span></div>
            <a href="<?= $appUrl ?>/?url=<?= $isGuest ? 'reservations/create' : 'auth/login' ?>" class="btn-sm-gold">Book Now</a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- SERVICES -->
<section class="section services-bg" id="services">
  <div class="section-inner">
    <div class="section-hd" data-aos="fade-up">
      <div class="section-label">What We Offer</div>
      <h2 class="section-title">Premium Services</h2>
    </div>
    <div class="services-grid">
      <?php foreach($services as $i=>$s): ?>
      <div class="svc-card" data-aos="fade-up" data-aos-delay="<?= $i*80 ?>">
        <div class="svc-icon"><i class="bi <?= $s['icon'] ?>"></i></div>
        <h4><?= htmlspecialchars($s['title']) ?></h4>
        <p><?= htmlspecialchars($s['desc']) ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- STATS -->
<div class="stats-band">
  <div class="section-inner">
    <div class="stats-grid">
      <div data-aos="zoom-in" data-aos-delay="0"><div class="stat-num" data-target="500">0</div><div class="stat-lbl">Happy Guests</div></div>
      <div data-aos="zoom-in" data-aos-delay="100"><div class="stat-num" data-target="20">0</div><div class="stat-lbl">Luxury Rooms</div></div>
      <div data-aos="zoom-in" data-aos-delay="200"><div class="stat-num" data-target="15">0</div><div class="stat-lbl">Years of Excellence</div></div>
      <div data-aos="zoom-in" data-aos-delay="300"><div class="stat-num" data-target="24">0</div><div class="stat-lbl">Hour Concierge</div></div>
    </div>
  </div>
</div>

<!-- TESTIMONIALS -->
<section class="section">
  <div class="section-inner">
    <div class="section-hd" data-aos="fade-up">
      <div class="section-label">Guest Stories</div>
      <h2 class="section-title">What Our Guests Say</h2>
    </div>
    <div class="testi-grid">
      <?php foreach($testimonials as $i=>$t): ?>
      <div class="testi-card" data-aos="fade-up" data-aos-delay="<?= $i*100 ?>">
        <div class="testi-stars"><?= str_repeat('★',$t['stars']) ?></div>
        <p class="testi-text"><?= htmlspecialchars($t['text']) ?></p>
        <div class="testi-author">
          <div class="testi-avatar"><?= strtoupper($t['name'][0]) ?></div>
          <div><div class="testi-name"><?= htmlspecialchars($t['name']) ?></div><div class="testi-loc"><?= htmlspecialchars($t['loc']) ?></div></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- GALLERY -->
<section class="section" style="padding-top:0;" id="gallery">
  <div class="section-inner">
    <div class="section-hd" data-aos="fade-up">
      <div class="section-label">Our Space</div>
      <h2 class="section-title">Photo Gallery</h2>
    </div>
    <div class="gallery-grid">
      <?php foreach($gallery as $i=>$img): ?>
      <div class="gallery-item" data-aos="fade" data-aos-delay="<?= $i*70 ?>" onclick="openLightbox('<?= $img ?>')">
        <img src="<?= $img ?>" alt="Hotel gallery <?= $i+1 ?>" loading="lazy">
        <div class="overlay"><i class="bi bi-zoom-in"></i></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- LIGHTBOX -->
<div id="lightbox"><button id="lb-close" onclick="closeLightbox()">×</button><img id="lb-img" src="" alt="Gallery"></div>

<!-- CTA -->
<div class="cta-banner" data-aos="fade-up">
  <h2>Ready for an Unforgettable Stay?</h2>
  <p>Reserve your room today and experience true luxury.</p>
  <a href="<?= $appUrl ?>/?url=<?= $isGuest ? 'reservations/create' : 'auth/login' ?>" class="btn-gold"><i class="bi bi-calendar-heart me-2"></i>Book Now</a>
</div>

<!-- FOOTER -->
<footer class="hm-footer" id="contact">
  <div class="footer-inner">
    <div class="footer-grid">
      <div>
        <div class="footer-brand"><i class="bi bi-building-fill me-2"></i>Grand Hotel</div>
        <p class="footer-desc">A world-class luxury destination where every moment is crafted to perfection.</p>
        <div class="footer-social">
          <a href="#" class="social-btn"><i class="bi bi-facebook"></i></a>
          <a href="#" class="social-btn"><i class="bi bi-instagram"></i></a>
          <a href="#" class="social-btn"><i class="bi bi-twitter-x"></i></a>
          <a href="#" class="social-btn"><i class="bi bi-linkedin"></i></a>
        </div>
      </div>
      <div>
        <div class="footer-h">Quick Links</div>
        <ul class="footer-links">
          <li><a href="#rooms">Rooms &amp; Suites</a></li>
          <li><a href="#services">Services</a></li>
          <li><a href="#gallery">Gallery</a></li>
          <li><a href="<?= $appUrl ?>/?url=auth/login">Guest Login</a></li>
        </ul>
      </div>
      <div>
        <div class="footer-h">Services</div>
        <ul class="footer-links">
          <li><a href="#">Spa &amp; Wellness</a></li>
          <li><a href="#">Fine Dining</a></li>
          <li><a href="#">Airport Transfer</a></li>
          <li><a href="#">Event Spaces</a></li>
        </ul>
      </div>
      <div>
        <div class="footer-h">Contact</div>
        <ul class="footer-links">
          <li><i class="bi bi-geo-alt me-1"></i>123 Luxury Ave, Cairo</li>
          <li><a href="tel:+201000000000"><i class="bi bi-telephone me-1"></i>+20 100 000 0000</a></li>
          <li><a href="mailto:info@grandhotel.com"><i class="bi bi-envelope me-1"></i>info@grandhotel.com</a></li>
          <li><i class="bi bi-clock me-1"></i>24/7 Concierge</li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">© <?= date('Y') ?> Grand Hotel. All rights reserved. Designed with ♥</div>
  </div>
</footer>

<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
AOS.init({duration:700,once:true,offset:60});
// Loader
window.addEventListener('load',()=>{
  document.getElementById('heroBg').style.backgroundImage="url('<?= $appUrl ?>/public/assets/images/hero_bg.png')";
  document.getElementById('heroBg').classList.add('loaded');
  setTimeout(()=>document.getElementById('loader').classList.add('hidden'),400);
});
// Sticky nav
window.addEventListener('scroll',()=>document.getElementById('mainNav').classList.toggle('scrolled',scrollY>60));
// Mobile menu
document.getElementById('navToggle').onclick=function(){
  const m=document.getElementById('mobileMenu');
  m.style.display=m.style.display==='none'?'flex':'none';
};
// Stats counter
const counters=document.querySelectorAll('.stat-num[data-target]');
const runCount=el=>{const t=+el.dataset.target,dur=1400,step=dur/t;let c=0;const i=setInterval(()=>{c=Math.min(c+1,t);el.textContent=c+(t>=100?'+':'');if(c>=t)clearInterval(i);},step);};
const ob=new IntersectionObserver(entries=>{entries.forEach(e=>{if(e.isIntersecting){runCount(e.target);ob.unobserve(e.target);}});},{threshold:.5});
counters.forEach(c=>ob.observe(c));
// Lightbox
function openLightbox(src){document.getElementById('lb-img').src=src;document.getElementById('lightbox').classList.add('open');}
function closeLightbox(){document.getElementById('lightbox').classList.remove('open');document.getElementById('lb-img').src='';}
document.getElementById('lightbox').addEventListener('click',e=>{if(e.target===e.currentTarget)closeLightbox();});
document.addEventListener('keydown',e=>{if(e.key==='Escape')closeLightbox();});
// Smooth scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach(a=>a.addEventListener('click',e=>{const t=document.querySelector(a.getAttribute('href'));if(t){e.preventDefault();t.scrollIntoView({behavior:'smooth',block:'start'});document.getElementById('mobileMenu').style.display='none';}}));
</script>
</body>
</html>
<?php
$content = ob_get_clean();
// Override main layout — homepage has its own full layout
echo $content;
?>
