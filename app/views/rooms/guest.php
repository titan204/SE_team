<?php
$pageTitle = $pageTitle ?? 'Available Rooms';
$rooms = $rooms ?? [];
$filters = $filters ?? [];
$errors = $errors ?? [];
$isFilteredByDates = $isFilteredByDates ?? false;
$isGuest = !empty($_SESSION['user_id']) && ($_SESSION['user_role'] ?? '') === 'guest';
$isLoggedIn = !empty($_SESSION['user_id']);
$guestName = htmlspecialchars($_SESSION['user_name'] ?? '');

ob_start();
?>
<link rel="stylesheet" href="<?= APP_URL ?>/public/assets/css/home.css">
<style>
/* Offset content below fixed navbar */
.rooms-page-wrap { padding-top: 80px; }
</style>

<!-- Luxury Navbar (matches homepage) -->
<nav class="hm-nav scrolled" id="guestNav">
  <div class="inner">
    <a href="<?= APP_URL ?>/?url=home/index" class="nav-brand"><i class="bi bi-building-fill"></i>Grand Hotel</a>
    <ul class="nav-links">
      <li><a href="<?= APP_URL ?>/?url=home/index">Home</a></li>
      <li><a href="<?= APP_URL ?>/?url=rooms/guest" style="color:var(--gold)">Rooms</a></li>
      <li><a href="<?= APP_URL ?>/?url=home/index#services">Services</a></li>
      <li><a href="<?= APP_URL ?>/?url=home/index#contact">Contact</a></li>
    </ul>
    <div class="nav-auth">
      <?php if($isGuest): ?>
        <a href="<?= APP_URL ?>/?url=home/guestprofile" class="btn-nav-login"><i class="bi bi-person-circle me-1"></i><?= $guestName ?></a>
        <a href="<?= APP_URL ?>/?url=auth/logout" class="btn-nav-reg">Sign Out</a>
      <?php elseif($isLoggedIn): ?>
        <a href="<?= APP_URL ?>/?url=Dashboard/index" class="btn-nav-login"><i class="bi bi-speedometer2 me-1"></i>Dashboard</a>
        <a href="<?= APP_URL ?>/?url=auth/logout" class="btn-nav-reg">Sign Out</a>
      <?php else: ?>
        <a href="<?= APP_URL ?>/?url=auth/login"    class="btn-nav-login">Sign In</a>
        <a href="<?= APP_URL ?>/?url=auth/register" class="btn-nav-reg">Register</a>
      <?php endif; ?>
    </div>
    <button class="nav-toggle" id="gNavToggle"><i class="bi bi-list"></i></button>
  </div>
  <div id="gMobileMenu" style="display:none;" class="mobile-menu">
    <a href="<?= APP_URL ?>/?url=home/index">Home</a>
    <a href="<?= APP_URL ?>/?url=rooms/guest">Rooms</a>
    <a href="<?= APP_URL ?>/?url=home/index#services">Services</a>
    <a href="<?= APP_URL ?>/?url=home/index#contact">Contact</a>
    <?php if($isLoggedIn): ?>
      <a href="<?= APP_URL ?>/?url=auth/logout">Sign Out</a>
    <?php else: ?>
      <a href="<?= APP_URL ?>/?url=auth/login">Sign In</a>
    <?php endif; ?>
  </div>
</nav>

<div class="rooms-page-wrap">

<style>
.guest-room-card {
    border: 1px solid #e9ecef;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.guest-room-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.08);
}

.guest-room-image {
    height: 210px;
    object-fit: cover;
}

.guest-room-detail {
    font-size: 0.95rem;
    color: #495057;
}
</style>

<div class="container py-2">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="mb-1"><i class="bi bi-door-open"></i> Available Rooms</h2>
            <p class="text-muted mb-0">Browse rooms that are ready for booking and view their details.</p>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="<?= APP_URL ?>/index.php" class="row g-3 align-items-end">
                <input type="hidden" name="url" value="rooms/guest">

                <div class="col-md-4">
                    <label for="check_in_date" class="form-label">Check-in Date</label>
                    <input
                        type="date"
                        id="check_in_date"
                        name="check_in_date"
                        class="form-control <?= !empty($errors['check_in_date']) ? 'is-invalid' : '' ?>"
                        value="<?= htmlspecialchars($filters['check_in_date'] ?? '') ?>">
                    <?php if (!empty($errors['check_in_date'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['check_in_date']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-md-4">
                    <label for="check_out_date" class="form-label">Check-out Date</label>
                    <input
                        type="date"
                        id="check_out_date"
                        name="check_out_date"
                        class="form-control <?= !empty($errors['check_out_date']) ? 'is-invalid' : '' ?>"
                        value="<?= htmlspecialchars($filters['check_out_date'] ?? '') ?>">
                    <?php if (!empty($errors['check_out_date'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['check_out_date']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Check Availability
                    </button>
                    <a href="<?= APP_URL ?>/?url=rooms/guest" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>

            <?php if (!empty($errors['date_range'])): ?>
                <div class="alert alert-warning mt-3 mb-0"><?= htmlspecialchars($errors['date_range']) ?></div>
            <?php endif; ?>

            <div class="mt-3 text-muted small">
                <?php if ($isFilteredByDates): ?>
                    Showing rooms available from
                    <strong><?= htmlspecialchars($filters['check_in_date'] ?? '') ?></strong>
                    to
                    <strong><?= htmlspecialchars($filters['check_out_date'] ?? '') ?></strong>.
                <?php else: ?>
                    Showing rooms currently marked as <strong>available</strong>.
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if (empty($rooms)): ?>
        <div class="alert alert-info">
            <?php if ($isFilteredByDates): ?>
                No rooms are available for the selected dates.
            <?php else: ?>
                No rooms are currently marked as available.
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($rooms as $room): ?>
                <div class="col-md-6 col-xl-4">
                    <div class="card guest-room-card h-100 shadow-sm">
                        <img
                            src="<?= APP_URL ?>/public/assets/images/room.jpg"
                            class="card-img-top guest-room-image"
                            alt="Room <?= htmlspecialchars($room['room_number']) ?>">

                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="card-title mb-1">Room <?= htmlspecialchars($room['room_number']) ?></h5>
                                    <p class="text-muted mb-0"><?= htmlspecialchars($room['type_name']) ?></p>
                                </div>
                                <span class="badge bg-success">Available</span>
                            </div>

                            <div class="guest-room-detail mb-3">
                                <div class="mb-2"><strong>Price:</strong> $<?= number_format((float) $room['base_price'], 2) ?> / night</div>
                                <div class="mb-2"><strong>Capacity:</strong> <?= (int) ($room['capacity'] ?? 0) ?> guest<?= ((int) ($room['capacity'] ?? 0) === 1) ? '' : 's' ?></div>
                                <div class="mb-2"><strong>Floor:</strong> <?= (int) $room['floor'] ?></div>
                                <?php if (!empty($room['type_description'])): ?>
                                    <div class="mb-2"><strong>Description:</strong> <?= htmlspecialchars($room['type_description']) ?></div>
                                <?php endif; ?>
                                <?php if (!empty($room['notes'])): ?>
                                    <div><strong>Details:</strong> <?= htmlspecialchars($room['notes']) ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mt-auto pt-2 border-top text-muted small">
                                <i class="bi bi-info-circle"></i> Contact reception to continue your booking.
                            </div>

                            <a
                                href="<?= APP_URL ?>/?url=<?= $isGuest ? 'reservations/create&room_id=' . (int)$room['id'] : 'auth/login' ?>"
                                class="btn btn-primary w-100 mt-3">
                                <?= $isGuest ? 'Reserve This Room' : 'Sign In to Book' ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
// Close the rooms-page-wrap div
$content .= '</div>';
// Mobile menu toggle script
$content .= "<script>
document.getElementById('gNavToggle').onclick=function(){
  var m=document.getElementById('gMobileMenu');
  m.style.display=m.style.display==='none'?'flex':'none';
};
</script>";
require VIEW_PATH . '/layouts/main.php';
?>
