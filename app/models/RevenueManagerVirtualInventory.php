<?php


class RevenueManagerVirtualInventory extends AbstractReport
{
    public function __construct($db = null, array $aggregates = [])
    {
        parent::__construct($db, $aggregates);
        $this->setReportScope('virtual_inventory');
        $this->setReportInputs(['room_type_id', 'date', 'days']);
        $this->registerAggregate('rooms', Room::class);
        $this->registerAggregate('roomTypes', RoomType::class);
        $this->registerAggregate('reservations', Reservation::class);
    }


    
    public function getPhysicalRooms($roomTypeId)
    {
        $roomTypeId = (int) $roomTypeId;
        $r = mysqli_query($this->db,
            "SELECT COUNT(*) AS cnt FROM rooms
             WHERE room_type_id = $roomTypeId AND status != 'out_of_order'");
        $row = mysqli_fetch_assoc($r);
        return (int) ($row['cnt'] ?? 0);
    }

    
    public function getRoomTypesWithCount()
    {
        $r = mysqli_query($this->db,
            "SELECT rt.id, rt.name,
                    COUNT(rm.id) AS physical_rooms
             FROM   room_types rt
             LEFT   JOIN rooms rm ON rm.room_type_id = rt.id
                         AND rm.status != 'out_of_order'
             GROUP  BY rt.id, rt.name
             ORDER  BY rt.id");
        if (!$r) die("Query Failed: " . mysqli_error($this->db));
        return mysqli_fetch_all($r, MYSQLI_ASSOC);
    }

    
    public function getInventoryGrid($days = 30)
    {
        $roomTypes = $this->getRoomTypesWithCount();
        $dates = [];
        for ($i = 0; $i < $days; $i++) {
            $dates[] = date('Y-m-d', strtotime("+$i days"));
        }
        $dateFrom = $dates[0];
        $dateTo   = $dates[$days - 1];

        
        $viResult = mysqli_query($this->db,
            "SELECT room_type_id, DATE(date) AS date,
                    physical_rooms, virtual_max, confirmed_count, updated_at
             FROM   virtual_inventory
             WHERE  date BETWEEN '$dateFrom' AND '$dateTo'");
        $viMap = [];
        while ($row = mysqli_fetch_assoc($viResult)) {
            $viMap[$row['room_type_id']][$row['date']] = $row;
        }

        
        $resResult = mysqli_query($this->db,
            "SELECT r.room_id, rm.room_type_id, r.check_in_date, r.check_out_date
             FROM   reservations r
             JOIN   rooms rm ON r.room_id = rm.id
             WHERE  r.status IN ('confirmed','checked_in')
               AND  r.check_in_date  <= '$dateTo'
               AND  r.check_out_date >  '$dateFrom'");
        $confirmedMap = [];
        while ($row = mysqli_fetch_assoc($resResult)) {
            $rtId = $row['room_type_id'];
            foreach ($dates as $d) {
                if ($d >= $row['check_in_date'] && $d < $row['check_out_date']) {
                    $confirmedMap[$rtId][$d] = ($confirmedMap[$rtId][$d] ?? 0) + 1;
                }
            }
        }

        
        $grid = [];
        foreach ($roomTypes as $rt) {
            $rtId        = $rt['id'];
            $physDefault = (int) $rt['physical_rooms'];
            foreach ($dates as $d) {
                $vi        = $viMap[$rtId][$d] ?? null;
                $physical  = $vi ? (int) $vi['physical_rooms'] : $physDefault;
                $virtMax   = $vi ? (int) $vi['virtual_max']    : $physical;
                $confirmed = $confirmedMap[$rtId][$d] ?? 0;
                $available = $virtMax - $confirmed;
                $pct       = $virtMax > 0 ? ($available / $virtMax) : 0;
                $color     = $available <= 0 ? 'red' : ($pct <= 0.2 ? 'yellow' : 'green');
                $updatedAt = $vi['updated_at'] ?? null;
                $grid[$rtId][$d] = compact(
                    'physical','virtMax','confirmed','available','color','updatedAt'
                );
            }
        }

        return ['roomTypes' => $roomTypes, 'dates' => $dates, 'grid' => $grid];
    }

    
    public function upsertVirtualMax($roomTypeId, $date, $physical, $newVirtualMax)
    {
        $roomTypeId    = (int)   $roomTypeId;
        $date          = mysqli_real_escape_string($this->db, $date);
        $physical      = (int)   $physical;
        $newVirtualMax = (int)   $newVirtualMax;
        $userId        = (int)   ($_SESSION['user_id'] ?? 0);

        $sql = "INSERT INTO virtual_inventory
                    (room_type_id, date, physical_rooms, virtual_max, confirmed_count, updated_by_user_id)
                VALUES ($roomTypeId, '$date', $physical, $newVirtualMax, 0, $userId)
                ON DUPLICATE KEY UPDATE
                    virtual_max          = $newVirtualMax,
                    physical_rooms       = $physical,
                    updated_by_user_id   = $userId,
                    updated_at           = NOW()";
        $result = mysqli_query($this->db, $sql);
        if (!$result) die("Upsert Failed: " . mysqli_error($this->db));
        return true;
    }

   
    public function logChange($roomTypeId, $date, $oldMax, $newMax, $reason = '')
    {
        $roomTypeId = (int) $roomTypeId;
        $date       = mysqli_real_escape_string($this->db, $date);
        $oldMax     = (int) $oldMax;
        $newMax     = (int) $newMax;
        $reason     = mysqli_real_escape_string($this->db, $reason ?: 'Manual adjustment');
        $userId     = (int) ($_SESSION['user_id'] ?? 0);

        mysqli_query($this->db,
            "INSERT INTO inventory_change_log
                 (room_type_id, date, old_virtual_max, new_virtual_max, changed_by_user_id, reason)
             VALUES ($roomTypeId, '$date', $oldMax, $newMax, $userId, '$reason')");
    }

    
    public function checkOverbooking($date, $roomTypeId)
    {
        $roomTypeId = (int) $roomTypeId;
        $date       = mysqli_real_escape_string($this->db, $date);

        
        $r = mysqli_query($this->db,
            "SELECT COUNT(*) AS cnt
             FROM   reservations res
             JOIN   rooms rm ON res.room_id = rm.id
             WHERE  rm.room_type_id = $roomTypeId
               AND  res.status IN ('confirmed','checked_in')
               AND  res.check_in_date  <= '$date'
               AND  res.check_out_date >  '$date'");
        $confirmed = (int) (mysqli_fetch_assoc($r)['cnt'] ?? 0);

        
        $physical = $this->getPhysicalRooms($roomTypeId);

        if ($confirmed > $physical) {
            $userId  = (int) ($_SESSION['user_id'] ?? 0);
            $message = mysqli_real_escape_string($this->db,
                "OVERBOOKING ALERT: room_type #$roomTypeId on $date — "
                . "confirmed=$confirmed > physical=$physical");
            mysqli_query($this->db,
                "INSERT INTO audit_log (user_id, action, target_type, target_id, old_value, new_value)
                 VALUES ($userId, 'overbooking_alert', 'room_type', $roomTypeId,
                         '$physical', '$message')");
            return true;
        }
        return false;
    }

   
    public function getCurrentVirtualMax($roomTypeId, $date)
    {
        $roomTypeId = (int) $roomTypeId;
        $date       = mysqli_real_escape_string($this->db, $date);
        $r = mysqli_query($this->db,
            "SELECT virtual_max FROM virtual_inventory
             WHERE  room_type_id = $roomTypeId AND date = '$date' LIMIT 1");
        $row = mysqli_fetch_assoc($r);
        return $row ? (int) $row['virtual_max'] : null;
    }

    
    public function getSyncStatus()
    {
        $r = mysqli_query($this->db,
            "SELECT rt.id AS room_type_id, rt.name AS room_type_name,
                    MAX(vi.updated_at)    AS last_synced_at,
                    COUNT(vi.id)          AS row_count,
                    SUM(CASE WHEN vi.updated_at < DATE_SUB(NOW(), INTERVAL 1 HOUR)
                             THEN 1 ELSE 0 END) AS stale_count
             FROM   room_types rt
             LEFT   JOIN virtual_inventory vi ON vi.room_type_id = rt.id
             GROUP  BY rt.id, rt.name
             ORDER  BY rt.id");
        if (!$r) die("Query Failed: " . mysqli_error($this->db));
        return mysqli_fetch_all($r, MYSQLI_ASSOC);
    }
}
