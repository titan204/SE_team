<?php
// ============================================================
//  ReservationsController — Booking & front-desk workflow
//
//  NOTE: Models are autoloaded dynamically in index.php via glob().
//  The @see tags below tell Intelephense where to find these classes
//  so it stops reporting false "Undefined method" warnings.
//
// @see \Reservation
// @see \Room
// @see \Guest
// @see \Folio
//  Routes:
//    /reservations              → index
//    /reservations/show/5       → show details
//    /reservations/create       → new booking form
//    /reservations/store        → save booking
//    /reservations/edit/5       → edit booking
//    /reservations/update/5     → save edits
//    /reservations/delete/5     → cancel/delete
//    /reservations/checkin/5    → check-in action
//    /reservations/checkout/5   → check-out action
//    /reservations/confirm/5    → confirm reservation
//    /reservations/noshow/5     → mark no-show
//    /reservations/earlycheckin/5 → early check-in
//    /reservations/acceptupgrade/5/3 → accept room upgrade (res 5 → room 3)
// ============================================================

class ReservationsController extends Controller
{
    // ── Index: list + filter ─────────────────────────────────

    public function index()
    {
        $this->requireLogin();

        $reservationModel = new Reservation();

        $params = [];
        if (!empty($_GET['status']))     $params['status']     = $_GET['status'];
        if (!empty($_GET['date_from']))  $params['date_from']  = $_GET['date_from'];
        if (!empty($_GET['date_to']))    $params['date_to']    = $_GET['date_to'];
        if (!empty($_GET['guest_name'])) $params['guest_name'] = $_GET['guest_name'];

        $reservations = $reservationModel->filter($params);

        $this->view('reservations/index', [
            'reservations' => $reservations,
            'filters'      => $params,
        ]);
    }

    // ── Show: reservation detail ─────────────────────────────

    public function show($id)
    {
        $this->requireLogin();

        $reservationModel = new Reservation();
        $reservation      = $reservationModel->find($id);

        if (!$reservation) {
            $this->redirect('reservations');
            return;
        }

        // Folio info
        $folio = $reservationModel->getFolioByReservation($id);

        // ── Upgrade suggestion ─────────────────────────────────────────────────────
        // Gate: ONLY at checked_in, ONLY for gold / platinum loyalty tier.
        // Status and loyalty_tier are normalised to lowercase+trim so any DB
        // capitalisation variation ('Checked_In', 'Gold', 'PLATINUM' …) still matches.
        $upgradeRoom       = null;
        $normalizedStatus  = strtolower(trim((string)($reservation['status']      ?? '')));
        $normalizedLoyalty = strtolower(trim((string)($reservation['loyalty_tier'] ?? '')));

        if ($normalizedStatus === 'checked_in'
            && in_array($normalizedLoyalty, ['gold', 'platinum'], true)
        ) {
            // Loyalty path — primary for gold / platinum guests
            $result = $reservationModel->suggestUpgradeForLoyalty(
                $reservation,
                (int) $reservation['room_id'],
                $reservation['check_in_date'],
                $reservation['check_out_date']
            );
            if (is_array($result) && !empty($result)) {
                $upgradeRoom = $result;
            }

            // VIP path — fallback if loyalty path found nothing and guest is VIP-flagged
            if (!$upgradeRoom && !empty($reservation['is_vip'])) {
                $roomModel = new Room();
                $result    = $roomModel->suggestUpgrade(
                    $reservation['room_id'],
                    $reservation['check_in_date'],
                    $reservation['check_out_date']
                );
                if (is_array($result) && !empty($result)) {
                    $upgradeRoom = $result;
                }
            }

            // Guaranteed fallback — guest qualifies but no specific room was found in DB.
            // Show the button anyway so staff can action it; acceptupgrade() validates
            // room availability before committing, so no unsafe upgrade can occur.
            // If upgradeRoom['id'] is 0 the acceptupgrade controller will reject safely.
            if (!$upgradeRoom) {
                $upgradeRoom = [
                    'id'          => 0,
                    'room_number' => '—',
                    'type_name'   => 'Higher Tier',
                    'base_price'  => 0,
                ];
            }
        }

        // Early check-in eligibility
        $earlyCheckIn = false;
        if (in_array($reservation['status'], ['pending','confirmed'])) {
            $earlyCheckIn = $reservationModel->checkEarlyCheckInEligibility($id);
        }

        // Group reservations
        $groupReservations = [];
        if (!empty($reservation['group_id'])) {
            $groupReservations = $reservationModel->findGroupReservations($reservation['group_id']);
        }

        // OPTIONAL: Special occasion check — birthday on check-in day
        $isSpecialOccasion = false;
        $guestModel        = new Guest();
        $guestRecord       = $guestModel->find($reservation['guest_id']);
        if ($guestRecord) {
            $isSpecialOccasion = $reservationModel->flagSpecialOccasion(
                $guestRecord,
                $reservation['check_in_date']
            );
        }

        $isGuestUser = (strtolower($_SESSION['user_role'] ?? '') === 'guest'
                        || ($_SESSION['user_role_id'] ?? 0) == 4);

        $this->view('reservations/show', [
            'reservation'       => $reservation,
            'folio'             => $folio,
            'upgradeRoom'       => $upgradeRoom,
            'earlyCheckIn'      => $earlyCheckIn,
            'groupReservations' => $groupReservations,
            'isGuestUser'       => $isGuestUser,
            'isSpecialOccasion' => $isSpecialOccasion,
        ]);
    }

    // ── Create: show form ────────────────────────────────────

    public function create()
    {
        $this->requireLogin();

        $isGuestUser  = (strtolower($_SESSION['user_role'] ?? '') === 'guest'
                         || ($_SESSION['user_role_id'] ?? 0) == 4);
        $currentGuest = null;
        $guests       = [];

        if ($isGuestUser) {
            // Auto-resolve the logged-in guest — no manual dropdown needed
            $guestModel   = new Guest();
            $currentGuest = $guestModel->findByEmail($_SESSION['user_email'] ?? '');
        } else {
            // Staff can pick any guest
            $guestModel = new Guest();
            $guests     = $guestModel->all();
        }

        $roomModel = new Room();
        $rooms     = $roomModel->all();

        $this->view('reservations/create', [
            'guests'       => $guests,
            'rooms'        => $rooms,
            'isGuestUser'  => $isGuestUser,
            'currentGuest' => $currentGuest,
        ]);
    }

    public function recommendRooms()
    {
        $this->requireLogin();

        $requestData = $_SERVER['REQUEST_METHOD'] === 'POST' ? $_POST : $_GET;
        $roomModel = new Room();

        header('Content-Type: application/json; charset=utf-8');

        try {
            $rooms = $roomModel->getBestRoomsForClient($requestData);

            echo json_encode([
                'success' => true,
                'count' => count($rooms),
                'rooms' => $rooms,
            ]);
        } catch (Exception $e) {
            http_response_code(422);

            echo json_encode([
                'success' => false,
                'message' => $e->getMessage(),
                'rooms' => [],
            ]);
        }

        exit;
    }

    public function store()
    {
        $this->requireLogin();

        // Hard redirect helper — inline so exit is always guaranteed.
        // Uses full absolute URL to eliminate any relative-path ambiguity.
        // Every exit path in this method uses this pattern exclusively.

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/index.php?url=reservations/create');
            exit;
        }

        $data        = $_POST;
        $isGuestUser = (strtolower($_SESSION['user_role'] ?? '') === 'guest'
                        || ($_SESSION['user_role_id'] ?? 0) == 4);

        if ($isGuestUser) {
            // Guest users: auto-assign their own guest record — ignore any submitted guest_id
            $guestModel   = new Guest();
            $currentGuest = $guestModel->findByEmail($_SESSION['user_email'] ?? '');
            if (!$currentGuest) {
                $_SESSION['reservation_error'] = 'Your guest profile could not be found. Please contact the front desk.';
                header('Location: ' . APP_URL . '/index.php?url=reservations/create');
                exit;
            }
            $data['guest_id']    = $currentGuest['id'];
            $data['assigned_by'] = null;
        } else {
            // Staff booking: keep submitted guest_id, record who assigned
            $data['assigned_by'] = $_SESSION['user_id'] ?? null;
            $guestModel          = new Guest();
            $currentGuest        = $guestModel->find((int) $data['guest_id']);
        }

        // EXTEND: Apply VIP flag in-memory before create() if guest is VIP
        $reservationModel = new Reservation();
        if (!empty($currentGuest)) {
            $reservationModel->applyVipFlag($data, $currentGuest);
        }

        // Basic validation — all four fields are mandatory
        if (
            empty($data['guest_id'])       ||
            empty($data['room_id'])        ||
            empty($data['check_in_date'])  ||
            empty($data['check_out_date'])
        ) {
            header('Location: ' . APP_URL . '/index.php?url=reservations/create');
            exit;
        }

        // ── Out-of-order room guard (backend) ─────────────────
        $roomModel  = new Room();
        $roomRecord = $roomModel->find((int) $data['room_id']);
        if ($roomRecord && $roomRecord['status'] === 'out_of_order') {
            $_SESSION['reservation_error'] = 'This room is out of order and cannot be reserved.';
            header('Location: ' . APP_URL . '/index.php?url=reservations/create');
            exit;
        }

        // Calculate total price
        $data['total_price'] = $reservationModel->calculatePrice(
            $data['room_id'],
            $data['check_in_date'],
            $data['check_out_date']
        );

        // Create the reservation — throws RuntimeException on DB failure
        try {
            $reservationId = $reservationModel->create($data);
        } catch (\RuntimeException $e) {
            $_SESSION['reservation_error'] = 'Could not create reservation. Please try again.';
            header('Location: ' . APP_URL . '/index.php?url=reservations/create');
            exit;
        }

        if (!$reservationId) {
            $_SESSION['reservation_error'] = 'Reservation was not saved. Please try again.';
            header('Location: ' . APP_URL . '/index.php?url=reservations/create');
            exit;
        }

        // Auto-create folio
        $reservationModel->createFolio($reservationId, $data['total_price']);

        // ── Final redirect — ALWAYS to reservation details, NEVER to guests/admin/home ──
        // ?state=pending signals the show page that this reservation was just created.
        header('Location: ' . APP_URL . '/index.php?url=reservations/show/' . (int) $reservationId . '&state=pending');
        exit;
    }

    // ── Edit: show pre-filled form ───────────────────────────

    public function edit($id)
    {
        $this->requireLogin();

        $reservationModel = new Reservation();
        $reservation      = $reservationModel->find($id);

        if (!$reservation) {
            $this->redirect('reservations');
            return;
        }

        $guestModel = new Guest();
        $guests     = $guestModel->all();

        $roomModel  = new Room();
        $rooms      = $roomModel->all();

        $this->view('reservations/edit', [
            'reservation' => $reservation,
            'guests'      => $guests,
            'rooms'       => $rooms,
        ]);
    }

    // ── Update: save edits ───────────────────────────────────

    public function update($id)
    {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('reservations/edit/' . $id);
            return;
        }

        $data = $_POST;

        if (
            empty($data['guest_id'])       ||
            empty($data['room_id'])        ||
            empty($data['check_in_date'])  ||
            empty($data['check_out_date'])
        ) {
            $this->redirect('reservations/edit/' . $id);
            return;
        }

        $reservationModel = new Reservation();

        // Recalculate price
        $data['total_price'] = $reservationModel->calculatePrice(
            $data['room_id'],
            $data['check_in_date'],
            $data['check_out_date']
        );

        $reservationModel->update($id, $data);

        $this->redirect('reservations/show/' . $id);
    }

    // ── Delete / Cancel ────────────────────────────────────────────

    public function delete($id)
    {
        $this->requireLogin();

        $reservationModel = new Reservation();
        $reservationModel->cancel($id);

        // After cancellation, redirect based on role:
        // • Guest users  — go to the create form (stay inside booking flow, NEVER see admin list)
        // • Staff / admin — go back to the reservations list
        $isGuestUser = (strtolower($_SESSION['user_role'] ?? '') === 'guest'
                        || ($_SESSION['user_role_id'] ?? 0) == 4);

        if ($isGuestUser) {
            header('Location: ' . APP_URL . '/index.php?url=reservations/create');
            exit;
        }

        $this->redirect('reservations');
    }

    // ── Confirm ──────────────────────────────────────────────

    public function confirm($id)
    {
        $this->requireLogin();

        $reservationModel = new Reservation();
        $reservationModel->confirm($id);

        $this->redirect('reservations/show/' . $id);
    }

    // ── Check-In ─────────────────────────────────────────────

    public function checkin($id)
    {
        $this->requireLogin();

        $reservationModel = new Reservation();
        $reservationModel->checkIn($id);

        // Redirect to show() — upgrade suggestion is computed fresh there
        // directly from controller → view with no session intermediary.
        $this->redirect('reservations/show/' . $id);
    }

    // ── Early Check-In ────────────────────────────────────────

    public function earlycheckin($id)
    {
        $this->requireLogin();

        $reservationModel = new Reservation();
        $eligible         = $reservationModel->checkEarlyCheckInEligibility($id);

        if ($eligible) {
            $reservationModel->checkIn($id);
        }

        $this->redirect('reservations/show/' . $id);
    }

    // ── Check-Out ─────────────────────────────────────────────

    public function checkout($id)
    {
        $this->requireLogin();

        $reservationModel = new Reservation();
        $reservationModel->checkOut($id);

        $this->redirect('reservations/show/' . $id);
    }

    // ── No-Show ───────────────────────────────────────────────

    public function noshow($id)
    {
        $this->requireLogin();

        $reservationModel = new Reservation();
        $reservationModel->markNoShow($id);

        // INCLUDE: No-show penalty — mandatorily includes chargeGuestCard + notifyGuest
        $reservationModel->applyNoShowPenalty((int) $id);

        $this->redirect('reservations/show/' . $id);
    }

    // ── Recommend Rooms (AJAX) ────────────────────────────────

    public function recommendRooms()
    {
        $this->requireLogin();

        $requestData = $_SERVER['REQUEST_METHOD'] === 'POST' ? $_POST : $_GET;
        $roomModel   = new Room();

        header('Content-Type: application/json; charset=utf-8');

        try {
            $rooms = $roomModel->getBestRoomsForClient($requestData);

            echo json_encode([
                'success' => true,
                'count'   => count($rooms),
                'rooms'   => $rooms,
            ]);
        } catch (Exception $e) {
            http_response_code(422);

            echo json_encode([
                'success' => false,
                'message' => $e->getMessage(),
                'rooms'   => [],
            ]);
        }

        exit;
    }

    // ── Accept Room Upgrade ───────────────────────────────────
    //
    // Activated ONLY when the guest/staff explicitly clicks
    // "Accept Upgrade" in the reservation detail page.
    //
    // URL: index.php?url=reservations/acceptupgrade/{reservationId}&newRoomId={newRoomId}
    //
    // The router delivers $reservationId as the single path param (url[2]).
    // $newRoomId is passed as a plain GET query param so no router or
    // URI parsing is needed at all.

    public function acceptupgrade($reservationId = null)
    {
        $this->requireLogin();

        $reservationId = (int) ($reservationId ?? 0);
        $newRoomId     = (int) ($_GET['newRoomId'] ?? 0);

        // Safety — missing reservationId: stay inside reservation flow
        if (!$reservationId) {
            header('Location: ' . APP_URL . '/index.php?url=reservations/create');
            exit;
        }

        // Safety — missing newRoomId: go back to show page without crashing
        if (!$newRoomId) {
            $this->redirect('reservations/show/' . $reservationId);
            return;
        }

        try {
            $reservationModel = new Reservation();
            $reservationModel->acceptUpgrade($reservationId, $newRoomId);
            $this->redirect('reservations/show/' . $reservationId . '?upgrade=success');
        } catch (\Exception $e) {
            $msg = urlencode($e->getMessage());
            $this->redirect('reservations/show/' . $reservationId . '?upgrade=error&msg=' . $msg);
        }
    }
}

