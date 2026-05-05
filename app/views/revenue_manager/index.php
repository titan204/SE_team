<?php
$pageTitle = 'Revenue Manager Workspace';
ob_start();
?>

<style>
  :root {
    --bg:      #FFF8F0;
    --accent:  #C08552;
    --accent2: #8C5A3C;
    --dark:    #4B2E2B;
  }
  body { background-color: var(--bg) !important; }
  .page-header { border-bottom: 2px solid var(--accent); padding-bottom: .75rem; margin-bottom: 1.5rem; }
  .page-header h2 { color: var(--dark); font-weight: 700; }
  .stat-card { background:#fff; border:1px solid #e8d5c0; border-radius:10px;
               padding:1.2rem 1.5rem; box-shadow:0 2px 8px rgba(192,133,82,.08); }
  .stat-card small { text-transform:uppercase; font-size:.72rem; color:#888; letter-spacing:.06em; }
  .stat-card h3 { color: var(--dark); font-weight:700; margin:0; }
  .action-card { background:#fff; border:1px solid #e8d5c0; border-radius:10px;
                 padding:1.4rem 1.5rem; box-shadow:0 2px 8px rgba(192,133,82,.08);
                 height:100%; display:flex; flex-direction:column; }
  .action-card h6 { color: var(--dark); font-weight:700; margin-bottom:.4rem; }
  .action-card p { color:#888; font-size:.84rem; flex:1; }
  .btn-accent { background-color:var(--accent); color:#fff; border:none; border-radius:8px; }
  .btn-accent:hover { background-color:var(--accent2); color:#fff; }
  .btn-dark-accent { background-color:var(--dark); color:#fff; border:none; border-radius:8px; }
  .btn-dark-accent:hover { background-color:var(--accent2); color:#fff; }
  .btn-outline-accent { border:1.5px solid var(--accent); color:var(--accent); background:transparent; border-radius:8px; }
  .btn-outline-accent:hover { background:var(--accent); color:#fff; }
  .section-title { color:var(--dark); font-weight:700; font-size:1rem;
                   border-left:3px solid var(--accent); padding-left:.6rem; margin-bottom:1rem; }
</style>

<div class="container-fluid py-3">

  <!-- Page Header -->
  <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h2><i class="bi bi-graph-up-arrow me-2"></i>Revenue Manager Workspace</h2>
  </div>

  <!-- Stats Row -->
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
      <div class="stat-card">
        <small>Billing Summary</small>
        <h3 class="mt-2">$124,500</h3>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-card">
        <small>Open Folios</small>
        <h3 class="mt-2">18</h3>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-card">
        <small>Group Reservations</small>
        <h3 class="mt-2">5</h3>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-card">
        <small>Overbooking Alerts</small>
        <h3 class="mt-2" style="color:#9e3030;">0</h3>
      </div>
    </div>
  </div>

  <!-- Virtual Inventory Section -->
  <div class="section-title"><i class="bi bi-grid-3x3 me-1"></i>Virtual Inventory</div>
  <div class="row g-3 mb-4">

    <div class="col-md-4">
      <div class="action-card">
        <h6><i class="bi bi-grid-3x3 me-1"></i>30-Day Grid</h6>
        <p>Color-coded room availability matrix. Click any cell to adjust the virtual allocation or trigger overbooking checks.</p>
        <a href="<?= APP_URL ?>/?url=revenue_manager_virtual_inventory/inventoryGrid"
           class="btn btn-accent w-100" id="btn-inventory-grid">
          Open Grid
        </a>
      </div>
    </div>

    <div class="col-md-4">
      <div class="action-card">
        <h6><i class="bi bi-speedometer2 me-1"></i>Inventory Dashboard</h6>
        <p>Overview of all virtual inventory channels and room type allocations by date range.</p>
        <a href="<?= APP_URL ?>/?url=revenue_manager_virtual_inventory/index"
           class="btn btn-outline-accent w-100" id="btn-inv-dashboard">
          Open Dashboard
        </a>
      </div>
    </div>

    <div class="col-md-4">
      <div class="action-card">
        <h6><i class="bi bi-arrow-repeat me-1"></i>Sync Status</h6>
        <p>Monitor the last sync timestamp per room type. Stale rows are flagged automatically.</p>
        <a href="<?= APP_URL ?>/?url=revenue_manager_virtual_inventory/syncStatus"
           class="btn btn-outline-accent w-100" id="btn-sync-status">
          Check Sync
        </a>
      </div>
    </div>

  </div>

  <!-- Billing Section -->
  <div class="section-title"><i class="bi bi-receipt me-1"></i>Billing</div>
  <div class="row g-3 mb-4">

    <div class="col-md-4">
      <div class="action-card">
        <h6><i class="bi bi-people me-1"></i>Group Billing</h6>
        <p>View and manage group reservations, per-member breakdowns, and consolidated invoices with group discounts.</p>
        <a href="<?= APP_URL ?>/?url=billing/group/<?= $firstGroupId ?>"
           class="btn btn-accent w-100" id="btn-group-billing">
          Open Group Billing
        </a>
      </div>
    </div>

    <div class="col-md-4">
      <div class="action-card">
        <h6><i class="bi bi-scissors me-1"></i>Split Billing</h6>
        <p>Split group invoices per member with proportional tax calculation. Supports partial splits and dispute handling.</p>
        <a href="<?= APP_URL ?>/?url=billing/splitBill/<?= $firstGroupId ?>"
           class="btn btn-outline-accent w-100" id="btn-split-billing">
          Open Split Preview
        </a>
      </div>
    </div>

    <div class="col-md-4">
      <div class="action-card">
        <h6><i class="bi bi-receipt me-1"></i>Guest Bill</h6>
        <p>Full individual billing lifecycle: add charges, apply discounts, redeem loyalty points, and finalize the bill.</p>
        <a href="<?= APP_URL ?>/?url=billing/guestBill/<?= $firstResId ?>"
           class="btn btn-outline-accent w-100" id="btn-guest-bill">
          Open Guest Bill
        </a>
      </div>
    </div>

  </div>

</div>

<?php
$content = ob_get_clean();
require VIEW_PATH . '/layouts/main.php';
?>
