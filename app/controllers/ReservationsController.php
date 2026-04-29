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

        // Room upgrade suggestion for VIP guests
        $upgradeRoom = null;
        if (!empty($reservation['is_vip']) && in_array($reservation['status'], ['pending','confirmed'])) {
            $roomModel   = new Room();
            $upgradeRoom = $roomModel->suggestUpgrade(
                $reservation['room_id'],
                $reservation['check_in_date'],
                $reservation['check_out_date']
            );
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

        $isGuestUser = (strtolower($_SESSION['user_role'] ?? '') === 'guest'
                        || ($_SESSION['user_role_id'] ?? 0) == 4);

        $this->view('reservations/show', [
            'reservation'       => $reservation,
            'folio'             => $folio,
            'upgradeRoom'       => $upgradeRoom,
            'earlyCheckIn'      => $earlyCheckIn,
            'groupReservations' => $groupReservations,
            'isGuestUser'       => $isGuestUser,
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

    // ── Store: save new reservation ──────────────────────────

    public function store()
    {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('reservations/create');
            return;
        }

        $data = $_POST;
        $isGuestUser = (strtolower($_SESSION['user_role'] ?? '') === 'guest'
                        || ($_SESSION['user_role_id'] ?? 0) == 4);

        if ($isGuestUser) {
            // Guest users: auto-assign their own guest record — ignore any submitted guest_id
            $guestModel   = new Guest();
            $currentGuest = $guestModel->findByEmail($_SESSION['user_email'] ?? '');
            if (!$currentGuest) {
                // Cannot find guest profile — redirect home
                $this->redirect('home/index');
                return;
            }
            $data['guest_id']    = $currentGuest['id'];
            $data['assigned_by'] = null; // self-booked
        } else {
            // Staff booking: keep submitted guest_id, record who assigned
            $data['assigned_by'] = $_SESSION['user_id'] ?? null;
        }

        // Basic validation
        if (
            empty($data['guest_id'])       ||
            empty($data['room_id'])        ||
            empty($data['check_in_date'])  ||
            empty($data['check_out_date'])
        ) {
            $this->redirect('reservations/create');
            return;
        }

        $reservationModel = new Reservation();

        // ── Out-of-order room guard (backend) ─────────────────
        $roomModel  = new Room();
        $roomRecord = $roomModel->find((int) $data['room_id']);
        if ($roomRecord && $roomRecord['status'] === 'out_of_order') {
            $_SESSION['reservation_error'] = 'This room is out of order and cannot be reserved.';
            $this->redirect('reservations/create');
            return;
        }

        // Calculate total price
        $data['total_price'] = $reservationModel->calculatePrice(
            $data['room_id'],
            $data['check_in_date'],
            $data['check_out_date']
        );

        // Create the reservation
        $reservationId = $reservationModel->create($data);

        // Auto-create folio
        $reservationModel->createFolio($reservationId, $data['total_price']);

        $this->redirect('reservations/show/' . $reservationId);
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

    // ── Delete / Cancel ──────────────────────────────────────

    public function delete($id)
    {
        $this->requireLogin();

        $reservationModel = new Reservation();
        $reservationModel->cancel($id);

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
}
