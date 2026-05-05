<?php
// ============================================================
//  HousekeepingController — Task management for housekeepers
//  Routes:
//    /housekeeping            → index (task board)
//    /housekeeping/show/5     → task details
//    /housekeeping/create     → new task form
//    /housekeeping/store      → save task
//    /housekeeping/complete/5 → mark task done
// ============================================================

class HousekeepingController extends Controller
{
    /** Allowed roles for housekeeping module. */
    private function requireHousekeeping(): void
    {
        $this->requireRoles(['housekeeper', 'manager', 'supervisor']);
    }

    public function index()
    {
        $this->requireLogin();
        $this->requireHousekeeping();

        $db = (new Model())->getDb();
        $this->distributeUnassigned($db); // auto-assign new tasks

        // Status/type filters
        $statusFilter = $_GET['status'] ?? '';
        $typeFilter   = $_GET['task_type'] ?? '';
        $where = ['1=1'];
        if ($statusFilter) $where[] = "ht.status = '" . mysqli_real_escape_string($db, $statusFilter) . "'";
        if ($typeFilter)   $where[] = "ht.task_type = '" . mysqli_real_escape_string($db, $typeFilter) . "'";
        $w = implode(' AND ', $where);

        $r = mysqli_query($db,
            "SELECT ht.id, ht.task_type, ht.status, ht.notes, ht.quality_score,
                    ht.created_at, ht.updated_at, ht.completed_at,
                    rm.room_number, rm.id AS room_id,
                    u.name AS assigned_name
             FROM   housekeeping_tasks ht
             JOIN   rooms rm ON ht.room_id   = rm.id
             LEFT   JOIN users u ON ht.assigned_to = u.id
             WHERE  $w
             ORDER  BY FIELD(ht.status,'pending','in_progress','done','skipped'),
                       ht.created_at DESC");
        $tasks = $r ? mysqli_fetch_all($r, MYSQLI_ASSOC) : [];

        $rCounts = mysqli_query($db,
            "SELECT status, COUNT(*) AS cnt FROM housekeeping_tasks GROUP BY status");
        $counts = ['pending'=>0,'in_progress'=>0,'done'=>0,'skipped'=>0];
        if ($rCounts) { while ($c = mysqli_fetch_assoc($rCounts)) $counts[$c['status']] = (int)$c['cnt']; }

        // Last update timestamp for sync indicator
        $rTs = mysqli_query($db, "SELECT MAX(updated_at) AS ts FROM housekeeping_tasks");
        $lastUpdate = $rTs ? (mysqli_fetch_assoc($rTs)['ts'] ?? '') : '';

        $this->view('housekeeping/index', compact('tasks','counts','statusFilter','typeFilter','lastUpdate'));
    }

    /**
     * GET /housekeeping/myTasks
     * Personal task board — only tasks assigned to the current logged-in user.
     */
    public function myTasks()
    {
        $this->requireLogin();
        $userId = (int)($_SESSION['user_id'] ?? 0);
        $db = (new Model())->getDb();
        $this->distributeUnassigned($db); // pick up any new tasks

        $r = mysqli_query($db,
            "SELECT ht.id, ht.task_type, ht.status, ht.notes, ht.quality_score,
                    ht.created_at, ht.updated_at, ht.completed_at,
                    rm.room_number, rm.id AS room_id
             FROM   housekeeping_tasks ht
             JOIN   rooms rm ON ht.room_id = rm.id
             WHERE  ht.assigned_to = $userId
             ORDER  BY FIELD(ht.status,'pending','in_progress','done','skipped'),
                       ht.created_at DESC");
        $tasks = $r ? mysqli_fetch_all($r, MYSQLI_ASSOC) : [];

        $counts = ['pending'=>0,'in_progress'=>0,'done'=>0,'skipped'=>0];
        foreach ($tasks as $t) {
            if (isset($counts[$t['status']])) $counts[$t['status']]++;
        }

        $rTs = mysqli_query($db, "SELECT MAX(updated_at) AS ts FROM housekeeping_tasks WHERE assigned_to = $userId");
        $lastUpdate = $rTs ? (mysqli_fetch_assoc($rTs)['ts'] ?? '') : '';

        $this->view('housekeeping/my_tasks', compact('tasks','counts','lastUpdate'));
    }

    /**
     * GET /housekeeping/boardData
     * AJAX endpoint: returns JSON snapshot of all tasks for live-sync polling.
     */
    public function boardData()
    {
        $this->requireLogin();
        $db = (new Model())->getDb();

        $r = mysqli_query($db,
            "SELECT ht.id, ht.task_type, ht.status, ht.quality_score,
                    ht.updated_at,
                    rm.room_number,
                    u.name AS assigned_name
             FROM   housekeeping_tasks ht
             JOIN   rooms rm ON ht.room_id   = rm.id
             LEFT   JOIN users u ON ht.assigned_to = u.id
             ORDER  BY FIELD(ht.status,'pending','in_progress','done','skipped'),
                       ht.created_at DESC");
        $tasks = $r ? mysqli_fetch_all($r, MYSQLI_ASSOC) : [];

        $rCounts = mysqli_query($db,
            "SELECT status, COUNT(*) AS cnt FROM housekeeping_tasks GROUP BY status");
        $counts = ['pending'=>0,'in_progress'=>0,'done'=>0,'skipped'=>0];
        if ($rCounts) { while ($c = mysqli_fetch_assoc($rCounts)) $counts[$c['status']] = (int)$c['cnt']; }

        $rTs = mysqli_query($db, "SELECT MAX(updated_at) AS ts FROM housekeeping_tasks");
        $lastUpdate = $rTs ? (mysqli_fetch_assoc($rTs)['ts'] ?? '') : '';

        header('Content-Type: application/json');
        echo json_encode(compact('tasks','counts','lastUpdate'));
        exit;
    }

    // ── Private: load-balanced task distribution ──────────────

    /**
     * Assign all unassigned pending tasks to active housekeepers
     * using a load-balanced (least-loaded first) strategy.
     * Safe to call on every page load — only touches NULL assigned_to.
     */
    private function distributeUnassigned($db): void
    {
        // Active housekeepers
        $rHK = mysqli_query($db,
            "SELECT u.id FROM users u
             JOIN   roles r ON u.role_id = r.id
             WHERE  r.name = 'housekeeper' AND u.is_active = 1
             ORDER  BY u.id");
        $hkIds = [];
        if ($rHK) while ($row = mysqli_fetch_assoc($rHK)) $hkIds[] = (int)$row['id'];
        if (empty($hkIds)) return;

        // Unassigned pending tasks
        $rTasks = mysqli_query($db,
            "SELECT id FROM housekeeping_tasks
             WHERE  assigned_to IS NULL AND status = 'pending'
             ORDER  BY id");
        $taskIds = [];
        if ($rTasks) while ($row = mysqli_fetch_assoc($rTasks)) $taskIds[] = (int)$row['id'];
        if (empty($taskIds)) return;

        // Current pending/in_progress load per housekeeper
        $hkList = implode(',', $hkIds);
        $load   = array_fill_keys($hkIds, 0);
        $rLoad  = mysqli_query($db,
            "SELECT assigned_to, COUNT(*) AS cnt
             FROM   housekeeping_tasks
             WHERE  status IN ('pending','in_progress')
               AND  assigned_to IN ($hkList)
             GROUP  BY assigned_to");
        if ($rLoad) {
            while ($row = mysqli_fetch_assoc($rLoad)) {
                $load[(int)$row['assigned_to']] = (int)$row['cnt'];
            }
        }

        // Assign each unassigned task to the least-loaded housekeeper
        foreach ($taskIds as $taskId) {
            $assignTo = array_keys($load, min($load))[0];
            mysqli_query($db,
                "UPDATE housekeeping_tasks
                 SET    assigned_to = $assignTo, updated_at = NOW()
                 WHERE  id = $taskId");
            $load[$assignTo]++;
        }
    }

    /**
     * POST /housekeeping/updateStatus/{id}
     * Ajax-friendly status update: pending→in_progress, in_progress→done, skip.
     */
    public function updateStatus($id)
    {
        $this->requireLogin();
        $id     = (int)$id;
        $status = $_POST['status'] ?? '';
        $allowed = ['in_progress','done','skipped'];
        if (!in_array($status, $allowed)) {
            $_SESSION['hk_error'] = 'Invalid status.';
            $this->redirect('housekeeping/index');
            return;
        }
        $db = (new Model())->getDb();
        // Set completed_at only when marking done
        $completedSql = ($status === 'done') ? ', completed_at = NOW()' : ', completed_at = NULL';
        mysqli_query($db,
            "UPDATE housekeeping_tasks
             SET    status='$status', updated_at=NOW() $completedSql
             WHERE  id=$id");

        AuditLog::log(
            (int)($_SESSION['user_id'] ?? null),
            'housekeeping.task.' . $status,
            'housekeeping_task', $id
        );

        $_SESSION['hk_success'] = "Task #$id marked as $status.";

        // Return to whichever page called us
        $ref = $_SERVER['HTTP_REFERER'] ?? null;
        if ($ref && str_contains($ref, 'myTasks')) {
            $this->redirect('housekeeping/myTasks');
        } else {
            $this->redirect('housekeeping/index');
        }
    }

    public function show($id)
    {
        $this->requireLogin();
        $this->view('housekeeping/show');
    }

    public function create()
    {
        $this->requireLogin();
        $this->view('housekeeping/create');
    }

    public function store()
    {
        $this->requireLogin();
        $this->redirect('housekeeping/index');
    }

    public function complete($id)
    {
        $this->requireLogin();
        $this->redirect('housekeeping/index');
    }


    // ── UC29: Minibar Consumption ─────────────────────────────

    /**
     * GET /housekeeping/rooms/{id}/minibar
     * Show minibar inventory + consumption form for a room.
     */
    public function minibar($id)
    {
        $this->requireLogin();
        $this->requireHousekeeping();
        $mb      = new Minibar();
        $roomId  = (int) $id;
        $inventory = $mb->getInventoryForRoom($roomId);
        $reservation = $mb->findActiveReservation($roomId);

        // Load room info
        $db   = (new Model())->getDb();
        $rr   = mysqli_query($db, "SELECT * FROM rooms WHERE id = $roomId LIMIT 1");
        $room = $rr ? mysqli_fetch_assoc($rr) : null;
        if (!$room) die("Room #$roomId not found.");

        $this->view('housekeeping/minibar', [
            'room'        => $room,
            'inventory'   => $inventory,
            'reservation' => $reservation,
        ]);
    }

    /**
     * POST /housekeeping/logMinibar/{room_id}
     * UC29 Step 2: Submit minibar consumption.
     */
    public function logMinibar($roomId)
    {
        $this->requireLogin();
        $this->requireHousekeeping();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect("housekeeping/minibar/$roomId");
            return;
        }

        $mb     = new Minibar();
        $sa     = new StockAlert();
        $items  = $_POST['items'] ?? [];
        $hkId   = (int) ($_SESSION['user_id'] ?? 0);

        // Manual entry if provided
        if (!empty($_POST['manual_description']) && !empty($_POST['manual_price'])) {
            $res = $mb->findActiveReservation((int)$roomId);
            if ($res) {
                $mb->addManualItem(
                    (int)$res['id'],
                    $_POST['manual_description'],
                    (float)$_POST['manual_price'],
                    (int)($_POST['manual_qty'] ?? 1)
                );
                $_SESSION['hk_success'] = 'Manual item added and flagged for manager review.';
            }
        }

        $result = $mb->logConsumption((int)$roomId, $items, $hkId);

        if (!$result['success']) {
            $_SESSION['hk_error'] = $result['error'] ?? 'Nothing to log.';
            $this->redirect("housekeeping/minibar/$roomId");
            return;
        }

        // UC31: check low stock for flagged items
        foreach ($result['low_stock_items'] as $itemId) {
            $sa->checkStockLevels($itemId);
        }

        $msg = "Minibar logged. Total: $" . number_format($result['total'], 2) . ".";
        if (!$result['has_reservation']) $msg .= " ⚠ No active guest — charges not posted.";
        if ($result['billing_queued'])   $msg .= " ⚠ Billing queued for retry.";
        if (!empty($result['low_stock_items'])) $msg .= " ⚠ Low-stock alert triggered.";

        $_SESSION['hk_success'] = $msg;
        $this->redirect("housekeeping/minibar/$roomId");
    }

    /**
     * GET /housekeeping/minibarLog/{room_id}
     * UC29 Dispute evidence: minibar_logs for a room with housekeeper + timestamp.
     * Accessible to: front_desk, manager (dispute resolution) + housekeeper (own logs).
     */
    public function minibarLog($roomId)
    {
        $this->requireLogin();
        $this->requireRoles(['housekeeper', 'manager', 'front_desk', 'supervisor']);

        $roomId = (int) $roomId;
        $db     = (new Model())->getDb();

        $rRoom = mysqli_query($db, "SELECT * FROM rooms WHERE id = $roomId LIMIT 1");
        $room  = $rRoom ? mysqli_fetch_assoc($rRoom) : null;
        if (!$room) die("Room #$roomId not found.");

        $rLogs = mysqli_query($db,
            "SELECT ml.id, ml.items, ml.total_amount, ml.logged_at,
                    ml.reservation_id, u.name AS housekeeper_name
             FROM   minibar_logs ml
             LEFT JOIN users u ON ml.housekeeper_id = u.id
             WHERE  ml.room_id = $roomId
             ORDER  BY ml.logged_at DESC
             LIMIT  100");
        $logs = $rLogs ? mysqli_fetch_all($rLogs, MYSQLI_ASSOC) : [];

        $this->view('housekeeping/minibar_log', compact('room', 'logs'));
    }

    // ── UC30: Found Item ──────────────────────────────────────

    /**
     * GET /housekeeping/foundItem
     * Show the log-found-item form.
     */
    public function foundItem()
    {
        $this->requireLogin();
        $this->requireHousekeeping();
        $db    = (new Model())->getDb();
        $rr    = mysqli_query($db, "SELECT room_number FROM rooms ORDER BY room_number");
        $rooms = $rr ? mysqli_fetch_all($rr, MYSQLI_ASSOC) : [];
        $this->view('housekeeping/found_item', ['rooms' => $rooms]);
    }

    /**
     * POST /housekeeping/logFoundItem
     * UC30: Submit found item.
     */
    public function logFoundItem()
    {
        $this->requireLogin();
        $this->requireHousekeeping();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('housekeeping/foundItem');
            return;
        }

        $fi = new FoundItem();

        // Duplicate detection
        $overrideKey = 'override_duplicate_' . md5(($_POST['room_number'] ?? '') . ($_POST['description'] ?? ''));
        if (empty($_POST['override_duplicate'])) {
            $roomNum = $_POST['room_number'] ?? '';
            $dupes   = $fi->checkDuplicate($roomNum, trim($_POST['description'] ?? ''));
            if (!empty($dupes)) {
                $_SESSION['hk_duplicates'] = $dupes;
                $_SESSION['hk_form_data']  = $_POST;
                $this->redirect('housekeeping/foundItem');
                return;
            }
        }

        // Handle photo upload
        $photoUrl = null;
        if (!empty($_FILES['photo']['name'])) {
            $uploadDir = __DIR__ . '/../../public/uploads/found-items/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $ext      = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $filename = 'laf_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadDir . $filename)) {
                $photoUrl = '/uploads/found-items/' . $filename;
            }
        }

        $result = $fi->create([
            'description'   => trim($_POST['description'] ?? ''),
            'location_type' => $_POST['location_type'] ?? 'room',
            'room_number'   => $_POST['room_number'] ?? '',
            'public_area'   => $_POST['public_area'] ?? null,
            'condition'     => $_POST['condition'] ?? 'good',
            'photo_url'     => $photoUrl,
            'is_high_value' => !empty($_POST['is_high_value']),
        ]);

        unset($_SESSION['hk_duplicates'], $_SESSION['hk_form_data']);
        $_SESSION['hk_success'] = "Item logged. Reference: {$result['lf_reference']}.";
        $this->redirect('housekeeping/foundItem');
    }

    // ── UC32: Quality Assurance ───────────────────────────────

    /**
     * GET /housekeeping/qa
     * Tab 1: rooms pending inspection. Tab 2: flagged by low feedback.
     */
    public function qa()
    {
        $this->requireLogin();
        $qa = new QAInspection();
        $this->view('housekeeping/qa_index', [
            'pendingRooms'  => $qa->getRoomsPendingInspection(),
            'flaggedRooms'  => $qa->getRoomsFlaggedByFeedback(),
        ]);
    }

    /**
     * GET /housekeeping/qaInspect/{room_id}
     * Show the QA checklist form for a room.
     */
    public function qaInspect($roomId)
    {
        $this->requireLogin();
        $qa   = new QAInspection();
        $data = $qa->getInspectionData((int)$roomId);
        if (!$data['room']) die("Room #$roomId not found.");
        $this->view('housekeeping/qa_inspect', $data);
    }

    /**
     * POST /housekeeping/qaSubmit
     * UC32 Step 4: Submit QA inspection.
     */
    public function qaSubmit()
    {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('housekeeping/qa');
            return;
        }

        $qa     = new QAInspection();
        $scores = $_POST['checklist'] ?? [];

        // Determine overall result
        // corrective_action = has failures AND housekeeper assignments were submitted
        $hasFail   = in_array('fail', $scores);
        $hasAssign = !empty(array_filter($_POST['corrective_hk'] ?? []));
        $result    = 'pass';
        if ($hasFail && $hasAssign) {
            $result = 'corrective_action';
        } elseif ($hasFail) {
            $result = 'fail';
        }

        $corrective = [];
        foreach ($_POST['corrective_hk'] ?? [] as $i => $hkId) {
            if (!$hkId) continue;
            $corrective[] = [
                'housekeeper_id'   => (int) $hkId,
                'task_description' => trim($_POST['corrective_task'][$i] ?? ''),
                'due_by'           => $_POST['corrective_due'][$i] ?? '',
            ];
        }

        $res = $qa->submitInspection([
            'room_id'                 => (int) ($_POST['room_id'] ?? 0),
            'checklist_scores'        => $scores,
            'overall_result'          => $result,
            'notes'                   => trim($_POST['notes'] ?? ''),
            'corrective_assignments'  => $corrective,
            'is_critical'             => !empty($_POST['is_critical']),
        ]);

        $msg = "Inspection submitted. Result: " . strtoupper($result) . ".";
        if ($res['room_ooo']) $msg .= " ⚠ Room set to OUT OF ORDER.";

        $_SESSION['hk_success'] = $msg;
        $this->redirect('housekeeping/qa');
    }

    /**
     * GET /housekeeping/qaScore/{inspection_id}
     * UC33: Score form for a completed inspection.
     */
    public function qaScore($inspectionId)
    {
        $this->requireLogin();
        $qa         = new QAInspection();
        $inspection = $qa->findInspection((int)$inspectionId);
        if (!$inspection) die("Inspection #$inspectionId not found.");

        // Load housekeeper list
        $db = (new Model())->getDb();
        $rh = mysqli_query($db,
            "SELECT u.id, u.name FROM users u
             JOIN   roles r ON u.role_id = r.id
             WHERE  r.name = 'housekeeper' AND u.is_active = 1
             ORDER  BY u.name");
        $housekeepers = $rh ? mysqli_fetch_all($rh, MYSQLI_ASSOC) : [];

        $this->view('housekeeping/qa_score', compact('inspection', 'housekeepers'));
    }

    /**
     * POST /housekeeping/qaScoreSubmit
     * UC33 Step 4: Submit quality score.
     */
    public function qaScoreSubmit()
    {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('housekeeping/qa');
            return;
        }

        $qa = new QAInspection();

        $inspId = (int) ($_POST['inspection_id'] ?? 0);
        $insp   = $qa->findInspection($inspId);
        if (!$insp) {
            $_SESSION['hk_error'] = 'Inspection not found.';
            $this->redirect('housekeeping/qa');
            return;
        }

        // Validate 0–100
        foreach (['cleanliness','presentation','completeness','speed'] as $dim) {
            $v = (int)($_POST[$dim] ?? 0);
            if ($v < 0 || $v > 100) {
                $_SESSION['hk_error'] = "Score must be 0–100. Invalid value for $dim.";
                $this->redirect("housekeeping/qaScore/$inspId");
                return;
            }
        }

        $result = $qa->submitScore([
            'inspection_id'  => $inspId,
            'housekeeper_id' => (int) ($_POST['housekeeper_id'] ?? 0),
            'room_id'        => (int) $insp['room_id'],
            'cleanliness'    => (int) ($_POST['cleanliness']   ?? 0),
            'presentation'   => (int) ($_POST['presentation']  ?? 0),
            'completeness'   => (int) ($_POST['completeness']  ?? 0),
            'speed'          => (int) ($_POST['speed']         ?? 0),
            'notes'          => trim($_POST['notes'] ?? ''),
            'photo_urls'     => [],
        ]);

        $msg = "Score submitted. Overall: {$result['overall']}/100.";
        if ($result['follow_up_triggered']) $msg .= " ⚠ Follow-up alert created (score < 60).";

        $_SESSION['hk_success'] = $msg;
        $this->redirect('housekeeping/qa');
    }

    /**
     * POST /housekeeping/disputeScore/{score_id}
     * UC33 Step 6a: Supervisor marks a quality_score as disputed.
     * Roles: supervisor, manager.
     */
    public function disputeScore($scoreId)
    {
        $this->requireLogin();
        $this->requireRoles(['supervisor', 'manager']);

        $note = trim($_POST['dispute_note'] ?? '');
        if ($note === '') {
            $_SESSION['hk_error'] = 'A dispute note is required.';
            $this->redirect('housekeeping/qa');
            return;
        }

        $qa  = new QAInspection();
        $ok  = $qa->disputeScore((int)$scoreId, $note);

        $uid = (int)($_SESSION['user_id'] ?? 0);
        AuditLog::log($uid, 'qa_score.disputed', 'quality_scores', (int)$scoreId);

        $_SESSION[$ok ? 'hk_success' : 'hk_error'] = $ok
            ? "Score #$scoreId flagged as disputed."
            : "Could not flag score — not found or already resolved.";
        $this->redirect('housekeeping/qa');
    }

    /**
     * POST /housekeeping/resolveDispute/{score_id}
     * UC33 Step 6b: Manager resolves dispute with override code.
     * Roles: manager only.
     */
    public function resolveDispute($scoreId)
    {
        $this->requireLogin();
        $this->requireRoles(['manager']);

        $overrideCode = trim($_POST['manager_override_code'] ?? '');
        $reason       = trim($_POST['resolution_reason']    ?? '');

        if ($overrideCode === '' || $reason === '') {
            $_SESSION['hk_error'] = 'Manager override code and resolution reason are required.';
            $this->redirect('housekeeping/qa');
            return;
        }

        // Simple validation: override code must match configured constant (default: 'MGR-OVERRIDE')
        $validCode = defined('MANAGER_OVERRIDE_CODE') ? MANAGER_OVERRIDE_CODE : 'MGR-OVERRIDE';
        if ($overrideCode !== $validCode) {
            $_SESSION['hk_error'] = 'Invalid manager override code.';
            $this->redirect('housekeeping/qa');
            return;
        }

        $scoreId = (int) $scoreId;
        $uid     = (int) ($_SESSION['user_id'] ?? 0);
        $db      = (new Model())->getDb();
        $reason  = mysqli_real_escape_string($db, $reason);

        mysqli_query($db,
            "UPDATE quality_scores
             SET    is_disputed = 0, dispute_resolution = '$reason'
             WHERE  id = $scoreId AND is_disputed = 1");
        $ok = mysqli_affected_rows($db) > 0;

        AuditLog::log($uid, 'qa_score.dispute_resolved', 'quality_scores', $scoreId);

        $_SESSION[$ok ? 'hk_success' : 'hk_error'] = $ok
            ? "Dispute on score #$scoreId resolved."
            : "Could not resolve — score not found or not disputed.";
        $this->redirect('housekeeping/qa');
    }

    /**
     * GET /housekeeping/qaTrends
     * UC32 Step 5: QA trends dashboard.
     */
    public function qaTrends()
    {
        $this->requireLogin();
        $qa   = new QAInspection();
        $days = (int) ($_GET['period'] ?? 30);
        $this->view('housekeeping/qa_trends', ['trends' => $qa->getTrends($days), 'days' => $days]);
    }

    // ── UC31: Low-Stock Alerts ────────────────────────────────

    /**
     * GET /housekeeping/stockAlerts
     * Dashboard: active low-stock alerts.
     * Roles: housekeeper, supervisor, manager.
     */
    public function stockAlerts()
    {
        $this->requireLogin();
        $this->requireHousekeeping();

        $sa = new StockAlert();

        // Run escalation pass on every page load (2-hour rule)
        $sa->escalateOverdue();

        $alerts = $sa->getActiveAlerts();

        // Load supply items for the requisition form
        $db = (new Model())->getDb();
        $r  = mysqli_query($db,
            "SELECT si.id, si.name, si.unit,
                    COALESCE(inv.current_stock, 0) AS current_stock,
                    si.min_threshold
             FROM   supply_items si
             LEFT JOIN supply_inventory inv ON inv.item_id = si.id
             WHERE  si.is_active = 1
             ORDER  BY si.name");
        $supplyItems = $r ? mysqli_fetch_all($r, MYSQLI_ASSOC) : [];

        $this->view('housekeeping/stock_alerts', compact('alerts', 'supplyItems'));
    }

    /**
     * POST /housekeeping/acknowledgeAlert/{id}
     * UC31 Step 3: Acknowledge a low-stock alert.
     * Roles: housekeeper, supervisor, manager.
     */
    public function acknowledgeAlert($alertId)
    {
        $this->requireLogin();
        $this->requireHousekeeping();

        $sa   = new StockAlert();
        $ok   = $sa->acknowledge((int)$alertId);
        $_SESSION['hk_success'] = $ok
            ? "Alert #$alertId acknowledged."
            : "Could not acknowledge — already resolved or not found.";
        $this->redirect('housekeeping/stockAlerts');
    }

    /**
     * POST /housekeeping/createRequisition
     * UC31 Step 4: Submit restocking requisition.
     * Roles: housekeeper, supervisor, manager.
     */
    public function createRequisition()
    {
        $this->requireLogin();
        $this->requireHousekeeping();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('housekeeping/stockAlerts');
            return;
        }

        $alertIds = array_map('intval', $_POST['alert_ids'] ?? []);
        $rawItems = $_POST['items'] ?? [];
        $items    = [];
        foreach ($rawItems as $row) {
            $iid = (int)($row['item_id'] ?? 0);
            $qty = (int)($row['quantity_needed'] ?? 0);
            if ($iid > 0 && $qty > 0) {
                $items[] = ['item_id' => $iid, 'quantity_needed' => $qty];
            }
        }

        if (empty($items)) {
            $_SESSION['hk_error'] = 'Enter at least one item with a quantity.';
            $this->redirect('housekeeping/stockAlerts');
            return;
        }

        $sa    = new StockAlert();
        $reqId = $sa->createRequisition($alertIds, $items);
        $_SESSION['hk_success'] = "Requisition #$reqId created. " . count($alertIds) . " alert(s) resolved.";
        $this->redirect('housekeeping/stockAlerts');
    }

    /**
     * POST /housekeeping/dismissAlert/{id}
     * UC31 Error: Supervisor dismisses a false-threshold alert with logged reason.
     * Roles: supervisor, manager only.
     */
    public function dismissAlert($alertId)
    {
        $this->requireLogin();
        $this->requireRoles(['supervisor', 'manager']);

        $reason = trim($_POST['reason'] ?? '');
        if ($reason === '') {
            $_SESSION['hk_error'] = 'A reason is required to dismiss an alert.';
            $this->redirect('housekeeping/stockAlerts');
            return;
        }

        $alertId = (int) $alertId;
        $uid     = (int) ($_SESSION['user_id'] ?? 0);
        $db      = (new Model())->getDb();
        $reason  = mysqli_real_escape_string($db, $reason);

        mysqli_query($db,
            "UPDATE low_stock_alerts
             SET    status = 'resolved',
                    acknowledged_by  = $uid,
                    acknowledged_at  = NOW(),
                    dismiss_reason   = '$reason'
             WHERE  id = $alertId");

        // Log the dismissal to audit_log
        AuditLog::log($uid, 'stock_alert.dismissed', 'low_stock_alerts', $alertId);

        $_SESSION['hk_success'] = "Alert #$alertId dismissed.";
        $this->redirect('housekeeping/stockAlerts');
    }
}
