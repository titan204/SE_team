<?php


class ReservationsController extends Controller
{
    

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

    

    public function show($id)
    {
        $this->requireLogin();

        $reservationModel = new Reservation();
        $reservation      = $reservationModel->find($id);

        if (!$reservation) {
            $this->redirect('reservations');
            return;
        }

        
        $folio = $reservationModel->getFolioByReservation($id);

        
        $upgradeRoom       = null;
        $normalizedStatus  = strtolower(trim((string)($reservation['status']      ?? '')));
        $normalizedLoyalty = strtolower(trim((string)($reservation['loyalty_tier'] ?? '')));

        if ($normalizedStatus === 'checked_in'
            && in_array($normalizedLoyalty, ['gold', 'platinum'], true)
        ) {
            
            $result = $reservationModel->suggestUpgradeForLoyalty(
                $reservation,
                (int) $reservation['room_id'],
                $reservation['check_in_date'],
                $reservation['check_out_date']
            );
            if (is_array($result) && !empty($result)) {
                $upgradeRoom = $result;
            }

            
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

            
            if (!$upgradeRoom) {
                $upgradeRoom = [
                    'id'          => 0,
                    'room_number' => '—',
                    'type_name'   => 'Higher Tier',
                    'base_price'  => 0,
                ];
            }
        }

        
        $earlyCheckIn = false;
        if (in_array($reservation['status'], ['pending','confirmed'])) {
            $earlyCheckIn = $reservationModel->checkEarlyCheckInEligibility($id);
        }

        
        $groupReservations = [];
        if (!empty($reservation['group_id'])) {
            $groupReservations = $reservationModel->findGroupReservations($reservation['group_id']);
        }

        
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



    public function create()
    {
        $this->requireLogin();

        $isGuestUser  = (strtolower($_SESSION['user_role'] ?? '') === 'guest'
                         || ($_SESSION['user_role_id'] ?? 0) == 4);
        $currentGuest = null;
        $guests       = [];

        if ($isGuestUser) {
            $guestModel   = new Guest();
            $currentGuest = $guestModel->findByEmail($_SESSION['user_email'] ?? '');
        } else {
            $guestModel = new Guest();
            $guests     = $guestModel->all();
        }

        $roomModel       = new Room();
        $rooms           = $roomModel->all();

        
        $preSelectedRoom = null;
        $preRoomId       = (int) ($_GET['room_id'] ?? 0);
        if ($preRoomId > 0) {
            $preSelectedRoom = $roomModel->find($preRoomId);
        }

        $this->view('reservations/create', [
            'guests'          => $guests,
            'rooms'           => $rooms,
            'isGuestUser'     => $isGuestUser,
            'currentGuest'    => $currentGuest,
            'preSelectedRoom' => $preSelectedRoom,
        ]);
    }


    public function store()
    {
        $this->requireLogin();

        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/index.php?url=reservations/create');
            exit;
        }

        $data        = $_POST;
        $isGuestUser = (strtolower($_SESSION['user_role'] ?? '') === 'guest'
                        || ($_SESSION['user_role_id'] ?? 0) == 4);

        if ($isGuestUser) {
            
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
            
            $data['assigned_by'] = $_SESSION['user_id'] ?? null;
            $guestModel          = new Guest();
            $currentGuest        = $guestModel->find((int) $data['guest_id']);
        }

        
        $reservationModel = new Reservation();
        if (!empty($currentGuest)) {
            $reservationModel->applyVipFlag($data, $currentGuest);
        }

        
        if (
            empty($data['guest_id'])       ||
            empty($data['room_id'])        ||
            empty($data['check_in_date'])  ||
            empty($data['check_out_date'])
        ) {
            header('Location: ' . APP_URL . '/index.php?url=reservations/create');
            exit;
        }

        
        $roomModel  = new Room();
        $roomRecord = $roomModel->find((int) $data['room_id']);
        if ($roomRecord && $roomRecord['status'] === 'out_of_order') {
            $_SESSION['reservation_error'] = 'This room is out of order and cannot be reserved.';
            header('Location: ' . APP_URL . '/index.php?url=reservations/create');
            exit;
        }

        
        $data['total_price'] = $reservationModel->calculatePrice(
            $data['room_id'],
            $data['check_in_date'],
            $data['check_out_date']
        );

        
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

        
        $reservationModel->createFolio($reservationId, $data['total_price']);

        
        if ($isGuestUser) {
            header('Location: ' . APP_URL . '/index.php?url=reservations/deposit/' . (int)$reservationId);
        } else {
            header('Location: ' . APP_URL . '/index.php?url=reservations/show/' . (int)$reservationId . '&state=pending');
        }
        exit;
    }

    

    public function deposit($id)
    {
        $this->requireLogin();

        $reservationModel = new Reservation();
        $reservation      = $reservationModel->find($id);

        if (!$reservation) {
            $this->redirect('reservations');
            return;
        }

        
        $depositAmount = round((float)$reservation['total_price'] * 0.20, 2);

        $this->view('reservations/deposit', [
            'reservation'   => $reservation,
            'depositAmount' => $depositAmount,
        ]);
    }

    

    public function payDeposit($id)
    {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('reservations/deposit/' . $id);
            return;
        }

        $reservationModel = new Reservation();
        $reservation      = $reservationModel->find($id);

        if (!$reservation) {
            $this->redirect('reservations');
            return;
        }

        $depositAmount = round((float)$reservation['total_price'] * 0.20, 2);

        $db = (new Model())->getDb();

        
        $stmt = mysqli_prepare(
            $db,
            "UPDATE reservations
             SET deposit_amount = ?, deposit_paid = 1, status = 'confirmed'
             WHERE id = ?"
        );
        mysqli_stmt_bind_param($stmt, 'di', $depositAmount, $id);
        mysqli_stmt_execute($stmt);

        
        $stmt2 = mysqli_prepare(
            $db,
            "UPDATE folios
             SET amount_paid  = amount_paid + ?,
                 balance_due  = GREATEST(0, COALESCE(balance_due, total_amount) - ?)
             WHERE reservation_id = ?"
        );
        mysqli_stmt_bind_param($stmt2, 'ddi', $depositAmount, $depositAmount, $id);
        mysqli_stmt_execute($stmt2);

        
        header('Location: ' . APP_URL . '/index.php?url=reservations/deposit/' . (int)$id . '&paid=1');
        exit;
    }

    

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

        
        $data['total_price'] = $reservationModel->calculatePrice(
            $data['room_id'],
            $data['check_in_date'],
            $data['check_out_date']
        );

        $reservationModel->update($id, $data);

        $this->redirect('reservations/show/' . $id);
    }

    

    public function delete($id)
    {
        $this->requireLogin();

        $reservationModel = new Reservation();
        $reservationModel->cancel($id);

        
        $isGuestUser = (strtolower($_SESSION['user_role'] ?? '') === 'guest'
                        || ($_SESSION['user_role_id'] ?? 0) == 4);

        if ($isGuestUser) {
            header('Location: ' . APP_URL . '/index.php?url=reservations/create');
            exit;
        }

        $this->redirect('reservations');
    }

    

    public function confirm($id)
    {
        $this->requireLogin();

        $reservationModel = new Reservation();
        $reservationModel->confirm($id);

        $this->redirect('reservations/show/' . $id);
    }

    

    public function checkin($id)
    {
        $this->requireLogin();

        $reservationModel = new Reservation();
        $reservationModel->checkIn($id);

        // Redirect to show() — upgrade suggestion is computed fresh there
        // directly from controller → view with no session intermediary.
        $this->redirect('reservations/show/' . $id);
    }

    

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

    

    public function checkout($id)
    {
        $this->requireLogin();

        $reservationModel = new Reservation();
        $reservationModel->checkOut($id);

        $this->redirect('reservations/show/' . $id);
    }

    

    public function noshow($id)
    {
        $this->requireLogin();

        $reservationModel = new Reservation();
        $reservationModel->markNoShow($id);

        
        $reservationModel->applyNoShowPenalty((int) $id);

        $this->redirect('reservations/show/' . $id);
    }

    

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

    

    public function acceptupgrade($reservationId = null)
    {
        $this->requireLogin();

        $reservationId = (int) ($reservationId ?? 0);
        $newRoomId     = (int) ($_GET['newRoomId'] ?? 0);

        
        if (!$reservationId) {
            header('Location: ' . APP_URL . '/index.php?url=reservations/create');
            exit;
        }

        
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

