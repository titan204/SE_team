<?php $pageTitle = 'Edit Guest';
ob_start();  ?>
<style>
body {
    background: #FBF9D1;
}

.card {
    border: none;
    border-radius: 18px;
    background: #ffffff;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    transition: 0.3s;
}

.card:hover {
    transform: translateY(-5px);
}

.card-header {
    background: #9A3F3F;
    color: #FBF9D1;
    font-weight: bold;
    text-align: center;
}

.form-control,
.form-select {
    border-radius: 10px;
    border: 1px solid #C1856D;
    background: #E6CFA9;
    transition: 0.3s;
}

.form-control:focus,
.form-select:focus {
    border-color: #9A3F3F;
    box-shadow: 0 0 10px rgba(154, 63, 63, 0.3);
    background: #fff;
}

.form-label {
    font-weight: 600;
    color: #9A3F3F;
}

.btn-success {
    background: #C1856D;
    border: none;
    color: white;
    border-radius: 10px;
    transition: 0.3s;
}

.btn-success:hover {
    background: #9A3F3F;
    transform: scale(1.05);
}

.btn-secondary {
    border-radius: 10px;
}

.container {
    animation: fadeIn 0.6s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(15px); }
    to   { opacity: 1; transform: translateY(0); }
}

.alert-danger {
    background: #9A3F3F;
    color: #fff;
    border: none;
}
</style>

<div class="container mt-4">

    <div class="card shadow">

        <div class="card-header">
            <h3>Edit Guest</h3>
        </div>

        <div class="card-body">

            <!-- Error Messages -->
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            
            <form method="POST"
                  action="<?= APP_URL ?>/guests/update/<?= $guest['id'] ?>"
                  class="needs-validation" novalidate>

                <div class="row">

                    <!-- Name -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Name *</label>
                        <input type="text" name="name" class="form-control"
                               value="<?= htmlspecialchars($old['name'] ?? $guest['name']) ?>"
                               required>
                        <div class="invalid-feedback">Name is required</div>
                    </div>

                    <!-- Email -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-control"
                               value="<?= htmlspecialchars($old['email'] ?? $guest['email']) ?>"
                               required>
                        <div class="invalid-feedback">Valid email is required</div>
                    </div>

                    <!-- Phone -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control"
                               value="<?= htmlspecialchars($old['phone'] ?? $guest['phone'] ?? '') ?>">
                    </div>

                    <!-- National ID — read only  -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">National ID</label>
                        <input type="text" name="national_id" class="form-control"
                               value="<?= htmlspecialchars($guest['national_id'] ?? '') ?>"
                               readonly>
                    </div>

                    <!-- Nationality -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nationality</label>
                        <input type="text" name="nationality" class="form-control"
                               value="<?= htmlspecialchars($old['nationality'] ?? $guest['nationality'] ?? '') ?>">
                    </div>

                    <!-- Date of Birth -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="date_of_birth" class="form-control"
                               value="<?= htmlspecialchars($old['date_of_birth'] ?? $guest['date_of_birth'] ?? '') ?>">
                    </div>

                </div>

                <!-- Buttons -->
                <div class="mt-3">
                    <button type="submit" class="btn btn-success">Update</button>
                    <a href="index.php?url=guests/show/<?= $guest['id'] ?>"
                       class="btn btn-secondary">Cancel</a>
                </div>

            </form>

        </div>
    </div>

</div>

<!-- Bootstrap Validation Script -->
<script>
(() => {
    'use strict';
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
})();
</script>
<?php
$content = ob_get_clean();
 require VIEW_PATH . '/layouts/main.php'; ?>
