<?php
$pageTitle = 'Audit Log';
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
  <div>
    <h1 class="h3 mb-0"><i class="bi bi-shield-check me-2"></i>Audit Log</h1>
    <small class="text-muted">Manager access only — full system trail</small>
  </div>
  <div class="d-flex gap-2">
    <a href="<?= APP_URL ?>/?url=reports/auditExport&<?= http_build_query($filters) ?>"
       class="btn btn-sm btn-outline-success">
      <i class="bi bi-download me-1"></i>Export CSV
    </a>
    <a href="<?= APP_URL ?>/?url=reports/index" class="btn btn-sm btn-outline-secondary">← Reports</a>
  </div>
</div>

<!-- Filters -->
<form method="GET" action="<?= APP_URL ?>/?url=reports/audit" class="rpt-card p-3 mb-4">
  <div class="row g-2 align-items-end">
    <div class="col-md-2">
      <label class="form-label small fw-semibold mb-1">User</label>
      <select name="user_id" class="form-select form-select-sm">
        <option value="">All Users</option>
        <?php foreach ($users as $u): ?>
          <option value="<?= $u['id'] ?>" <?= ($filters['user_id'] == $u['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($u['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-2">
      <label class="form-label small fw-semibold mb-1">Action</label>
      <input type="text" name="action" class="form-control form-control-sm"
             placeholder="e.g. login, update…" value="<?= htmlspecialchars($filters['action']) ?>">
    </div>
    <div class="col-md-2">
      <label class="form-label small fw-semibold mb-1">Target Type</label>
      <input type="text" name="target_type" class="form-control form-control-sm"
             placeholder="e.g. room, guest…" value="<?= htmlspecialchars($filters['target_type']) ?>">
    </div>
    <div class="col-md-2">
      <label class="form-label small fw-semibold mb-1">From</label>
      <input type="date" name="start" class="form-control form-control-sm" value="<?= htmlspecialchars($filters['start']) ?>">
    </div>
    <div class="col-md-2">
      <label class="form-label small fw-semibold mb-1">To</label>
      <input type="date" name="end" class="form-control form-control-sm" value="<?= htmlspecialchars($filters['end']) ?>">
    </div>
    <div class="col-md-1">
      <button class="btn btn-primary btn-sm w-100">Filter</button>
    </div>
    <div class="col-md-1">
      <a href="<?= APP_URL ?>/?url=reports/audit" class="btn btn-outline-secondary btn-sm w-100">Clear</a>
    </div>
  </div>
</form>

<?php if (empty($entries)): ?>
  <div class="text-center py-5 text-muted">
    <i class="bi bi-inbox fs-1 d-block mb-2"></i>No audit entries match the selected filters.
  </div>
<?php else: ?>
<div class="rpt-card">
  <div class="rpt-hd d-flex justify-content-between align-items-center">
    <span><i class="bi bi-list-ul me-2"></i>Audit Entries</span>
    <span class="badge bg-light text-dark"><?= count($entries) ?> records</span>
  </div>
  <div class="table-responsive">
    <table class="table table-striped table-hover table-sm mb-0">
      <thead><tr>
        <th>User</th>
        <th>Action</th>
        <th>Target</th>
        <th>Old Value</th>
        <th>New Value</th>
        <th>IP Address</th>
        <th class="text-nowrap">Timestamp</th>
      </tr></thead>
      <tbody>
      <?php foreach ($entries as $e):
        $oldTrunc = substr((string)($e['old_value'] ?? ''), 0, 50);
        $newTrunc = substr((string)($e['new_value'] ?? ''), 0, 50);
        $oldFull  = htmlspecialchars((string)($e['old_value'] ?? ''));
        $newFull  = htmlspecialchars((string)($e['new_value'] ?? ''));
        $hasOldMore = strlen((string)($e['old_value'] ?? '')) > 50;
        $hasNewMore = strlen((string)($e['new_value'] ?? '')) > 50;
      ?>
        <tr>
          <td class="fw-semibold"><?= htmlspecialchars($e['user_name'] ?? ('User #' . ($e['user_id'] ?? 'System'))) ?></td>
          <td><code class="small bg-light px-1 rounded"><?= htmlspecialchars($e['action'] ?? '') ?></code></td>
          <td>
            <?php if ($e['target_type'] ?? ''): ?>
              <span class="badge bg-light text-dark border"><?= htmlspecialchars($e['target_type']) ?></span>
              <?php if ($e['target_id'] ?? ''): ?><span class="text-muted small">#<?= (int)$e['target_id'] ?></span><?php endif; ?>
            <?php else: ?><span class="text-muted">—</span><?php endif; ?>
          </td>
          <td class="small">
            <?php if ($oldTrunc): ?>
              <span <?= $hasOldMore ? 'data-bs-toggle="tooltip" title="'.$oldFull.'"' : '' ?>>
                <?= htmlspecialchars($oldTrunc) ?><?= $hasOldMore ? '…' : '' ?>
              </span>
            <?php else: ?><span class="text-muted">—</span><?php endif; ?>
          </td>
          <td class="small">
            <?php if ($newTrunc): ?>
              <span <?= $hasNewMore ? 'data-bs-toggle="tooltip" title="'.$newFull.'"' : '' ?>>
                <?= htmlspecialchars($newTrunc) ?><?= $hasNewMore ? '…' : '' ?>
              </span>
            <?php else: ?><span class="text-muted">—</span><?php endif; ?>
          </td>
          <td class="small text-muted font-monospace"><?= htmlspecialchars($e['ip_address'] ?? '—') ?></td>
          <td class="text-nowrap text-muted small"><?= htmlspecialchars($e['created_at'] ?? '') ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; ?>
<?php $content = ob_get_clean(); require VIEW_PATH . '/layouts/main.php'; ?>
