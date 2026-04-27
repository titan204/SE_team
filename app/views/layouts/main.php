<?php
$isLoggedIn     = !empty($_SESSION['user_id']);
$currentUserName = htmlspecialchars($_SESSION['user_name'] ?? 'User', ENT_QUOTES, 'UTF-8');
$currentRole    = strtolower($_SESSION['user_role'] ?? '');
$isGuest        = ($currentRole === 'guest' || ($_SESSION['user_role_id'] ?? 0) == 4);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?> - <?= $pageTitle ?? 'Dashboard' ?></title>

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?= APP_URL ?>/public/assets/css/style.css" rel="stylesheet">
</head>

<body class="d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= APP_URL ?>">
                <i class="bi bi-building"></i> <?= APP_NAME ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <?php if ($isLoggedIn && !$isGuest): ?>
                    <!-- ── Staff Navigation ── -->
        
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item"><a class="nav-link" href="<?= APP_URL ?>/dashboard">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= APP_URL ?>/reservations">Reservations</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= APP_URL ?>/guests">Guests</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= APP_URL ?>/rooms">Rooms</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= APP_URL ?>/housekeeping">Housekeeping</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= APP_URL ?>/maintenance">Maintenance</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= APP_URL ?>/billing">Billing</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= APP_URL ?>/users">Staff</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= APP_URL ?>/reports">Reports</a></li>
                    </ul>
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <span class="navbar-text text-white me-3">
                                <i class="bi bi-person-circle me-1"></i><?= $currentUserName ?>
                            </span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= APP_URL ?>/?url=auth/logout">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </a>
                        </li>
                    </ul>

                <?php elseif ($isLoggedIn && $isGuest): ?>
                    <!-- ── Guest Navigation ── -->
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="<?= APP_URL ?>/?url=rooms/guest">
                                Rooms
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= APP_URL ?>/?url=home/externalServices">
                                External Services Booking
                            </a>
                        </li>
                    </ul>
                    <ul class="navbar-nav ms-auto align-items-center gap-2">
                        <!-- Profile Icon -->
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center gap-2"
                               href="<?= APP_URL ?>/?url=Home/guestprofile"
                               title="My Profile">
                                <div style="
                                    width:34px;height:34px;border-radius:50%;
                                    background:linear-gradient(135deg,#9A3F3F,#D4B483);
                                    display:flex;align-items:center;justify-content:center;
                                    font-size:13px;font-weight:600;color:#fff;
                                    border:2px solid rgba(255,255,255,0.25);
                                ">
                                    <?= strtoupper(substr($_SESSION['user_name'] ?? 'G', 0, 1)) ?>
                                </div>
                                <span class="text-white" style="font-size:14px"><?= $currentUserName ?></span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= APP_URL ?>/?url=auth/logout">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </a>
                        </li>
                    </ul>
                <?php else: ?>
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="<?= APP_URL ?>/?url=rooms/guest">Rooms</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= APP_URL ?>/?url=auth/login">Login</a>
                        </li>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <main class="container-fluid py-4 flex-grow-1">
        <?= $content ?? '' ?>
    </main>

    <footer class="bg-dark text-white text-center py-3 mt-auto">
        <small>&copy; <?= date('Y') ?> <?= APP_NAME ?> - SE Project</small>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= APP_URL ?>/public/assets/js/app.js"></script>
</body>

</html>
