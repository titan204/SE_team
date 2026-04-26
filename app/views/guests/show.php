<?php $pageTitle = 'Guest Details'; ?>
<?php require VIEW_PATH . '/layouts/main.php'; ?>

<style>
body { background: #FBF9D1; }

.card {
    border: none;
    border-radius: 18px;
    background: #ffffff;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    margin-bottom: 20px;
}

.card-header {
    background: #9A3F3F;
    color: #FBF9D1;
    font-weight: bold;
    border-radius: 18px 18px 0 0 !important;
}

.badge-vip       { background: #C1856D; color: white; }
.badge-blacklist { background: #9A3F3F; color: white; }
.badge-platinum  { background: #7B5EA7; color: white; }
.badge-gold      { background: #C9A84C; color: white; }
.badge-silver    { background: #888;    color: white; }
.badge-standard  { background: #aaa;    color: white; }

.info-label {
    font-weight: 600;
    color: #9A3F3F;
    font-size: 13px;
}

.btn-vip       { background: #C1856D; color: white; border: none; border-radius: 10px; }
.btn-vip:hover { background: #9A3F3F; color: white; }

.btn-blacklist       { background: #9A3F3F; color: white; border: none; border-radius: 10px; }
.btn-blacklist:hover { background: #7a2f2f; color: white; }

.btn-gdpr       { background: #555; color: white; border: none; border-radius: 10px; }
.btn-gdpr:hover { background: #333; color: white; }

.container { animation: fadeIn 0.5s ease-in-out; }

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to   { opacity: 1; transform: translateY(0); }
}
</style>

<div class="container mt-4">

    <!-- ═══════════════════════════════
         HEADER — Name + Action Buttons
    ══════════════════════════════════ -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>
            <?= htmlspecialchars($guest['name']) ?>
            <?php if (!empty($guest['is_vip'])): ?>
                <span class="badge badge-vip ms-2">⭐ VIP</span>
            <?php endif; ?>
            <?php if (!empty($guest['is_blacklisted'])): ?>
                <span class="badge badge-blacklist ms-2">🚫 Blacklisted</span>
            <?php endif; ?>
        </h3>

        <div class="d-flex gap-2">
            <!-- Edit -->
            <a href="index.php?url=guests/edit/<?= $guest['id'] ?>"
               class="btn btn-warning btn-sm">✏️ Edit</a>

            <!-- Flag VIP -->
            <?php if (empty($guest['is_vip'])): ?>
            <form method="POST"
                  action="index.php?url=guests/flagVip/<?= $guest['id'] ?>"
                  style="display:inline">
                <button class="btn btn-vip btn-sm">⭐ Flag VIP</button>
            </form>
            <?php endif; ?>

            <!-- Blacklist -->
            <?php if (empty($guest['is_blacklisted'])): ?>
            <button class="btn btn-blacklist btn-sm"
                    onclick="document.getElementById('blacklist-form').style.display='block'">
                🚫 Blacklist
            </button>
            <?php endif; ?>

            <!-- Anonymize (GDPR) -->
            <?php if (empty($guest['gdpr_anonymized'])): ?>
            <form method="POST"
                  action="index.php?url=guests/anonymize/<?= $guest['id'] ?>"
                  style="display:inline"
                  onsubmit="return confirm('This will permanently anonymize this guest. Continue?')">
                <button class="btn btn-gdpr btn-sm">🔒 Anonymize</button>
            </form>
            <?php endif; ?>

        </div>
    </div>

    <!-- Blacklist Reason Form (hidden by default) -->
    <div id="blacklist-form" style="display:none" class="card mb-3">
        <div class="card-body">
            <form method="POST"
                  action="index.php?url=guests/blacklist/<?= $guest['id'] ?>">
                <label class="form-label info-label">Blacklist Reason</label>
                <input type="text" name="reason" class="form-control mb-2"
                       placeholder="Enter reason..." required>
                <button type="submit" class="btn btn-blacklist btn-sm">Confirm Blacklist</button>
                <button type="button" class="btn btn-secondary btn-sm"
                        onclick="document.getElementById('blacklist-form').style.display='none'">
                    Cancel
                </button>
            </form>
        </div>
    </div>

    <!-- ═══════════════════════════════
         PROFILE CARD
    ══════════════════════════════════ -->
    <div class="card">
        <div class="card-header">👤 Guest Profile</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-2">
                    <div class="info-label">Email</div>
                    <div><?= htmlspecialchars($guest['email'] ?? '-') ?></div>
                </div>
                <div class="col-md-4 mb-2">
                    <div class="info-label">Phone</div>
                    <div><?= htmlspecialchars($guest['phone'] ?? '-') ?></div>
                </div>
                <div class="col-md-4 mb-2">
                    <div class="info-label">Nationality</div>
                    <div><?= htmlspecialchars($guest['nationality'] ?? '-') ?></div>
                </div>
                <div class="col-md-4 mb-2">
                    <div class="info-label">Date of Birth</div>
                    <div><?= htmlspecialchars($guest['date_of_birth'] ?? '-') ?></div>
                </div>
                <div class="col-md-4 mb-2">
                    <div class="info-label">National ID</div>
                    <div><?= htmlspecialchars($guest['national_id'] ?? '-') ?></div>
                </div>
                <div class="col-md-4 mb-2">
                    <div class="info-label">Member Since</div>
                    <div><?= htmlspecialchars($guest['created_at'] ?? '-') ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════
         LOYALTY + LTV
    ══════════════════════════════════ -->
    <div class="card">
        <div class="card-header">🏅 Loyalty & Value</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-2">
                    <div class="info-label">Loyalty Tier</div>
                    <span class="badge badge-<?= $guest['loyalty_tier'] ?? 'standard' ?> fs-6">
                        <?= ucfirst($guest['loyalty_tier'] ?? 'standard') ?>
                    </span>
                </div>
                <div class="col-md-4 mb-2">
                    <div class="info-label">Lifetime Nights</div>
                    <div><?= htmlspecialchars($guest['lifetime_nights'] ?? 0) ?> nights</div>
                </div>
                <div class="col-md-4 mb-2">
                    <div class="info-label">Lifetime Value (LTV)</div>
                    <div class="fs-5 fw-bold text-success">
                        $<?= number_format($ltv ?? 0, 2) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════
         PREFERENCES
    ══════════════════════════════════ -->
    <div class="card">
        <div class="card-header">⚙️ Preferences</div>
        <div class="card-body">
            <?php if (empty($preferences)): ?>
                <p class="text-muted">No preferences recorded.</p>
            <?php else: ?>
                <ul class="list-group list-group-flush">
                    <?php foreach ($preferences as $pref): ?>
                        <li class="list-group-item">
                            <span class="info-label"><?= htmlspecialchars($pref['preference_type']) ?>:</span>
                            <?= htmlspecialchars($pref['preference_value']) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>

    <!-- ═══════════════════════════════
         RESERVATION HISTORY
    ══════════════════════════════════ -->
    <div class="card">
        <div class="card-header">🛏️ Reservation History</div>
        <div class="card-body">
            <?php if (empty($reservations)): ?>
                <p class="text-muted">No reservations found.</p>
            <?php else: ?>
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Room</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservations as $res): ?>
                        <tr>
                            <td><?= $res['id'] ?></td>
                            <td><?= htmlspecialchars($res['check_in']) ?></td>
                            <td><?= htmlspecialchars($res['check_out']) ?></td>
                            <td><?= htmlspecialchars($res['room_id'] ?? '-') ?></td>
                            <td>$<?= number_format($res['total_price'] ?? 0, 2) ?></td>
                            <td>
                                <span class="badge bg-secondary">
                                    <?= ucfirst($res['status'] ?? '-') ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <!-- ═══════════════════════════════
         FEEDBACK HISTORY
    ══════════════════════════════════ -->
    <div class="card">
        <div class="card-header">💬 Feedback</div>
        <div class="card-body">
            <?php if (empty($feedback)): ?>
                <p class="text-muted">No feedback recorded.</p>
            <?php else: ?>
                <?php foreach ($feedback as $fb): ?>
                <div class="border rounded p-3 mb-2">
                    <div class="d-flex justify-content-between">
                        <span class="info-label">Rating: <?= $fb['rating'] ?? '-' ?>/5</span>
                        <small class="text-muted"><?= htmlspecialchars($fb['created_at'] ?? '') ?></small>
                    </div>
                    <p class="mb-0 mt-1"><?= htmlspecialchars($fb['comment'] ?? '-') ?></p>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Back Button -->
    <div class="mb-4">
        <a href="index.php?url=guests" class="btn btn-secondary">← Back to Guests</a>
    </div>

</div>