<?php
// ============================================================
//  MaintenanceController — UC34/UC35/UC36
//  Routes:
//    /maintenance                         → index (work-order list)
//    /maintenance/show/5                  → order details
//    /maintenance/emergency               → UC35 form / submit
//    /maintenance/preventative            → UC36 form / submit
//    /maintenance/workOrders              → GET list (alias)
//    /maintenance/progress/5              → POST technician progress
//    /maintenance/complete/5              → POST technician complete
//    /maintenance/close/5                 → POST supervisor close
//    /maintenance/reject/5                → POST supervisor reject
//    /maintenance/availability            → GET conflict check (UC36)
// ============================================================

class MaintenanceController extends Controller
{
    /** Restrict to maintenance staff and management only. */
    private function requireMaintenance(): void
    {
        $this->requireRoles(['maintenance_technician', 'maintenance', 'manager', 'supervisor']);
    }

    // ── UC34: Work-order list ─────────────────────────────────

    public function index()
    {
        $this->requireLogin();
        $this->requireMaintenance();
        $wo      = new WorkOrder();

        // UC36: Run overdue escalation on every page load for supervisors/managers
        $role = $_SESSION['user_role'] ?? '';
        if (in_array($role, ['supervisor', 'manager'])) {
            $wo->escalateOverdue();
        }

        $filters = [
            'status'     => $_GET['status']     ?? '',
            'priority'   => $_GET['priority']   ?? '',
            'type'       => $_GET['type']       ?? '',
            'technician' => $_GET['technician'] ?? '',
            'date_from'  => $_GET['date_from']  ?? '',
            'date_to'    => $_GET['date_to']    ?? '',
        ];
        $orders      = $wo->getList($filters);
        $technicians = $wo->getTechnicians();

        $this->view('maintenance/index', compact('orders', 'filters', 'technicians'));
    }

    public function show($id)
    {
        $this->requireLogin();
        $this->requireMaintenance();
        $wo    = new WorkOrder();
        $order = $wo->find((int)$id);
        if (!$order) die("Work order #$id not found.");
        $logs = $wo->getLogs((int)$id);
        $this->view('maintenance/show', compact('order', 'logs'));
    }

    // ── UC35: Emergency Repair ────────────────────────────────

    /**
     * GET /maintenance/emergency — show the emergency form.
     * POST /maintenance/emergency — create emergency work order.
     */
    public function emergency()
    {
        $this->requireLogin();
        $this->requireMaintenance();

        $wo = new WorkOrder();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (empty($_POST['description'])) {
                $_SESSION['maint_error'] = 'Description is required.';
                $this->redirect('maintenance/emergency');
                return;
            }

            $result = $wo->createEmergency([
                'description'          => trim($_POST['description']  ?? ''),
                'room_id'              => !empty($_POST['room_id'])  ? (int)$_POST['room_id']  : null,
                'asset_id'             => !empty($_POST['asset_id']) ? (int)$_POST['asset_id'] : null,
                'failure_type'         => $_POST['failure_type']        ?? 'other',
                'severity'             => $_POST['severity']            ?? 'high',
                'immediate_safety_risk'=> !empty($_POST['immediate_safety_risk']),
                'contractor_required'  => !empty($_POST['contractor_required']),
                'assigned_to_user_id'  => !empty($_POST['assigned_to']) ? (int)$_POST['assigned_to'] : null,
            ]);

            $msg = "Emergency work order #{$result['work_order_id']} created.";
            if ($result['property_alert']) $msg .= ' ⚠ Property-wide safety alert triggered.';
            $_SESSION['maint_success'] = $msg;
            $this->redirect('maintenance/index');
            return;
        }

        $this->view('maintenance/emergency', [
            'rooms'       => $wo->getRooms(),
            'assets'      => $wo->getAssets(),
            'technicians' => $wo->getTechnicians(),
        ]);
    }

    // ── UC36: Preventative Maintenance ───────────────────────

    /**
     * GET /maintenance/preventative — show scheduling form.
     * POST /maintenance/preventative — create preventative work order.
     */
    public function preventative()
    {
        $this->requireLogin();
        $this->requireMaintenance();

        $wo = new WorkOrder();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (empty($_POST['description'])) {
                $_SESSION['maint_error'] = 'Description is required.';
                $this->redirect('maintenance/preventative');
                return;
            }

            // Conflict check before accepting
            $roomId = !empty($_POST['room_id']) ? (int)$_POST['room_id'] : null;
            $date   = $_POST['scheduled_date'] ?? date('Y-m-d');
            if ($roomId && empty($_POST['override_conflict'])) {
                $avail = $wo->checkAvailability($roomId, $date);
                if ($avail['conflict']) {
                    $_SESSION['maint_conflict']      = true;
                    $_SESSION['maint_alternatives']  = $avail['alternatives'];
                    $_SESSION['maint_form_data']     = $_POST;
                    $this->redirect('maintenance/preventative');
                    return;
                }
            }

            $estMins = ((int)($_POST['est_hours'] ?? 0) * 60) + (int)($_POST['est_minutes'] ?? 0);

            $result = $wo->createPreventative([
                'description'          => trim($_POST['description']  ?? ''),
                'room_id'              => $roomId,
                'asset_id'             => !empty($_POST['asset_id']) ? (int)$_POST['asset_id'] : null,
                'maintenance_type'     => $_POST['maintenance_type']      ?? 'other',
                'scheduled_date'       => $date,
                'estimated_minutes'    => max(15, $estMins),
                'assigned_to_user_id'  => !empty($_POST['assigned_to']) ? (int)$_POST['assigned_to'] : null,
                'is_recurring'         => !empty($_POST['is_recurring']),
                'recurrence_frequency' => $_POST['recurrence_frequency'] ?? null,
            ]);

            unset($_SESSION['maint_conflict'], $_SESSION['maint_alternatives'], $_SESSION['maint_form_data']);
            $msg = "Preventative work order #{$result['work_order_id']} scheduled.";
            if ($result['is_recurring']) $msg .= ' Recurring schedule created.';
            $_SESSION['maint_success'] = $msg;
            $this->redirect('maintenance/index');
            return;
        }

        $this->view('maintenance/preventative', [
            'rooms'        => $wo->getRooms(),
            'assets'       => $wo->getAssets(),
            'technicians'  => $wo->getTechnicians(),
            'conflict'     => $_SESSION['maint_conflict']     ?? false,
            'alternatives' => $_SESSION['maint_alternatives'] ?? [],
            'formData'     => $_SESSION['maint_form_data']    ?? [],
        ]);
    }

    /**
     * GET /maintenance/availability
     * UC36 conflict check (AJAX-friendly).
     */
    public function availability()
    {
        $this->requireLogin();
        // Availability check is also needed by housekeeping supervisors
        $this->requireRoles(['maintenance_technician', 'maintenance', 'manager', 'supervisor', 'housekeeper']);
        $wo     = new WorkOrder();
        $roomId = (int) ($_GET['room_id'] ?? 0);
        $date   = $_GET['date'] ?? date('Y-m-d');
        header('Content-Type: application/json');
        echo json_encode($wo->checkAvailability($roomId, $date));
        exit;
    }

    // ── UC34: Status transitions ──────────────────────────────

    /**
     * POST /maintenance/progress/{id}
     * Technician updates progress notes.
     */
    public function progress($id)
    {
        $this->requireLogin();
        $this->requireMaintenance();
        $wo    = new WorkOrder();
        $notes = trim($_POST['notes'] ?? '');
        $wo->updateProgress((int)$id, $notes);
        $_SESSION['maint_success'] = 'Progress updated.';
        $this->redirect("maintenance/show/$id");
    }

    /**
     * POST /maintenance/complete/{id}
     * Technician marks work complete.
     */
    public function complete($id)
    {
        $this->requireLogin();
        $this->requireMaintenance();
        $wo = new WorkOrder();
        if (empty($_POST['work_performed'])) {
            $_SESSION['maint_error'] = 'Work performed description is required.';
            $this->redirect("maintenance/show/$id");
            return;
        }
        $wo->technicianCompleteWO((int)$id, [
            'work_performed'    => trim($_POST['work_performed']  ?? ''),
            'parts_used'        => json_decode($_POST['parts_used'] ?? '[]', true) ?: [],
            'time_spent_minutes'=> (int)($_POST['time_spent_minutes'] ?? 0),
        ]);
        $_SESSION['maint_success'] = 'Work order marked as completed. Awaiting supervisor sign-off.';
        $this->redirect("maintenance/show/$id");
    }

    /**
     * POST /maintenance/close/{id}
     * Supervisor closes the work order.
     */
    public function close($id)
    {
        $this->requireLogin();
        $this->requireRoles(['supervisor', 'manager']); // supervisor sign-off only
        $wo     = new WorkOrder();
        $result = $wo->supervisorCloseWO((int)$id, (int)($_SESSION['user_id'] ?? 0));
        if (!$result['success']) {
            $_SESSION['maint_error'] = $result['error'];
        } else {
            $_SESSION['maint_success'] = 'Work order closed.';
        }
        $this->redirect("maintenance/show/$id");
    }

    /**
     * POST /maintenance/reject/{id}
     * Supervisor rejects the work order.
     */
    public function reject($id)
    {
        $this->requireLogin();
        $this->requireRoles(['supervisor', 'manager']); // supervisor action only
        $wo     = new WorkOrder();
        $reason = trim($_POST['rejection_reason'] ?? '');
        if (!$reason) {
            $_SESSION['maint_error'] = 'Rejection reason is required.';
            $this->redirect("maintenance/show/$id");
            return;
        }
        $wo->reject((int)$id, $reason);
        $_SESSION['maint_success'] = 'Work order rejected.';
        $this->redirect('maintenance/index');
    }

    // Keep legacy aliases for previously linked routes
    public function create()   { $this->redirect('maintenance/emergency'); }
    public function store()    { $this->redirect('maintenance/emergency'); }
    public function resolve($id) { $this->redirect("maintenance/show/$id"); }
    public function escalate($id){ $this->redirect("maintenance/show/$id"); }

    /**
     * POST /maintenance/decommission/{asset_id}
     * UC36 Step 4: Decommission an asset — manager only.
     * Cancels all open/in_progress WOs for that asset and logs cancellation.
     */
    public function decommission($assetId)
    {
        $this->requireLogin();
        $this->requireRoles(['manager']);

        $reason = trim($_POST['reason'] ?? 'Asset decommissioned by manager.');
        if (!$reason) {
            $_SESSION['maint_error'] = 'A cancellation reason is required.';
            $this->redirect('maintenance/index');
            return;
        }

        $wo     = new WorkOrder();
        $result = $wo->decommissionAsset((int)$assetId, $reason);

        $_SESSION['maint_success'] =
            "Asset #{$result['asset_id']} decommissioned. "
            . "{$result['cancelled_work_orders']} work order(s) cancelled.";
        $this->redirect('maintenance/index');
    }
}
