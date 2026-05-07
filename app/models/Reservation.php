<?php

class Reservation extends AbstractReservation
{
    protected $id;
    protected $guest_id;
    protected $room_id;
    protected $assigned_by;
    protected $check_in_date;
    protected $check_out_date;
    protected $actual_check_in;
    protected $actual_check_out;
    protected $status;         // pending, confirmed, checked_in, checked_out, cancelled, no_show
    protected $adults;
    protected $children;
    protected $special_requests;
    protected $deposit_amount;
    protected $deposit_paid;
    protected $is_group;
    protected $group_id;
    protected $total_price;
    protected $created_at;
    protected $updated_at;

    public function __construct($db = null, $reservationDetails = null, array $aggregates = [])
    {
        parent::__construct($db, $reservationDetails, $aggregates);
        $this->registerAggregate('guest', Guest::class);
        $this->registerAggregate('room', Room::class);
        $this->registerAggregate('folio', Folio::class);
        $this->registerAggregate('serviceBookings', ServiceBooking::class);
    }

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
        $this->hydrateReservationDetails($data);
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
        if (!$result) {
            throw new \RuntimeException('Reservation could not be saved: ' . mysqli_error($this->db));
        }
        $newId = mysqli_insert_id($this->db);

        // If group booking without an existing group lead, self-reference as group leader
        if ($isGroup && $groupId === 'NULL') {
            mysqli_query($this->db, "UPDATE reservations SET group_id = $newId WHERE id = $newId");
        }

        return $newId;
    }

    public function update($id, $data)
    {
        $this->hydrateReservationDetails($data);
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
        $roomModel = $this->getAggregate('room') ?: new Room($this->db);
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
        $roomModel = $this->getAggregate('room') ?: new Room($this->db);
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
    $sql     = "SELECT r.*,
                       r.check_in_date  AS check_in,
                       r.check_out_date AS check_out,
                       rm.room_number,
                       rt.name AS room_type_name
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
            "INSERT INTO folios (reservation_id, total_amount, amount_paid, balance_due, status)
             VALUES ($reservationId, $totalAmount, 0.00, $totalAmount, 'open')");
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

    // ── UC: Flag Special Occasion (helper method only) ───────
    //
    // IMPORTANT: This is a STANDALONE HELPER — it MUST NOT be called
    // automatically, must NOT write to the database, and must NOT
    // connect to any reservation workflow.
    //
    // Logic: returns true when the guest's date_of_birth month+day
    // matches the reservation check-in month+day (birthday stay).
    //
    // Usage example (call explicitly when needed):
    //   $reservationModel->flagSpecialOccasion($guestData, $reservation['check_in_date']);
    //
    // @param  array  $guestData    Guest record (must contain 'date_of_birth')
    // @param  string $checkInDate  Reservation check-in date (Y-m-d). Defaults to today.
    // @return bool                 true if guest's birthday falls on the check-in date

    public function flagSpecialOccasion(array $guestData, string $checkInDate = ''): bool
    {
        $dob = trim((string) ($guestData['date_of_birth'] ?? ''));

        // Cannot determine birthday without a date_of_birth value
        if ($dob === '') {
            return false;
        }

        // Use provided check-in date, or fall back to today
        $checkIn = ($checkInDate !== '') ? $checkInDate : date('Y-m-d');

        try {
            $dobDate     = new DateTime($dob);
            $checkInDate = new DateTime($checkIn);
        } catch (\Exception $e) {
            return false; // Malformed date — fail gracefully
        }

        // Compare month and day only (birthday regardless of year)
        return $dobDate->format('m-d') === $checkInDate->format('m-d');
    }

    // ── UC: Trigger VIP Flag (EXTEND — conditional only) ─────
    //
    // EXTEND use case: executes ONLY when guest is marked as VIP.
    // Mutates the reservation data array in-memory so that is_vip
    // is propagated into the reservation record at creation time.
    // Does NOT auto-apply for non-VIP guests.
    //
    // @param  array  $reservationData  Reservation data array (passed by reference)
    // @param  array  $guestData        Guest record (must contain 'is_vip')
    // @return bool                     true if VIP flag was applied, false otherwise

    public function applyVipFlag(array &$reservationData, array $guestData): bool
    {
        // EXTEND condition: only runs when the guest is marked as VIP
        if (empty($guestData['is_vip'])) {
            return false; // Not a VIP — extension does not execute
        }

        // Mark the reservation data so the reservation record carries the VIP flag.
        // This can be used for UI highlighting, reporting, or priority queuing later.
        $reservationData['is_vip'] = 1;

        return true; // VIP flag applied
    }

    // ── UC: Suggest Room Upgrade (EXTEND Room Allocation) ────
    //
    // EXTEND use case: executes ONLY when guest has high loyalty status
    // (gold or platinum). Returns a room suggestion — does NOT auto-apply.
    //
    // Design note: Room.php is owned by Dev 2 (read-only).
    // This method lives here and calls Room::suggestUpgrade() externally,
    // preserving Room.php exactly as written.
    //
    // @param  array  $guestData     Guest record (must contain 'loyalty_tier')
    // @param  int    $currentRoomId Currently allocated room ID
    // @param  string $checkIn       Check-in date  (Y-m-d)
    // @param  string $checkOut      Check-out date (Y-m-d)
    // @return array|false           Suggested upgrade room row, or false

    public function suggestUpgradeForLoyalty(
        array  $guestData,
        int    $currentRoomId,
        string $checkIn,
        string $checkOut
    ) {
        // EXTEND condition: only fires for gold / platinum loyalty tiers
        $tier = strtolower(trim((string) ($guestData['loyalty_tier'] ?? '')));

        if (!in_array($tier, ['gold', 'platinum'], true)) {
            return false; // Loyalty level too low — extension does not execute
        }

        // Delegate to Dev 2's existing Room::suggestUpgrade() — called externally,
        // Room.php is not modified in any way.
        $roomModel = $this->getAggregate('room') ?: new Room($this->db);
        return $roomModel->suggestUpgrade($currentRoomId, $checkIn, $checkOut);
    }

    // ── UC: Accept Room Upgrade ───────────────────────────────
    //
    // Converts a suggestion into an actual room switch.
    // Called explicitly by the controller only when the guest/staff
    // clicks "Accept Upgrade" — NEVER triggered automatically.
    //
    // Actions performed:
    //   1. Validate new room (must be available & not out_of_order)
    //   2. Update reservation.room_id to $newRoomId
    //   3. Set old room → available
    //   4. Set new room → occupied (if reservation is checked_in)
    //      or keep as available (if pending/confirmed, reserved via booking)
    //   5. Write audit log entry
    //
    // @param  int  $reservationId
    // @param  int  $newRoomId
    // @return bool  true on success
    // @throws Exception on validation failure

    public function acceptUpgrade(int $reservationId, int $newRoomId): bool
    {
        $reservationId = (int) $reservationId;
        $newRoomId     = (int) $newRoomId;

        // Load reservation
        $res = mysqli_fetch_assoc(
            mysqli_query($this->db,
                "SELECT * FROM reservations WHERE id = $reservationId LIMIT 1")
        );
        if (!$res) {
            throw new \Exception("Reservation #$reservationId not found.");
        }

        $oldRoomId = (int) $res['room_id'];

        // Guard: no-op if same room
        if ($oldRoomId === $newRoomId) {
            throw new \Exception("New room is the same as the current room.");
        }

        // Validate new room — must exist, be available, not out_of_order
        $newRoom = mysqli_fetch_assoc(
            mysqli_query($this->db,
                "SELECT rooms.*, room_types.base_price, room_types.capacity
                 FROM rooms
                 JOIN room_types ON rooms.room_type_id = room_types.id
                 WHERE rooms.id = $newRoomId LIMIT 1")
        );
        if (!$newRoom) {
            throw new \Exception("Room #$newRoomId does not exist.");
        }
        if ($newRoom['status'] === 'out_of_order') {
            throw new \Exception("Room #$newRoomId is out of order and cannot be assigned.");
        }
        if ($newRoom['status'] !== 'available') {
            throw new \Exception("Room #$newRoomId is not available (status: {$newRoom['status']}).");
        }

        // ── 1. Swap room_id on the reservation ────────────────
        $updated = mysqli_query($this->db,
            "UPDATE reservations SET room_id = $newRoomId WHERE id = $reservationId");
        if (!$updated) {
            throw new \Exception("Failed to update reservation: " . mysqli_error($this->db));
        }

        // ── 2. Release old room back to available ─────────────
        // Uses direct UPDATE (bypasses Room::updateStatus state machine)
        // because the transition available→occupied already happened at check-in;
        // we are simply re-assigning, not changing lifecycle state.
        mysqli_query($this->db,
            "UPDATE rooms SET status = 'available' WHERE id = $oldRoomId");

        // ── 3. Mark new room status ───────────────────────────
        $newRoomStatus = ($res['status'] === 'checked_in') ? 'occupied' : 'available';
        mysqli_query($this->db,
            "UPDATE rooms SET status = '$newRoomStatus' WHERE id = $newRoomId");

        // ── 4. Audit log ──────────────────────────────────────
        $userId = (int) ($_SESSION['user_id'] ?? 0);
        $note   = mysqli_real_escape_string($this->db,
            "Room upgrade accepted: room #$oldRoomId → #$newRoomId");
        mysqli_query($this->db,
            "INSERT INTO audit_log (user_id, action, target_type, target_id, old_value, new_value)
             VALUES ($userId, 'room_upgrade', 'reservation', $reservationId,
                     '$oldRoomId', '$newRoomId')");

        return true;
    }



    // ── UC: No-Show Penalty (INCLUDE structure) ───────────────

    //
    // INCLUDE relationships — this use case MANDATORILY includes:
    //   1. chargeGuestCard()   → «include» Charge Guest Card
    //   2. notifyGuestNoShow() → «include» Notify Guest
    //
    // Orchestrates the full no-show penalty flow without touching
    // the existing markNoShow() method.
    //
    // @param  int   $id            Reservation ID
    // @param  float $penaltyAmount Amount to charge (default: one night's rate)
    // @return array                Result summary with 'charged' and 'notified' keys

    public function applyNoShowPenalty(int $id, float $penaltyAmount = 0.0): array
    {
        $id  = (int) $id;
        $res = mysqli_fetch_assoc(
            mysqli_query($this->db,
                "SELECT r.*, g.name AS guest_name, g.email AS guest_email
                 FROM reservations r
                 JOIN guests g ON r.guest_id = g.id
                 WHERE r.id = $id LIMIT 1")
        );

        if (!$res) {
            return ['charged' => false, 'notified' => false, 'error' => 'Reservation not found'];
        }

        // If no explicit penalty amount provided, default to the reservation's total price
        if ($penaltyAmount <= 0.0) {
            $penaltyAmount = (float) ($res['total_price'] ?? 0.0);
        }

        // «include» Charge Guest Card — mandatory step 1
        $charged = $this->chargeGuestCard($res, $penaltyAmount);

        // «include» Notify Guest — mandatory step 2
        $notified = $this->notifyGuestNoShow($res);

        return [
            'charged'        => $charged,
            'notified'       => $notified,
            'penalty_amount' => $penaltyAmount,
        ];
    }

    // ── INCLUDE: Charge Guest Card ────────────────────────────
    //
    // Mandatory sub-step of applyNoShowPenalty().
    // Records a penalty charge on the reservation's folio.
    // Does NOT call markNoShow() — separation of concerns preserved.
    //
    // @param  array $reservation  Full reservation+guest row
    // @param  float $amount       Penalty amount to charge
    // @return bool

    private function chargeGuestCard(array $reservation, float $amount): bool
    {
        if ($amount <= 0.0) {
            return false;
        }

        $reservationId = (int) $reservation['id'];
        $amount        = round($amount, 2);
        $description   = mysqli_real_escape_string(
            $this->db,
            "No-show penalty for reservation #{$reservationId}"
        );

        // Append a charge row to the folio (if one exists for this reservation)
        $folioRow = mysqli_fetch_assoc(
            mysqli_query($this->db,
                "SELECT id FROM folios WHERE reservation_id = $reservationId LIMIT 1")
        );

        if (!$folioRow) {
            return false; // No folio — cannot charge
        }

        $folioId = (int) $folioRow['id'];

        $postedBy = (int) ($_SESSION['user_id'] ?? 0) ?: 'NULL';

        $result = mysqli_query($this->db,
            "INSERT INTO folio_charges (folio_id, charge_type, description, amount, posted_by)
             VALUES ($folioId, 'penalty', '$description', $amount, $postedBy)");

        if (!$result) {
            return false; // folio_charges table may not exist — fail gracefully
        }

        // Update the folio total
        mysqli_query($this->db,
            "UPDATE folios
             SET total_amount = total_amount + $amount
             WHERE id = $folioId");

        return true;
    }

    // ── INCLUDE: Notify Guest (No-Show) ──────────────────────
    //
    // Mandatory sub-step of applyNoShowPenalty().
    // Logs a notification record so the guest is informed of the
    // no-show penalty. Actual email delivery is handled by an
    // external mail service and is outside this model's scope.
    //
    // @param  array $reservation  Full reservation+guest row
    // @return bool

    private function notifyGuestNoShow(array $reservation): bool
    {
        $reservationId = (int) $reservation['id'];
        $guestId       = (int) $reservation['guest_id'];
        $message       = mysqli_real_escape_string(
            $this->db,
            "You have been marked as a no-show for reservation #{$reservationId}. "
            . "A penalty charge has been applied to your folio."
        );

        // Write to audit_log as a notification record
        $userId = (int) ($_SESSION['user_id'] ?? 0);
        $result = mysqli_query($this->db,
            "INSERT INTO audit_log (user_id, action, target_type, target_id, old_value, new_value)
             VALUES ($userId, 'no_show_notify', 'reservation', $reservationId,
                     '$guestId', '$message')");

        return (bool) $result;
    }
}
