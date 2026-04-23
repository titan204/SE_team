<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?> — <?= $pageTitle ?? 'Dashboard' ?></title>

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?= APP_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>

<!-- ── Navbar ────────────────────────────────────────────── -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= APP_URL ?>">
            <i class="bi bi-building"></i> <?= APP_NAME ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <!-- TODO: Show these links only when user is logged in -->
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="<?= APP_URL ?>/dashboard">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= APP_URL ?>/reservations">Reservations</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= APP_URL ?>/guests">Guests</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= APP_URL ?>/rooms">Rooms</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= APP_URL ?>/housekeeping">Housekeeping</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= APP_URL ?>/maintenance">Maintenance</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= APP_URL ?>/billing">Billing</a></li>
                <!-- TODO: Show only for manager role -->
                <li class="nav-item"><a class="nav-link" href="<?= APP_URL ?>/users">Staff</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= APP_URL ?>/reports">Reports</a></li>
            </ul>
            <ul class="navbar-nav">
                <!-- TODO: Show user name from session -->
                <li class="nav-item"><a class="nav-link" href="<?= APP_URL ?>/auth/logout"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- ── Page Content ──────────────────────────────────────── -->
<main class="container-fluid py-4">
    <?= $content ?? '' ?>
</main>

<!-- ── Footer ────────────────────────────────────────────── -->
<footer class="bg-dark text-white text-center py-3 mt-auto">
    <small>&copy; <?= date('Y') ?> <?= APP_NAME ?> — SE Project</small>
</footer>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom JS -->
<script src="<?= APP_URL ?>/assets/js/app.js"></script>
</body>
</html>
