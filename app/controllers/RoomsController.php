<?php
// ============================================================
//  RoomsController — Room inventory management
//  Routes:
//    /rooms            → index
//    /rooms/show/5     → show room details
//    /rooms/create     → new room form
//    /rooms/store      → save new room
//    /rooms/edit/5     → edit room
//    /rooms/update/5   → save edits
//    /rooms/delete/5   → delete room
// ============================================================

class RoomsController extends Controller
{
    public function index()
    {
        $this->requireLogin();
        $room  = new Room();
        $rooms = $room->all();
        $this->view('rooms/index', ['rooms' => $rooms]);
    }

    public function show($id)
    {
         $this->requireLogin();
 
        $room = new Room();
        $data = $room->find($id);
 
        if (!$data) {
            $this->redirect('rooms', 'Room not found.', 'error');
        }
 
        $room->id = $id;
 
        $this->view('rooms/show', [
            'room'             => $data,
            'reservations'     => $room->reservations(),
            'housekeeping'     => $room->housekeepingTasks(),
            'maintenance'      => $room->maintenanceOrders(),
        ]);
    }

    public function create()
    {
        $this->requireLogin();

        $roomType  = new RoomType();
        $roomTypes = $roomType->all();
 
        $this->view('rooms/create', ['roomTypes' => $roomTypes]);
    }

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
 
        $this->redirect("rooms/show/$id", 'Room created successfully.', 'success');
    }

    public function edit($id)
    {
        $this->requireLogin();
 
        $room     = new Room();
        $roomData = $room->find($id);
 
        if (!$roomData) {
            $this->redirect('rooms', 'Room not found.', 'error');
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
 
        $this->redirect("rooms/show/$id", 'Room updated successfully.', 'success');
    }

    public function delete($id)
    {
         $this->requireLogin();
 
        $room   = new Room();
        $result = $room->delete($id);
 
        if ($result === true) {
            $this->redirect('rooms', 'Room deleted successfully.', 'success');
        } else {
            $this->redirect('rooms', $result, 'error');
        }
    }

    public function updateStatus($id)
    {
         $this->requireLogin();
 
        $newStatus = $_POST['status'] ?? '';
 
        if (empty($newStatus)) {
            $this->redirect("rooms/show/$id", 'Status is required.', 'error');
        }
 
        $room   = new Room();
        $room->id = $id;
        $result = $room->updateStatus($newStatus);
 
        if ($result === true) {
            
            $this->logAudit(
                'room_status_change',   
                'room',                 
                $id,                    
                null,                    
                $newStatus               
            );
            $this->redirect("rooms/show/$id", "Status updated to '$newStatus'.", 'success');
        } else {
            $this->redirect("rooms/show/$id", $result, 'error');
        }
    }
     private function requireLogin()
    {
        if (empty($_SESSION['user_id'])) {
            $this->redirect('login');
        }
    }
 

    private function logAudit($action, $targetType, $targetId, $oldValue, $newValue)
    {
        $userId     = $_SESSION['user_id'] ?? null;
        $auditModel = new AuditLog(); 
        $auditModel->create([
            'user_id'     => $userId,
            'action'      => $action,
            'target_type' => $targetType,
            'target_id'   => $targetId,
            'old_value'   => $oldValue,
            'new_value'   => $newValue,
        ]);
    }
}
