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

        $room  = new Room();
        $rooms = $room->all();

        $this->view('rooms/index', ['rooms' => $rooms]);
    }

    /**
     * Show a single room with reservations, housekeeping, maintenance.
     */
    public function show($id)
    {
        $this->requireLogin();

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

