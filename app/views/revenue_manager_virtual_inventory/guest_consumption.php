<?php
$pageTitle = 'Guest Consumption';
ob_start();
?>

<div class="container mt-4">
    <!-- Billing System: future guest folio reading can populate the consumption summary area. -->
    <!-- Housekeeping System: future amenity and servicing cost references can feed this profile. -->
    <!-- Reservation System: future stay pattern and room-category influence can appear in this profile. -->

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= APP_URL ?>/dashboard">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= APP_URL ?>/revenue_manager_virtual_inventory">Virtual Inventory</a></li>
            <li class="breadcrumb-item active" aria-current="page">Guest Consumption</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0"><i class="bi bi-person-vcard"></i> Guest Consumption Profile</h2>
        <a href="<?= APP_URL ?>/revenue_manager_virtual_inventory" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-person-circle"></i> Guest Profile</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr><th>Guest ID</th><td>501</td></tr>
                        <tr><th>Name</th><td>Guest Placeholder</td></tr>
                        <tr><th>Stay Type</th><td>Business</td></tr>
                        <tr><th>Room Category</th><td>Deluxe Suite</td></tr>
                        <tr><th>Consumption Tier</th><td><span class="badge bg-info text-dark">Tracked</span></td></tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-receipt"></i> Consumption Breakdown</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Category</th>
                                <th>Reference</th>
                                <th>Virtual Value</th>
                                <th>Revenue Weight</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Room Usage</td>
                                <td>Reservation influence</td>
                                <td>$420</td>
                                <td><span class="badge bg-success">Stable</span></td>
                            </tr>
                            <tr>
                                <td>Amenities</td>
                                <td>Housekeeping reference</td>
                                <td>$65</td>
                                <td><span class="badge bg-warning text-dark">Watch</span></td>
                            </tr>
                            <tr>
                                <td>Folio Exposure</td>
                                <td>Billing read-only</td>
                                <td>$180</td>
                                <td><span class="badge bg-info text-dark">Visible</span></td>
                            </tr>
                            <tr>
                                <td>Service Layers</td>
                                <td>Operational abstraction</td>
                                <td>$90</td>
                                <td><span class="badge bg-secondary">Placeholder</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require VIEW_PATH . '/layouts/main.php';
?>
