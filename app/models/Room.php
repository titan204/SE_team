<?php


class Room extends AbstractModel
{
    protected $id;
    protected $room_type_id;
    protected $room_number;
    protected $floor;
    protected $status;       // available, occupied, dirty, cleaning, inspecting, out_of_order
    protected $notes;
    protected $created_at;
    protected $updated_at;
    private $features;

    public function __construct($db = null, $features = null, array $aggregates = [])
    {
        parent::__construct($db, $aggregates);
        $this->features = $features ?: new RoomFeatures();
        $this->registerAggregate('roomType', RoomType::class);
        $this->registerAggregate('reservations', Reservation::class);
        $this->registerAggregate('housekeepingTasks', HousekeepingTask::class);
        $this->registerAggregate('maintenanceOrders', MaintenanceOrder::class);
    }

    public function getRoomFeatures()
    {
        return $this->features;
    }

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
            "SELECT rooms.*,
                    room_types.name AS type_name,
                    room_types.base_price AS base_price,
                    room_types.description AS type_description,
                    room_types.capacity AS capacity
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
            throw new Exception("Cannot delete: room has {$row['cnt']} active reservation(s).");
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
    public function updateStatus($roomId, $newStatus = null)
    {
        if ($newStatus === null) {
            $newStatus = $roomId;
            $roomId = $this->id;
        }

        if (empty($roomId)) {
            throw new Exception("Room ID is required to update status.");
        }

        // Fetch current status
        $stmt = mysqli_prepare($this->db, "SELECT status FROM rooms WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $roomId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        if (!$row) {
            throw new Exception("Room not found.");
        }

        $current = $row['status'];

        // Validate that newStatus is a known status
        $allStatuses = array_keys($this->transitions);
        if (!in_array($newStatus, $allStatuses)) {
            throw new Exception("Invalid status: '$newStatus'.");
        }

        // Validate transition
        $allowed = $this->transitions[$current] ?? [];
        if (!in_array($newStatus, $allowed)) {
            throw new Exception("Transition from '$current' to '$newStatus' is not allowed.");
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
        $sql = "SELECT rooms.*,
                        room_types.name AS type_name,
                        room_types.base_price AS base_price,
                        room_types.description AS type_description,
                        room_types.capacity AS capacity
                 FROM   rooms
                 JOIN   room_types ON rooms.room_type_id = room_types.id
                 WHERE  rooms.status = 'available'";

        if ($typeId) {
            $sql .= " AND rooms.room_type_id = ?";
        }

        $sql .= " AND rooms.id NOT IN (
                            SELECT DISTINCT room_id
                            FROM   reservations
                            WHERE  status NOT IN ('cancelled', 'no_show', 'checked_out')
                              AND  check_in_date  < ?
                              AND  check_out_date > ?
                        )
                 ORDER BY rooms.room_number ASC";

        $stmt = mysqli_prepare($this->db, $sql);
        
        if ($typeId) {
            mysqli_stmt_bind_param($stmt, 'iss', $typeId, $checkOut, $checkIn);
        } else {
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

    /**
     * Dynamic room allocation:
     * - excludes overlapping reservations (reuses findAvailable)
     * - applies capacity/budget/preference matching
     * - returns rooms ordered by best match
     */
    public function getBestRoomsForClient($clientData)
    {
        $request = $this->normalizeAllocationInput((array) $clientData);

        if ($request['check_in_date'] === '' || $request['check_out_date'] === '') {
            throw new Exception('check_in_date and check_out_date are required.');
        }

        if ($request['check_in_date'] >= $request['check_out_date']) {
            throw new Exception('check_out_date must be after check_in_date.');
        }

        $available = $this->findAvailable(
            $request['check_in_date'],
            $request['check_out_date'],
            $request['preferred_room_type_id']
        );

        // If preferred type has no inventory, still return best alternatives.
        if (empty($available) && !empty($request['preferred_room_type_id'])) {
            $available = $this->findAvailable($request['check_in_date'], $request['check_out_date']);
        }

        if (empty($available)) {
            return [];
        }

        $floorRange = $this->getFloorRange($available);
        $ranked = [];

        foreach ($available as $room) {
            $capacity = (int) ($room['capacity'] ?? 0);
            if ($capacity > 0 && $capacity < $request['guest_count']) {
                continue;
            }

            $room['match_score'] = $this->scoreRoomForClient($room, $request, $floorRange);
            $ranked[] = $room;
        }

        usort($ranked, function ($a, $b) {
            $scoreComparison = ((float) $b['match_score']) <=> ((float) $a['match_score']);
            if ($scoreComparison !== 0) {
                return $scoreComparison;
            }

            $priceComparison = ((float) $a['base_price']) <=> ((float) $b['base_price']);
            if ($priceComparison !== 0) {
                return $priceComparison;
            }

            return strcmp((string) $a['room_number'], (string) $b['room_number']);
        });

        if ($request['result_limit'] > 0) {
            return array_slice($ranked, 0, $request['result_limit']);
        }

        return $ranked;
    }

    private function normalizeAllocationInput(array $clientData)
    {
        $checkIn = $this->normalizeDate(
            $this->pickFirstValue($clientData, ['check_in_date', 'check_in', 'checkInDate', 'arrival_date'])
        );
        $checkOut = $this->normalizeDate(
            $this->pickFirstValue($clientData, ['check_out_date', 'check_out', 'checkOutDate', 'departure_date'])
        );

        $adults = (int) ($this->pickFirstValue($clientData, ['adults']) ?: 1);
        $children = (int) ($this->pickFirstValue($clientData, ['children']) ?: 0);
        $guestCount = (int) ($this->pickFirstValue($clientData, ['guest_count', 'guests', 'total_guests']) ?: 0);
        if ($guestCount <= 0) {
            $guestCount = max(1, $adults + max(0, $children));
        }

        $guestId = (int) ($this->pickFirstValue($clientData, ['guest_id']) ?: 0);
        $guestPreferences = $this->loadGuestPreferences($guestId);

        $preferredTypeId = (int) ($this->pickFirstValue($clientData, ['room_type_id', 'preferred_room_type_id', 'type_id']) ?: 0);
        $preferredTypeName = trim((string) $this->pickFirstValue($clientData, ['room_type', 'preferred_room_type', 'room_type_name']));
        if ($preferredTypeId <= 0 && $preferredTypeName !== '') {
            $preferredTypeId = (int) $this->findRoomTypeIdByName($preferredTypeName);
        }
        if ($preferredTypeId <= 0 && !empty($guestPreferences['room_type'])) {
            $preferredTypeId = (int) $this->findRoomTypeIdByName($guestPreferences['room_type']);
        }
        if ($preferredTypeId <= 0) {
            $preferredTypeId = null;
        }

        $floorPreferenceRaw = $this->pickFirstValue($clientData, ['floor_preference', 'preferred_floor', 'floor']);
        if ($floorPreferenceRaw === null || $floorPreferenceRaw === '') {
            $floorPreferenceRaw = $guestPreferences['floor_preference'] ?? ($guestPreferences['floor_level_preference'] ?? null);
        }
        $floorPreference = $this->normalizeFloorPreference($floorPreferenceRaw);

        $nights = $this->calculateNights($checkIn, $checkOut);
        $maxBudgetPerNight = $this->normalizeBudget($clientData, $nights);

        $resultLimit = (int) ($this->pickFirstValue($clientData, ['limit', 'max_results']) ?: 0);
        if ($resultLimit < 0) {
            $resultLimit = 0;
        }

        return [
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
            'guest_count' => $guestCount,
            'preferred_room_type_id' => $preferredTypeId,
            'floor_preference' => $floorPreference,
            'max_budget_per_night' => $maxBudgetPerNight,
            'result_limit' => $resultLimit,
        ];
    }

    private function pickFirstValue(array $source, array $keys)
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $source)) {
                return $source[$key];
            }
        }

        return null;
    }

    private function normalizeDate($value)
    {
        $raw = trim((string) $value);
        if ($raw === '') {
            return '';
        }

        $raw = substr($raw, 0, 10);
        $date = DateTime::createFromFormat('Y-m-d', $raw);

        if (!$date || $date->format('Y-m-d') !== $raw) {
            return '';
        }

        return $raw;
    }

    private function calculateNights($checkIn, $checkOut)
    {
        if ($checkIn === '' || $checkOut === '') {
            return 0;
        }

        $in = DateTime::createFromFormat('Y-m-d', $checkIn);
        $out = DateTime::createFromFormat('Y-m-d', $checkOut);
        if (!$in || !$out) {
            return 0;
        }

        return max(0, (int) $in->diff($out)->format('%a'));
    }

    private function normalizeBudget(array $clientData, $nights)
    {
        $perNightCandidates = ['budget_per_night', 'max_budget', 'budget', 'max_price', 'price_limit'];
        foreach ($perNightCandidates as $key) {
            if (!array_key_exists($key, $clientData)) {
                continue;
            }

            $value = (float) $clientData[$key];
            if ($value > 0) {
                return $value;
            }
        }

        if (array_key_exists('total_budget', $clientData)) {
            $totalBudget = (float) $clientData['total_budget'];
            if ($totalBudget > 0 && $nights > 0) {
                return $totalBudget / $nights;
            }
        }

        return null;
    }

    private function loadGuestPreferences($guestId)
    {
        if ($guestId <= 0) {
            return [];
        }

        $guestPreferenceModel = new GuestPreference();
        $rows = $guestPreferenceModel->findByGuest($guestId);
        if (empty($rows)) {
            return [];
        }

        $preferences = [];
        foreach ($rows as $row) {
            $key = trim((string) ($row['pref_key'] ?? ''));
            if ($key === '') {
                continue;
            }

            $preferences[$key] = trim((string) ($row['pref_value'] ?? ''));
        }

        return $preferences;
    }

    private function findRoomTypeIdByName($name)
    {
        $normalizedName = trim((string) $name);
        if ($normalizedName === '') {
            return null;
        }

        if (ctype_digit($normalizedName)) {
            return (int) $normalizedName;
        }

        $stmt = mysqli_prepare(
            $this->db,
            "SELECT id
             FROM room_types
             WHERE LOWER(name) = LOWER(?)
             LIMIT 1"
        );
        mysqli_stmt_bind_param($stmt, 's', $normalizedName);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        return $row ? (int) $row['id'] : null;
    }

    private function normalizeFloorPreference($value)
    {
        if ($value === null) {
            return null;
        }

        $raw = trim((string) $value);
        if ($raw === '') {
            return null;
        }

        if (ctype_digit($raw)) {
            return (int) $raw;
        }

        $normalized = strtolower(str_replace(['-', '_'], ' ', $raw));
        if (in_array($normalized, ['high', 'high floor', 'upper floor', 'top floor'], true)) {
            return 'high';
        }

        if (in_array($normalized, ['low', 'low floor', 'lower floor'], true)) {
            return 'low';
        }

        return null;
    }

    private function getFloorRange(array $rooms)
    {
        if (empty($rooms)) {
            return ['min' => null, 'max' => null];
        }

        $floors = array_map(function ($room) {
            return (int) ($room['floor'] ?? 0);
        }, $rooms);

        return [
            'min' => min($floors),
            'max' => max($floors),
        ];
    }

    private function scoreRoomForClient(array $room, array $request, array $floorRange)
    {
        $score = 0.0;
        $capacity = (int) ($room['capacity'] ?? 0);
        $guestCount = (int) $request['guest_count'];
        $price = (float) ($room['base_price'] ?? 0.0);
        $roomFloor = (int) ($room['floor'] ?? 0);

        // Capacity fit: tighter fit ranks higher.
        if ($capacity > 0) {
            $difference = $capacity - $guestCount;
            if ($difference === 0) {
                $score += 35;
            } elseif ($difference === 1) {
                $score += 28;
            } elseif ($difference === 2) {
                $score += 20;
            } elseif ($difference > 2) {
                $score += 12;
            }
        } else {
            $score += 10;
        }

        // Budget fit.
        if ($request['max_budget_per_night'] !== null) {
            $budget = (float) $request['max_budget_per_night'];
            if ($price <= $budget) {
                $score += 30;
            } else {
                $overRatio = ($price - $budget) / max($budget, 1.0);
                $score -= min(25, $overRatio * 30);
            }
        } else {
            $score += 15;
        }

        // Room type preference.
        if (!empty($request['preferred_room_type_id'])) {
            if ((int) $room['room_type_id'] === (int) $request['preferred_room_type_id']) {
                $score += 25;
            } else {
                $score -= 8;
            }
        }

        // Floor preference.
        if ($request['floor_preference'] !== null) {
            $preference = $request['floor_preference'];

            if (is_int($preference)) {
                if ($roomFloor === $preference) {
                    $score += 20;
                } else {
                    $score -= min(10, abs($roomFloor - $preference) * 3);
                }
            } elseif ($preference === 'high' && $floorRange['max'] !== null) {
                if ($roomFloor >= ($floorRange['max'] - 1)) {
                    $score += 15;
                }
            } elseif ($preference === 'low' && $floorRange['min'] !== null) {
                if ($roomFloor <= ($floorRange['min'] + 1)) {
                    $score += 15;
                }
            }
        }

        // Tie-break helper: slight preference for lower nightly price.
        $score += max(0, 5 - ($price / 1000));

        return round($score, 2);
    }
}
