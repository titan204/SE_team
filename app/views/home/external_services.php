<?php
$pageTitle = $pageTitle ?? 'External Services Booking';
$services = $services ?? [];
$bookings = $bookings ?? [];
$errors = $errors ?? [];
$old = $old ?? [];
$message = $message ?? null;

ob_start();
?>
<style>
body {
    background: #FBF9D1;
}

.service-shell {
    max-width: 1180px;
    margin: 0 auto;
}

.service-hero,
.booking-card,
.history-card,
.service-card {
    border: none;
    border-radius: 20px;
    background: #ffffff;
    box-shadow: 0 14px 38px rgba(72, 37, 24, 0.08);
}

.service-hero {
    padding: 28px;
    background: linear-gradient(135deg, #9A3F3F 0%, #C1856D 55%, #E6CFA9 100%);
    color: #fff;
}

.service-kicker {
    letter-spacing: 0.16em;
    text-transform: uppercase;
    font-size: 0.78rem;
    opacity: 0.9;
}

.service-card {
    height: 100%;
}

.service-card .card-body,
.booking-card .card-body,
.history-card .card-body {
    padding: 1.4rem;
}

.service-type-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.35rem 0.8rem;
    border-radius: 999px;
    background: rgba(154, 63, 63, 0.12);
    color: #9A3F3F;
    font-weight: 600;
    font-size: 0.8rem;
    text-transform: capitalize;
}

.service-title,
.booking-title {
    color: #6F3028;
    font-weight: 700;
}

.service-text {
    color: #73594E;
    min-height: 3rem;
}

.form-label {
    color: #8A4737;
    font-weight: 600;
}

.form-control,
.form-select {
    border-radius: 12px;
    border: 1px solid #D9B89D;
    background: #FFFDF9;
}

.form-control:focus,
.form-select:focus {
    border-color: #9A3F3F;
    box-shadow: 0 0 0 0.2rem rgba(154, 63, 63, 0.12);
}

.btn-booking {
    background: #9A3F3F;
    border: none;
    color: #fff;
    border-radius: 12px;
    padding: 0.75rem 1.1rem;
    font-weight: 600;
}

.btn-booking:hover {
    background: #7E312F;
    color: #fff;
}

.history-table thead th {
    color: #8A4737;
    border-bottom-color: rgba(193, 133, 109, 0.35);
}

.history-table td {
    vertical-align: middle;
    color: #5C463B;
}

.status-pill {
    display: inline-block;
    padding: 0.35rem 0.75rem;
    border-radius: 999px;
    background: rgba(193, 133, 109, 0.16);
    color: #8A4737;
    font-weight: 600;
    text-transform: capitalize;
    font-size: 0.8rem;
}
</style>

<div class="service-shell py-2">
    <div class="service-hero mb-4">
        <div class="service-kicker mb-2">Guest Experience</div>
        <h1 class="h3 mb-2">External Services Booking</h1>
        <p class="mb-0">
            Browse available services and reserve your preferred date and time.
        </p>
    </div>

    <?php if (!empty($message)): ?>
        <div class="alert alert-success border-0 shadow-sm"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger border-0 shadow-sm">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="row g-4 mb-4">
        <?php if (empty($services)): ?>
            <div class="col-12">
                <div class="service-card">
                    <div class="card-body text-muted">No external services are available right now.</div>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($services as $service): ?>
                <div class="col-md-6 col-xl-3">
                    <div class="service-card">
                        <div class="card-body">
                            <span class="service-type-badge mb-3">
                                <?= htmlspecialchars($service['service_type']) ?>
                            </span>
                            <h2 class="h5 service-title mb-2"><?= htmlspecialchars($service['name']) ?></h2>
                            <p class="service-text mb-0">
                                <?= htmlspecialchars($service['description'] ?: 'Service details will be confirmed after booking.') ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="booking-card">
                <div class="card-body">
                    <h2 class="h5 booking-title mb-3">Book a Service</h2>
                    <form method="POST" action="<?= APP_URL ?>/?url=home/storeServiceBooking">
                        <div class="mb-3">
                            <label class="form-label">Service</label>
                            <select name="service_id" class="form-select" required>
                                <option value="">Choose a service</option>
                                <?php foreach ($services as $service): ?>
                                    <option value="<?= $service['id'] ?>" <?= (string) ($old['service_id'] ?? '') === (string) $service['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($service['name']) ?> (<?= htmlspecialchars(ucfirst($service['service_type'])) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date</label>
                            <input
                                type="date"
                                name="booking_date"
                                class="form-control"
                                value="<?= htmlspecialchars($old['booking_date'] ?? '') ?>"
                                required
                            >
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Time Slot</label>
                            <input
                                type="time"
                                name="booking_time"
                                class="form-control"
                                value="<?= htmlspecialchars($old['booking_time'] ?? '') ?>"
                                required
                            >
                        </div>
                        <button type="submit" class="btn btn-booking w-100">Submit Booking</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="history-card">
                <div class="card-body">
                    <h2 class="h5 booking-title mb-3">My Service Bookings</h2>
                    <div class="table-responsive">
                        <table class="table history-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Service</th>
                                    <th>Type</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($bookings)): ?>
                                    <tr>
                                        <td colspan="5" class="text-muted py-4">You have not booked any external services yet.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($bookings as $booking): ?>
                                        <tr>
                                            <td>
                                                <div class="fw-semibold"><?= htmlspecialchars($booking['service_name']) ?></div>
                                                <div class="small text-muted"><?= htmlspecialchars($booking['service_description'] ?? '') ?></div>
                                            </td>
                                            <td class="text-capitalize"><?= htmlspecialchars($booking['service_type']) ?></td>
                                            <td><?= htmlspecialchars($booking['booking_date']) ?></td>
                                            <td><?= htmlspecialchars(substr($booking['booking_time'], 0, 5)) ?></td>
                                            <td><span class="status-pill"><?= htmlspecialchars($booking['status']) ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require VIEW_PATH . '/layouts/main.php';
?>
