<?php $pageTitle = 'Add New Guest';
ob_start();  ?>
<!--
============================================================
  Used by: GuestsController@create
  Form submits to: GuestsController@store

  Fields:
  - name, email, phone, national_id, nationality, date_of_birth
  - password, confirm_password (bcrypt-hashed before DB insert)
  - Bootstrap validation + error handling

  Security:
  - "Referred By" field removed (PII reduction).
  - Password is hashed with PASSWORD_DEFAULT (bcrypt) via User::create().
  - Guest user account is created alongside the guests record so the
    new guest can log in immediately with the password entered here.
============================================================
-->

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
    to { opacity: 1; transform: translateY(0); }
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
            <h3>Add New Guest</h3>
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

            <form method="POST" action="index.php?url=guests/store" class="needs-validation" novalidate>

                <div class="row">

                    <!-- Name -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Name *</label>
                        <input type="text" name="name" class="form-control"
                               value="<?= htmlspecialchars($old['name'] ?? '') ?>" required>
                        <div class="invalid-feedback">Name is required</div>
                    </div>

                    <!-- Email -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-control"
                               value="<?= htmlspecialchars($old['email'] ?? '') ?>" required>
                        <div class="invalid-feedback">Valid email is required</div>
                    </div>

                    <!-- Phone -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control"
                               value="<?= htmlspecialchars($old['phone'] ?? '') ?>">
                    </div>

                    <!-- National ID -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">National ID</label>
                        <input type="text" name="national_id" class="form-control"
                               value="<?= htmlspecialchars($old['national_id'] ?? '') ?>">
                    </div>

                    <!-- Nationality -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nationality</label>
                        <input type="text" name="nationality" class="form-control"
                               value="<?= htmlspecialchars($old['nationality'] ?? '') ?>">
                    </div>

                    <!-- Date of Birth -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="date_of_birth" class="form-control"
                               value="<?= htmlspecialchars($old['date_of_birth'] ?? '') ?>">
                    </div>

                    <!-- Password -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" id="guestPassword"
                               class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                               placeholder="Min. 6 characters" required
                               autocomplete="new-password">
                        <?php if (isset($errors['password'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['password']) ?></div>
                        <?php else: ?>
                            <div class="form-text">Minimum 6 characters. Stored securely (bcrypt).</div>
                        <?php endif; ?>
                    </div>

                    <!-- Confirm Password -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                        <input type="password" name="confirm_password" id="guestConfirmPassword"
                               class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>"
                               placeholder="Re-enter password" required
                               autocomplete="new-password">
                        <?php if (isset($errors['confirm_password'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['confirm_password']) ?></div>
                        <?php else: ?>
                            <div class="form-text">Must match the password above.</div>
                        <?php endif; ?>
                    </div>

                </div>

                <!-- Buttons -->
                <div class="mt-3">
                    <button type="submit" class="btn btn-success">Save</button>
                    <a href="index.php?url=guests" class="btn btn-secondary">Cancel</a>
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