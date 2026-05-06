<?php $pageTitle = 'Inventory Sync Status'; ob_start(); ?>

<div class="container py-4">

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0"><i class="bi bi-arrow-repeat me-2"></i>Inventory Sync Status</h4>
    <a href="<?= APP_URL ?>/index.php?url=revenue_manager_virtual_inventory/inventoryGrid"
       class="btn btn-sm btn-outline-secondary" id="btn-back-grid">
      <i class="bi bi-arrow-left me-1"></i>Back to Grid
    </a>
  </div>

  <p class="text-muted small mb-3">
    Each row represents a room-type inventory channel.
    Rows with no update in the last hour are flagged as <span class="badge bg-warning text-dark">Stale</span>.
    A sync failure retains the last valid snapshot shown here.
  </p>

  <div class="table-responsive">
    <table class="table table-hover align-middle" id="sync-status-table">
      <thead class="table-dark">
        <tr>
          <th>Room Type</th>
          <th>Last Synced At</th>
          <th>Rows in Inventory</th>
          <th>Stale Rows</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($channels as $ch): ?>
        <?php
          $isStale   = (int)$ch['stale_count'] > 0;
          $noData    = $ch['last_synced_at'] === null;
          $rowClass  = $noData ? 'table-secondary' : ($isStale ? 'table-warning' : '');
        ?>
        <tr class="<?= $rowClass ?>">
          <td><strong><?= htmlspecialchars($ch['room_type_name']) ?></strong></td>
          <td>
            <?php if ($noData): ?>
              <span class="text-muted">Never synced</span>
            <?php else: ?>
              <?= htmlspecialchars($ch['last_synced_at']) ?>
            <?php endif; ?>
          </td>
          <td><?= (int)$ch['row_count'] ?></td>
          <td><?= (int)$ch['stale_count'] ?></td>
          <td>
            <?php if ($noData): ?>
              <span class="badge bg-secondary">No Data</span>
            <?php elseif ($isStale): ?>
              <span class="badge bg-warning text-dark">
                <i class="bi bi-exclamation-triangle me-1"></i>Stale
              </span>
              <small class="text-muted d-block">
                Data may be outdated as of <?= htmlspecialchars($ch['last_synced_at']) ?>
              </small>
            <?php else: ?>
              <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>OK</span>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>

</div>

<?php $content = ob_get_clean(); require VIEW_PATH . '/layouts/main.php'; ?>
