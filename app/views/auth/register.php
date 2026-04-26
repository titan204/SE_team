<?php
$pageTitle = 'Guest Registration';
$errors = $errors ?? [];
$old = $old ?? [];
$message = $message ?? null;
$roomTypes = $roomTypes ?? [];
$floorOptions = $floorOptions ?? [];
$name = htmlspecialchars($old['name'] ?? '', ENT_QUOTES, 'UTF-8');
$email = htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8');
$phone = htmlspecialchars($old['phone'] ?? '', ENT_QUOTES, 'UTF-8');
$roomTypeId = htmlspecialchars($old['room_type_id'] ?? '', ENT_QUOTES, 'UTF-8');
$smokingPreference = htmlspecialchars($old['smoking_preference'] ?? '', ENT_QUOTES, 'UTF-8');
$floorPreference = htmlspecialchars($old['floor_preference'] ?? '', ENT_QUOTES, 'UTF-8');
$specialRequests = htmlspecialchars($old['special_requests'] ?? '', ENT_QUOTES, 'UTF-8');
$quietRoomChecked = ($old['quiet_room'] ?? '') === '1';
$nearElevatorChecked = ($old['near_elevator'] ?? '') === '1';
$floorLevelPreference = htmlspecialchars($old['floor_level_preference'] ?? '', ENT_QUOTES, 'UTF-8');
$viewPreference = htmlspecialchars($old['view_preference'] ?? '', ENT_QUOTES, 'UTF-8');
$extraPillowChecked = ($old['extra_pillow'] ?? '') === '1';
$extraBlanketChecked = ($old['extra_blanket'] ?? '') === '1';
$babyCribChecked = ($old['baby_crib'] ?? '') === '1';
$accessibleRoomChecked = ($old['accessible_room'] ?? '') === '1';
$connectingRoomChecked = ($old['connecting_room'] ?? '') === '1';
$earlyCheckInRequestChecked = ($old['early_check_in_request'] ?? '') === '1';
$lateCheckInRequestChecked = ($old['late_check_in_request'] ?? '') === '1';
$allergyFreeRoomChecked = ($old['allergy_free_room'] ?? '') === '1';
$nonSmokingGuaranteeChecked = ($old['non_smoking_guarantee'] ?? '') === '1';
$workDeskNeededChecked = ($old['work_desk_needed'] ?? '') === '1';
$balconyPreferredChecked = ($old['balcony_preferred'] ?? '') === '1';
$specialNotes = htmlspecialchars($old['special_notes'] ?? '', ENT_QUOTES, 'UTF-8');

ob_start();
?>

<div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
  <div class="card shadow-lg" style="width: 100%; max-width: 640px; border-radius: 15px;">
    <div class="card-body p-5">
      <h2 class="card-title text-center fw-bold mb-4">Guest Registration</h2>
      <p class="text-muted text-center mb-4">Create your guest account to continue</p>

      <?php if (!empty($message)): ?>
        <div class="alert alert-danger d-flex align-items-center" role="alert">
          <i class="bi bi-exclamation-triangle-fill me-2"></i>
          <div><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
        </div>
      <?php endif; ?>

      <form action="<?= APP_URL ?>/?url=auth/doRegister" method="POST">
        <div class="mb-3">
          <label for="name" class="form-label fw-semibold">Full Name</label>
          <input
            type="text"
            name="name"
            id="name"
            class="form-control <?= !empty($errors['name']) ? 'is-invalid' : '' ?>"
            placeholder="Enter your full name"
            value="<?= $name ?>"
            autocomplete="name"
            required
            autofocus>
          <?php if (!empty($errors['name'])): ?>
            <div class="invalid-feedback"><?= htmlspecialchars($errors['name'], ENT_QUOTES, 'UTF-8') ?></div>
          <?php endif; ?>
        </div>

        <div class="mb-3">
          <label for="email" class="form-label fw-semibold">Email Address</label>
          <input
            type="email"
            name="email"
            id="email"
            class="form-control <?= !empty($errors['email']) ? 'is-invalid' : '' ?>"
            placeholder="name@example.com"
            value="<?= $email ?>"
            autocomplete="email"
            required>
          <?php if (!empty($errors['email'])): ?>
            <div class="invalid-feedback"><?= htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8') ?></div>
          <?php endif; ?>
        </div>

        <div class="mb-3">
          <label for="phone" class="form-label fw-semibold">Phone</label>
          <input
            type="text"
            name="phone"
            id="phone"
            class="form-control <?= !empty($errors['phone']) ? 'is-invalid' : '' ?>"
            placeholder="Enter your phone number"
            value="<?= $phone ?>"
            autocomplete="tel"
            required>
          <?php if (!empty($errors['phone'])): ?>
            <div class="invalid-feedback"><?= htmlspecialchars($errors['phone'], ENT_QUOTES, 'UTF-8') ?></div>
          <?php endif; ?>
        </div>

        <div class="mb-3">
          <label for="password" class="form-label fw-semibold">Password</label>
          <input
            type="password"
            name="password"
            id="password"
            class="form-control <?= !empty($errors['password']) ? 'is-invalid' : '' ?>"
            placeholder="Create a password"
            autocomplete="new-password"
            required>
          <?php if (!empty($errors['password'])): ?>
            <div class="invalid-feedback"><?= htmlspecialchars($errors['password'], ENT_QUOTES, 'UTF-8') ?></div>
          <?php endif; ?>
        </div>

        <div class="mb-4">
          <label for="confirm_password" class="form-label fw-semibold">Confirm Password</label>
          <input
            type="password"
            name="confirm_password"
            id="confirm_password"
            class="form-control <?= !empty($errors['confirm_password']) ? 'is-invalid' : '' ?>"
            placeholder="Confirm your password"
            autocomplete="new-password"
            required>
          <?php if (!empty($errors['confirm_password'])): ?>
            <div class="invalid-feedback"><?= htmlspecialchars($errors['confirm_password'], ENT_QUOTES, 'UTF-8') ?></div>
          <?php endif; ?>
        </div>

        <hr class="my-4">

        <div class="mb-3">
          <h5 class="fw-bold mb-1">Room Preferences</h5>
          <p class="text-muted mb-0">Add your stay preferences so future bookings can match them.</p>
        </div>

        <div class="mb-3">
          <label for="room_type_id" class="form-label fw-semibold">Preferred Room Type</label>
          <select
            name="room_type_id"
            id="room_type_id"
            class="form-select <?= !empty($errors['room_type_id']) ? 'is-invalid' : '' ?>">
            <option value="">No preference</option>
            <?php foreach ($roomTypes as $roomType): ?>
              <option value="<?= (int) $roomType['id'] ?>" <?= $roomTypeId === (string) $roomType['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($roomType['name'], ENT_QUOTES, 'UTF-8') ?>
              </option>
            <?php endforeach; ?>
          </select>
          <?php if (!empty($errors['room_type_id'])): ?>
            <div class="invalid-feedback"><?= htmlspecialchars($errors['room_type_id'], ENT_QUOTES, 'UTF-8') ?></div>
          <?php endif; ?>
        </div>

        <div class="mb-3">
          <label for="smoking_preference" class="form-label fw-semibold">Smoking Preference</label>
          <select
            name="smoking_preference"
            id="smoking_preference"
            class="form-select <?= !empty($errors['smoking_preference']) ? 'is-invalid' : '' ?>">
            <option value="">No preference</option>
            <option value="non_smoking" <?= $smokingPreference === 'non_smoking' ? 'selected' : '' ?>>Non-Smoking</option>
            <option value="smoking" <?= $smokingPreference === 'smoking' ? 'selected' : '' ?>>Smoking</option>
          </select>
          <?php if (!empty($errors['smoking_preference'])): ?>
            <div class="invalid-feedback"><?= htmlspecialchars($errors['smoking_preference'], ENT_QUOTES, 'UTF-8') ?></div>
          <?php endif; ?>
        </div>

        <div class="mb-3">
          <label for="floor_preference" class="form-label fw-semibold">Floor Preference</label>
          <select
            name="floor_preference"
            id="floor_preference"
            class="form-select <?= !empty($errors['floor_preference']) ? 'is-invalid' : '' ?>">
            <option value="">No preference</option>
            <?php foreach ($floorOptions as $floor): ?>
              <option value="<?= (int) $floor ?>" <?= $floorPreference === (string) $floor ? 'selected' : '' ?>>
                Floor <?= (int) $floor ?>
              </option>
            <?php endforeach; ?>
          </select>
          <?php if (!empty($errors['floor_preference'])): ?>
            <div class="invalid-feedback"><?= htmlspecialchars($errors['floor_preference'], ENT_QUOTES, 'UTF-8') ?></div>
          <?php endif; ?>
        </div>

        <div class="mb-4">
          <label for="special_requests" class="form-label fw-semibold">Special Requests</label>
          <textarea
            name="special_requests"
            id="special_requests"
            class="form-control <?= !empty($errors['special_requests']) ? 'is-invalid' : '' ?>"
            rows="3"
            maxlength="255"
            placeholder="Any additional requests for your stay"><?= $specialRequests ?></textarea>
          <?php if (!empty($errors['special_requests'])): ?>
            <div class="invalid-feedback"><?= htmlspecialchars($errors['special_requests'], ENT_QUOTES, 'UTF-8') ?></div>
          <?php endif; ?>
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold d-block">Room Location Options</label>
          <div class="row">
            <div class="col-md-6">
              <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="quiet_room" id="quiet_room" value="1" <?= $quietRoomChecked ? 'checked' : '' ?>>
                <label class="form-check-label" for="quiet_room">Quiet Room</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="near_elevator" id="near_elevator" value="1" <?= $nearElevatorChecked ? 'checked' : '' ?>>
                <label class="form-check-label" for="near_elevator">Near Elevator</label>
              </div>
            </div>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold d-block">Floor Level Preference</label>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="floor_level_preference" id="no_floor_level_preference" value="" <?= $floorLevelPreference === '' ? 'checked' : '' ?>>
            <label class="form-check-label" for="no_floor_level_preference">No Preference</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="floor_level_preference" id="high_floor" value="high_floor" <?= $floorLevelPreference === 'high_floor' ? 'checked' : '' ?>>
            <label class="form-check-label" for="high_floor">High Floor</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="floor_level_preference" id="low_floor" value="low_floor" <?= $floorLevelPreference === 'low_floor' ? 'checked' : '' ?>>
            <label class="form-check-label" for="low_floor">Low Floor</label>
          </div>
          <?php if (!empty($errors['floor_level_preference'])): ?>
            <div class="text-danger small mt-1"><?= htmlspecialchars($errors['floor_level_preference'], ENT_QUOTES, 'UTF-8') ?></div>
          <?php endif; ?>
        </div>

        <div class="mb-3">
          <label for="view_preference" class="form-label fw-semibold">View Preference</label>
          <select
            name="view_preference"
            id="view_preference"
            class="form-select <?= !empty($errors['view_preference']) ? 'is-invalid' : '' ?>">
            <option value="">No preference</option>
            <option value="sea_view" <?= $viewPreference === 'sea_view' ? 'selected' : '' ?>>Sea View</option>
            <option value="city_view" <?= $viewPreference === 'city_view' ? 'selected' : '' ?>>City View</option>
            <option value="garden_view" <?= $viewPreference === 'garden_view' ? 'selected' : '' ?>>Garden View</option>
          </select>
          <?php if (!empty($errors['view_preference'])): ?>
            <div class="invalid-feedback"><?= htmlspecialchars($errors['view_preference'], ENT_QUOTES, 'UTF-8') ?></div>
          <?php endif; ?>
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold d-block">Extra Comfort</label>
          <div class="row">
            <div class="col-md-6">
              <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="extra_pillow" id="extra_pillow" value="1" <?= $extraPillowChecked ? 'checked' : '' ?>>
                <label class="form-check-label" for="extra_pillow">Extra Pillow</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="extra_blanket" id="extra_blanket" value="1" <?= $extraBlanketChecked ? 'checked' : '' ?>>
                <label class="form-check-label" for="extra_blanket">Extra Blanket</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="baby_crib" id="baby_crib" value="1" <?= $babyCribChecked ? 'checked' : '' ?>>
                <label class="form-check-label" for="baby_crib">Baby Crib</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="accessible_room" id="accessible_room" value="1" <?= $accessibleRoomChecked ? 'checked' : '' ?>>
                <label class="form-check-label" for="accessible_room">Accessible Room</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="connecting_room" id="connecting_room" value="1" <?= $connectingRoomChecked ? 'checked' : '' ?>>
                <label class="form-check-label" for="connecting_room">Connecting Room</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="allergy_free_room" id="allergy_free_room" value="1" <?= $allergyFreeRoomChecked ? 'checked' : '' ?>>
                <label class="form-check-label" for="allergy_free_room">Allergy-Free Room</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="work_desk_needed" id="work_desk_needed" value="1" <?= $workDeskNeededChecked ? 'checked' : '' ?>>
                <label class="form-check-label" for="work_desk_needed">Work Desk Needed</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="balcony_preferred" id="balcony_preferred" value="1" <?= $balconyPreferredChecked ? 'checked' : '' ?>>
                <label class="form-check-label" for="balcony_preferred">Balcony Preferred</label>
              </div>
            </div>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold d-block">Stay Requests</label>
          <div class="row">
            <div class="col-md-6">
              <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="early_check_in_request" id="early_check_in_request" value="1" <?= $earlyCheckInRequestChecked ? 'checked' : '' ?>>
                <label class="form-check-label" for="early_check_in_request">Early Check-in Request</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="late_check_in_request" id="late_check_in_request" value="1" <?= $lateCheckInRequestChecked ? 'checked' : '' ?>>
                <label class="form-check-label" for="late_check_in_request">Late Check-in Request</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="non_smoking_guarantee" id="non_smoking_guarantee" value="1" <?= $nonSmokingGuaranteeChecked ? 'checked' : '' ?>>
                <label class="form-check-label" for="non_smoking_guarantee">Non-Smoking Guarantee</label>
              </div>
            </div>
          </div>
        </div>

        <div class="mb-4">
          <label for="special_notes" class="form-label fw-semibold">Special Notes</label>
          <textarea
            name="special_notes"
            id="special_notes"
            class="form-control <?= !empty($errors['special_notes']) ? 'is-invalid' : '' ?>"
            rows="3"
            maxlength="255"
            placeholder="Add any extra notes about your room preferences"><?= $specialNotes ?></textarea>
          <?php if (!empty($errors['special_notes'])): ?>
            <div class="invalid-feedback"><?= htmlspecialchars($errors['special_notes'], ENT_QUOTES, 'UTF-8') ?></div>
          <?php endif; ?>
        </div>

        <div class="d-grid gap-2">
          <button type="submit" class="btn btn-primary btn-lg shadow-sm">Register</button>
        </div>
      </form>
    </div>
    <div class="card-footer text-center py-3 bg-light" style="border-bottom-left-radius: 15px; border-bottom-right-radius: 15px;">
      <small class="text-muted">Already have an account? <a href="<?= APP_URL ?>/?url=auth/login">Sign in</a></small>
    </div>
  </div>
</div>

<?php
$content = ob_get_clean();
require VIEW_PATH . '/layouts/main.php';
?>
