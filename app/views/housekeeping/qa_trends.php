<?php $pageTitle = 'QA Trends — Last ' . $days . ' Days'; ob_start(); ?>

<style>
  .trend-card { background:#fff; border:1px solid #e0d0c0; border-radius:10px; padding:1.4rem; box-shadow:0 2px 8px rgba(0,0,0,.05); }
  .trend-table thead { background:#4B2E2B; color:#fff; }
  .rate-bar   { height:8px; border-radius:4px; background:#198754; display:inline-block; min-width:4px; }
</style>

<div class="container-fluid py-3">
  <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h4 class="mb-0"><i class="bi bi-bar-chart-line me-2"></i>QA Trends</h4>
    <form method="GET" action="<?= APP_URL ?>/" class="d-flex align-items-center gap-2">
      <input type="hidden" name="url" value="housekeeping/qaTrends">
      <select name="period" class="form-select form-select-sm" style="width:140px">
        <?php foreach ([7,14,30,60,90] as $p): ?>
          <option value="<?= $p ?>" <?= $p == $days ? 'selected' : '' ?>>Last <?= $p ?> days</option>
        <?php endforeach; ?>
      </select>
      <button type="submit" class="btn btn-sm btn-outline-primary">Go</button>
    </form>
    <a href="<?= APP_URL ?>/?url=housekeeping/qa" class="btn btn-sm btn-outline-secondary">
      <i class="bi bi-arrow-left me-1"></i>Back
    </a>
  </div>

  <div class="row g-3">

    <!-- Per Inspector -->
    <div class="col-lg-6">
      <div class="trend-card h-100">
        <h6 class="fw-semibold mb-3"><i class="bi bi-person me-1"></i>Pass Rate by Inspector</h6>
        <?php if (empty($trends['byInspector'])): ?>
          <p class="text-muted">No inspection data for this period.</p>
        <?php else: ?>
          <table class="table trend-table align-middle mb-0">
            <thead><tr><th>Inspector</th><th>Total</th><th>Passed</th><th>Pass Rate</th></tr></thead>
            <tbody>
              <?php foreach ($trends['byInspector'] as $row): ?>
              <tr>
                <td><?= htmlspecialchars($row['inspector_name']) ?></td>
                <td><?= $row['total'] ?></td>
                <td><?= $row['passed'] ?></td>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <span class="rate-bar" style="width:<?= $row['pass_rate'] ?>px"></span>
                    <strong><?= $row['pass_rate'] ?>%</strong>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>
    </div>

    <!-- Per Floor -->
    <div class="col-lg-6">
      <div class="trend-card h-100">
        <h6 class="fw-semibold mb-3"><i class="bi bi-building me-1"></i>Pass Rate by Floor</h6>
        <?php if (empty($trends['byFloor'])): ?>
          <p class="text-muted">No inspection data for this period.</p>
        <?php else: ?>
          <table class="table trend-table align-middle mb-0">
            <thead><tr><th>Floor</th><th>Total</th><th>Passed</th><th>Pass Rate</th></tr></thead>
            <tbody>
              <?php foreach ($trends['byFloor'] as $row): ?>
              <tr>
                <td>Floor <?= $row['floor'] ?></td>
                <td><?= $row['total'] ?></td>
                <td><?= $row['passed'] ?></td>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <span class="rate-bar" style="width:<?= $row['pass_rate'] ?>px"></span>
                    <strong><?= $row['pass_rate'] ?>%</strong>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>
    </div>

  </div>
</div>

<?php $content = ob_get_clean(); require VIEW_PATH . '/layouts/main.php'; ?>
