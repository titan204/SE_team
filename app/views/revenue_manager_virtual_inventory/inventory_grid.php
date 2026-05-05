<?php $pageTitle = 'Virtual Inventory Grid'; ob_start(); ?>

<style>
  .inv-grid-wrap { overflow-x: auto; }
  .inv-grid { border-collapse: collapse; min-width: 900px; font-size: .78rem; }
  .inv-grid th, .inv-grid td { border: 1px solid #ddd; padding: 4px 6px; white-space: nowrap; }
  .inv-grid thead th { background: #2d3a4a; color: #fff; text-align: center; position: sticky; top: 0; }
  .inv-grid .col-rt  { background: #f5f5f5; font-weight: 600; min-width: 110px;
                        position: sticky; left: 0; z-index: 1; border-right: 2px solid #aaa; }
  .cell-green  { background: #d4edda; }
  .cell-yellow { background: #fff3cd; }
  .cell-red    { background: #f8d7da; }
  .cell-inner  { font-size: .72rem; line-height: 1.4; text-align: center; }
  .cell-avail  { font-weight: 700; font-size: .85rem; }
  .stale-banner { background: #fff3cd; border: 1px solid #ffc107; border-radius: 6px;
                  padding: .6rem 1rem; margin-bottom: 1rem; font-size: .9rem; }
  .legend span { display: inline-block; width: 14px; height: 14px;
                 border-radius: 3px; margin-right: 4px; vertical-align: middle; }
</style>

<div class="container-fluid py-3">

  <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h4 class="mb-0"><i class="bi bi-grid-3x3 me-2"></i>Virtual Inventory Grid — Next 30 Days</h4>
    <div class="d-flex gap-2">
      <a href="<?= APP_URL ?>/index.php?url=revenue_manager_virtual_inventory/syncStatus"
         class="btn btn-sm btn-outline-secondary" id="btn-sync-status">
        <i class="bi bi-arrow-repeat me-1"></i>Sync Status
      </a>
      <a href="<?= APP_URL ?>/index.php?url=revenue_manager_virtual_inventory"
         class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back
      </a>
    </div>
  </div>

  <?php if (!empty($_SESSION['inv_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show py-2">
      <?= htmlspecialchars($_SESSION['inv_error']) ?>
      <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['inv_error']); ?>
  <?php endif; ?>

  <?php if (!empty($_SESSION['inv_warning'])): ?>
    <div class="alert alert-warning alert-dismissible fade show py-2">
      <?= htmlspecialchars($_SESSION['inv_warning']) ?>
      <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['inv_warning']); ?>
  <?php endif; ?>

  <?php if (!empty($_SESSION['inv_success'])): ?>
    <div class="alert alert-success alert-dismissible fade show py-2">
      <?= htmlspecialchars($_SESSION['inv_success']) ?>
      <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['inv_success']); ?>
  <?php endif; ?>

  <?php if ($anyStale): ?>
    <div class="stale-banner">
      <i class="bi bi-exclamation-triangle-fill text-warning me-1"></i>
      <strong>Data may be outdated</strong> as of <?= htmlspecialchars($staleTime ?? 'unknown') ?>.
      Some inventory rows have not been synced recently.
      <a href="<?= APP_URL ?>/index.php?url=revenue_manager_virtual_inventory/syncStatus"
         class="ms-2 small">View sync status →</a>
    </div>
  <?php endif; ?>

  <!-- Legend -->
  <div class="legend mb-2 small text-muted">
    <span class="cell-green" style="border:1px solid #aaa;"></span> Available &gt;20% &nbsp;
    <span class="cell-yellow" style="border:1px solid #aaa;"></span> Low (&le;20%) &nbsp;
    <span class="cell-red" style="border:1px solid #aaa;"></span> Sold out / Overbooked &nbsp;&nbsp;
    Each cell: <strong>Avail</strong> / VMax | Conf | Phys
  </div>

  <!-- Grid -->
  <div class="inv-grid-wrap">
    <table class="inv-grid" id="inventory-grid-table">
      <thead>
        <tr>
          <th class="col-rt">Room Type</th>
          <?php foreach ($dates as $d): ?>
            <th style="min-width:70px;">
              <?= date('M j', strtotime($d)) ?><br>
              <small style="font-weight:400;opacity:.7;"><?= date('D', strtotime($d)) ?></small>
            </th>
          <?php endforeach; ?>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($roomTypes as $rt): $rtId = $rt['id']; ?>
        <tr>
          <td class="col-rt">
            <?= htmlspecialchars($rt['name']) ?>
            <small class="d-block text-muted"><?= $rt['physical_rooms'] ?> rooms</small>
          </td>
          <?php foreach ($dates as $d):
            $cell = $grid[$rtId][$d] ?? ['physical'=>0,'virtMax'=>0,'confirmed'=>0,'available'=>0,'color'=>'red'];
            $colorClass = 'cell-' . $cell['color'];
          ?>
            <td class="<?= $colorClass ?>" style="cursor:pointer;"
                onclick="openAdjustModal(<?= $rtId ?>, '<?= $d ?>', <?= $cell['virtMax'] ?>, <?= $cell['physical'] ?>)"
                title="Click to adjust | <?= htmlspecialchars($rt['name']) ?> on <?= $d ?>">
              <div class="cell-inner">
                <div class="cell-avail"><?= $cell['available'] ?></div>
                <div style="color:#555;"><?= $cell['virtMax'] ?> / <?= $cell['confirmed'] ?> / <?= $cell['physical'] ?></div>
              </div>
            </td>
          <?php endforeach; ?>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <p class="text-muted small mt-2">Click any cell to adjust its virtual_max allocation.</p>
</div>

<!-- Adjust Modal -->
<div class="modal fade" id="adjustModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-sliders me-2"></i>Adjust Virtual Max</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="<?= APP_URL ?>/index.php?url=revenue_manager_virtual_inventory/adjust"
            id="adjust-form">
        <div class="modal-body">
          <input type="hidden" name="room_type_id" id="adj_room_type_id">
          <input type="hidden" name="date"         id="adj_date">

          <div class="mb-2">
            <label class="form-label fw-semibold">Date</label>
            <div id="adj_date_display" class="form-control-plaintext fw-bold"></div>
          </div>
          <div class="mb-2">
            <label class="form-label fw-semibold">Physical Rooms</label>
            <div id="adj_physical_display" class="form-control-plaintext text-muted"></div>
          </div>
          <div class="mb-3">
            <label for="adj_virtual_max" class="form-label fw-semibold">New Virtual Max</label>
            <input type="number" id="adj_virtual_max" name="virtual_max" min="0"
                   class="form-control" required>
            <div class="form-text" id="adj_range_hint"></div>
          </div>
          <div class="mb-3">
            <label for="adj_reason" class="form-label">Reason <small class="text-muted">(optional)</small></label>
            <input type="text" id="adj_reason" name="reason" class="form-control"
                   placeholder="e.g. maintenance block, seasonal adjustment">
          </div>
        </div>
        <div class="modal-footer gap-2">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="btn-save-adjust">Save</button>
          <button type="button" class="btn btn-warning" id="btn-switch-override"
                  onclick="switchToOverride()">Override (allow overbooking)</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Override Modal -->
<div class="modal fade" id="overrideModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content border-warning">
      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i>Manual Override</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="<?= APP_URL ?>/index.php?url=revenue_manager_virtual_inventory/override"
            id="override-form">
        <div class="modal-body">
          <div class="alert alert-warning py-2 small">
            Override allows virtual_max to exceed physical rooms. This will trigger an overbooking alert (UC15).
          </div>
          <input type="hidden" name="room_type_id" id="ovr_room_type_id">
          <input type="hidden" name="date"         id="ovr_date">

          <div class="mb-2">
            <label class="form-label fw-semibold">Date</label>
            <div id="ovr_date_display" class="form-control-plaintext fw-bold"></div>
          </div>
          <div class="mb-3">
            <label for="ovr_virtual_max" class="form-label fw-semibold">New Virtual Max</label>
            <input type="number" id="ovr_virtual_max" name="virtual_max" min="0"
                   class="form-control border-warning" required>
          </div>
          <div class="mb-3">
            <label for="ovr_reason" class="form-label fw-semibold">
              Reason <span class="text-danger">*</span>
            </label>
            <textarea id="ovr_reason" name="reason" class="form-control" rows="2"
                      placeholder="Required: explain why overbooking is authorized" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-warning" id="btn-save-override">Apply Override</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function openAdjustModal(rtId, date, virtMax, physical) {
  document.getElementById('adj_room_type_id').value = rtId;
  document.getElementById('adj_date').value          = date;
  document.getElementById('adj_date_display').textContent    = date;
  document.getElementById('adj_physical_display').textContent = physical + ' rooms';
  document.getElementById('adj_virtual_max').value   = virtMax;
  document.getElementById('adj_virtual_max').max     = physical * 2;
  document.getElementById('adj_range_hint').textContent = 'Valid range: 0 – ' + (physical * 2);

  // Store for override switch
  document.getElementById('adj_room_type_id').dataset.physical = physical;

  var modal = new bootstrap.Modal(document.getElementById('adjustModal'));
  modal.show();
}

function switchToOverride() {
  var rtId     = document.getElementById('adj_room_type_id').value;
  var date     = document.getElementById('adj_date').value;
  var virtMax  = document.getElementById('adj_virtual_max').value;

  document.getElementById('ovr_room_type_id').value   = rtId;
  document.getElementById('ovr_date').value            = date;
  document.getElementById('ovr_date_display').textContent = date;
  document.getElementById('ovr_virtual_max').value     = virtMax;

  bootstrap.Modal.getInstance(document.getElementById('adjustModal')).hide();
  var ovrModal = new bootstrap.Modal(document.getElementById('overrideModal'));
  ovrModal.show();
}
</script>

<?php $content = ob_get_clean(); require VIEW_PATH . '/layouts/main.php'; ?>
