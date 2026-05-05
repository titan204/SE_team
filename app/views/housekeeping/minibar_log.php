<?php $pageTitle = 'Minibar Log — Room ' . htmlspecialchars($room['room_number']); ob_start(); ?>

<style>
  .log-card { background:#fff; border:1px solid #e0d0c0; border-radius:10px; padding:1.5rem; box-shadow:0 2px 8px rgba(0,0,0,.06); }
  .log-table thead { background:#4B2E2B; color:#fff; }
  .evidence-badge { font-size:.75rem; background:#e8f4fd; border:1px solid #bee3f8; color:#1a6e9e; border-radius:6px; padding:.2em .6em; }
</style>

<div class="container-fluid py-3">
  <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h4 class="mb-0">
      <i class="bi bi-journal-text me-2"></i>Minibar Log — Room
      <span class="badge bg-secondary"><?= htmlspecialchars($room['room_number']) ?></span>
    </h4>
    <a href="<?= APP_URL ?>/?url=housekeeping/minibar/<?= (int)$room['id'] ?>"
       class="btn btn-sm btn-outline-secondary">
      <i class="bi bi-arrow-left me-1"></i>Back to Minibar Form
    </a>
  </div>

  <div class="alert alert-info py-2 small mb-3">
    <i class="bi bi-shield-check me-1"></i>
    This log serves as <strong>dispute evidence</strong> during checkout.
    Each row records the housekeeper who submitted the charge and the exact timestamp.
    Accessible to: <strong>Front Desk, Manager, Supervisor, Housekeeper</strong>.
  </div>

  <?php if (empty($logs)): ?>
    <div class="alert alert-secondary">No minibar consumption logs found for this room.</div>
  <?php else: ?>
  <div class="log-card">
    <div class="table-responsive">
      <table class="table log-table table-hover align-middle mb-0" id="minibar-log-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Logged At</th>
            <th>Housekeeper</th>
            <th>Reservation</th>
            <th>Items Consumed</th>
            <th class="text-end">Total</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($logs as $log):
          $items = json_decode($log['items'] ?? '[]', true) ?: [];
        ?>
          <tr>
            <td><?= (int)$log['id'] ?></td>
            <td>
              <strong><?= htmlspecialchars($log['logged_at']) ?></strong>
              <span class="evidence-badge ms-1"><i class="bi bi-clock"></i> Evidence</span>
            </td>
            <td>
              <i class="bi bi-person-badge me-1 text-secondary"></i>
              <strong><?= htmlspecialchars($log['housekeeper_name'] ?? 'Unknown') ?></strong>
            </td>
            <td>
              <?php if ($log['reservation_id']): ?>
                #<?= (int)$log['reservation_id'] ?>
              <?php else: ?>
                <span class="text-muted small">No active guest</span>
              <?php endif; ?>
            </td>
            <td>
              <?php if (!empty($items)): ?>
                <ul class="mb-0 ps-3 small">
                  <?php foreach ($items as $it): ?>
                    <li>
                      <?= htmlspecialchars($it['name'] ?? '') ?> ×<?= (int)($it['quantity'] ?? 1) ?>
                      <span class="text-muted">@ $<?= number_format((float)($it['unit_price'] ?? 0), 2) ?></span>
                      = <strong>$<?= number_format((float)($it['line_total'] ?? 0), 2) ?></strong>
                    </li>
                  <?php endforeach; ?>
                </ul>
              <?php else: ?>
                <span class="text-muted small">—</span>
              <?php endif; ?>
            </td>
            <td class="text-end"><strong>$<?= number_format((float)$log['total_amount'], 2) ?></strong></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php endif; ?>
</div>

<?php $content = ob_get_clean(); require VIEW_PATH . '/layouts/main.php'; ?>
