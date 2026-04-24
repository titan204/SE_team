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

    private $transitions = [
        'available'    => ['occupied',   'out_of_order'],
        'occupied'     => ['dirty',      'out_of_order'],
        'dirty'        => ['cleaning',   'out_of_order'],
        'cleaning'     => ['inspecting', 'out_of_order'],
        'inspecting'   => ['available',  'out_of_order'],
        'out_of_order' => ['available'],
    ];

    // ── CRUD ─────────────────────────────────────────────────

    public function all()
    {
        $result = mysqli_query(
            $this->db,
            "SELECT rooms.*, room_types.name AS type_name, room_types.base_price AS base_price
             FROM   rooms
             JOIN   room_types ON rooms.room_type_id = room_types.id
             ORDER BY rooms.room_number ASC"
        );
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function find($id)
    {
        $id = mysqli_real_escape_string($this->db, $id);
        $result = mysqli_query(
            $this->db,
            "SELECT rooms.*,
                    room_types.name        AS type_name,
                    room_types.base_price  AS base_price,
                    room_types.description AS type_description
             FROM   rooms
             JOIN   room_types ON rooms.room_type_id = room_types.id
             WHERE  rooms.id = '$id'
             LIMIT  1"
        );
        return mysqli_fetch_assoc($result);
    }

    public function create($data)
    {
        $room_type_id = mysqli_real_escape_string($this->db, $data['room_type_id']);
        $room_number  = mysqli_real_escape_string($this->db, $data['room_number']);
        $floor        = mysqli_real_escape_string($this->db, $data['floor']);
        $notes        = mysqli_real_escape_string($this->db, $data['notes'] ?? '');
 
        mysqli_query(
            $this->db,
            "INSERT INTO rooms (room_type_id, room_number, floor, status, notes)
             VALUES ('$room_type_id', '$room_number', '$floor', 'available', '$notes')"
        );
        return mysqli_insert_id($this->db);
    }

    public function update($id, $data)
    {
        $id           = mysqli_real_escape_string($this->db, $id);
        $room_type_id = mysqli_real_escape_string($this->db, $data['room_type_id']);
        $room_number  = mysqli_real_escape_string($this->db, $data['room_number']);
        $floor        = mysqli_real_escape_string($this->db, $data['floor']);
        $notes        = mysqli_real_escape_string($this->db, $data['notes'] ?? '');
 
        mysqli_query(
            $this->db,
            "UPDATE rooms
             SET room_type_id = '$room_type_id',
                 room_number  = '$room_number',
                 floor        = '$floor',
                 notes        = '$notes'
             WHERE id = '$id'"
        );
    }

    public function delete($id)
    {
        $id = mysqli_real_escape_string($this->db, $id);
 
        $check = mysqli_query(
            $this->db,
            "SELECT COUNT(*) AS cnt
             FROM   reservations
             WHERE  room_id = '$id'
               AND  status IN ('confirmed', 'checked_in')"
        );
        $row = mysqli_fetch_assoc($check);
 
        if ($row['cnt'] > 0) {
            return "Cannot delete: room has {$row['cnt']} active reservation(s).";
        }
 
        mysqli_query($this->db, "DELETE FROM rooms WHERE id = '$id'");
        return true;
    }

    // ── Relationships ────────────────────────────────────────

    public function roomType()
    {
        $type_id = mysqli_real_escape_string($this->db, $this->room_type_id);
        $result  = mysqli_query(
            $this->db,
            "SELECT * FROM room_types WHERE id = '$type_id' LIMIT 1"
        );
        return mysqli_fetch_assoc($result);
    }

    public function reservations()
    {
        $id     = mysqli_real_escape_string($this->db, $this->id);
        $result = mysqli_query(
            $this->db,
            "SELECT * FROM reservations WHERE room_id = '$id' ORDER BY check_in_date DESC"
        );
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function housekeepingTasks()
    {
        $id     = mysqli_real_escape_string($this->db, $this->id);
        $result = mysqli_query(
            $this->db,
            "SELECT * FROM housekeeping_tasks WHERE room_id = '$id' ORDER BY created_at DESC"
        );
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function maintenanceOrders()
    {
        $id     = mysqli_real_escape_string($this->db, $this->id);
        $result = mysqli_query(
            $this->db,
            "SELECT * FROM maintenance_orders WHERE room_id = '$id' ORDER BY created_at DESC"
        );
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    // ── Status Helpers ───────────────────────────────────────

    public function updateStatus($newStatus)
    {
    
        $id      = mysqli_real_escape_string($this->db, $this->id);
        $result  = mysqli_query($this->db, "SELECT status FROM rooms WHERE id = '$id'");
        $row     = mysqli_fetch_assoc($result);
 
        if (!$row) {
            return "Room not found.";
        }
 
        $current = $row['status'];
 
        $allStatuses = array_keys($this->transitions);
        if (!in_array($newStatus, $allStatuses)) {
            return "Invalid status: '$newStatus'.";
        }
 
        $allowed = $this->transitions[$current] ?? [];
        if (!in_array($newStatus, $allowed)) {
            return "Transition from '$current' to '$newStatus' is not allowed.";
        }
 
        $newStatus = mysqli_real_escape_string($this->db, $newStatus);
        mysqli_query($this->db, "UPDATE rooms SET status = '$newStatus' WHERE id = '$id'");
 
        return true;
    }

    public function findAvailable($checkIn, $checkOut, $typeId = null)
    {
        $checkIn  = mysqli_real_escape_string($this->db, $checkIn);
        $checkOut = mysqli_real_escape_string($this->db, $checkOut);
 
        $typeFilter = '';
        if ($typeId) {
            $typeId     = mysqli_real_escape_string($this->db, $typeId);
            $typeFilter = "AND rooms.room_type_id = '$typeId'";
        }
 
        $result = mysqli_query(
            $this->db,
            "SELECT rooms.*, room_types.name AS type_name, room_types.base_price AS base_price
             FROM   rooms
             JOIN   room_types ON rooms.room_type_id = room_types.id
             WHERE  rooms.status = 'available'
             $typeFilter
               AND  rooms.id NOT IN (
                        SELECT DISTINCT room_id
                        FROM   reservations
                        WHERE  status IN ('confirmed', 'checked_in')
                          AND  check_in_date  < '$checkOut'
                          AND  check_out_date > '$checkIn'
                    )
             ORDER BY rooms.room_number ASC"
        );
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function suggestUpgrade($currentRoomId)
    {
    
        $currentRoomId = mysqli_real_escape_string($this->db, $currentRoomId);
        $result        = mysqli_query(
            $this->db,
            "SELECT rooms.id, room_types.base_price
             FROM   rooms
             JOIN   room_types ON rooms.room_type_id = room_types.id
             WHERE  rooms.id = '$currentRoomId'
             LIMIT  1"
        );
        $current = mysqli_fetch_assoc($result);
 
        if (!$current) {
            return false;
        }
 
        $currentPrice = mysqli_real_escape_string($this->db, $current['base_price']);
 

        $upgrade = mysqli_query(
            $this->db,
            "SELECT rooms.*, room_types.name AS type_name, room_types.base_price AS base_price
             FROM   rooms
             JOIN   room_types ON rooms.room_type_id = room_types.id
             WHERE  rooms.status     = 'available'
               AND  room_types.base_price > '$currentPrice'
               AND  rooms.id         != '$currentRoomId'
             ORDER BY room_types.base_price ASC
             LIMIT 1"
        );
        $row = mysqli_fetch_assoc($upgrade);
        return $row ?: false;
    }
}
