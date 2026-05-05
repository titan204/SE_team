<?php
// ============================================================
//  ReportsController — Occupancy, Revenue, Audit Log
//  Access: manager (all) + revenue_manager (occupancy + revenue)
// ============================================================

class ReportsController extends Controller
{
    public function index()
    {
        $this->requireLogin();
        $this->requireRoles(['manager', 'revenue_manager']);
        $this->view('reports/index');
    }

    // ── REPORT 1: OCCUPANCY ───────────────────────────────────

    public function occupancy()
    {
        $this->requireLogin();
        $this->requireRoles(['manager', 'revenue_manager']);

        $start = $_GET['start'] ?? date('Y-m-d', strtotime('-30 days'));
        $end   = $_GET['end']   ?? date('Y-m-d');
        $db    = (new Model())->getDb();

        $startSafe = mysqli_real_escape_string($db, $start);
        $endSafe   = mysqli_real_escape_string($db, $end);

        // Total rooms
        $rTotal = mysqli_query($db, "SELECT COUNT(*) AS total FROM rooms");
        $totalRooms = (int)(mysqli_fetch_assoc($rTotal)['total'] ?? 0);

        // Daily occupancy: count distinct rooms checked_in or checked_out in range
        $rDaily = mysqli_query($db,
            "SELECT DATE(actual_check_in) AS day, COUNT(DISTINCT room_id) AS occupied
             FROM   reservations
             WHERE  status IN ('checked_in','checked_out')
               AND  DATE(actual_check_in) BETWEEN '$startSafe' AND '$endSafe'
             GROUP  BY day ORDER BY day ASC");
        $dailyData = $rDaily ? mysqli_fetch_all($rDaily, MYSQLI_ASSOC) : [];

        // Per room-type breakdown
        $rTypes = mysqli_query($db,
            "SELECT rt.name AS room_type,
                    COUNT(DISTINCT r.id)      AS nights_occupied,
                    COUNT(DISTINCT rm.id) * DATEDIFF('$endSafe','$startSafe') AS nights_available
             FROM   reservations r
             JOIN   rooms rm     ON r.room_id      = rm.id
             JOIN   room_types rt ON rm.room_type_id = rt.id
             WHERE  r.status IN ('checked_in','checked_out')
               AND  DATE(r.check_in_date) <= '$endSafe'
               AND  DATE(r.check_out_date) >= '$startSafe'
             GROUP  BY rt.name");
        $typeData = $rTypes ? mysqli_fetch_all($rTypes, MYSQLI_ASSOC) : [];

        // Overall occupancy %
        $avgOccupied = $totalRooms > 0 && !empty($dailyData)
            ? round(array_sum(array_column($dailyData, 'occupied')) / (count($dailyData) * $totalRooms) * 100, 1)
            : 0;

        $this->view('reports/occupancy', compact(
            'start', 'end', 'totalRooms', 'dailyData', 'typeData', 'avgOccupied'
        ));
    }

    // ── REPORT 2: REVENUE ─────────────────────────────────────

    public function revenue()
    {
        $this->requireLogin();
        $this->requireRoles(['manager', 'revenue_manager']);

        $start = $_GET['start'] ?? date('Y-m-d', strtotime('-30 days'));
        $end   = $_GET['end']   ?? date('Y-m-d');
        $db    = (new Model())->getDb();

        $startSafe = mysqli_real_escape_string($db, $start);
        $endSafe   = mysqli_real_escape_string($db, $end);

        // ── Total revenue from actual payments ──
        $rTotal = mysqli_query($db,
            "SELECT COALESCE(SUM(amount), 0) AS total
             FROM   payments
             WHERE  DATE(processed_at) BETWEEN '$startSafe' AND '$endSafe'");
        $totalRevenue = round((float)(mysqli_fetch_assoc($rTotal)['total'] ?? 0), 2);

        $days    = max(1, (int)((strtotime($end) - strtotime($start)) / 86400) + 1);
        $avgDaily = round($totalRevenue / $days, 2);

        // ── Daily revenue breakdown ──
        $rDaily = mysqli_query($db,
            "SELECT DATE(processed_at) AS day,
                    COUNT(*)           AS transactions,
                    SUM(amount)        AS daily_total
             FROM   payments
             WHERE  DATE(processed_at) BETWEEN '$startSafe' AND '$endSafe'
             GROUP  BY day ORDER BY day ASC");
        $dailyData = $rDaily ? mysqli_fetch_all($rDaily, MYSQLI_ASSOC) : [];

        // ── Breakdown by payment method ──
        $rMethods = mysqli_query($db,
            "SELECT method,
                    COUNT(*)    AS tx_count,
                    SUM(amount) AS subtotal
             FROM   payments
             WHERE  DATE(processed_at) BETWEEN '$startSafe' AND '$endSafe'
             GROUP  BY method
             ORDER  BY subtotal DESC");
        $byMethod = $rMethods ? mysqli_fetch_all($rMethods, MYSQLI_ASSOC) : [];

        // ── Top folios by revenue in period ──
        $rFolios = mysqli_query($db,
            "SELECT f.id AS folio_id, g.name AS guest_name,
                    rm.room_number, f.total_amount, f.amount_paid,
                    f.balance_due, f.status,
                    SUM(p.amount) AS period_paid
             FROM   payments p
             JOIN   folios f   ON p.folio_id    = f.id
             JOIN   reservations res ON f.reservation_id = res.id
             LEFT   JOIN guests g   ON res.guest_id = g.id
             LEFT   JOIN rooms  rm  ON res.room_id  = rm.id
             WHERE  DATE(p.processed_at) BETWEEN '$startSafe' AND '$endSafe'
             GROUP  BY f.id
             ORDER  BY period_paid DESC
             LIMIT  10");
        $topFolios = $rFolios ? mysqli_fetch_all($rFolios, MYSQLI_ASSOC) : [];

        // ── Previous period comparison ──
        $periodDays  = max(1, (int)((strtotime($end) - strtotime($start)) / 86400) + 1);
        $prevEnd     = date('Y-m-d', strtotime($start) - 86400);
        $prevStart   = date('Y-m-d', strtotime($prevEnd) - ($periodDays - 1) * 86400);
        $prevStartS  = mysqli_real_escape_string($db, $prevStart);
        $prevEndS    = mysqli_real_escape_string($db, $prevEnd);

        $rPrev = mysqli_query($db,
            "SELECT COALESCE(SUM(amount), 0) AS total
             FROM payments
             WHERE DATE(processed_at) BETWEEN '$prevStartS' AND '$prevEndS'");
        $prevRevenue = round((float)(mysqli_fetch_assoc($rPrev)['total'] ?? 0), 2);
        $growthPct   = $prevRevenue > 0
            ? round(($totalRevenue - $prevRevenue) / $prevRevenue * 100, 1)
            : null;

        $this->view('reports/revenue', compact(
            'start', 'end', 'totalRevenue', 'avgDaily',
            'dailyData', 'byMethod', 'topFolios',
            'prevRevenue', 'growthPct', 'prevStart', 'prevEnd'
        ));
    }


    // ── REPORT 3: AUDIT LOG ───────────────────────────────────

    public function audit()
    {
        $this->requireLogin();
        $this->requireRoles(['manager']);

        $filters = [
            'user_id'     => $_GET['user_id']     ?? '',
            'action'      => $_GET['action']       ?? '',
            'target_type' => $_GET['target_type']  ?? '',
            'start'       => $_GET['start']        ?? date('Y-m-d', strtotime('-7 days')),
            'end'         => $_GET['end']           ?? date('Y-m-d'),
        ];

        $al      = new AuditLog();
        $entries = $al->all($filters, 500, 0);

        // User list for filter dropdown
        $db  = (new Model())->getDb();
        $rU  = mysqli_query($db, "SELECT id, name FROM users ORDER BY name");
        $users = $rU ? mysqli_fetch_all($rU, MYSQLI_ASSOC) : [];

        $this->view('reports/audit', compact('entries', 'filters', 'users'));
    }

    /** GET /reports/audit/export — CSV download. */
    public function auditExport()
    {
        $this->requireLogin();
        $this->requireRoles(['manager']);

        $filters = [
            'user_id'     => $_GET['user_id']     ?? '',
            'action'      => $_GET['action']       ?? '',
            'target_type' => $_GET['target_type']  ?? '',
            'start'       => $_GET['start']        ?? date('Y-m-d', strtotime('-30 days')),
            'end'         => $_GET['end']           ?? date('Y-m-d'),
        ];

        $al      = new AuditLog();
        $entries = $al->all($filters, 5000, 0);

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="audit-log-' . date('Y-m-d') . '.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['ID','User','Action','Target Type','Target ID','Old Value','New Value','Timestamp']);
        foreach ($entries as $e) {
            fputcsv($out, [
                $e['id'],
                $e['user_name'] ?? $e['user_id'] ?? 'System',
                $e['action'],
                $e['target_type'],
                $e['target_id'],
                substr((string)($e['old_value'] ?? ''), 0, 200),
                substr((string)($e['new_value'] ?? ''), 0, 200),
                $e['created_at'],
            ]);
        }
        fclose($out);
        exit;
    }
}
