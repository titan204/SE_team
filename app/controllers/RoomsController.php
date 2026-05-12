<?php


class RoomsController extends Controller
{
    
    public function index()
    {
        $this->requireLogin();
        $this->requireRoles(['manager', 'front_desk']);

        $room  = new Room();
        $rooms = $room->all();

        $this->view('rooms/index', ['rooms' => $rooms]);
    }

    
    public function guest()
    {
        $roomModel = new Room();
        $checkIn = trim((string) ($_GET['check_in_date'] ?? ''));
        $checkOut = trim((string) ($_GET['check_out_date'] ?? ''));
        $errors = [];
        $rooms = [];

        if ($checkIn !== '' || $checkOut !== '') {
            $checkInDate = DateTime::createFromFormat('Y-m-d', $checkIn);
            $checkOutDate = DateTime::createFromFormat('Y-m-d', $checkOut);

            if ($checkIn === '' || !$checkInDate || $checkInDate->format('Y-m-d') !== $checkIn) {
                $errors['check_in_date'] = 'Please choose a valid check-in date.';
            }

            if ($checkOut === '' || !$checkOutDate || $checkOutDate->format('Y-m-d') !== $checkOut) {
                $errors['check_out_date'] = 'Please choose a valid check-out date.';
            }

            if (empty($errors) && $checkIn >= $checkOut) {
                $errors['date_range'] = 'Check-out date must be after check-in date.';
            }

            if (empty($errors)) {
                $rooms = $roomModel->findAvailable($checkIn, $checkOut);
            }
        }

        if (($checkIn === '' && $checkOut === '') || !empty($errors)) {
            $rooms = array_values(array_filter($roomModel->all(), function ($room) {
                return ($room['status'] ?? '') === 'available';
            }));
        }

        $this->view('rooms/guest', [
            'pageTitle' => 'Available Rooms',
            'rooms' => $rooms,
            'filters' => [
                'check_in_date' => $checkIn,
                'check_out_date' => $checkOut,
            ],
            'errors' => $errors,
            'isFilteredByDates' => ($checkIn !== '' && $checkOut !== '' && empty($errors)),
        ]);
    }

    
    public function show($id)
    {
        $this->requireLogin();
        $this->requireRoles(['manager', 'front_desk']);
        $room = new Room();
        $data = $room->find($id);

        if (!$data) {
            $_SESSION['error'] = 'Room not found.';
            $this->redirect('rooms');
        }

        $room->id = $id;

        $this->view('rooms/show', [
            'room'         => $data,
            'reservations' => $room->reservations(),
            'housekeeping' => $room->housekeepingTasks(),
            'maintenance'  => $room->maintenanceOrders(),
        ]);
    }

    
    public function create()
    {
        $this->requireLogin();
        $this->requireRole("manager");
        $roomType  = new RoomType();
        $roomTypes = $roomType->all();

        $this->view('rooms/create', ['roomTypes' => $roomTypes]);
    }

   
    public function store()
    {
        $this->requireLogin();
        $this->requireRole("manager");

        $errors = [];

        if (empty($_POST['room_number'])) {
            $errors[] = 'Room number is required.';
        }
        if (empty($_POST['room_type_id'])) {
            $errors[] = 'Room type is required.';
        }
        if (empty($_POST['floor'])) {
            $errors[] = 'Floor is required.';
        }

        if (!empty($errors)) {
            $_SESSION['errors']    = $errors;
            $_SESSION['old_input'] = $_POST;
            $this->redirect('rooms/create');
        }

        $room = new Room();
        $id   = $room->create([
            'room_number'  => $_POST['room_number'],
            'room_type_id' => $_POST['room_type_id'],
            'floor'        => $_POST['floor'],
            'notes'        => $_POST['notes'] ?? '',
        ]);

        $_SESSION['success'] = 'Room created successfully.';
        $this->redirect("rooms/show/$id");
    }

    
    public function edit($id)
    {
        $this->requireLogin();
        $this->requireRole("manager");

        $room     = new Room();
        $roomData = $room->find($id);

        if (!$roomData) {
            $_SESSION['error'] = 'Room not found.';
            $this->redirect('rooms');
        }

        $roomType  = new RoomType();
        $roomTypes = $roomType->all();

        $this->view('rooms/edit', [
            'room'      => $roomData,
            'roomTypes' => $roomTypes,
        ]);
    }

    
    public function update($id)
    {
        $this->requireLogin();
        $this->requireRole("manager");

        $errors = [];

        if (empty($_POST['room_number'])) {
            $errors[] = 'Room number is required.';
        }
        if (empty($_POST['room_type_id'])) {
            $errors[] = 'Room type is required.';
        }
        if (empty($_POST['floor'])) {
            $errors[] = 'Floor is required.';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $this->redirect("rooms/edit/$id");
        }

        $room = new Room();
        $room->update($id, [
            'room_number'  => $_POST['room_number'],
            'room_type_id' => $_POST['room_type_id'],
            'floor'        => $_POST['floor'],
            'notes'        => $_POST['notes'] ?? '',
        ]);

        $_SESSION['success'] = 'Room updated successfully.';
        $this->redirect("rooms/show/$id");
    }

    
    public function delete($id)
    {
        $this->requireLogin();
        $this->requireRole("manager");

        $room   = new Room();
        try {
            $room->delete($id);
            $_SESSION['success'] = 'Room deleted successfully.';
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        $this->redirect('rooms');
    }


    public function updateStatus($id)
    {
        $this->requireLogin();
        $this->requireRoles(array("manager" ,"housekeeper" , "front_desk"));
        $newStatus = $_POST['status'] ?? '';

        if (empty($newStatus)) {
            $_SESSION['error'] = 'Status is required.';
            $this->redirect("rooms/show/$id");
        }

        $room = new Room();
        try {
            $room->updateStatus($id, $newStatus);
            
            AuditLog::log($_SESSION['user_id'] ?? null, 'room_status_change', 'room', $id, null, $newStatus);
            $_SESSION['success'] = "Status updated to '$newStatus'.";
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        $this->redirect("rooms/show/$id");
    }
}

