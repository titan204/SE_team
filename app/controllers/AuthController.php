<?php
// ============================================================
//  AuthController — Login / Logout / Session Management
//  Routes:
//    /auth/login   → login form
//    /auth/doLogin → process login
//    /auth/logout  → destroy session
// ============================================================

class AuthController extends Controller
{
    public function login()
    {
        if (!empty($_SESSION['user_id'])) {
            $this->redirect($this->getRedirectPath([
                'role_id'   => $_SESSION['user_role_id'] ?? null,
                'role_name' => $_SESSION['user_role'] ?? null,
            ]));
        }

        $data = [
            'message' => $_SESSION['error'] ?? null,
            'errors'  => $_SESSION['errors'] ?? [],
            'old'     => $_SESSION['old'] ?? [],
        ];

        unset($_SESSION['error'], $_SESSION['errors'], $_SESSION['old']);

        $this->view('auth/login', $data);
    }

    public function register()
    {
        if (!empty($_SESSION['user_id'])) {
            $this->redirect($this->getRedirectPath([
                'role_id'   => $_SESSION['user_role_id'] ?? null,
                'role_name' => $_SESSION['user_role'] ?? null,
            ]));
        }

        $data = [
            'message' => $_SESSION['error'] ?? null,
            'errors'  => $_SESSION['errors'] ?? [],
            'old'     => $_SESSION['old'] ?? [],
            'roomTypes' => (new RoomType())->all(),
            'floorOptions' => $this->getFloorOptions(),
        ];

        unset($_SESSION['error'], $_SESSION['errors'], $_SESSION['old']);

        $this->view('auth/register', $data);
    }

    public function doLogin()
    {


        $userModel = new User();

        // 1. Get data from form
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $errors = [];

        if ($email === '') {
            $errors['email'] = 'Email address is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
        }

        if ($password === '') {
            $errors['password'] = 'Password is required.';
        }

        $_SESSION['old'] = ['email' => $email];

        if (!empty($errors)) {
            $_SESSION['error'] = 'Please correct the highlighted fields.';
            $_SESSION['errors'] = $errors;
            $this->redirect('auth/login');
        }

        // 3. Try to authenticate user
        $user = $userModel->authenticate($email, $password);

        // 4. If login failed
        if (!$user) {
            $_SESSION['error'] = 'Invalid email or password.';
            $_SESSION['errors'] = [];
            $this->redirect('auth/login');
        }


        $_SESSION['user_id']      = $user['id'];
        $_SESSION['user_name']    = $user['name'];
        $_SESSION['user_email']   = $user['email'];
        $_SESSION['user_role_id'] = $user['role_id'];
        $_SESSION['user_role']    = $user['role_name'] ?? null;

        unset($_SESSION['error'], $_SESSION['errors'], $_SESSION['old']);

        $this->redirect($this->getRedirectPath($user));
    }

    public function doRegister()
    {
        if (!empty($_SESSION['user_id'])) {
            $this->redirect($this->getRedirectPath([
                'role_id'   => $_SESSION['user_role_id'] ?? null,
                'role_name' => $_SESSION['user_role'] ?? null,
            ]));
        }

        $userModel = new User();
        $guestModel = new Guest();
        $guestPreferenceModel = new GuestPreference();
        $roleModel = new Role();
        $roomTypeModel = new RoomType();

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $roomTypeId = trim($_POST['room_type_id'] ?? '');
        $smokingPreference = trim($_POST['smoking_preference'] ?? '');
        $floorPreference = trim($_POST['floor_preference'] ?? '');
        $specialRequests = trim($_POST['special_requests'] ?? '');
        $quietRoom = isset($_POST['quiet_room']) ? '1' : '';
        $nearElevator = isset($_POST['near_elevator']) ? '1' : '';
        $floorLevelPreference = trim($_POST['floor_level_preference'] ?? '');
        $viewPreference = trim($_POST['view_preference'] ?? '');
        $extraPillow = isset($_POST['extra_pillow']) ? '1' : '';
        $extraBlanket = isset($_POST['extra_blanket']) ? '1' : '';
        $babyCrib = isset($_POST['baby_crib']) ? '1' : '';
        $accessibleRoom = isset($_POST['accessible_room']) ? '1' : '';
        $connectingRoom = isset($_POST['connecting_room']) ? '1' : '';
        $earlyCheckInRequest = isset($_POST['early_check_in_request']) ? '1' : '';
        $lateCheckInRequest = isset($_POST['late_check_in_request']) ? '1' : '';
        $allergyFreeRoom = isset($_POST['allergy_free_room']) ? '1' : '';
        $nonSmokingGuarantee = isset($_POST['non_smoking_guarantee']) ? '1' : '';
        $workDeskNeeded = isset($_POST['work_desk_needed']) ? '1' : '';
        $balconyPreferred = isset($_POST['balcony_preferred']) ? '1' : '';
        $specialNotes = trim($_POST['special_notes'] ?? '');
        $errors = [];
        $allowedSmokingPreferences = ['smoking', 'non_smoking'];
        $allowedFloorLevelPreferences = ['high_floor', 'low_floor'];
        $allowedViewPreferences = ['sea_view', 'city_view', 'garden_view'];
        $floorOptions = $this->getFloorOptions();
        $selectedRoomType = null;

        if ($name === '') {
            $errors['name'] = 'Full name is required.';
        }

        if ($email === '') {
            $errors['email'] = 'Email address is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
        }

        if ($phone === '') {
            $errors['phone'] = 'Phone number is required.';
        }

        if ($password === '') {
            $errors['password'] = 'Password is required.';
        } elseif (strlen($password) < 6) {
            $errors['password'] = 'Password must be at least 6 characters.';
        }

        if ($confirmPassword === '') {
            $errors['confirm_password'] = 'Please confirm your password.';
        } elseif ($password !== '' && $password !== $confirmPassword) {
            $errors['confirm_password'] = 'Passwords do not match.';
        }

        if ($roomTypeId !== '') {
            if (!ctype_digit($roomTypeId)) {
                $errors['room_type_id'] = 'Please choose a valid room type.';
            } else {
                $selectedRoomType = $roomTypeModel->find((int) $roomTypeId);

                if (!$selectedRoomType) {
                    $errors['room_type_id'] = 'Please choose a valid room type.';
                }
            }
        }

        if ($smokingPreference !== '' && !in_array($smokingPreference, $allowedSmokingPreferences, true)) {
            $errors['smoking_preference'] = 'Please choose a valid smoking preference.';
        }

        if ($floorPreference !== '') {
            if (!ctype_digit($floorPreference) || !in_array((int) $floorPreference, $floorOptions, true)) {
                $errors['floor_preference'] = 'Please choose a valid floor preference.';
            }
        }

        if ($floorLevelPreference !== '' && !in_array($floorLevelPreference, $allowedFloorLevelPreferences, true)) {
            $errors['floor_level_preference'] = 'Please choose a valid floor level preference.';
        }

        if ($viewPreference !== '' && !in_array($viewPreference, $allowedViewPreferences, true)) {
            $errors['view_preference'] = 'Please choose a valid view preference.';
        }

        if ($specialRequests !== '' && strlen($specialRequests) > 255) {
            $errors['special_requests'] = 'Special requests must be 255 characters or less.';
        }

        if ($specialNotes !== '' && strlen($specialNotes) > 255) {
            $errors['special_notes'] = 'Special notes must be 255 characters or less.';
        }

        $preferenceOldInput = [
            'room_type_id' => $roomTypeId,
            'smoking_preference' => $smokingPreference,
            'floor_preference' => $floorPreference,
            'special_requests' => $specialRequests,
            'quiet_room' => $quietRoom,
            'near_elevator' => $nearElevator,
            'floor_level_preference' => $floorLevelPreference,
            'view_preference' => $viewPreference,
            'extra_pillow' => $extraPillow,
            'extra_blanket' => $extraBlanket,
            'baby_crib' => $babyCrib,
            'accessible_room' => $accessibleRoom,
            'connecting_room' => $connectingRoom,
            'early_check_in_request' => $earlyCheckInRequest,
            'late_check_in_request' => $lateCheckInRequest,
            'allergy_free_room' => $allergyFreeRoom,
            'non_smoking_guarantee' => $nonSmokingGuarantee,
            'work_desk_needed' => $workDeskNeeded,
            'balcony_preferred' => $balconyPreferred,
            'special_notes' => $specialNotes,
        ];

        $guestRole = $roleModel->findByName('guest');
        if (!$guestRole) {
            $_SESSION['error'] = 'Guest registration is unavailable right now.';
            $_SESSION['errors'] = [];
            $_SESSION['old'] = array_merge([
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
            ], $preferenceOldInput);
            $this->redirect('auth/register');
        }

        if (!isset($errors['email'])) {
            if ($userModel->findByEmail($email) || $guestModel->findByEmail($email)) {
                $errors['email'] = 'This email is already registered.';
            }
        }

        $_SESSION['old'] = array_merge([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
        ], $preferenceOldInput);

        if (!empty($errors)) {
            $_SESSION['error'] = 'Please correct the highlighted fields.';
            $_SESSION['errors'] = $errors;
            $this->redirect('auth/register');
        }

        $guestId = $guestModel->create([
            'name'  => $name,
            'email' => $email,
            'phone' => $phone,
        ]);

        $userId = $userModel->create([
            'name'     => $name,
            'email'    => $email,
            'password' => $userModel->hashPassword($password),
            'role_id'  => $guestRole['id'],
        ]);

        if ((int) $userId <= 0) {
            $guestModel->delete($guestId);
            $_SESSION['error'] = 'Unable to complete registration right now.';
            $_SESSION['errors'] = [];
            $this->redirect('auth/register');
        }

        if ($selectedRoomType) {
            $guestPreferenceModel->create([
                'guest_id' => $guestId,
                'pref_key' => 'room_type',
                'pref_value' => $selectedRoomType['name'],
            ]);
        }

        if ($smokingPreference !== '') {
            $guestPreferenceModel->create([
                'guest_id' => $guestId,
                'pref_key' => 'smoking_preference',
                'pref_value' => $smokingPreference === 'non_smoking' ? 'Non-Smoking' : 'Smoking',
            ]);
        }

        if ($floorPreference !== '') {
            $guestPreferenceModel->create([
                'guest_id' => $guestId,
                'pref_key' => 'floor_preference',
                'pref_value' => $floorPreference,
            ]);
        }

        if ($specialRequests !== '') {
            $guestPreferenceModel->create([
                'guest_id' => $guestId,
                'pref_key' => 'special_requests',
                'pref_value' => $specialRequests,
            ]);
        }

        if ($quietRoom === '1') {
            $guestPreferenceModel->create([
                'guest_id' => $guestId,
                'pref_key' => 'quiet_room',
                'pref_value' => 'Requested',
            ]);
        }

        if ($nearElevator === '1') {
            $guestPreferenceModel->create([
                'guest_id' => $guestId,
                'pref_key' => 'near_elevator',
                'pref_value' => 'Requested',
            ]);
        }

        if ($floorLevelPreference !== '') {
            $guestPreferenceModel->create([
                'guest_id' => $guestId,
                'pref_key' => 'floor_level_preference',
                'pref_value' => $floorLevelPreference === 'high_floor' ? 'High Floor' : 'Low Floor',
            ]);
        }

        if ($viewPreference !== '') {
            $viewPreferenceLabel = 'Garden View';

            if ($viewPreference === 'sea_view') {
                $viewPreferenceLabel = 'Sea View';
            } elseif ($viewPreference === 'city_view') {
                $viewPreferenceLabel = 'City View';
            }

            $guestPreferenceModel->create([
                'guest_id' => $guestId,
                'pref_key' => 'view_preference',
                'pref_value' => $viewPreferenceLabel,
            ]);
        }

        if ($extraPillow === '1') {
            $guestPreferenceModel->create([
                'guest_id' => $guestId,
                'pref_key' => 'extra_pillow',
                'pref_value' => 'Requested',
            ]);
        }

        if ($extraBlanket === '1') {
            $guestPreferenceModel->create([
                'guest_id' => $guestId,
                'pref_key' => 'extra_blanket',
                'pref_value' => 'Requested',
            ]);
        }

        if ($babyCrib === '1') {
            $guestPreferenceModel->create([
                'guest_id' => $guestId,
                'pref_key' => 'baby_crib',
                'pref_value' => 'Requested',
            ]);
        }

        if ($accessibleRoom === '1') {
            $guestPreferenceModel->create([
                'guest_id' => $guestId,
                'pref_key' => 'accessible_room',
                'pref_value' => 'Requested',
            ]);
        }

        if ($connectingRoom === '1') {
            $guestPreferenceModel->create([
                'guest_id' => $guestId,
                'pref_key' => 'connecting_room',
                'pref_value' => 'Requested',
            ]);
        }

        if ($earlyCheckInRequest === '1') {
            $guestPreferenceModel->create([
                'guest_id' => $guestId,
                'pref_key' => 'early_check_in_request',
                'pref_value' => 'Requested',
            ]);
        }

        if ($lateCheckInRequest === '1') {
            $guestPreferenceModel->create([
                'guest_id' => $guestId,
                'pref_key' => 'late_check_in_request',
                'pref_value' => 'Requested',
            ]);
        }

        if ($allergyFreeRoom === '1') {
            $guestPreferenceModel->create([
                'guest_id' => $guestId,
                'pref_key' => 'allergy_free_room',
                'pref_value' => 'Requested',
            ]);
        }

        if ($nonSmokingGuarantee === '1') {
            $guestPreferenceModel->create([
                'guest_id' => $guestId,
                'pref_key' => 'non_smoking_guarantee',
                'pref_value' => 'Requested',
            ]);
        }

        if ($workDeskNeeded === '1') {
            $guestPreferenceModel->create([
                'guest_id' => $guestId,
                'pref_key' => 'work_desk_needed',
                'pref_value' => 'Requested',
            ]);
        }

        if ($balconyPreferred === '1') {
            $guestPreferenceModel->create([
                'guest_id' => $guestId,
                'pref_key' => 'balcony_preferred',
                'pref_value' => 'Requested',
            ]);
        }

        if ($specialNotes !== '') {
            $guestPreferenceModel->create([
                'guest_id' => $guestId,
                'pref_key' => 'special_notes',
                'pref_value' => $specialNotes,
            ]);
        }

        unset($_SESSION['error'], $_SESSION['errors'], $_SESSION['old']);

        $this->redirect('auth/login');
    }

    public function logout()
    {
        $_SESSION = [];

        session_destroy();
        $this->redirect('auth/login');
    }

    private function getRedirectPath($user)
    {
        $roleId = (int) ($user['role_id'] ?? 0);
        $roleName = strtolower((string) ($user['role_name'] ?? ''));

        if ($roleName === 'manager' || $roleId === 1) {
            return 'Dashboard/index';
        }

        if ($roleName === 'front_desk' || $roleName === 'frontdesk' || $roleId === 2) {
            return 'Frontdesk/index';
        }

        if ($roleName === 'housekeeper' || $roleId === 3) {
            return 'Housekeeping/index';
        }

        if ($roleName === 'guest' || $roleId === 4) {
            return 'home/index';
        }

        return '';
    }

    private function getFloorOptions()
    {
        $rooms = (new Room())->all();
        $floors = array_map('intval', array_column($rooms, 'floor'));
        $floors = array_values(array_unique($floors));
        sort($floors);

        return $floors;
    }
}
