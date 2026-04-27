<?php $pageTitle = 'All Guests'; 
ob_start(); 
?>
<style>
body {
    background: #FBF9D1;
}

.table-container {
    background: white;
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
}

.btn-primary {
    background: #9A3F3F;
    border: none;
}

.btn-primary:hover {
    background: #7a2f2f;
}

.badge-vip {
    background: #C1856D;
    color: white;
}

.badge-blacklist {
    background: #9A3F3F;
    color: white;
}

.row-vip {
    background: rgba(193, 133, 109, 0.15);
}

.row-blacklist {
    background: rgba(154, 63, 63, 0.15);
}

.search-box {
    border-radius: 10px;
    padding: 8px;
}

.container {
    animation: fadeIn 0.5s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to   { opacity: 1; }
}

mark {
    background: #ffe08a;
}
</style>

<div class="container mt-4">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>All Guests</h3>
        <a href="index.php?url=guests/create" class="btn btn-primary">
            + Add New Guest
        </a>
    </div>

    <!-- FILTERS -->
    <div class="d-flex gap-2 mb-3">
        <a href="index.php?url=guests" class="btn btn-outline-secondary btn-sm">All</a>
        <a href="index.php?url=guests&filter=vip" class="btn btn-outline-warning btn-sm">VIP</a>
        <a href="index.php?url=guests&filter=blacklist" class="btn btn-outline-danger btn-sm">Blacklisted</a>
    </div>

    <!-- SEARCH (NO AJAX TO AVOID 404 ISSUE) -->
    <form method="GET" action="index.php" class="mb-3">
        <input type="hidden" name="url" value="guests">
        <div class="input-group">
            <input type="text"
                   name="search"
                   class="form-control search-box"
                   placeholder="Search by name or email..."
                   value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            <button type="submit" class="btn btn-outline-secondary">Search</button>
        </div>
    </form>

    <!-- TABLE -->
    <div class="table-container">
        <table class="table table-hover align-middle">
            <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Loyalty</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            </thead>

            <tbody>
            <?php if (empty($guests)): ?>
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">No guests found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($guests as $guest): ?>
                    <tr id="row-<?= $guest['id'] ?>" class="<?= trim((!empty($guest['is_vip']) ? 'row-vip ' : '') . (!empty($guest['is_blacklisted']) ? 'row-blacklist' : '')) ?>">

                        <td><?= htmlspecialchars($guest['name']) ?></td>
                        <td><?= htmlspecialchars($guest['email']) ?></td>
                        <td><?= htmlspecialchars($guest['phone'] ?? '-') ?></td>

                        <td>
                            <span class="badge bg-secondary">
                                <?= ucfirst($guest['loyalty_tier'] ?? 'standard') ?>
                            </span>
                        </td>

                        <td>
                            <?php if (!empty($guest['is_vip'])): ?>
                                <span class="badge badge-vip">⭐ VIP</span>
                            <?php endif; ?>

                            <?php if (!empty($guest['is_blacklisted'])): ?>
                                <span class="badge badge-blacklist">🚫 Blacklisted</span>
                            <?php endif; ?>

                            <?php if (empty($guest['is_vip']) && empty($guest['is_blacklisted'])): ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>

                        <td class="d-flex gap-1">
                            <a href="index.php?url=guests/show/<?= $guest['id'] ?>" class="btn btn-sm btn-info">View</a>
                            <a href="index.php?url=guests/edit/<?= $guest['id'] ?>" class="btn btn-sm btn-warning">Edit</a>

                            <form method="POST"
                                  action="index.php?url=guests/delete/<?= $guest['id'] ?>"
                                   onsubmit="return confirm('Delete this guest?')"
                                  style="display:inline">
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>

                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- PAGINATION (SAFE SEARCH PRESERVATION) -->
     <div class="d-flex justify-content-between align-items-center mt-3">

        <div>
            <?php if (($page ?? 1) > 1): ?>
                <a href="index.php?url=guests&page=<?= $page - 1 ?>&search=<?= urlencode($_GET['search'] ?? '') ?>"
                   class="btn btn-outline-secondary">
                    ← Prev
                </a>
            <?php endif; ?>
        </div>

        <div class="text-muted">
            Page <?= $page ?? 1 ?>
        </div>

        <div>
            <?php if (count($guests) === ($perPage ?? 10)): ?>
                <a href="index.php?url=guests&page=<?= ($page ?? 1) + 1 ?>&search=<?= urlencode($_GET['search'] ?? '') ?>"
                   class="btn btn-outline-secondary">
                    Next →
                </a>
            <?php endif; ?>
        </div>

    </div>

</div>
<?php 
$content = ob_get_clean();
require VIEW_PATH . '/layouts/main.php'; ?>
