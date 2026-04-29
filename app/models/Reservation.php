<?php
// ============================================================
//  Reservation Model — Bookings / check-in / check-out
//  Table: reservations
// ============================================================

class Reservation extends Model
{
    public $id;
    public $guest_id;
    public $room_id;
    public $assigned_by;
    public $check_in_date;
    public $check_out_date;
    public $actual_check_in;
    public $actual_check_out;
    public $status;         // pending, confirmed, checked_in, checked_out, cancelled, no_show
    public $adults;
    public $children;
    public $special_requests;
    public $deposit_amount;
    public $deposit_paid;
    public $is_group;
    public $group_id;
    public $total_price;
    public $created_at;
    public $updated_at;

    // ── CRUD ─────────────────────────────────────────────────

    public function all()
    {
        $sql = "SELECT r.*,
                       g.name           AS guest_name,
                       g.is_vip,
                       g.loyalty_tier,
                       rm.room_number,
                       rm.floor,
                       rt.name          AS room_type_name,
                       rt.base_price
                FROM   reservations r
                JOIN   guests    g  ON r.guest_id = g.id
                JOIN   rooms     rm ON r.room_id  = rm.id
                JOIN   room_types rt ON rm.room_type_id = rt.id
                ORDER  BY r.created_at DESC";

        $result = mysqli_query($this->db, $sql);
        if (!$result) die("Query Failed: " . mysqli_error($this->db));
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function find($id)
    {
        $id  = (int) $id;
        $sql = "SELECT r.*,
                       g.name           AS guest_name,
                       g.email          AS guest_email,
                       g.phone          AS guest_phone,
                       g.is_vip,
                       g.loyalty_tier,
                       g.is_blacklisted,
                       rm.room_number,
                       rm.floor,
                       rm.status        AS room_status,
                       rt.name          AS room_type_name,
                       rt.base_price
                FROM   reservations r
                JOIN   guests    g  ON r.guest_id = g.id
                JOIN   rooms     rm ON r.room_id  = rm.id
                JOIN   room_types rt ON rm.room_type_id = rt.id
                WHERE  r.id = $id
                LIMIT  1";

        $result = mysqli_query($this->db, $sql);
        if (!$result) die("Query Failed: " . mysqli_error($this->db));
        return mysqli_fetch_assoc($result);
    }

    public function create($data)
    {
        $guestId     = (int) $data['guest_id'];
        $roomId      = (int) $data['room_id'];
        $assignedBy  = !empty($data['assigned_by']) ? (int) $data['assigned_by'] : 'NULL';
        $checkIn     = mysqli_real_escape_string($this->db, $data['check_in_date']);
        $checkOut    = mysqli_real_escape_string($this->db, $data['check_out_date']);
        $adults      = (int) ($data['adults']   ?? 1);
        $children    = (int) ($data['children'] ?? 0);
        $specialReqs = mysqli_real_escape_string($this->db, $data['special_requests'] ?? '');
        $deposit     = (float) ($data['deposit_amount'] ?? 0);
        $depositPaid = isset($data['deposit_paid']) ? 1 : 0;
        $isGroup     = !empty($data['is_group']) ? 1 : 0;
        $groupId     = !empty($data['group_id']) ? (int) $data['group_id'] : 'NULL';
        $totalPrice  = (float) ($data['total_price'] ?? $this->calculatePrice($roomId, $checkIn, $checkOut));

        $sql = "INSERT INTO reservations
                    (guest_id, room_id, assigned_by, check_in_date, check_out_date,
                     status, adults, children, special_requests,
                     deposit_amount, deposit_paid, is_group, group_id, total_price)
                VALUES
                    ($guestId, $roomId, $assignedBy, '$checkIn', '$checkOut',
                     'pending', $adults, $children, '$specialReqs',
                     $deposit, $depositPaid, $isGroup, $groupId, $totalPrice)";

        $result = mysqli_query($this->db, $sql);
        if (!$result) die("Insert Failed: " . mysqli_error($this->db));
        $newId = mysqli_insert_id($this->db);

        // If group booking without an existing group lead, self-reference as group leader
        if ($isGroup && $groupId === 'NULL') {
            mysqli_query($this->db, "UPDATE reservations SET group_id = $newId WHERE id = $newId");
        }

        return $newId;
    }

    public function update($id, $data)
    {
        $id          = (int) $id;
        $guestId     = (int) $data['guest_id'];
        $roomId      = (int) $data['room_id'];
        $checkIn     = mysqli_real_escape_string($this->db, $data['check_in_date']);
        $checkOut    = mysqli_real_escape_string($this->db, $data['check_out_date']);
        $adults      = (int) ($data['adults']   ?? 1);
        $children    = (int) ($data['children'] ?? 0);
        $specialReqs = mysqli_real_escape_string($this->db, $data['special_requests'] ?? '');
        $deposit     = (float) ($data['deposit_amount'] ?? 0);
        $depositPaid = !empty($data['deposit_paid']) ? 1 : 0;
        $isGroup     = !empty($data['is_group']) ? 1 : 0;
        $groupId     = !empty($data['group_id']) ? (int) $data['group_id'] : 'NULL';
        $totalPrice  = (float) ($data['total_price'] ?? $this->calculatePrice($roomId, $checkIn, $checkOut));

        $sql = "UPDATE reservations SET
                    guest_id         = $guestId,
                    room_id          = $roomId,
                    check_in_date    = '$checkIn',
                    check_out_date   = '$checkOut',
                    adults           = $adults,
                    children         = $children,
                    special_requests = '$specialReqs',
                    deposit_amount   = $deposit,
                    deposit_paid     = $depositPaid,
                    is_group         = $isGroup,
                    group_id         = $groupId,
                    total_price      = $totalPrice
                WHERE id = $id";

        $result = mysqli_query($this->db, $sql);
        if (!$result) die("Update Failed: " . mysqli_error($this->db));
        return true;
    }

    public function delete($id)
    {
        $id     = (int) $id;
        $result = mysqli_query($this->db, "DELETE FROM reservations WHERE id = $id");
        if (!$result) die("Delete Failed: " . mysqli_error($this->db));
        return true;
    }

    // ── Relationships ────────────────────────────────────────

    public function guest()
    {
        $guestId = (int) $this->guest_id;
        $result  = mysqli_query($this->db, "SELECT * FROM guests WHERE id = $guestId LIMIT 1");
        return mysqli_fetch_assoc($result);
    }

    public function room()
    {
        $roomId = (int) $this->room_id;
        $sql    = "SELECT rooms.*, room_types.name AS type_name, room_types.base_price
                   FROM rooms
                   JOIN room_types ON rooms.room_type_id = room_types.id
                   WHERE rooms.id = $roomId LIMIT 1";
        $result = mysqli_query($this->db, $sql);
        return mysqli_fetch_assoc($result);
    }

    public function folio()
    {
        $id     = (int) $this->id;
        $result = mysqli_query($this->db, "SELECT * FROM folios WHERE reservation_id = $id LIMIT 1");
        return mysqli_fetch_assoc($result);
    }

    public function getFolioByReservation($reservationId)
    {
        $reservationId = (int) $reservationId;
        $result = mysqli_query($this->db, "SELECT * FROM folios WHERE reservation_id = $reservationId LIMIT 1");
        return $result ? mysqli_fetch_assoc($result) : null;
    }

    // ── Workflow Methods ─────────────────────────────────────

    public function confirm($id)
    {
        $id     = (int) $id;
        $result = mysqli_query($this->db,
            "UPDATE reservations SET status = 'confirmed'
             WHERE id = $id AND status = 'pending'");
        if (!$result) die("Confirm Failed: " . mysqli_error($this->db));
        return mysqli_affected_rows($this->db) > 0;
    }

    public function checkIn($id)
    {
        $id  = (int) $id;
        $res = mysqli_fetch_assoc(
            mysqli_query($this->db, "SELECT * FROM reservations WHERE id = $id LIMIT 1")
        );
        if (!$res) return false;

        $result = mysqli_query($this->db,
            "UPDATE reservations
             SET status = 'checked_in', actual_check_in = NOW()
             WHERE id = $id AND status IN ('pending','confirmed')");
        if (!$result) die("Check-in Failed: " . mysqli_error($this->db));

        // Update room → occupied
        $roomModel = new Room();
        try { $roomModel->updateStatus($res['room_id'], 'occupied'); } catch (Exception $e) {}

        // Audit log
        $userId  = (int) ($_SESSION['user_id'] ?? 0);
        $resId   = $id;
        mysqli_query($this->db,
            "INSERT INTO audit_log (user_id, action, target_type, target_id, old_value, new_value)
             VALUES ($userId, 'check_in', 'reservation', $resId, '{$res['status']}', 'checked_in')");

        return true;
    }

    public function checkOut($id)
    {
        $id  = (int) $id;
        $res = mysqli_fetch_assoc(
            mysqli_query($this->db, "SELECT * FROM reservations WHERE id = $id LIMIT 1")
        );
        if (!$res) return false;

        $result = mysqli_query($this->db,
            "UPDATE reservations
             SET status = 'checked_out', actual_check_out = NOW()
             WHERE id = $id AND status = 'checked_in'");
        if (!$result) die("Check-out Failed: " . mysqli_error($this->db));

        // Update room → dirty
        $roomModel = new Room();
        try { $roomModel->updateStatus($res['room_id'], 'dirty'); } catch (Exception $e) {}

        // Create housekeeping task
        $roomId = (int) $res['room_id'];
        $note   = mysqli_real_escape_string($this->db, "Post-checkout clean for reservation #$id");
        mysqli_query($this->db,
            "INSERT INTO housekeeping_tasks (room_id, task_type, status, notes)
             VALUES ($roomId, 'cleaning', 'pending', '$note')");

        // Update guest lifetime stats
        $nights     = $this->calculateNights($res['check_in_date'], $res['check_out_date']);
        $totalPrice = (float) $res['total_price'];
        $guestId    = (int) $res['guest_id'];
        mysqli_query($this->db,
            "UPDATE guests
             SET lifetime_nights = lifetime_nights + $nights,
                 lifetime_value  = lifetime_value  + $totalPrice
             WHERE id = $guestId");

        // Audit log
        $userId = (int) ($_SESSION['user_id'] ?? 0);
        mysqli_query($this->db,
            "INSERT INTO audit_log (user_id, action, target_type, target_id, old_value, new_value)
             VALUES ($userId, 'check_out', 'reservation', $id, 'checked_in', 'checked_out')");

        return true;
    }

    public function cancel($id)
    {
        $id     = (int) $id;
        $result = mysqli_query($this->db,
            "UPDATE reservations SET status = 'cancelled'
             WHERE id = $id AND status NOT IN ('checked_in','checked_out','cancelled')");
        if (!$result) die("Cancel Failed: " . mysqli_error($this->db));

        $userId = (int) ($_SESSION['user_id'] ?? 0);
        mysqli_query($this->db,
            "INSERT INTO audit_log (user_id, action, target_type, target_id, old_value, new_value)
             VALUES ($userId, 'status_change', 'reservation', $id, 'active', 'cancelled')");

        return mysqli_affected_rows($this->db) > 0;
    }

    public function markNoShow($id)
    {
        $id     = (int) $id;
        $result = mysqli_query($this->db,
            "UPDATE reservations SET status = 'no_show'
             WHERE id = $id AND status IN ('pending','confirmed')");
        if (!$result) die("No-show Failed: " . mysqli_error($this->db));

        $userId = (int) ($_SESSION['user_id'] ?? 0);
        mysqli_query($this->db,
            "INSERT INTO audit_log (user_id, action, target_type, target_id, old_value, new_value)
             VALUES ($userId, 'no_show', 'reservation', $id, 'confirmed', 'no_show')");

        return mysqli_affected_rows($this->db) > 0;
    }

    // ── Search / Filter ──────────────────────────────────────

    public function filter($params = [])
    {
        $conditions = ['1=1'];

        if (!empty($params['status'])) {
            $s = mysqli_real_escape_string($this->db, $params['status']);
            $conditions[] = "r.status = '$s'";
        }
        if (!empty($params['date_from'])) {
            $d = mysqli_real_escape_string($this->db, $params['date_from']);
            $conditions[] = "r.check_in_date >= '$d'";
        }
        if (!empty($params['date_to'])) {
            $d = mysqli_real_escape_string($this->db, $params['date_to']);
            $conditions[] = "r.check_out_date <= '$d'";
        }
        if (!empty($params['guest_name'])) {
            $kw = '%' . mysqli_real_escape_string($this->db, $params['guest_name']) . '%';
            $conditions[] = "g.name LIKE '$kw'";
        }

        $where = implode(' AND ', $conditions);

        $sql = "SELECT r.*,
                       g.name        AS guest_name,
                       g.is_vip,
                       rm.room_number,
                       rt.name       AS room_type_name,
                       rt.base_price
                FROM   reservations r
                JOIN   guests     g  ON r.guest_id = g.id
                JOIN   rooms      rm ON r.room_id  = rm.id
                JOIN   room_types rt ON rm.room_type_id = rt.id
                WHERE  $where
                ORDER  BY r.created_at DESC";

        $result = mysqli_query($this->db, $sql);
        if (!$result) die("Query Failed: " . mysqli_error($this->db));
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function findByDateRange($from, $to)
    {
        $from   = mysqli_real_escape_string($this->db, $from);
        $to     = mysqli_real_escape_string($this->db, $to);
        $sql    = "SELECT r.*, g.name AS guest_name, rm.room_number
                   FROM reservations r
                   JOIN guests g ON r.guest_id = g.id
                   JOIN rooms rm ON r.room_id  = rm.id
                   WHERE r.check_in_date >= '$from' AND r.check_out_date <= '$to'
                   ORDER BY r.check_in_date ASC";
        $result = mysqli_query($this->db, $sql);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function findByStatus($status)
    {
        $status = mysqli_real_escape_string($this->db, $status);
        $sql    = "SELECT r.*, g.name AS guest_name, rm.room_number
                   FROM reservations r
                   JOIN guests g ON r.guest_id = g.id
                   JOIN rooms rm ON r.room_id  = rm.id
                   WHERE r.status = '$status'
                   ORDER BY r.check_in_date DESC";
        $result = mysqli_query($this->db, $sql);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function findByGuest($guestId)
    {
        $guestId = (int) $guestId;
        $sql     = "SELECT r.*, rm.room_number, rt.name AS room_type_name
                    FROM reservations r
                    JOIN rooms rm ON r.room_id = rm.id
                    JOIN room_types rt ON rm.room_type_id = rt.id
                    WHERE r.guest_id = $guestId
                    ORDER BY r.check_in_date DESC";
        $result  = mysqli_query($this->db, $sql);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function findGroupReservations($groupId)
    {
        $groupId = (int) $groupId;
        $sql     = "SELECT r.*, g.name AS guest_name, rm.room_number
                    FROM reservations r
                    JOIN guests g  ON r.guest_id = g.id
                    JOIN rooms  rm ON r.room_id  = rm.id
                    WHERE r.group_id = $groupId
                    ORDER BY r.check_in_date ASC";
        $result  = mysqli_query($this->db, $sql);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    // ── Price Calculation ────────────────────────────────────

    public function calculatePrice($roomId, $checkIn, $checkOut)
    {
        $roomId = (int) $roomId;
        $result = mysqli_query($this->db,
            "SELECT rt.base_price
             FROM rooms r
             JOIN room_types rt ON r.room_type_id = rt.id
             WHERE r.id = $roomId LIMIT 1");
        $row = mysqli_fetch_assoc($result);
        if (!$row) return 0;
        $nights = $this->calculateNights($checkIn, $checkOut);
        return round((float) $row['base_price'] * $nights, 2);
    }

    private function calculateNights($checkIn, $checkOut)
    {
        try {
            $in  = new DateTime($checkIn);
            $out = new DateTime($checkOut);
            return max(0, (int) $in->diff($out)->format('%a'));
        } catch (Exception $e) {
            return 0;
        }
    }

    // ── Auto-create Folio ────────────────────────────────────

    public function createFolio($reservationId, $totalAmount = 0.00)
    {
        $reservationId = (int) $reservationId;
        $totalAmount   = (float) $totalAmount;

        // Avoid duplicate folios
        $check = mysqli_query($this->db,
            "SELECT id FROM folios WHERE reservation_id = $reservationId LIMIT 1");
        if (mysqli_num_rows($check) > 0) {
            return mysqli_fetch_assoc($check)['id'];
        }

        mysqli_query($this->db,
            "INSERT INTO folios (reservation_id, total_amount, amount_paid, status)
             VALUES ($reservationId, $totalAmount, 0.00, 'open')");
        return mysqli_insert_id($this->db);
    }

    // ── Early Check-In ───────────────────────────────────────

    public function checkEarlyCheckInEligibility($id)
    {
        $id  = (int) $id;
        $res = mysqli_fetch_assoc(
            mysqli_query($this->db, "SELECT room_id FROM reservations WHERE id = $id LIMIT 1")
        );
        if (!$res) return false;

        $roomId = (int) $res['room_id'];
        $result = mysqli_query($this->db,
            "SELECT COUNT(*) AS cnt
             FROM housekeeping_tasks
             WHERE room_id = $roomId
               AND status = 'done'
               AND task_type IN ('cleaning','inspection')");
        $row = mysqli_fetch_assoc($result);
        return $row && (int) $row['cnt'] > 0;
    }
}
