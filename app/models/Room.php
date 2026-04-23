<?php
// ============================================================
//  Room Model — Individual hotel rooms
//  Table: rooms
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
        // SELECT rooms.*, room_types.name AS type_name
        // FROM rooms JOIN room_types ON rooms.room_type_id = room_types.id
    }

    public function find($id)
    {
        // TODO: Team will implement query logic here
        // SELECT * FROM rooms WHERE id = ?
    }

    public function create($data)
    {
        // TODO: Team will implement query logic here
        // INSERT INTO rooms (room_type_id, room_number, floor, status) VALUES (?, ?, ?, ?)
    }

    public function update($id, $data)
    {
        // TODO: Team will implement query logic here
        // UPDATE rooms SET room_type_id=?, room_number=?, floor=?, status=? WHERE id = ?
    }

    public function delete($id)
    {
        // TODO: Team will implement query logic here
        // DELETE FROM rooms WHERE id = ?
    }

    // ── Relationships ────────────────────────────────────────

    public function roomType()
    {
        // TODO: Return the room type for this room
        // SELECT * FROM room_types WHERE id = this->room_type_id
    }

    public function reservations()
    {
        // TODO: Return all reservations for this room
        // SELECT * FROM reservations WHERE room_id = ?
    }

    public function housekeepingTasks()
    {
        // TODO: Return housekeeping tasks for this room
        // SELECT * FROM housekeeping_tasks WHERE room_id = ?
    }

    public function maintenanceOrders()
    {
        // TODO: Return maintenance orders for this room
        // SELECT * FROM maintenance_orders WHERE room_id = ?
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
