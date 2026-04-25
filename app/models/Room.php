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

    /**
     * State machine — only these transitions are valid:
     *   available    → occupied       (guest checks in)
     *   occupied     → dirty          (guest checks out)
     *   dirty        → cleaning       (housekeeping starts)
     *   cleaning     → inspecting     (housekeeping done)
     *   inspecting   → available      (inspection passed)
     *   any status   → out_of_order   (escalation / maintenance)
     *   out_of_order → available      (issue resolved)
     */
    private $transitions = [
        'available'    => ['occupied',   'out_of_order'],
        'occupied'     => ['dirty',      'out_of_order'],
        'dirty'        => ['cleaning',   'out_of_order'],
        'cleaning'     => ['inspecting', 'out_of_order'],
        'inspecting'   => ['available',  'out_of_order'],
        'out_of_order' => ['available'],
    ];

    // ── CRUD ─────────────────────────────────────────────────

    /**
     * SELECT all rooms with JOIN on room_types to include type_name and base_price.
     */
    public function all()
    {
        $stmt = mysqli_prepare(
            $this->db,
            "SELECT rooms.*, room_types.name AS type_name, room_types.base_price AS base_price
             FROM   rooms
             JOIN   room_types ON rooms.room_type_id = room_types.id
             ORDER BY rooms.room_number ASC"
        );
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    /**
     * SELECT one room by ID with JOIN on room_types.
     */
    public function find($id)
    {
        $stmt = mysqli_prepare(
            $this->db,
            "SELECT rooms.*,
                    room_types.name        AS type_name,
                    room_types.base_price  AS base_price,
                    room_types.description AS type_description
             FROM   rooms
             JOIN   room_types ON rooms.room_type_id = room_types.id
             WHERE  rooms.id = ?
             LIMIT  1"
        );
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($result);
    }

    /**
     * INSERT new room; default status is 'available'.
     */
    public function create($data)
    {
        $stmt = mysqli_prepare(
            $this->db,
            "INSERT INTO rooms (room_type_id, room_number, floor, status, notes)
             VALUES (?, ?, ?, 'available', ?)"
        );
        $room_type_id = $data['room_type_id'];
        $room_number  = $data['room_number'];
        $floor        = $data['floor'];
        $notes        = $data['notes'] ?? '';
        mysqli_stmt_bind_param($stmt, 'isis', $room_type_id, $room_number, $floor, $notes);
        mysqli_stmt_execute($stmt);
        return mysqli_insert_id($this->db);
    }

    /**
     * UPDATE room fields.
     */
    public function update($id, $data)
    {
        $stmt = mysqli_prepare(
            $this->db,
            "UPDATE rooms
             SET room_type_id = ?, room_number = ?, floor = ?, notes = ?
             WHERE id = ?"
        );
        $room_type_id = $data['room_type_id'];
        $room_number  = $data['room_number'];
        $floor        = $data['floor'];
        $notes        = $data['notes'] ?? '';
        mysqli_stmt_bind_param($stmt, 'isisi', $room_type_id, $room_number, $floor, $notes, $id);
        mysqli_stmt_execute($stmt);
    }

    /**
     * DELETE only if no active reservations exist (pending / confirmed / checked_in).
     */
    public function delete($id)
    {
        $stmt = mysqli_prepare(
            $this->db,
            "SELECT COUNT(*) AS cnt
             FROM   reservations
             WHERE  room_id = ?
               AND  status IN ('pending', 'confirmed', 'checked_in')"
        );
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        if ($row['cnt'] > 0) {
            return "Cannot delete: room has {$row['cnt']} active reservation(s).";
        }

        $stmt = mysqli_prepare($this->db, "DELETE FROM rooms WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        return true;
    }

    // ── Relationships ────────────────────────────────────────

    /**
     * Return the associated room_type record for this room.
     */
    public function roomType()
    {
        $stmt = mysqli_prepare($this->db, "SELECT * FROM room_types WHERE id = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, 'i', $this->room_type_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($result);
    }

    /**
     * Return all reservations for this room.
     */
    public function reservations()
    {
        $stmt = mysqli_prepare(
            $this->db,
            "SELECT * FROM reservations WHERE room_id = ? ORDER BY check_in_date DESC"
        );
        mysqli_stmt_bind_param($stmt, 'i', $this->id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    /**
     * Return all housekeeping tasks for this room.
     */
    public function housekeepingTasks()
    {
        $stmt = mysqli_prepare(
            $this->db,
            "SELECT * FROM housekeeping_tasks WHERE room_id = ? ORDER BY created_at DESC"
        );
        mysqli_stmt_bind_param($stmt, 'i', $this->id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    /**
     * Return all maintenance orders for this room.
     */
    public function maintenanceOrders()
    {
        $stmt = mysqli_prepare(
            $this->db,
            "SELECT * FROM maintenance_orders WHERE room_id = ? ORDER BY created_at DESC"
        );
        mysqli_stmt_bind_param($stmt, 'i', $this->id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    // ── Status Helpers ───────────────────────────────────────

    /**
     * Validate the transition against the state machine, then UPDATE if valid.
     * Returns true on success, or an error message string if the transition is invalid.
     */
    public function updateStatus($roomId, $newStatus)
    {
        // Fetch current status
        $stmt = mysqli_prepare($this->db, "SELECT status FROM rooms WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $roomId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        if (!$row) {
            return "Room not found.";
        }

        $current = $row['status'];

        // Validate that newStatus is a known status
        $allStatuses = array_keys($this->transitions);
        if (!in_array($newStatus, $allStatuses)) {
            return "Invalid status: '$newStatus'.";
        }

        // Validate transition
        $allowed = $this->transitions[$current] ?? [];
        if (!in_array($newStatus, $allowed)) {
            return "Transition from '$current' to '$newStatus' is not allowed.";
        }

        // Perform update
        $stmt = mysqli_prepare($this->db, "UPDATE rooms SET status = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'si', $newStatus, $roomId);
        mysqli_stmt_execute($stmt);
        return true;
    }

    /**
     * Find all rooms NOT reserved during the given date range.
     * Excludes reservations with status IN ('cancelled', 'no_show', 'checked_out').
     * Optionally filter by room_type_id.
     */
    public function findAvailable($checkIn, $checkOut, $typeId = null)
    {
        if ($typeId) {
            $stmt = mysqli_prepare(
                $this->db,
                "SELECT rooms.*, room_types.name AS type_name, room_types.base_price AS base_price
                 FROM   rooms
                 JOIN   room_types ON rooms.room_type_id = room_types.id
                 WHERE  rooms.status = 'available'
                   AND  rooms.room_type_id = ?
                   AND  rooms.id NOT IN (
                            SELECT DISTINCT room_id
                            FROM   reservations
                            WHERE  status NOT IN ('cancelled', 'no_show', 'checked_out')
                              AND  check_in_date  < ?
                              AND  check_out_date > ?
                        )
                 ORDER BY rooms.room_number ASC"
            );
            mysqli_stmt_bind_param($stmt, 'iss', $typeId, $checkOut, $checkIn);
        } else {
            $stmt = mysqli_prepare(
                $this->db,
                "SELECT rooms.*, room_types.name AS type_name, room_types.base_price AS base_price
                 FROM   rooms
                 JOIN   room_types ON rooms.room_type_id = room_types.id
                 WHERE  rooms.status = 'available'
                   AND  rooms.id NOT IN (
                            SELECT DISTINCT room_id
                            FROM   reservations
                            WHERE  status NOT IN ('cancelled', 'no_show', 'checked_out')
                              AND  check_in_date  < ?
                              AND  check_out_date > ?
                        )
                 ORDER BY rooms.room_number ASC"
            );
            mysqli_stmt_bind_param($stmt, 'ss', $checkOut, $checkIn);
        }

        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    /**
     * Suggest an upgrade: find the current room's base_price, then find
     * room types with a higher base_price, and return the first available
     * higher-tier room for the given date range.
     */
    public function suggestUpgrade($currentRoomId, $checkIn, $checkOut)
    {
        // Find current room's type and base_price
        $stmt = mysqli_prepare(
            $this->db,
            "SELECT room_types.base_price
             FROM   rooms
             JOIN   room_types ON rooms.room_type_id = room_types.id
             WHERE  rooms.id = ?
             LIMIT  1"
        );
        mysqli_stmt_bind_param($stmt, 'i', $currentRoomId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $current = mysqli_fetch_assoc($result);

        if (!$current) {
            return false;
        }

        $currentPrice = $current['base_price'];

        // Find room types with a higher base_price
        $stmt = mysqli_prepare(
            $this->db,
            "SELECT id FROM room_types WHERE base_price > ? ORDER BY base_price ASC"
        );
        mysqli_stmt_bind_param($stmt, 'd', $currentPrice);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $higherTypes = mysqli_fetch_all($result, MYSQLI_ASSOC);

        // For each higher type, call findAvailable and return the first hit
        foreach ($higherTypes as $type) {
            $available = $this->findAvailable($checkIn, $checkOut, $type['id']);
            if (!empty($available)) {
                return $available[0];
            }
        }

        return false;
    }
}
