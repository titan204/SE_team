<?php
// ============================================================
//  RoomsController — Room inventory management
//  Routes:
//    /rooms              → index
//    /rooms/show/5       → show room details
//    /rooms/create       → new room form
//    /rooms/store        → save new room
//    /rooms/edit/5       → edit room
//    /rooms/update/5     → save edits
//    /rooms/delete/5     → delete room
//    /rooms/updateStatus/5 → change room status
// ============================================================

class RoomsController extends Controller
{
    /**
     * List all rooms.
     */
    public function index()
    {
        $this->requireLogin();
        $this->requireRole("manager"); 

        $room  = new Room();
        $rooms = $room->all();

        $this->view('rooms/index', ['rooms' => $rooms]);
    }

    /**
     * Show guest-facing available rooms.
     * Uses date-range availability logic when dates are provided,
     * otherwise falls back to rooms currently marked as available.
     */
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

    /**
     * Show a single room with reservations, housekeeping, maintenance.
     */
    public function show($id)
    {
        $this->requireLogin();
        $this->requireRole("manager"); 
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

    /**
     * Show the create-room form.
     */
    public function create()
    {
        $this->requireLogin();
        $this->requireRole("manager");
        $roomType  = new RoomType();
        $roomTypes = $roomType->all();

        $this->view('rooms/create', ['roomTypes' => $roomTypes]);
    }

    /**
     * Validate POST data and insert a new room.
     */
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

    /**
     * Show the edit-room form.
     */
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

    /**
     * Validate POST data and update a room.
     */
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

    /**
     * Delete a room (only if no active reservations).
     */
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

    /**
     * Read new status from POST, validate transition, update.
     */
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
            // Log the status change using the existing AuditLog model
            AuditLog::log($_SESSION['user_id'] ?? null, 'room_status_change', 'room', $id, null, $newStatus);
            $_SESSION['success'] = "Status updated to '$newStatus'.";
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        $this->redirect("rooms/show/$id");
    }
}

