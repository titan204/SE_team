<?php
// ============================================================
//  RevenueManagerVirtualInventoryController - Revenue Manager
//  virtual inventory control dashboard scaffold.
//  Routes:
//    /revenue_manager_virtual_inventory                          -> index
//    /revenue_manager_virtual_inventory/dashboard                -> dashboard
//    /revenue_manager_virtual_inventory/roomCostAnalysis/5       -> roomCostAnalysis
//    /revenue_manager_virtual_inventory/guestConsumption/5       -> guestConsumption
//    /revenue_manager_virtual_inventory/departmentCostBreakdown  -> departmentCostBreakdown
//    /revenue_manager_virtual_inventory/revenueImpactReport      -> revenueImpactReport
//    /revenue_manager_virtual_inventory/limitCheck               -> limitCheck
//    /revenue_manager_virtual_inventory/triggerAlerts            -> triggerAlerts
//
//  Integration notes:
//  - Billing System: future read-only folio reading hooks belong here.
//  - Housekeeping System: future virtual cost references belong here.
//  - Reservation System: future room-booking influence hooks belong here.
// ============================================================

class RevenueManagerVirtualInventoryController extends Controller
{
    public function __construct()
    {
        $this->requireRole('revenue_manager');
    }

    public function index()
    {
        $db = (new Model())->getDb();

        // Total rooms + occupied count
        $rRooms = mysqli_query($db, "SELECT COUNT(*) AS total FROM rooms");
        $totalRooms = $rRooms ? (int)(mysqli_fetch_assoc($rRooms)['total'] ?? 0) : 0;

        // Virtual cost pool: sum of (base_price × 20%) × room_count per type
        $rPool = mysqli_query($db,
            "SELECT SUM(rt.base_price * 0.20 * cnt.room_count) AS pool
             FROM   room_types rt
             JOIN   (SELECT room_type_id, COUNT(*) AS room_count FROM rooms GROUP BY room_type_id) cnt
                    ON cnt.room_type_id = rt.id");
        $totalPool = $rPool ? round((float)(mysqli_fetch_assoc($rPool)['pool'] ?? 0), 0) : 0;

        // Total guests tracked (all guests in DB)
        $rGuests = mysqli_query($db, "SELECT COUNT(*) AS cnt FROM guests");
        $totalGuests = $rGuests ? (int)(mysqli_fetch_assoc($rGuests)['cnt'] ?? 0) : 0;

        // Open alerts count
        $rAlerts = mysqli_query($db, "SELECT COUNT(*) AS cnt FROM property_wide_alerts WHERE status='active'");
        $openAlerts = $rAlerts ? (int)(mysqli_fetch_assoc($rAlerts)['cnt'] ?? 0) : 0;

        $this->view('revenue_manager_virtual_inventory/index', compact(
            'totalRooms', 'totalPool', 'totalGuests', 'openAlerts'
        ));
    }

    public function dashboard()
    {
        $this->view('revenue_manager_virtual_inventory/index');
    }

    public function roomCostAnalysis($roomId = null)
    {
        $db = (new Model())->getDb();

        // ── Real room-type breakdown ──────────────────────────
        $rTypes = mysqli_query($db,
            "SELECT rt.id, rt.name AS room_type,
                    COUNT(r.id)          AS room_count,
                    MIN(r.room_number)   AS min_room,
                    MAX(r.room_number)   AS max_room,
                    rt.base_price,
                    SUM(CASE WHEN r.status = 'occupied' THEN 1 ELSE 0 END) AS occupied_count
             FROM   rooms r
             JOIN   room_types rt ON r.room_type_id = rt.id
             GROUP  BY rt.id, rt.name, rt.base_price
             ORDER  BY rt.base_price ASC");
        $roomTypes = $rTypes ? mysqli_fetch_all($rTypes, MYSQLI_ASSOC) : [];

        // ── HK tasks completed this week ─────────────────────
        $rHk = mysqli_query($db,
            "SELECT COUNT(*) AS cnt FROM housekeeping_tasks
             WHERE  status = 'done'
               AND  updated_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
        $hkDoneWeek = $rHk ? (int)(mysqli_fetch_assoc($rHk)['cnt'] ?? 0) : 0;

        // ── Totals ────────────────────────────────────────────
        $totalRooms    = array_sum(array_column($roomTypes, 'room_count'));
        $totalOccupied = array_sum(array_column($roomTypes, 'occupied_count'));
        $occupancyPct  = $totalRooms > 0 ? round($totalOccupied / $totalRooms * 100, 1) : 0;

        // Virtual service cost = 20% of base_price per room (per-night operational estimate)
        $costRate = 0.20;
        $totalPool = 0;
        foreach ($roomTypes as &$rt) {
            $rt['service_cost_per_room'] = round($rt['base_price'] * $costRate, 2);
            $rt['cluster_total']         = round($rt['service_cost_per_room'] * $rt['room_count'], 2);
            $totalPool += $rt['cluster_total'];
        }
        unset($rt);

        $avgCostPerRoom = $totalRooms > 0 ? round($totalPool / $totalRooms, 2) : 0;

        // Highest-impact tier (most expensive base_price)
        $topTier = !empty($roomTypes) ? end($roomTypes) : null;

        $this->view('revenue_manager_virtual_inventory/room_cost_analysis', [
            'roomId'        => $roomId,
            'roomTypes'     => $roomTypes,
            'totalRooms'    => $totalRooms,
            'totalOccupied' => $totalOccupied,
            'occupancyPct'  => $occupancyPct,
            'hkDoneWeek'    => $hkDoneWeek,
            'totalPool'     => $totalPool,
            'avgCostPerRoom'=> $avgCostPerRoom,
            'topTier'       => $topTier,
        ]);
    }

    public function guestConsumption($guestId = null)
    {
        $this->view('revenue_manager_virtual_inventory/guest_consumption', [
            'guestId' => $guestId,
        ]);
    }

    public function departmentCostBreakdown()
    {
        $db = (new Model())->getDb();

        // Housekeeping tasks done this week
        $rHk = mysqli_query($db,
            "SELECT COUNT(*) AS cnt FROM housekeeping_tasks
             WHERE status = 'done' AND updated_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
        $hkDoneWeek = $rHk ? (int)(mysqli_fetch_assoc($rHk)['cnt'] ?? 0) : 0;

        // HK tasks in progress / pending today
        $rHkToday = mysqli_query($db,
            "SELECT COUNT(*) AS cnt FROM housekeeping_tasks
             WHERE status IN ('pending','in_progress') AND DATE(created_at) = CURDATE()");
        $hkTodayPending = $rHkToday ? (int)(mysqli_fetch_assoc($rHkToday)['cnt'] ?? 0) : 0;

        // Front desk: check-ins + check-outs this week
        $rFd = mysqli_query($db,
            "SELECT
                SUM(CASE WHEN check_in_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) AS checkins,
                SUM(CASE WHEN check_out_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) AS checkouts
             FROM reservations
             WHERE status IN ('checked_in','checked_out','confirmed')");
        $fdRow    = $rFd ? mysqli_fetch_assoc($rFd) : ['checkins'=>0,'checkouts'=>0];
        $fdCheckins  = (int)($fdRow['checkins']  ?? 0);
        $fdCheckouts = (int)($fdRow['checkouts'] ?? 0);

        // Maintenance: open work orders
        $rWo = mysqli_query($db,
            "SELECT COUNT(*) AS cnt FROM work_orders WHERE status IN ('open','in_progress','pending_parts')");
        $openWorkOrders = $rWo ? (int)(mysqli_fetch_assoc($rWo)['cnt'] ?? 0) : 0;

        // Occupied rooms (for laundry dept reference)
        $rOcc = mysqli_query($db, "SELECT COUNT(*) AS cnt FROM rooms WHERE status = 'occupied'");
        $occupiedRooms = $rOcc ? (int)(mysqli_fetch_assoc($rOcc)['cnt'] ?? 0) : 0;

        // Guests tracked this month (for concierge)
        $rGuestMonth = mysqli_query($db,
            "SELECT COUNT(*) AS cnt FROM guests
             WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())");
        $guestsThisMonth = $rGuestMonth ? (int)(mysqli_fetch_assoc($rGuestMonth)['cnt'] ?? 0) : 0;
        // Fallback: total guests if none this month
        if ($guestsThisMonth === 0) {
            $rGAll = mysqli_query($db, "SELECT COUNT(*) AS cnt FROM guests");
            $guestsThisMonth = $rGAll ? (int)(mysqli_fetch_assoc($rGAll)['cnt'] ?? 0) : 0;
        }

        // Virtual cost pool for room cost reference
        $rPool = mysqli_query($db,
            "SELECT SUM(rt.base_price * 0.20 * cnt.room_count) AS pool
             FROM room_types rt
             JOIN (SELECT room_type_id, COUNT(*) AS room_count FROM rooms GROUP BY room_type_id) cnt
                  ON cnt.room_type_id = rt.id");
        $roomCostPool = $rPool ? round((float)(mysqli_fetch_assoc($rPool)['pool'] ?? 0), 0) : 0;

        $this->view('revenue_manager_virtual_inventory/department_cost_breakdown', compact(
            'hkDoneWeek', 'hkTodayPending',
            'fdCheckins', 'fdCheckouts',
            'openWorkOrders', 'occupiedRooms',
            'guestsThisMonth', 'roomCostPool'
        ));
    }

    public function revenueImpactReport()
    {
        $db = (new Model())->getDb();

        // Revenue this month from payments
        $rRev = mysqli_query($db,
            "SELECT COALESCE(SUM(amount), 0) AS total FROM payments
             WHERE YEAR(processed_at) = YEAR(CURDATE()) AND MONTH(processed_at) = MONTH(CURDATE())");
        $revenueMonth = (float)(mysqli_fetch_assoc($rRev)['total'] ?? 0);

        // Virtual cost pool
        $rPool = mysqli_query($db,
            "SELECT SUM(rt.base_price * 0.20 * cnt.room_count) AS pool
             FROM room_types rt
             JOIN (SELECT room_type_id, COUNT(*) AS room_count FROM rooms GROUP BY room_type_id) cnt
                  ON cnt.room_type_id = rt.id");
        $costPool = round((float)(mysqli_fetch_assoc($rPool)['pool'] ?? 0), 0);

        // Margin signal
        $marginPct = $revenueMonth > 0
            ? round(($revenueMonth - $costPool) / $revenueMonth * 100, 1)
            : 0;

        // Active alerts
        $rAlerts = mysqli_query($db, "SELECT COUNT(*) AS cnt FROM property_wide_alerts WHERE status='active'");
        $openAlerts = $rAlerts ? (int)(mysqli_fetch_assoc($rAlerts)['cnt'] ?? 0) : 0;

        // Per-room-type breakdown: occupied, revenue signal (base_price × nights × occupied)
        $rTypes = mysqli_query($db,
            "SELECT rt.name AS room_type,
                    COUNT(r.id)          AS room_count,
                    MIN(r.room_number)   AS min_room,
                    MAX(r.room_number)   AS max_room,
                    rt.base_price,
                    SUM(CASE WHEN r.status='occupied' THEN 1 ELSE 0 END) AS occupied_count,
                    ROUND(rt.base_price * 0.20 * COUNT(r.id), 0) AS cost_ref
             FROM rooms r
             JOIN room_types rt ON r.room_type_id = rt.id
             GROUP BY rt.id, rt.name, rt.base_price
             ORDER BY rt.base_price ASC");
        $roomTypes = $rTypes ? mysqli_fetch_all($rTypes, MYSQLI_ASSOC) : [];

        $totalRooms    = array_sum(array_column($roomTypes, 'room_count'));
        $totalOccupied = array_sum(array_column($roomTypes, 'occupied_count'));
        $occupancyPct  = $totalRooms > 0 ? round($totalOccupied / $totalRooms * 100, 1) : 0;

        $this->view('revenue_manager_virtual_inventory/revenue_report', compact(
            'revenueMonth', 'costPool', 'marginPct',
            'openAlerts', 'roomTypes',
            'totalRooms', 'totalOccupied', 'occupancyPct'
        ));
    }

    public function limitCheck()
    {
        $this->view('revenue_manager_virtual_inventory/limit_check');
    }

    public function triggerAlerts()
    {
        $this->view('revenue_manager_virtual_inventory/trigger_alerts');
    }

    // ── UC07: Virtual Inventory ──────────────────────────────

    /**
     * GET /revenue_manager_virtual_inventory/inventoryGrid
     * 30-day grid: rows = room types, columns = dates.
     */
    public function inventoryGrid()
    {
        $inv  = new RevenueManagerVirtualInventory();
        $data = $inv->getInventoryGrid(30);

        // Check for any stale rows to show the "outdated" banner
        $syncRows  = $inv->getSyncStatus();
        $anyStale  = false;
        $staleTime = null;
        foreach ($syncRows as $s) {
            if ((int)$s['stale_count'] > 0) {
                $anyStale  = true;
                $staleTime = $staleTime ?? $s['last_synced_at'];
            }
        }

        $this->view('revenue_manager_virtual_inventory/inventory_grid', [
            'roomTypes' => $data['roomTypes'],
            'dates'     => $data['dates'],
            'grid'      => $data['grid'],
            'anyStale'  => $anyStale,
            'staleTime' => $staleTime,
        ]);
    }

    /**
     * POST /revenue_manager_virtual_inventory/adjust
     * Adjust virtual_max for a room_type + date.
     * Triggers UC15 checkOverbooking after every save.
     */
    public function adjust()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('revenue_manager_virtual_inventory/inventoryGrid');
            return;
        }

        $roomTypeId    = (int)   ($_POST['room_type_id'] ?? 0);
        $date          = trim($_POST['date'] ?? '');
        $newVirtualMax = (int)   ($_POST['virtual_max'] ?? -1);
        $reason        = trim($_POST['reason'] ?? '');

        if (!$roomTypeId || !$date) {
            $_SESSION['inv_error'] = 'Missing room type or date.';
            $this->redirect('revenue_manager_virtual_inventory/inventoryGrid');
            return;
        }

        $inv      = new RevenueManagerVirtualInventory();
        $physical = $inv->getPhysicalRooms($roomTypeId);

        // Validation: 0 ≤ virtual_max ≤ physical × 2
        if ($newVirtualMax < 0 || $newVirtualMax > $physical * 2) {
            $_SESSION['inv_error'] =
                "Invalid value: virtual_max must be between 0 and " . ($physical * 2) . ".";
            $this->redirect('revenue_manager_virtual_inventory/inventoryGrid');
            return;
        }

        $oldMax = $inv->getCurrentVirtualMax($roomTypeId, $date) ?? $physical;
        $inv->upsertVirtualMax($roomTypeId, $date, $physical, $newVirtualMax);
        $inv->logChange($roomTypeId, $date, $oldMax, $newVirtualMax, $reason ?: 'Manual adjustment');

        // UC15: trigger overbooking check on every adjustment
        $overbooked = $inv->checkOverbooking($date, $roomTypeId);
        if ($overbooked) {
            $_SESSION['inv_warning'] =
                "⚠ Overbooking detected for room type #$roomTypeId on $date. Alert logged.";
        }

        $_SESSION['inv_success'] = "Virtual max updated to $newVirtualMax for $date.";
        $this->redirect('revenue_manager_virtual_inventory/inventoryGrid');
    }

    /**
     * POST /revenue_manager_virtual_inventory/override
     * Manual override: allows virtual_max > physical. Requires reason.
     */
    public function override()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('revenue_manager_virtual_inventory/inventoryGrid');
            return;
        }

        $roomTypeId    = (int) ($_POST['room_type_id'] ?? 0);
        $date          = trim($_POST['date'] ?? '');
        $newVirtualMax = (int) ($_POST['virtual_max'] ?? 0);
        $reason        = trim($_POST['reason'] ?? '');

        if (!$roomTypeId || !$date || !$reason) {
            $_SESSION['inv_error'] = 'Override requires a reason.';
            $this->redirect('revenue_manager_virtual_inventory/inventoryGrid');
            return;
        }

        $inv      = new RevenueManagerVirtualInventory();
        $physical = $inv->getPhysicalRooms($roomTypeId);

        // Override allows exceeding physical, but still caps at physical × 2 as a sanity guard
        if ($newVirtualMax < 0 || $newVirtualMax > $physical * 2) {
            $_SESSION['inv_error'] =
                "Override value out of range (0 – " . ($physical * 2) . ").";
            $this->redirect('revenue_manager_virtual_inventory/inventoryGrid');
            return;
        }

        $oldMax = $inv->getCurrentVirtualMax($roomTypeId, $date) ?? $physical;
        $inv->upsertVirtualMax($roomTypeId, $date, $physical, $newVirtualMax);
        $inv->logChange($roomTypeId, $date, $oldMax, $newVirtualMax, 'OVERRIDE: ' . $reason);

        // UC15: always check after override too
        $overbooked = $inv->checkOverbooking($date, $roomTypeId);
        if ($overbooked) {
            $_SESSION['inv_warning'] =
                "⚠ Overbooking confirmed on $date for room type #$roomTypeId — override logged.";
        }

        $_SESSION['inv_success'] = "Override applied: virtual_max=$newVirtualMax for $date.";
        $this->redirect('revenue_manager_virtual_inventory/inventoryGrid');
    }

    /**
     * GET /revenue_manager_virtual_inventory/syncStatus
     * Shows last_synced_at per room type and any stale data warnings.
     */
    public function syncStatus()
    {
        $inv      = new RevenueManagerVirtualInventory();
        $channels = $inv->getSyncStatus();

        $this->view('revenue_manager_virtual_inventory/sync_status', [
            'channels' => $channels,
        ]);
    }
}
