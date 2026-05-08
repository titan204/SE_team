<?php
/* Home Page — Grand Hotel  |  route: home/index */
$guestName   = $guestName   ?? '';
$checkinDate = $checkinDate ?? null;
$roomNumber  = $roomNumber  ?? null;
$isGuest     = !empty($_SESSION['user_id']) && ($_SESSION['user_role'] ?? '') === 'guest';
$appUrl      = APP_URL;
$imgBase     = $appUrl . '/public/assets/images/';
$vidBase     = $appUrl . '/public/assets/videos/';

$rooms = [
  ['name'=>'Standard Room','price'=>500,'desc'=>'Elegantly furnished with premium bedding, smart TV, and serene city views for a perfect stay.','badge'=>'Most Popular','img'=>$imgBase.'hallway.jpg'],
  ['name'=>'Deluxe Room','price'=>800,'desc'=>'Spacious interiors with a private lounge, butler-pressed linens, and panoramic city skyline views.','badge'=>'Best Value','img'=>$imgBase.'suite_view.jpg'],
  ['name'=>'Grand Suite','price'=>1500,'desc'=>'Our signature suite — private jacuzzi, in-room dining, dedicated butler, and breathtaking rooftop views.','badge'=>'Luxury','img'=>$imgBase.'rooftop.jpg'],
];
$services=[
  ['icon'=>'bi-wifi','title'=>'Free Wi-Fi','desc'=>'Fibre-speed internet throughout every room and public space.'],
  ['icon'=>'bi-flower1','title'=>'Spa & Wellness','desc'=>'World-class treatments, heated pool, and holistic therapies.'],
  ['icon'=>'bi-airplane','title'=>'Airport Transfer','desc'=>'Luxury private vehicle service available around the clock.'],
  ['icon'=>'bi-headset','title'=>'24/7 Concierge','desc'=>'Our dedicated team is always ready to assist you.'],
  ['icon'=>'bi-cup-hot','title'=>'Fine Dining','desc'=>'Award-winning rooftop restaurant with international cuisine.'],
  ['icon'=>'bi-shield-check','title'=>'Safe & Private','desc'=>'Your security and privacy are our highest priority.'],
];
$testimonials=[
  ['stars'=>5,'text'=>'"From the moment we arrived, everything felt magical. The suite was breathtaking and the staff anticipated our every need."','name'=>'Sarah M.','loc'=>'New York, USA'],
  ['stars'=>5,'text'=>'"The finest hotel experience I have had in over a decade. Impeccable service, stunning interiors, and a spa that is truly world-class."','name'=>'James K.','loc'=>'London, UK'],
  ['stars'=>5,'text'=>'"Every detail was crafted to perfection — the food, the ambience, the personalised touches. We will absolutely return."','name'=>'Layla H.','loc'=>'Dubai, UAE'],
];
$gallery=[
  ['src'=>$imgBase.'lobby_fountain.jpg','alt'=>'Hotel lobby with fountain'],
  ['src'=>$imgBase.'lobby_enter.jpg','alt'=>'Grand Hotel entrance lobby'],
  ['src'=>$imgBase.'suite_view.jpg','alt'=>'Luxury suite city view'],
  ['src'=>$imgBase.'hallway.jpg','alt'=>'Luxury hotel hallway'],
  ['src'=>$imgBase.'rooftop.jpg','alt'=>'Rooftop restaurant at sunset'],
  ['src'=>$imgBase.'rainy_night.jpg','alt'=>'Grand Hotel rainy night arrivals'],
  ['src'=>$imgBase.'closeup.jpg','alt'=>'Hotel detail close-up'],
  ['src'=>$imgBase.'hotel_exterior.jpg','alt'=>'Hotel exterior'],
  ['src'=>$imgBase.'elevator.jpg','alt'=>'Luxury elevator interior'],
];
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Grand Hotel — Experience Luxury</title>
<meta name="description" content="Grand Hotel — a world-class luxury destination with premium rooms, fine dining, spa, and personalised service in Cairo.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">
<link rel="stylesheet" href="<?= $appUrl ?>/public/assets/css/home.css">
<!-- Three.js + GSAP -->
<script src="https://cdn.jsdelivr.net/npm/three@0.149.0/build/three.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
</head>
<body>

<!-- LOADER -->
<div id="loader">
  <div class="loader-inner">
    <div class="loader-logo">GRAND HOTEL</div>
    <div class="loader-ring"></div>
  </div>
</div>

<!-- NAV -->
<nav class="hm-nav" id="mainNav">
  <div class="inner">
    <a href="<?= $appUrl ?>/?url=home/index" class="nav-brand"><i class="bi bi-building-fill"></i>Grand Hotel</a>
    <ul class="nav-links">
      <li><a href="#rooms">Rooms</a></li>
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
  <div id="mobileMenu" class="mobile-menu">
    <a href="#rooms">Rooms</a><a href="#services">Services</a>
    <a href="#gallery">Gallery</a><a href="#contact">Contact</a>
    <?php if($isGuest): ?>
      <a href="<?= $appUrl ?>/?url=auth/logout">Sign Out</a>
    <?php else: ?>
      <a href="<?= $appUrl ?>/?url=auth/login">Sign In</a>
      <a href="<?= $appUrl ?>/?url=auth/register">Register</a>
    <?php endif; ?>
  </div>
</nav>

<!-- ═══ HERO — CINEMATIC LUXURY ═══ -->
<section class="hero-3d" id="hero">

  <!-- 1. Video base layer -->
  <div class="hero-vid-layer">
    <video autoplay muted loop playsinline>
      <source src="<?= $vidBase ?>hotel_commercial_1.mp4" type="video/mp4">
    </video>
  </div>

  <!-- 2. Three.js canvas -->
  <canvas id="heroCanvas"></canvas>

  <!-- 3. CSS atmospheric orbs -->
  <div class="h-orb h-orb-1"></div>
  <div class="h-orb h-orb-2"></div>
  <div class="h-orb h-orb-3"></div>

  <!-- 4. Overlays -->
  <div class="h-overlay"></div>
  <div class="h-vignette"></div>
  <div class="h-bottom-glow"></div>

  <!-- Side text (desktop only) -->
  <div class="h-side h-side-l h-side">
    <div class="h-side-bar"></div>
    <span>GRAND HOTEL · CAIRO · EGYPT</span>
    <div class="h-side-bar"></div>
  </div>
  <div class="h-side h-side-r h-side">
    <div class="h-side-bar"></div>
    <span>LUXURY · ELEGANCE · PRESTIGE</span>
    <div class="h-side-bar"></div>
  </div>

  <!-- ── MAIN CONTENT ── -->
  <div class="h-content">

    <!-- Pre-tag -->
    <div class="h-pretag">
      <span class="h-pretag-bar"></span>
      Established 2009 &nbsp;·&nbsp; Cairo, Egypt
      <span class="h-pretag-bar"></span>
    </div>

    <!-- MASSIVE Title -->
    <h1 class="h-title">
      <span class="h-word-top">Experience</span>
      <span class="h-word-mid"><em>Luxury</em></span>
      <span class="h-word-bot">Like Never Before</span>
    </h1>

    <!-- Gold ruled divider -->
    <div class="h-ruled">
      <div class="h-ruled-line"></div>
      <i class="bi bi-diamond-fill h-ruled-diamond"></i>
      <div class="h-ruled-line"></div>
    </div>

    <!-- Tagline -->
    <p class="h-sub">
      Every detail is crafted to perfection — from our award-winning suites<br>
      to our rooftop fine dining above the skyline.
    </p>

    <!-- CTA Buttons -->
    <div class="h-btns">
      <a href="<?= $appUrl ?>/?url=<?= $isGuest ? 'reservations/create' : 'auth/login' ?>" class="h-btn-primary" id="btnBookNow">
        <span class="h-btn-primary-sweep"></span>
        <span class="h-btn-primary-inner">
          <i class="bi bi-calendar-check"></i>Book Your Stay
        </span>
      </a>
      <a href="<?= $appUrl ?>/?url=rooms/guest" class="h-btn-ghost">
        <i class="bi bi-door-open"></i>Explore Rooms
      </a>
    </div>

    <!-- Stats -->
    <div class="h-stats">
      <div class="h-stat">
        <span class="h-stat-n">5,000+</span>
        <span class="h-stat-l">Delighted Guests</span>
      </div>
      <div class="h-stat-sep"></div>
      <div class="h-stat">
        <span class="h-stat-n">120</span>
        <span class="h-stat-l">Luxury Rooms</span>
      </div>
      <div class="h-stat-sep"></div>
      <div class="h-stat">
        <span class="h-stat-n">&#9733;&thinsp;4.9</span>
        <span class="h-stat-l">Guest Rating</span>
      </div>
    </div>

  </div><!-- /.h-content -->

  <!-- Scroll CTA -->
  <button class="h-scroll-btn" onclick="document.getElementById('rooms').scrollIntoView({behavior:'smooth'})" aria-label="Scroll down">
    <div class="h-scroll-mouse"><div class="h-scroll-wheel"></div></div>
    <span>Scroll</span>
  </button>

</section>

<!-- ══ ABOUT ══════════════════════════════════════════════════════ -->
<section class="p-about" id="about">
  <div class="p-wrap">
    <div class="p-about-inner">
      <div class="p-about-text" data-aos="fade-right">
        <div class="p-label">Our Story</div>
        <h2 class="p-title">Where Timeless<br><em>Elegance Meets</em><br>Modern Luxury</h2>
        <div class="p-about-accent"></div>
        <p class="p-about-desc">Since 2009, Grand Hotel Cairo has stood as the pinnacle of luxury hospitality in Egypt. Nestled in the heart of Cairo, our hotel blends iconic architecture with contemporary sophistication — offering guests an experience that transcends ordinary stays.</p>
        <p class="p-about-desc" style="margin-top:.8rem">Every suite, every meal, every service is curated with obsessive attention to detail, guided by our unwavering commitment to excellence.</p>
        <div class="p-about-pills">
          <span><i class="bi bi-award-fill"></i>5-Star Rated</span>
          <span><i class="bi bi-gem"></i>Award Winning</span>
          <span><i class="bi bi-clock-history"></i>Est. 2009</span>
          <span><i class="bi bi-geo-alt-fill"></i>Cairo, Egypt</span>
        </div>
        <a href="#rooms" class="p-btn-outline">Discover Our Rooms <i class="bi bi-arrow-right ms-2"></i></a>
      </div>
      <div class="p-about-imgs" data-aos="fade-left">
        <div class="p-img-main"><img src="<?= $imgBase ?>lobby_fountain.jpg" alt="Grand Hotel lobby" loading="lazy"></div>
        <div class="p-img-sm"><img src="<?= $imgBase ?>lobby_enter.jpg" alt="Hotel entrance" loading="lazy"></div>
        <div class="p-about-badge"><span class="p-badge-num">15+</span><span class="p-badge-txt">Years of Excellence</span></div>
      </div>
    </div>
  </div>
</section>

<!-- ══ ROOMS ══════════════════════════════════════════════════════ -->
<section class="p-rooms p-dark" id="rooms">
  <div class="p-wrap">
    <div class="p-sec-hd center" data-aos="fade-up">
      <div class="p-label">Accommodations</div>
      <h2 class="p-title light">Rooms &amp; <em>Suites</em></h2>
      <p class="p-sec-sub">Each room is a private sanctuary — meticulously designed to balance luxury with comfort.</p>
    </div>
    <div class="p-rooms-grid">
      <?php foreach($rooms as $i=>$r): ?>
      <div class="p-room-card" data-aos="fade-up" data-aos-delay="<?= $i*120 ?>">
        <div class="p-room-img">
          <img src="<?= $r['img'] ?>" alt="<?= htmlspecialchars($r['name']) ?>" loading="lazy">
          <div class="p-room-img-overlay"></div>
          <span class="p-room-badge"><?= $r['badge'] ?></span>
        </div>
        <div class="p-room-body">
          <h3 class="p-room-name"><?= htmlspecialchars($r['name']) ?></h3>
          <p class="p-room-desc"><?= htmlspecialchars($r['desc']) ?></p>
          <div class="p-room-amenities">
            <span><i class="bi bi-wifi"></i>Wi-Fi</span>
            <span><i class="bi bi-tv"></i>Smart TV</span>
            <span><i class="bi bi-cup-hot"></i>Minibar</span>
          </div>
          <div class="p-room-footer">
            <div class="p-room-price">
              <span class="p-price-from">from</span>
              <span class="p-price-val">$<?= number_format($r['price']) ?></span>
              <span class="p-price-night">/night</span>
            </div>
            <a href="<?= $appUrl ?>/?url=<?= $isGuest ? 'reservations/create' : 'auth/login' ?>" class="p-room-btn">Book <i class="bi bi-arrow-right"></i></a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ══ SERVICES ═══════════════════════════════════════════════════ -->
<section class="p-services" id="services">
  <div class="p-wrap">
    <div class="p-sec-hd center" data-aos="fade-up">
      <div class="p-label">What We Offer</div>
      <h2 class="p-title">Premium <em>Services</em></h2>
    </div>
    <div class="p-svc-grid">
      <?php foreach($services as $i=>$s): ?>
      <div class="p-svc-card" data-aos="fade-up" data-aos-delay="<?= $i*80 ?>">
        <div class="p-svc-icon-wrap"><i class="bi <?= $s['icon'] ?>"></i></div>
        <h4 class="p-svc-title"><?= htmlspecialchars($s['title']) ?></h4>
        <p class="p-svc-desc"><?= htmlspecialchars($s['desc']) ?></p>
        <div class="p-svc-line"></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ══ OFFERS ═════════════════════════════════════════════════════ -->
<section class="p-offers p-dark">
  <div class="p-wrap">
    <div class="p-sec-hd center" data-aos="fade-up">
      <div class="p-label">Limited Time</div>
      <h2 class="p-title light">Exclusive <em>Offers</em></h2>
    </div>
    <div class="p-offers-grid" data-aos="fade-up">
      <div class="p-offer-card" style="--bg:url('<?= $imgBase ?>rooftop.jpg')">
        <div class="p-offer-overlay"></div>
        <div class="p-offer-body">
          <span class="p-offer-badge">Save 20%</span>
          <h3>Rooftop Honeymoon Suite</h3>
          <p>Exclusive package with private jacuzzi, champagne welcome &amp; rooftop dinner for two.</p>
          <a href="<?= $appUrl ?>/?url=<?= $isGuest ? 'reservations/create' : 'auth/login' ?>" class="p-offer-btn">Claim Offer</a>
        </div>
      </div>
      <div class="p-offer-card" style="--bg:url('<?= $imgBase ?>pool.jpg')">
        <div class="p-offer-overlay"></div>
        <div class="p-offer-body">
          <span class="p-offer-badge">3 Nights Free</span>
          <h3>Long Stay Luxury Package</h3>
          <p>Book 7 nights and receive 3 nights complimentary with full breakfast and spa access.</p>
          <a href="<?= $appUrl ?>/?url=<?= $isGuest ? 'reservations/create' : 'auth/login' ?>" class="p-offer-btn">Claim Offer</a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ══ RESTAURANT & SPA ════════════════════════════════════════════ -->
<section class="p-dining">
  <div class="p-wrap">
    <div class="p-dining-block" data-aos="fade-up">
      <div class="p-dining-img"><img src="<?= $imgBase ?>restaurant.jpg" alt="Fine Dining" loading="lazy"><div class="p-dining-tag"><i class="bi bi-star-fill"></i> Michelin Inspired</div></div>
      <div class="p-dining-text">
        <div class="p-label">Fine Dining</div>
        <h2 class="p-title">Skyline <em>Restaurant</em></h2>
        <p class="p-about-desc">Perched atop the 30th floor, our award-winning restaurant serves internationally acclaimed cuisine with panoramic views of Cairo's iconic skyline. Led by our Executive Chef, each dish is a work of art — pairing local flavours with global techniques.</p>
        <ul class="p-dining-list">
          <li><i class="bi bi-check-circle-fill"></i> International &amp; Egyptian cuisine</li>
          <li><i class="bi bi-check-circle-fill"></i> Private dining rooms available</li>
          <li><i class="bi bi-check-circle-fill"></i> Open daily 6am – midnight</li>
        </ul>
        <a href="#contact" class="p-btn-outline">Make a Reservation <i class="bi bi-arrow-right ms-2"></i></a>
      </div>
    </div>
    <div class="p-dining-block reverse" data-aos="fade-up">
      <div class="p-dining-img"><img src="<?= $imgBase ?>wellness.jpg" alt="Spa &amp; Wellness" loading="lazy"><div class="p-dining-tag"><i class="bi bi-flower1"></i> Award Winning</div></div>
      <div class="p-dining-text">
        <div class="p-label">Spa &amp; Wellness</div>
        <h2 class="p-title">The <em>Sanctuary Spa</em></h2>
        <p class="p-about-desc">Enter a world of complete serenity at our 3,000 m² Sanctuary Spa. Featuring heated pools, hammam, aromatherapy suites, and 22 private treatment rooms staffed by world-certified therapists.</p>
        <ul class="p-dining-list">
          <li><i class="bi bi-check-circle-fill"></i> 22 private treatment suites</li>
          <li><i class="bi bi-check-circle-fill"></i> Heated infinity pool &amp; hammam</li>
          <li><i class="bi bi-check-circle-fill"></i> Daily from 7am – 10pm</li>
        </ul>
        <a href="#contact" class="p-btn-outline">Book a Treatment <i class="bi bi-arrow-right ms-2"></i></a>
      </div>
    </div>
  </div>
</section>

<!-- ══ VIDEO ══════════════════════════════════════════════════════ -->
<section class="p-video-section p-dark">
  <div class="p-wrap">
    <div class="p-sec-hd center" data-aos="fade-up">
      <div class="p-label">Experience Grand Hotel</div>
      <h2 class="p-title light">See It For <em>Yourself</em></h2>
    </div>
    <div class="p-video-grid" data-aos="fade-up">
      <div class="p-video-card" onclick="toggleVideo(this,0)">
        <video id="vid0" src="<?= $vidBase ?>hotel_commercial_1.mp4" muted loop></video>
        <div class="p-video-overlay"><span class="p-video-label">The Grand Experience</span></div>
        <button class="p-video-play" aria-label="Play"><i class="bi bi-play-fill"></i></button>
      </div>
      <div class="p-video-card" onclick="toggleVideo(this,1)">
        <video id="vid1" src="<?= $vidBase ?>hotel_commercial_2.mp4" muted loop></video>
        <div class="p-video-overlay"><span class="p-video-label">Luxury Redefined</span></div>
        <button class="p-video-play" aria-label="Play"><i class="bi bi-play-fill"></i></button>
      </div>
    </div>
  </div>
</section>

<!-- ══ STATS ══════════════════════════════════════════════════════ -->
<div class="p-stats-band">
  <div class="p-wrap">
    <div class="p-stats-grid">
      <div class="p-stat-item" data-aos="zoom-in" data-aos-delay="0">
        <div class="p-stat-num" data-target="5000">0</div>
        <div class="p-stat-line"></div>
        <div class="p-stat-lbl">Happy Guests</div>
      </div>
      <div class="p-stat-item" data-aos="zoom-in" data-aos-delay="100">
        <div class="p-stat-num" data-target="120">0</div>
        <div class="p-stat-line"></div>
        <div class="p-stat-lbl">Luxury Rooms</div>
      </div>
      <div class="p-stat-item" data-aos="zoom-in" data-aos-delay="200">
        <div class="p-stat-num" data-target="15">0</div>
        <div class="p-stat-line"></div>
        <div class="p-stat-lbl">Years of Excellence</div>
      </div>
      <div class="p-stat-item" data-aos="zoom-in" data-aos-delay="300">
        <div class="p-stat-num" data-target="24">0</div>
        <div class="p-stat-line"></div>
        <div class="p-stat-lbl">Hour Concierge</div>
      </div>
    </div>
  </div>
</div>

<!-- ══ GALLERY ════════════════════════════════════════════════════ -->
<section class="p-gallery" id="gallery">
  <div class="p-wrap">
    <div class="p-sec-hd center" data-aos="fade-up">
      <div class="p-label">Our Space</div>
      <h2 class="p-title">Photo <em>Gallery</em></h2>
      <p class="p-sec-sub">Every corner of Grand Hotel tells a story of elegance and refined taste.</p>
    </div>
    <div class="p-gallery-grid">
      <?php foreach($gallery as $i=>$img): ?>
      <div class="p-gallery-item" data-aos="fade" data-aos-delay="<?= $i*50 ?>" onclick="openLightbox(<?= $i ?>)">
        <img src="<?= $img['src'] ?>" alt="<?= htmlspecialchars($img['alt']) ?>" loading="lazy">
        <div class="p-gallery-over"><i class="bi bi-zoom-in"></i></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Lightbox -->
<div id="lightbox">
  <button id="lb-close" onclick="closeLightbox()">×</button>
  <button id="lb-prev" onclick="lbNav(-1)"><i class="bi bi-chevron-left"></i></button>
  <img id="lb-img" src="" alt="Gallery">
  <button id="lb-next" onclick="lbNav(1)"><i class="bi bi-chevron-right"></i></button>
</div>

<!-- ══ TESTIMONIALS ═══════════════════════════════════════════════ -->
<section class="p-testi p-dark">
  <div class="p-wrap">
    <div class="p-sec-hd center" data-aos="fade-up">
      <div class="p-label">Guest Stories</div>
      <h2 class="p-title light">What Our <em>Guests Say</em></h2>
    </div>
    <div class="p-testi-grid">
      <?php foreach($testimonials as $i=>$t): ?>
      <div class="p-testi-card" data-aos="fade-up" data-aos-delay="<?= $i*120 ?>">
        <div class="p-testi-quote"><i class="bi bi-quote"></i></div>
        <div class="p-testi-stars"><?= str_repeat('★',$t['stars']) ?></div>
        <p class="p-testi-text"><?= htmlspecialchars($t['text']) ?></p>
        <div class="p-testi-author">
          <div class="p-testi-avatar"><?= strtoupper($t['name'][0]) ?></div>
          <div><div class="p-testi-name"><?= htmlspecialchars($t['name']) ?></div><div class="p-testi-loc"><?= htmlspecialchars($t['loc']) ?></div></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ══ BOOKING PREVIEW ════════════════════════════════════════════ -->
<section class="p-booking" style="background-image:url('<?= $imgBase ?>hotel_exterior.jpg')">
  <div class="p-booking-overlay"></div>
  <div class="p-wrap" style="position:relative;z-index:2">
    <div class="p-booking-inner" data-aos="fade-up">
      <div class="p-booking-hd">
        <div class="p-label" style="color:var(--gold2)">Reserve Your Stay</div>
        <h2 class="p-title light">Book Your <em>Room</em> Now</h2>
        <p style="color:rgba(255,255,255,.55);margin-top:.5rem">Check availability and secure your luxury experience today.</p>
      </div>
      <form class="p-booking-form" action="<?= $appUrl ?>/?url=<?= $isGuest ? 'reservations/create' : 'auth/login' ?>" method="GET">
        <div class="p-booking-fields">
          <div class="p-bfield">
            <label><i class="bi bi-calendar3"></i> Check-in</label>
            <input type="date" name="checkin" min="<?= date('Y-m-d') ?>" placeholder="DD/MM/YYYY">
          </div>
          <div class="p-bfield">
            <label><i class="bi bi-calendar3"></i> Check-out</label>
            <input type="date" name="checkout" min="<?= date('Y-m-d') ?>" placeholder="DD/MM/YYYY">
          </div>
          <div class="p-bfield">
            <label><i class="bi bi-door-open"></i> Room Type</label>
            <select name="room_type">
              <option value="">Any Room</option>
              <option value="standard">Standard Room</option>
              <option value="deluxe">Deluxe Room</option>
              <option value="suite">Grand Suite</option>
            </select>
          </div>
          <div class="p-bfield">
            <label><i class="bi bi-people"></i> Guests</label>
            <select name="guests">
              <option>1 Guest</option>
              <option selected>2 Guests</option>
              <option>3 Guests</option>
              <option>4 Guests</option>
            </select>
          </div>
        </div>
        <button type="submit" class="p-booking-btn"><i class="bi bi-search me-2"></i>Check Availability</button>
      </form>
    </div>
  </div>
</section>

<!-- ══ FOOTER ═════════════════════════════════════════════════════ -->
<footer class="p-footer" id="contact">
  <div class="p-footer-top">
    <div class="p-wrap">
      <div class="p-footer-grid">
        <div class="p-footer-brand-col">
          <div class="p-footer-logo"><i class="bi bi-building-fill"></i> Grand Hotel</div>
          <p class="p-footer-desc">A world-class luxury destination where every moment is crafted to perfection. Cairo's finest hotel since 2009.</p>
          <div class="p-footer-social">
            <a href="#" class="p-social-btn" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
            <a href="#" class="p-social-btn" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
            <a href="#" class="p-social-btn" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
            <a href="#" class="p-social-btn" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
          </div>
        </div>
        <div>
          <div class="p-footer-h">Explore</div>
          <ul class="p-footer-links">
            <li><a href="#rooms">Rooms &amp; Suites</a></li>
            <li><a href="#services">Services</a></li>
            <li><a href="#gallery">Gallery</a></li>
            <li><a href="#about">About Us</a></li>
          </ul>
        </div>
        <div>
          <div class="p-footer-h">Services</div>
          <ul class="p-footer-links">
            <li><a href="#services">Spa &amp; Wellness</a></li>
            <li><a href="#services">Fine Dining</a></li>
            <li><a href="#services">Airport Transfer</a></li>
            <li><a href="#services">Event Spaces</a></li>
            <li><a href="<?= $appUrl ?>/?url=auth/login">Guest Login</a></li>
          </ul>
        </div>
        <div>
          <div class="p-footer-h">Contact</div>
          <ul class="p-footer-links">
            <li><i class="bi bi-geo-alt me-2"></i>123 Luxury Ave, Cairo, Egypt</li>
            <li><a href="tel:+201000000000"><i class="bi bi-telephone me-2"></i>+20 100 000 0000</a></li>
            <li><a href="mailto:info@grandhotel.com"><i class="bi bi-envelope me-2"></i>info@grandhotel.com</a></li>
            <li><i class="bi bi-clock me-2"></i>24/7 Concierge Service</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="p-footer-bottom">
    <div class="p-wrap">
      <span>© <?= date('Y') ?> Grand Hotel Cairo. All rights reserved.</span>
      <span class="p-footer-sep">·</span>
      <span>Designed with ♥ for luxury</span>
    </div>
  </div>
</footer>

<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script src="<?= $appUrl ?>/public/assets/js/hero3d.js"></script>
<script>
AOS.init({duration:800,once:true,offset:50,easing:'ease-out-cubic'});

// Loader
window.addEventListener('load',()=>setTimeout(()=>document.getElementById('loader').classList.add('hidden'),500));

// Sticky nav
window.addEventListener('scroll',()=>document.getElementById('mainNav').classList.toggle('scrolled',scrollY>60));

// Mobile menu
document.getElementById('navToggle').onclick=function(){
  const m=document.getElementById('mobileMenu');
  m.style.display=m.style.display==='flex'?'none':'flex';
};

// Stats counter
const counters=document.querySelectorAll('.p-stat-num[data-target]');
const runCount=el=>{const t=+el.dataset.target,dur=2000,step=Math.max(dur/t,16);let c=0;const iv=setInterval(()=>{c=Math.min(c+1,t);el.textContent=c+(t>=100?'+':'');if(c>=t)clearInterval(iv);},step);};
const ob=new IntersectionObserver(entries=>entries.forEach(e=>{if(e.isIntersecting){runCount(e.target);ob.unobserve(e.target);}}),{threshold:.5});
counters.forEach(c=>ob.observe(c));

// Lightbox
const galleryImgs=<?= json_encode(array_map(fn($g)=>$g['src'],$gallery)) ?>;
let lbIndex=0;
function openLightbox(i){lbIndex=i;document.getElementById('lb-img').src=galleryImgs[i];document.getElementById('lightbox').classList.add('open');}
function closeLightbox(){document.getElementById('lightbox').classList.remove('open');document.getElementById('lb-img').src='';}
function lbNav(dir){lbIndex=(lbIndex+dir+galleryImgs.length)%galleryImgs.length;document.getElementById('lb-img').src=galleryImgs[lbIndex];}
document.getElementById('lightbox').addEventListener('click',e=>{if(e.target===e.currentTarget)closeLightbox();});
document.addEventListener('keydown',e=>{if(e.key==='Escape')closeLightbox();if(e.key==='ArrowLeft')lbNav(-1);if(e.key==='ArrowRight')lbNav(1);});

// Video toggle
function toggleVideo(card,idx){
  const vid=document.getElementById('vid'+idx);
  const btn=card.querySelector('.p-video-play i');
  if(vid.paused){vid.play();btn.className='bi bi-pause-fill';}
  else{vid.pause();btn.className='bi bi-play-fill';}
}

// Smooth scroll
document.querySelectorAll('a[href^="#"]').forEach(a=>a.addEventListener('click',e=>{
  const t=document.querySelector(a.getAttribute('href'));
  if(t){e.preventDefault();t.scrollIntoView({behavior:'smooth',block:'start'});document.getElementById('mobileMenu').style.display='none';}
}));
</script>
</body>
</html>
<?php
$content = ob_get_clean();
echo $content;
?>
