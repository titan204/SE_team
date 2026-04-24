<?php
// ============================================================
//  Room Model — Individual hotel rooms
//  Table: rooms
//
//  Usage:
//    $room = new Room();
//    $all  = $room->all();
// ============================================================

class Room extends Model
{
    public $id;
    public $room_type_id;
    public $room_number;
    public $floor;
    public $status;       // available, occupied, dirty, cleaning, inspecting, out_of_order
    public $notes;
    public $created_at;
    public $updated_at;

    // ── CRUD ─────────────────────────────────────────────────

    public function all()
    {
        // TODO: Team will implement query logic here
        // $result = mysqli_query($this->db, "SELECT rooms.*, room_types.name AS type_name FROM rooms JOIN room_types ON rooms.room_type_id = room_types.id");
        // return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function find($id)
    {
        // TODO: Team will implement query logic here
        // $id = mysqli_real_escape_string($this->db, $id);
        // $result = mysqli_query($this->db, "SELECT * FROM rooms WHERE id = '$id'");
        // return mysqli_fetch_assoc($result);
    }

    public function create($data)
    {
        // TODO: Team will implement query logic here
        // $room_type_id = mysqli_real_escape_string($this->db, $data['room_type_id']);
        // $room_number  = mysqli_real_escape_string($this->db, $data['room_number']);
        // $floor        = mysqli_real_escape_string($this->db, $data['floor']);
        // $status       = mysqli_real_escape_string($this->db, $data['status']);
        // mysqli_query($this->db, "INSERT INTO rooms (room_type_id, room_number, floor, status) VALUES ('$room_type_id', '$room_number', '$floor', '$status')");
        // return mysqli_insert_id($this->db);
    }

    public function update($id, $data)
    {
        // TODO: Team will implement query logic here
        // $room_type_id = mysqli_real_escape_string($this->db, $data['room_type_id']);
        // $id = mysqli_real_escape_string($this->db, $id);
        // mysqli_query($this->db, "UPDATE rooms SET room_type_id='$room_type_id', ... WHERE id = '$id'");
    }

    public function delete($id)
    {
        // TODO: Team will implement query logic here
        // $id = mysqli_real_escape_string($this->db, $id);
        // mysqli_query($this->db, "DELETE FROM rooms WHERE id = '$id'");
    }

    // ── Relationships ────────────────────────────────────────

    public function roomType()
    {
        // TODO: Return the room type for this room
        // $result = mysqli_query($this->db, "SELECT * FROM room_types WHERE id = '$this->room_type_id'");
        // return mysqli_fetch_assoc($result);
    }

    public function reservations()
    {
        // TODO: Return all reservations for this room
        // $result = mysqli_query($this->db, "SELECT * FROM reservations WHERE room_id = '$this->id'");
        // return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function housekeepingTasks()
    {
        // TODO: Return housekeeping tasks for this room
        // $result = mysqli_query($this->db, "SELECT * FROM housekeeping_tasks WHERE room_id = '$this->id'");
        // return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function maintenanceOrders()
    {
        // TODO: Return maintenance orders for this room
        // $result = mysqli_query($this->db, "SELECT * FROM maintenance_orders WHERE room_id = '$this->id'");
        // return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    // ── Status Helpers ───────────────────────────────────────

    public function updateStatus($newStatus)
    {
        // TODO: Update the room status (state machine logic)
        // Validate: clean->occupied->dirty->cleaning->inspecting->available
    }

    public function findAvailable($checkIn, $checkOut, $typeId = null)
    {
        // TODO: Return rooms that are not reserved during the given dates
        // Used by the Dynamic Room-Allocation Engine
    }

    public function suggestUpgrade($currentRoomId)
    {
        // TODO: Find a higher-tier room that is available for upgrade
    }
}
