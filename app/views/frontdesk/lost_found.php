<?php $pageTitle = 'Lost & Found Management'; ob_start(); ?>

<style>
  .lf-card    { background:#fff; border:1px solid #e0d0c0; border-radius:10px; padding:1.4rem; box-shadow:0 2px 8px rgba(0,0,0,.06); }
  .lf-table   thead { background:#4B2E2B; color:#fff; }
  .lf-table   tbody tr:hover { background:#fff8f0; }
  .hv-badge   { background:#dc3545; color:#fff; font-size:.7rem; padding:2px 7px; border-radius:4px; }
  .candidate-card { border:1px solid #0d6efd; border-radius:8px; padding:.8rem; background:#f0f6ff; }
</style>

<div class="container-fluid py-3">
  <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h4 class="mb-0"><i class="bi bi-search-heart me-2"></i>Lost &amp; Found Management</h4>
    <div class="d-flex gap-2">
      <a href="<?= APP_URL ?>/?url=housekeeping/foundItem" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-plus me-1"></i>Log Found Item
      </a>
    </div>
  </div>

  <?php foreach (['fd_success'=>'success','fd_error'=>'danger','fd_info'=>'info'] as $key=>$cls): ?>
    <?php if (!empty($_SESSION[$key])): ?>
      <div class="alert alert-<?= $cls ?> alert-dismissible fade show py-2">
        <?= htmlspecialchars($_SESSION[$key]) ?>
        <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
      </div>
      <?php unset($_SESSION[$key]); ?>
    <?php endif; ?>
  <?php endforeach; ?>

  <?php
    // UC37 Step 5: Overdue items past 90-day retention — visible to supervisor/manager
    $role = $_SESSION['user_role'] ?? '';
    if (!empty($overdueItems) && in_array($role, ['supervisor','manager','front_desk'])):
  ?>
  <div class="alert alert-warning alert-dismissible fade show py-2 mb-3" id="overdue-items-alert">
    <i class="bi bi-clock-history me-1"></i>
    <strong><?= count($overdueItems) ?> item(s)</strong> have exceeded the 90-day retention period and require disposal.
    <?php foreach ($overdueItems as $oi): ?>
      <span class="badge bg-warning text-dark ms-1"><?= htmlspecialchars($oi['lf_reference']) ?></span>
    <?php endforeach; ?>
    <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
  </div>
  <?php endif; ?>

  <!-- Auto-match candidates (shown when report submitted) -->
  <?php if (!empty($_SESSION['lf_candidates'])): ?>
  <div class="alert alert-info mb-3">
    <h6 class="fw-semibold mb-2"><i class="bi bi-magic me-1"></i>Possible Matches Found — Please Confirm</h6>
    <div class="row g-2">
      <?php foreach ($_SESSION['lf_candidates'] as $c): ?>
      <div class="col-md-4">
        <div class="candidate-card">
          <div class="fw-semibold"><?= htmlspecialchars($c['lf_reference']) ?></div>
          <div class="small text-muted mb-1"><?= htmlspecialchars(substr($c['description'], 0, 100)) ?></div>
          <div class="small text-muted mb-2">Found: <?= htmlspecialchars($c['found_at']) ?></div>
          <?php if ($c['photo_url']): ?>
            <img src="<?= htmlspecialchars($c['photo_url']) ?>" class="img-thumbnail mb-2" style="max-height:80px">
          <?php endif; ?>
          <form method="POST" action="<?= APP_URL ?>/?url=frontdesk/matchItem">
            <input type="hidden" name="found_item_id" value="<?= (int)$c['id'] ?>">
            <input type="hidden" name="report_id"     value="<?= (int)$_SESSION['lf_report_id'] ?>">
            <button type="submit" class="btn btn-sm btn-primary w-100" id="btn-match-<?= $c['id'] ?>">
              <i class="bi bi-check me-1"></i>Confirm Match
            </button>
          </form>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <form method="POST" action="<?= APP_URL ?>/?url=frontdesk/lostFound" class="mt-2">
      <input type="hidden" name="_dismiss_candidates" value="1">
      <button type="submit" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-x me-1"></i>None match — dismiss
      </button>
    </form>
  </div>
  <?php endif; ?>

  <!-- Tabs -->
  <ul class="nav nav-tabs mb-3" id="lfTabs">
    <li class="nav-item">
      <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-inventory">
        Found Items <span class="badge bg-secondary ms-1"><?= count($foundItems) ?></span>
      </button>
    </li>
    <li class="nav-item">
      <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-reports">
        Guest Reports <span class="badge bg-secondary ms-1"><?= count($lostReports) ?></span>
      </button>
    </li>
    <li class="nav-item">
      <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-report-form">
        <i class="bi bi-plus-circle me-1"></i>New Guest Report
      </button>
    </li>
  </ul>

  <div class="tab-content">

    <!-- Tab 1: Found Items Queue -->
    <div class="tab-pane fade show active" id="tab-inventory">

      <!-- Filters -->
      <div class="lf-card mb-3">
        <form method="GET" action="<?= APP_URL ?>/" class="d-flex flex-wrap gap-2 align-items-end">
          <input type="hidden" name="url" value="frontdesk/lostFound">
          <div>
            <label class="form-label small mb-1">Status</label>
            <select name="status" class="form-select form-select-sm">
              <option value="">All</option>
              <?php foreach (['stored','matched','claimed','shipped','returned','disposed'] as $s): ?>
                <option value="<?= $s ?>" <?= ($filters['status'] ?? '') === $s ? 'selected' : '' ?>>
                  <?= ucfirst($s) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label class="form-label small mb-1">From</label>
            <input type="date" name="date_from" class="form-control form-control-sm"
                   value="<?= htmlspecialchars($filters['date_from'] ?? '') ?>">
          </div>
          <div>
            <label class="form-label small mb-1">To</label>
            <input type="date" name="date_to" class="form-control form-control-sm"
                   value="<?= htmlspecialchars($filters['date_to'] ?? '') ?>">
          </div>
          <button type="submit" class="btn btn-sm btn-outline-primary">Filter</button>
          <a href="<?= APP_URL ?>/?url=frontdesk/lostFound" class="btn btn-sm btn-outline-secondary">Clear</a>
        </form>
      </div>

      <div class="lf-card">
        <?php if (empty($foundItems)): ?>
          <p class="text-muted mb-0">No found items match the current filter.</p>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table lf-table align-middle mb-0">
              <thead>
                <tr>
                  <th>Ref</th><th>Description</th><th>Location</th><th>Found</th>
                  <th>Condition</th><th>Status</th><th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($foundItems as $fi): ?>
                <tr>
                  <td>
                    <span class="fw-semibold"><?= htmlspecialchars($fi['lf_reference']) ?></span>
                    <?php if ($fi['is_high_value']): ?>
                      <span class="hv-badge d-block mt-1">HIGH VALUE</span>
                    <?php endif; ?>
                    <?php if ($fi['escalated_to_security']): ?>
                      <span class="badge bg-danger mt-1">Security</span>
                    <?php endif; ?>
                  </td>
                  <td class="small" style="max-width:200px">
                    <?= htmlspecialchars(substr($fi['description'], 0, 100)) ?>
                    <?php if ($fi['photo_url']): ?>
                      <br><a href="<?= htmlspecialchars($fi['photo_url']) ?>" target="_blank" class="small">
                        <i class="bi bi-image me-1"></i>Photo
                      </a>
                    <?php endif; ?>
                  </td>
                  <td class="small">
                    <?php if ($fi['location_type'] === 'room'): ?>
                      <i class="bi bi-door-closed me-1"></i>Room <?= htmlspecialchars($fi['room_number']) ?>
                    <?php else: ?>
                      <i class="bi bi-geo-alt me-1"></i><?= ucfirst($fi['public_area'] ?? '') ?>
                    <?php endif; ?>
                  </td>
                  <td class="small"><?= date('d M H:i', strtotime($fi['found_at'])) ?></td>
                  <td>
                    <?php $cmap = ['good'=>'success','damaged'=>'warning','fragile'=>'info']; ?>
                    <span class="badge bg-<?= $cmap[$fi['condition']] ?? 'secondary' ?>">
                      <?= ucfirst($fi['condition']) ?>
                    </span>
                  </td>
                  <td>
                    <?php $smap = [
                      'stored'=>'secondary','matched'=>'info text-dark','claimed'=>'success',
                      'shipped'=>'primary','returned'=>'dark','disposed'=>'danger'
                    ]; ?>
                    <span class="badge bg-<?= $smap[$fi['status']] ?? 'secondary' ?>">
                      <?= ucfirst($fi['status']) ?>
                    </span>
                  </td>
                  <td>
                    <?php if ($fi['status'] === 'matched' && !$fi['is_high_value']): ?>
                      <button class="btn btn-sm btn-outline-success mb-1"
                              onclick="openReturnModal(<?= $fi['id'] ?>, '<?= addslashes($fi['lf_reference']) ?>')"
                              id="btn-return-<?= $fi['id'] ?>">
                        <i class="bi bi-box-arrow-right me-1"></i>Arrange Return
                      </button>
                    <?php endif; ?>
                    <?php if ($fi['status'] === 'stored' && !$fi['is_high_value']): ?>
                      <button class="btn btn-sm btn-outline-danger"
                              onclick="openDisposeModal(<?= $fi['id'] ?>, '<?= addslashes($fi['lf_reference']) ?>')"
                              id="btn-dispose-<?= $fi['id'] ?>">
                        <i class="bi bi-trash me-1"></i>Dispose
                      </button>
                    <?php endif; ?>
                    <?php if ($fi['is_high_value']): ?>
                      <span class="text-danger small"><i class="bi bi-shield-lock me-1"></i>Security only</span>
                    <?php endif; ?>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </div><!-- tab-inventory -->

    <!-- Tab 2: Guest Reports -->
    <div class="tab-pane fade" id="tab-reports">
      <div class="lf-card">
        <?php if (empty($lostReports)): ?>
          <p class="text-muted mb-0">No guest reports yet.</p>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table lf-table align-middle mb-0">
              <thead>
                <tr><th>Guest</th><th>Description</th><th>Lost Date</th><th>Status</th><th>Matched Ref</th></tr>
              </thead>
              <tbody>
                <?php foreach ($lostReports as $lr): ?>
                <tr>
                  <td>
                    <strong><?= htmlspecialchars($lr['guest_name']) ?></strong>
                    <div class="small text-muted"><?= htmlspecialchars($lr['guest_email'] ?? '') ?></div>
                  </td>
                  <td class="small" style="max-width:200px">
                    <?= htmlspecialchars(substr($lr['description'], 0, 100)) ?>
                  </td>
                  <td class="small"><?= htmlspecialchars($lr['lost_date'] ?? '—') ?></td>
                  <td>
                    <?php $rmap = ['open'=>'warning text-dark','matched'=>'success','closed'=>'secondary']; ?>
                    <span class="badge bg-<?= $rmap[$lr['status']] ?? 'secondary' ?>">
                      <?= ucfirst($lr['status']) ?>
                    </span>
                  </td>
                  <td class="small fw-semibold"><?= htmlspecialchars($lr['matched_ref'] ?? '—') ?></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </div><!-- tab-reports -->

    <!-- Tab 3: New Guest Report Form -->
    <div class="tab-pane fade" id="tab-report-form">
      <div class="lf-card" style="max-width:640px">
        <h6 class="fw-semibold mb-3"><i class="bi bi-person-exclamation me-1"></i>Accept Guest Lost-Item Report</h6>
        <form method="POST" action="<?= APP_URL ?>/?url=frontdesk/lostReport" id="lost-report-form">

          <div class="mb-3">
            <label for="lr_guest" class="form-label fw-semibold">
              Guest <span class="text-danger">*</span>
            </label>
            <select id="lr_guest" name="guest_id" class="form-select" required>
              <option value="">— Select guest —</option>
              <?php foreach ($guests as $g): ?>
                <option value="<?= $g['id'] ?>"><?= htmlspecialchars($g['name']) ?>
                  <?= $g['email'] ? '(' . htmlspecialchars($g['email']) . ')' : '' ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-3">
            <label for="lr_desc" class="form-label fw-semibold">
              Item Description <span class="text-danger">*</span>
            </label>
            <textarea id="lr_desc" name="description" class="form-control" rows="3"
                      placeholder="Describe the lost item: type, colour, brand, any unique features…"
                      required></textarea>
          </div>

          <div class="row g-2 mb-3">
            <div class="col-md-6">
              <label for="lr_date" class="form-label fw-semibold small">Approximate Date Lost</label>
              <input type="date" id="lr_date" name="lost_date" class="form-control"
                     max="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d') ?>">
            </div>
            <div class="col-md-6">
              <label for="lr_res" class="form-label fw-semibold small">Reservation # (if known)</label>
              <input type="number" id="lr_res" name="reservation_id" class="form-control"
                     placeholder="Optional">
            </div>
          </div>

          <div class="alert alert-info py-2 small mb-3">
            <i class="bi bi-lightning me-1"></i>
            The system will automatically search for matching found items within ±3 days of the reported date.
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary px-4" id="btn-submit-report">
              <i class="bi bi-person-plus me-1"></i>Register Report
            </button>
          </div>
        </form>
      </div>
    </div><!-- tab-report-form -->

  </div><!-- tab-content -->
</div>

<!-- Return Modal -->
<div class="modal fade" id="returnModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title"><i class="bi bi-box-arrow-right me-2"></i>Arrange Return</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" id="return-form">
        <div class="modal-body">
          <p class="small text-muted mb-3">Item: <strong id="return-ref"></strong></p>

          <div class="mb-3">
            <label class="form-label fw-semibold">Return Method</label>
            <div class="d-flex gap-3">
              <div class="form-check">
                <input class="form-check-input" type="radio" name="return_method" value="pickup"
                       id="rm_pickup" checked onchange="toggleAddress(this.value)">
                <label class="form-check-label" for="rm_pickup">In-Person Pickup</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="return_method" value="courier"
                       id="rm_courier" onchange="toggleAddress(this.value)">
                <label class="form-check-label" for="rm_courier">Courier / Shipping</label>
              </div>
            </div>
          </div>

          <div id="courier-fields" style="display:none">
            <div class="mb-2">
              <label class="form-label small fw-semibold">Delivery Address</label>
              <textarea name="return_address" class="form-control form-control-sm" rows="2"
                        placeholder="Full delivery address…"></textarea>
            </div>
            <div class="mb-2">
              <label class="form-label small fw-semibold">Shipping Cost ($)</label>
              <input type="number" name="shipping_cost" class="form-control form-control-sm"
                     step="0.01" min="0" value="0.00" id="ret_shipping_cost">
            </div>
            <div class="form-check mb-2" id="consent-check" style="display:none">
              <input class="form-check-input" type="checkbox" name="guest_consent"
                     value="1" id="ret_guest_consent">
              <label class="form-check-label small text-danger fw-semibold" for="ret_guest_consent">
                <i class="bi bi-check-circle me-1"></i>
                Guest has verbally confirmed they agree to the shipping charge.
              </label>
            </div>
          </div>

          <div class="mb-3">
            <label for="ret_guest" class="form-label fw-semibold small">Guest</label>
            <select id="ret_guest" name="guest_id" class="form-select form-select-sm" required>
              <option value="">— Select guest —</option>
              <?php foreach ($guests as $g): ?>
                <option value="<?= $g['id'] ?>"><?= htmlspecialchars($g['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success" id="btn-confirm-return">
            <i class="bi bi-check me-1"></i>Confirm Return
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Dispose Modal -->
<div class="modal fade" id="disposeModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content border-danger">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title"><i class="bi bi-trash me-2"></i>Dispose of Item</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" id="dispose-form">
        <div class="modal-body">
          <p class="text-muted small mb-3">Item: <strong id="dispose-ref"></strong></p>
          <div class="alert alert-danger py-2 small">
            <i class="bi bi-exclamation-octagon me-1"></i>
            This action is <strong>irreversible</strong>. Supervisor approval code is mandatory.
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Disposal Method</label>
            <div class="d-flex gap-3">
              <div class="form-check">
                <input class="form-check-input" type="radio" name="dispose_method" value="donate"
                       id="dm_donate" checked>
                <label class="form-check-label" for="dm_donate">Donate to Charity</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="dispose_method" value="discard"
                       id="dm_discard">
                <label class="form-check-label" for="dm_discard">Discard</label>
              </div>
            </div>
          </div>

          <div class="mb-3">
            <label for="disp_code" class="form-label fw-semibold">
              Supervisor Approval Code <span class="text-danger">*</span>
            </label>
            <input type="password" id="disp_code" name="supervisor_approval_code"
                   class="form-control" placeholder="Enter supervisor code" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger" id="btn-confirm-dispose">
            <i class="bi bi-trash me-1"></i>Confirm Disposal
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function openReturnModal(itemId, ref) {
  document.getElementById('return-ref').textContent = ref;
  document.getElementById('return-form').action =
    '<?= APP_URL ?>/?url=frontdesk/returnItem/' + itemId;
  new bootstrap.Modal(document.getElementById('returnModal')).show();
}

function openDisposeModal(itemId, ref) {
  document.getElementById('dispose-ref').textContent = ref;
  document.getElementById('dispose-form').action =
    '<?= APP_URL ?>/?url=frontdesk/disposeItem/' + itemId;
  document.getElementById('disp_code').value = '';
  new bootstrap.Modal(document.getElementById('disposeModal')).show();
}

function toggleAddress(method) {
  const courierFields = document.getElementById('courier-fields');
  const consentCheck  = document.getElementById('consent-check');
  courierFields.style.display = method === 'courier' ? 'block' : 'none';
  if (method !== 'courier') { consentCheck.style.display = 'none'; }
}

// Show consent checkbox only when shipping cost > 0
document.addEventListener('DOMContentLoaded', function() {
  const shippingInput = document.getElementById('ret_shipping_cost');
  if (shippingInput) {
    shippingInput.addEventListener('input', function() {
      const consentCheck = document.getElementById('consent-check');
      if (consentCheck) {
        consentCheck.style.display = parseFloat(this.value) > 0 ? 'block' : 'none';
      }
    });
  }
});
</script>

<?php $content = ob_get_clean(); require VIEW_PATH . '/layouts/main.php'; ?>
