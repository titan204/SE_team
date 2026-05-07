<?php
class GuestProfileController extends Controller
{
    // ── Helpers ───────────────────────────────────────────────

    /**
     * Resolves the logged-in user's guest record.
     * We look up by email so the users ↔ guests link is email-based,
     * matching the existing registration flow in AuthController.
     *
     * @return array|null
     */
    private function resolveGuest(): ?array
    {
        $email = trim((string) ($_SESSION['user_email'] ?? ''));
        if ($email === '') {
            return null;
        }
        $guestModel = new Guest();
        return $guestModel->findByEmail($email) ?: null;
    }

    /**
     * Emits a JSON response and terminates.
     *
     * @param  int   $status   HTTP status code
     * @param  array $payload
     */
    private function json(int $status, array $payload): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    // ── GET /guestprofile ─────────────────────────────────────

    
    public function index(): void
    {
        $this->requireRole('guest');

        $guest = $this->resolveGuest();
        if (!$guest) {
            $_SESSION['profile_error'] = 'Guest profile not found. Please contact reception.';
            $this->redirect('home/index');
        }

        $guestModel = new Guest();
        $profile    = $guestModel->getProfileWithRelations((int) $guest['id']);

        if (!$profile) {
            $_SESSION['profile_error'] = 'Unable to load your profile right now.';
            $this->redirect('home/index');
        }

        // ── Room types for the preferences dropdown ───────────
        try {
            $roomTypeModel = new RoomType();
            $roomTypes     = $roomTypeModel->all();
            if (!is_array($roomTypes)) $roomTypes = [];
        } catch (\Throwable $e) {
            $roomTypes = [];
        }

        $this->view('home/guestprofile', [
            'pageTitle'    => 'My Profile',
            'guest'        => $profile['guest'],
            'reservations' => $profile['reservations'],
            'preferences'  => $profile['preferences'],
            'roomTypes'    => $roomTypes,
            'errors'       => $_SESSION['profile_errors'] ?? [],
            'old'          => $_SESSION['profile_old']    ?? [],
            'success'      => $_SESSION['profile_success'] ?? null,
        ]);

        unset($_SESSION['profile_errors'], $_SESSION['profile_old'], $_SESSION['profile_success']);
    }

    // ── POST /updateProfile ───────────────────────────────────

   
    public function updateProfile(): void
    {
        $this->requireRole('guest');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('guestProfile/index');
        }

        $guest = $this->resolveGuest();
        if (!$guest) {
            $this->jsonOrRedirect(
                403,
                ['ok' => false, 'errors' => ['auth' => 'Session expired. Please log in again.']],
                'guestProfile/index'
            );
        }

        $guestModel = new Guest();
        $result = $guestModel->updateProfile((int) $guest['id'], [
            'name'          => $_POST['name']          ?? '',
            'email'         => $_POST['email']         ?? '',
            'phone'         => $_POST['phone']         ?? '',
            'nationality'   => $_POST['nationality']   ?? '',
            'date_of_birth' => $_POST['date_of_birth'] ?? '',
        ]);

        if ($this->isAjax()) {
            $this->json($result['ok'] ? 200 : 422, $result);
        }

        if ($result['ok']) {
            // Keep session name in sync if name changed
            $_SESSION['user_name']  = trim($_POST['name'] ?? $_SESSION['user_name']);
            $_SESSION['user_email'] = trim($_POST['email'] ?? $_SESSION['user_email']);
            $_SESSION['profile_success'] = 'Your profile has been updated successfully.';
        } else {
            $_SESSION['profile_errors'] = $result['errors'];
            $_SESSION['profile_old']    = $_POST;
        }

        $this->redirect('guestProfile/index');
    }

    // ── POST /cancelReservation ───────────────────────────────

   
    public function cancelReservation(): void
    {
        $this->requireRole('guest');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('guestProfile/index');
        }

        $guest = $this->resolveGuest();
        if (!$guest) {
            $this->json(403, ['ok' => false, 'message' => 'Unauthorized.']);
        }

        $reservationId = (int) ($_POST['reservation_id'] ?? 0);
        if ($reservationId <= 0) {
            $this->jsonOrRedirect(
                422,
                ['ok' => false, 'message' => 'Invalid reservation.'],
                'guestProfile/index'
            );
        }

        $guestModel = new Guest();
        $ok = $guestModel->cancelReservation($reservationId, (int) $guest['id']);

        if ($this->isAjax()) {
            $this->json(
                $ok ? 200 : 422,
                [
                    'ok'      => $ok,
                    'message' => $ok
                        ? 'Reservation cancelled successfully.'
                        : 'Unable to cancel — reservation not found or not in "confirmed" status.',
                ]
            );
        }

        if ($ok) {
            $_SESSION['profile_success'] = 'Reservation #' . $reservationId . ' has been cancelled.';
        } else {
            $_SESSION['profile_errors'] = ['reservation' => 'Unable to cancel that reservation.'];
        }
        $this->redirect('guestProfile/index');
    }

    // ── POST /addPreference ───────────────────────────────────

    
    public function addPreference(): void
    {
        $this->requireRole('guest');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('guestProfile/index');
        }

        $guest = $this->resolveGuest();
        if (!$guest) {
            $this->json(403, ['ok' => false, 'message' => 'Unauthorized.']);
        }

        $key   = trim($_POST['pref_key']   ?? '');
        $value = trim($_POST['pref_value'] ?? '');

        if ($key === '' || $value === '') {
            $this->jsonOrRedirect(
                422,
                ['ok' => false, 'message' => 'Preference type and value are required.'],
                'guestProfile/index'
            );
        }

        $guestModel = new Guest();
        $newId = $guestModel->addPreference((int) $guest['id'], [
            'pref_key'   => $key,
            'pref_value' => $value,
        ]);

        if ($this->isAjax()) {
            $this->json(
                $newId ? 201 : 500,
                [
                    'ok'      => (bool) $newId,
                    'id'      => $newId,
                    'message' => $newId ? 'Preference added.' : 'Could not save preference.',
                ]
            );
        }

        if ($newId) {
            $_SESSION['profile_success'] = 'Preference added successfully.';
        } else {
            $_SESSION['profile_errors'] = ['preference' => 'Could not save preference.'];
        }
        $this->redirect('guestProfile/index');
    }

    // ── POST /updatePreference ────────────────────────────────

    /**
     * Updates an existing preference.
     * Route: ?url=guestProfile/updatePreference
     */
    public function updatePreference(): void
    {
        $this->requireRole('guest');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('guestProfile/index');
        }

        $guest = $this->resolveGuest();
        if (!$guest) {
            $this->json(403, ['ok' => false, 'message' => 'Unauthorized.']);
        }

        $prefId = (int) ($_POST['pref_id'] ?? 0);
        $key    = trim($_POST['pref_key']   ?? '');
        $value  = trim($_POST['pref_value'] ?? '');

        if ($prefId <= 0 || $key === '' || $value === '') {
            $this->jsonOrRedirect(
                422,
                ['ok' => false, 'message' => 'Preference ID, type, and value are required.'],
                'guestProfile/index'
            );
        }

        $guestModel = new Guest();
        $ok = $guestModel->updatePreference($prefId, (int) $guest['id'], [
            'pref_key'   => $key,
            'pref_value' => $value,
        ]);

        if ($this->isAjax()) {
            $this->json(
                $ok ? 200 : 422,
                [
                    'ok'      => $ok,
                    'message' => $ok
                        ? 'Preference updated.'
                        : 'Preference not found or not yours.',
                ]
            );
        }

        if ($ok) {
            $_SESSION['profile_success'] = 'Preference updated.';
        } else {
            $_SESSION['profile_errors'] = ['preference' => 'Preference not found.'];
        }
        $this->redirect('guestProfile/index');
    }

    // ── POST /deletePreference ────────────────────────────────

    
    public function deletePreference(): void
    {
        $this->requireRole('guest');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('guestProfile/index');
        }

        $guest = $this->resolveGuest();
        if (!$guest) {
            $this->json(403, ['ok' => false, 'message' => 'Unauthorized.']);
        }

        $prefId = (int) ($_POST['pref_id'] ?? 0);
        if ($prefId <= 0) {
            $this->jsonOrRedirect(
                422,
                ['ok' => false, 'message' => 'Invalid preference ID.'],
                'guestProfile/index'
            );
        }

        $guestModel = new Guest();
        $ok = $guestModel->deletePreference($prefId, (int) $guest['id']);

        if ($this->isAjax()) {
            $this->json(
                $ok ? 200 : 422,
                [
                    'ok'      => $ok,
                    'message' => $ok ? 'Preference deleted.' : 'Preference not found or not yours.',
                ]
            );
        }

        if ($ok) {
            $_SESSION['profile_success'] = 'Preference deleted.';
        } else {
            $_SESSION['profile_errors'] = ['preference' => 'Preference not found.'];
        }
        $this->redirect('guestProfile/index');
    }

    // ── POST /savePreferences ─────────────────────────────────

    
    public function savePreferences(): void
    {
        $this->requireRole('guest');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('guestProfile/index');
        }

        $guest = $this->resolveGuest();
        if (!$guest) {
            $this->json(403, ['ok' => false, 'message' => 'Unauthorized.']);
        }

        // Whitelist of allowed preference keys
        $allowed = [
            'room_type', 'bed_type', 'smoking', 'floor_level',
            'view', 'dietary', 'special_requests',
            'quiet_room', 'near_elevator', 'extra_pillow', 'extra_blanket',
            'baby_crib', 'accessible_room', 'connecting_room',
            'allergy_free_room', 'non_smoking_guarantee',
            'work_desk_needed', 'balcony_preferred',
            'early_check_in_request', 'late_check_in_request',
        ];

        $prefs = [];
        foreach ($allowed as $key) {
            $prefs[$key] = trim($_POST[$key] ?? '');
        }

        $guestModel = new Guest();
        $guestModel->saveAllPreferences((int) $guest['id'], $prefs);

        $this->json(200, ['ok' => true, 'message' => 'Preferences saved successfully.']);
    }

    // ── Private utilities ─────────────────────────────────────

    /**
     * Returns true when the request was sent with the X-Requested-With header
     * (fetch / XMLHttpRequest) or when the client accepts JSON only.
     */
    private function isAjax(): bool
    {
        $xhr    = strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        return $xhr || (str_contains($accept, 'application/json') && !str_contains($accept, 'text/html'));
    }

    /**
     * Sends a JSON response for AJAX callers or redirects for plain form posts.
     *
     * @param int    $status
     * @param array  $payload
     * @param string $redirectPath
     */
    private function jsonOrRedirect(int $status, array $payload, string $redirectPath): void
    {
        if ($this->isAjax()) {
            $this->json($status, $payload);
        }
        $this->redirect($redirectPath);
    }
}
