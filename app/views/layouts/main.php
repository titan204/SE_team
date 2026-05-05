<?php
$isLoggedIn      = !empty($_SESSION['user_id']);
$currentUserName = htmlspecialchars($_SESSION['user_name'] ?? 'User', ENT_QUOTES, 'UTF-8');
$currentRole     = strtolower($_SESSION['user_role'] ?? '');
$isGuest         = ($currentRole === 'guest' || ($_SESSION['user_role_id'] ?? 0) == 4);
$roleLabel       = ucfirst(str_replace('_', ' ', $currentRole));
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?> - <?= $pageTitle ?? 'Dashboard' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= APP_URL ?>/public/assets/css/style.css" rel="stylesheet">
    <style>
        /* ── Brand Palette ── */
        :root {
            --bg:      #FFF8F0;
            --accent:  #C08552;
            --accent2: #8C5A3C;
            --dark:    #4B2E2B;
        }
        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background-color: var(--bg) !important;
        }
        /* Navbar */
        .navbar.bg-dark {
            background-color: var(--dark) !important;
            box-shadow: 0 2px 6px rgba(75,46,43,.4);
        }
        .navbar-brand { color: var(--accent) !important; font-weight: 700; }
        .navbar-brand i { color: var(--accent); }
        .navbar-nav .nav-link { color: rgba(255,248,240,.88) !important; transition: color .15s; }
        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link:focus  { color: var(--accent) !important; }
        .navbar-toggler { border-color: rgba(192,133,82,.5); }
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(192,133,82,.9)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }
        /* Dropdown */
        .navbar .dropdown-menu {
            background-color: var(--dark);
            border: 1px solid rgba(192,133,82,.3);
            border-radius: 6px;
        }
        .navbar .dropdown-item { color: rgba(255,248,240,.88); font-size: .88rem; }
        .navbar .dropdown-item:hover { background-color: var(--accent2); color: #fff; }
        .navbar .dropdown-divider { border-color: rgba(192,133,82,.25); }
        /* Buttons */
        .btn-primary {
            background-color: var(--accent) !important;
            border-color: var(--accent) !important;
            color: #fff !important;
        }
        .btn-primary:hover, .btn-primary:focus {
            background-color: var(--accent2) !important;
            border-color: var(--accent2) !important;
        }
        .btn-outline-primary {
            color: var(--accent) !important;
            border-color: var(--accent) !important;
        }
        .btn-outline-primary:hover {
            background-color: var(--accent) !important;
            border-color: var(--accent) !important;
            color: #fff !important;
        }
        /* Cards */
        .card { background: #fff; border: 1px solid #e8d5c0; border-radius: 10px; box-shadow: 0 2px 8px rgba(192,133,82,.08); }
        .card-header { background: #fff; border-bottom: 1px solid #e8d5c0; color: var(--dark); font-weight: 600; }
        /* Tables */
        .table th { background-color: var(--dark); color: #fff; font-weight: 600; }
        .table tbody tr:hover { background-color: #fff3e8; }
        /* Footer */
        footer.bg-dark { background-color: var(--dark) !important; color: rgba(255,248,240,.7); }
        /* Forms */
        .form-control:focus, .form-select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 .2rem rgba(192,133,82,.25);
        }
        /* Headings */
        h1,h2,h3,h4,h5,.h1,.h2,.h3,.h4,.h5 { color: var(--dark); }
        /* Links */
        a { color: var(--accent); }
        a:hover { color: var(--accent2); }
    </style>
</head>

<body class="d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-xl navbar-dark bg-dark">
        <div class="container-fluid">
            <?php
            // Role-aware brand home link
            $brandUrl = APP_URL . '/?url=dashboard/index'; // default staff
            if ($currentRole === 'revenue_manager') $brandUrl = APP_URL . '/?url=revenue_manager/index';
            elseif ($currentRole === 'guest')        $brandUrl = APP_URL . '/?url=home/index';
            elseif (!$isLoggedIn)                    $brandUrl = APP_URL . '/?url=home/index';
            ?>
            <a class="navbar-brand" href="<?= $brandUrl ?>">
                <i class="bi bi-building"></i> <?= APP_NAME ?>
            </a>
            <button class="navbar-toggler" type="button"
                    data-bs-toggle="collapse" data-bs-target="#mainNav"
                    aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">

                <?php if ($isLoggedIn && !$isGuest): ?>
                <!-- ══════════════════════════════════════════════
                     ROLE-BASED NAVIGATION — each role is isolated
                     ══════════════════════════════════════════════ -->

                <?php if ($currentRole === 'manager'): ?>
                <!-- ── MANAGER ── Full operational access -->
                <div class="d-flex flex-column flex-xl-row align-items-start align-items-xl-center w-100">
                <ul class="navbar-nav me-auto flex-wrap">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= APP_URL ?>/?url=dashboard/index">
                            <i class="bi bi-house-door me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-calendar-check me-1"></i>Reservations
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= APP_URL ?>/?url=reservations/index"><i class="bi bi-list-ul me-1"></i>All Reservations</a></li>
                            <li><a class="dropdown-item" href="<?= APP_URL ?>/?url=reservations/create"><i class="bi bi-plus me-1"></i>New Reservation</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-people me-1"></i>Guests
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= APP_URL ?>/?url=guests/index"><i class="bi bi-person me-1"></i>Guest Directory</a></li>
                            <li><a class="dropdown-item" href="<?= APP_URL ?>/?url=guests/create"><i class="bi bi-person-plus me-1"></i>Add Guest</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-brush me-1"></i>Housekeeping
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= APP_URL ?>/?url=housekeeping/index"><i class="bi bi-kanban me-1"></i>Task Board</a></li>
                            <li><a class="dropdown-item" href="<?= APP_URL ?>/?url=housekeeping/qa"><i class="bi bi-clipboard2-check me-1"></i>Quality Inspections</a></li>
                            <li><a class="dropdown-item" href="<?= APP_URL ?>/?url=housekeeping/qaTrends"><i class="bi bi-bar-chart me-1"></i>Quality Trends</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-wrench-adjustable me-1"></i>Maintenance
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= APP_URL ?>/?url=maintenance/index"><i class="bi bi-list-check me-1"></i>Work Orders</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= APP_URL ?>/?url=maintenance/emergency"><i class="bi bi-exclamation-triangle me-1"></i>Log Emergency</a></li>
                            <li><a class="dropdown-item" href="<?= APP_URL ?>/?url=maintenance/preventative"><i class="bi bi-calendar-plus me-1"></i>Schedule Preventative</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-receipt me-1"></i>Billing
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= APP_URL ?>/?url=billing/index"><i class="bi bi-list me-1"></i>Billing Overview</a></li>
                            <li><a class="dropdown-item" href="<?= APP_URL ?>/?url=frontdesk/lostFound"><i class="bi bi-search-heart me-1"></i>Lost &amp; Found</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-bar-chart me-1"></i>Reports
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= APP_URL ?>/?url=reports/index"><i class="bi bi-speedometer2 me-1"></i>Overview</a></li>
                            <li><a class="dropdown-item" href="<?= APP_URL ?>/?url=reports/occupancy"><i class="bi bi-bar-chart me-1"></i>Occupancy</a></li>
                            <li><a class="dropdown-item" href="<?= APP_URL ?>/?url=reports/revenue"><i class="bi bi-graph-up me-1"></i>Revenue</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= APP_URL ?>/?url=users/index">
                            <i class="bi bi-people-fill me-1"></i>Staff
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto ms-xl-3 align-items-start align-items-xl-center">
                    <li class="nav-item">
                        <span class="navbar-text text-white me-2">
                            <i class="bi bi-person-circle me-1"></i><?= $currentUserName ?>
                            <span class="badge bg-secondary ms-1"><?= $roleLabel ?></span>
                        </span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= APP_URL ?>/?url=auth/logout">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </li>
                </ul>
                </div><!-- /.manager-nav-wrapper -->

                <?php elseif ($currentRole === 'front_desk'): ?>
                <!-- ── FRONT DESK ── Check-in, reservations, guests, billing, L&F -->
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= APP_URL ?>/?url=dashboard/index">
                            <i class="bi bi-house-door me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-calendar-check me-1"></i>Reservations
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= APP_URL ?>/?url=reservations/index"><i class="bi bi-list-ul me-1"></i>All Reservations</a></li>
                            <li><a class="dropdown-item" href="<?= APP_URL ?>/?url=reservations/create"><i class="bi bi-plus me-1"></i>New Reservation</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-people me-1"></i>Guests
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= APP_URL ?>/?url=guests/index"><i class="bi bi-person me-1"></i>Guest Directory</a></li>
                            <li><a class="dropdown-item" href="<?= APP_URL ?>/?url=guests/create"><i class="bi bi-person-plus me-1"></i>Add Guest</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-receipt me-1"></i>Billing
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= APP_URL ?>/?url=billing/index"><i class="bi bi-list me-1"></i>Billing Overview</a></li>
                            <li><a class="dropdown-item" href="<?= APP_URL ?>/?url=billing/splitBill/1"><i class="bi bi-scissors me-1"></i>Split Bill</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= APP_URL ?>/?url=frontdesk/lostFound">
                            <i class="bi bi-search-heart me-1"></i>Lost &amp; Found
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= APP_URL ?>/?url=rooms/index">
                            <i class="bi bi-door-closed me-1"></i>Rooms
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <span class="navbar-text text-white me-3">
                            <i class="bi bi-person-circle me-1"></i><?= $currentUserName ?>
                            <span class="badge bg-secondary ms-1"><?= $roleLabel ?></span>
                        </span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= APP_URL ?>/?url=auth/logout">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </li>
                </ul>
                </div><!-- /.front-desk-nav-wrapper -->

                <?php elseif ($currentRole === 'housekeeper'): ?>
                <!-- ── HOUSEKEEPER ── Tasks, found items, quality only -->
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= APP_URL ?>/?url=dashboard/index">
                            <i class="bi bi-house-door me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= APP_URL ?>/?url=housekeeping/myTasks">
                            <i class="bi bi-person-check me-1"></i>My Tasks
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= APP_URL ?>/?url=housekeeping/index">
                            <i class="bi bi-kanban me-1"></i>Task Board
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= APP_URL ?>/?url=housekeeping/foundItem">
                            <i class="bi bi-search-heart me-1"></i>Log Found Item
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-clipboard2-check me-1"></i>Quality
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= APP_URL ?>/?url=housekeeping/qa"><i class="bi bi-clipboard2-check me-1"></i>Inspections</a></li>
                            <li><a class="dropdown-item" href="<?= APP_URL ?>/?url=housekeeping/qaTrends"><i class="bi bi-bar-chart me-1"></i>My Trends</a></li>
                        </ul>
                    </li>
                </ul>
                <!-- Right side for housekeeper: user info + logout -->
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <span class="navbar-text text-white me-3">
                            <i class="bi bi-person-circle me-1"></i><?= $currentUserName ?>
                            <span class="badge bg-secondary ms-1"><?= $roleLabel ?></span>
                        </span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= APP_URL ?>/?url=auth/logout">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </li>
                </ul>

                <?php elseif ($currentRole === 'revenue_manager'): ?>
                <!-- ── REVENUE MANAGER ── Inventory and reports only -->
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= APP_URL ?>/?url=revenue_manager/index">
                            <i class="bi bi-house-door me-1"></i>Home
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-grid-3x3 me-1"></i>Inventory
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= APP_URL ?>/?url=revenue_manager_virtual_inventory/index"><i class="bi bi-speedometer2 me-1"></i>Dashboard</a></li>
                            <li><a class="dropdown-item" href="<?= APP_URL ?>/?url=revenue_manager_virtual_inventory/inventoryGrid"><i class="bi bi-grid-3x3 me-1"></i>30-Day Grid</a></li>
                            <li><a class="dropdown-item" href="<?= APP_URL ?>/?url=revenue_manager_virtual_inventory/syncStatus"><i class="bi bi-arrow-repeat me-1"></i>Sync Status</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-graph-up me-1"></i>Reports
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= APP_URL ?>/?url=reports/revenue"><i class="bi bi-graph-up me-1"></i>Revenue Report</a></li>
                            <li><a class="dropdown-item" href="<?= APP_URL ?>/?url=reports/occupancy"><i class="bi bi-bar-chart me-1"></i>Occupancy Report</a></li>
                        </ul>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <span class="navbar-text text-white me-3">
                            <i class="bi bi-person-circle me-1"></i><?= $currentUserName ?>
                            <span class="badge bg-secondary ms-1"><?= $roleLabel ?></span>
                        </span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= APP_URL ?>/?url=auth/logout">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </li>
                </ul>

                <?php else: ?>
                <!-- ── FALLBACK (supervisor / unknown role) -->
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= APP_URL ?>/?url=frontdesk/index">
                            <i class="bi bi-house-door me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= APP_URL ?>/?url=reservations/index">Reservations</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <span class="navbar-text text-white me-3">
                            <i class="bi bi-person-circle me-1"></i><?= $currentUserName ?>
                            <span class="badge bg-secondary ms-1"><?= $roleLabel ?></span>
                        </span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= APP_URL ?>/?url=auth/logout">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </li>
                </ul>
                <?php endif; ?>

                <?php elseif ($isLoggedIn && $isGuest): ?>
                <!-- ── GUEST Navigation ── -->
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= APP_URL ?>/?url=rooms/guest">Rooms</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= APP_URL ?>/?url=home/externalServices">External Services Booking</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto align-items-center gap-2">
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center gap-2"
                           href="<?= APP_URL ?>/?url=Home/guestprofile" title="My Profile">
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
                <!-- ── PUBLIC (not logged in) ── -->
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= APP_URL ?>/?url=rooms/guest">Rooms</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= APP_URL ?>/?url=auth/login">Login</a>
                    </li>
                </ul>
                <?php endif; ?>

            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>

    <main class="container-fluid py-4 flex-grow-1">
        <?= $content ?? '' ?>
    </main>

    <footer class="bg-dark text-center py-3 mt-auto">
        <small>&copy; <?= date('Y') ?> <?= APP_NAME ?> &mdash; Hotel Management System</small>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= APP_URL ?>/public/assets/js/app.js"></script>
</body>

</html>
