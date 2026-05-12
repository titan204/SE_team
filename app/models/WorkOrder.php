<?php


class WorkOrder extends AbstractModel
{
    public function __construct($db = null, array $aggregates = [])
    {
        parent::__construct($db, $aggregates);
        $this->registerAggregate('rooms', Room::class);
        $this->registerAggregate('users', User::class);
        $this->registerAggregate('auditLogs', AuditLog::class);
    }

    
    public function getList(array $filters = []): array
    {
        $where = ['1=1'];
        if (!empty($filters['status'])) {
            $s = mysqli_real_escape_string($this->db, $filters['status']);
            $where[] = "wo.status = '$s'";
        }
        if (!empty($filters['priority'])) {
            $p = mysqli_real_escape_string($this->db, $filters['priority']);
            $where[] = "wo.priority = '$p'";
        }
        if (!empty($filters['type'])) {
            $t = mysqli_real_escape_string($this->db, $filters['type']);
            $where[] = "wo.type = '$t'";
        }
        if (!empty($filters['technician'])) {
            $u = (int) $filters['technician'];
            $where[] = "wo.assigned_to_user_id = $u";
        }
        if (!empty($filters['date_from'])) {
            $d = mysqli_real_escape_string($this->db, $filters['date_from']);
            $where[] = "DATE(wo.created_at) >= '$d'";
        }
        if (!empty($filters['date_to'])) {
            $d = mysqli_real_escape_string($this->db, $filters['date_to']);
            $where[] = "DATE(wo.created_at) <= '$d'";
        }
        $w = implode(' AND ', $where);

        $r = mysqli_query($this->db,
            "SELECT wo.*,
                    rm.room_number,
                    a.name  AS asset_name,
                    ua.name AS assigned_name,
                    uc.name AS created_by_name
             FROM   work_orders wo
             LEFT   JOIN rooms rm ON wo.room_id   = rm.id
             LEFT   JOIN assets a  ON wo.asset_id  = a.id
             LEFT   JOIN users  ua ON wo.assigned_to_user_id = ua.id
             LEFT   JOIN users  uc ON wo.created_by_user_id  = uc.id
             WHERE  $w
             ORDER  BY FIELD(wo.priority,'emergency','high','normal','low'),
                        wo.created_at DESC");
        if (!$r) return [];
        return mysqli_fetch_all($r, MYSQLI_ASSOC);
    }

    
    public function find(int $id): ?array
    {
        $id = (int) $id;
        $r  = mysqli_query($this->db,
            "SELECT wo.*, rm.room_number, a.name AS asset_name,
                    ua.name AS assigned_name, uc.name AS created_by_name,
                    us.name AS supervisor_name
             FROM   work_orders wo
             LEFT   JOIN rooms rm ON wo.room_id             = rm.id
             LEFT   JOIN assets a  ON wo.asset_id            = a.id
             LEFT   JOIN users  ua ON wo.assigned_to_user_id = ua.id
             LEFT   JOIN users  uc ON wo.created_by_user_id  = uc.id
             LEFT   JOIN users  us ON wo.supervisor_id       = us.id
             WHERE  wo.id = $id LIMIT 1");
        if (!$r) return null;
        return mysqli_fetch_assoc($r) ?: null;
    }

    
    public function createWorkOrder(array $data): int
    {
        $type        = in_array($data['type'] ?? '', ['emergency','preventative']) ? $data['type'] : 'emergency';
        $roomId      = !empty($data['room_id'])   ? (int)$data['room_id']   : 'NULL';
        $assetId     = !empty($data['asset_id'])  ? (int)$data['asset_id']  : 'NULL';
        $desc        = mysqli_real_escape_string($this->db, $data['description'] ?? '');
        $priority    = in_array($data['priority'] ?? '', ['low','normal','high','emergency']) ? $data['priority'] : 'normal';
        $assigned    = !empty($data['assigned_to_user_id']) ? (int)$data['assigned_to_user_id'] : 'NULL';
        $created     = (int) ($_SESSION['user_id'] ?? 0);
        
        $failType    = mysqli_real_escape_string($this->db, $data['failure_type'] ?? '');
        $failTypeSql = $failType ? "'$failType'" : 'NULL';
        $contractor  = (int) !empty($data['contractor_required']);
        $safetyRisk  = (int) !empty($data['immediate_safety_risk']);

        mysqli_query($this->db,
            "INSERT INTO work_orders
                 (type, failure_type, room_id, asset_id, description, priority,
                  assigned_to_user_id, created_by_user_id, contractor_required, immediate_safety_risk)
             VALUES ('$type', $failTypeSql, $roomId, $assetId, '$desc', '$priority',
                     $assigned, $created, $contractor, $safetyRisk)");
        $id = (int) mysqli_insert_id($this->db);

        $this->logAction($id, 'created');
        return $id;
    }

    
    public function updateWorkOrder(int $id, array $fields): bool
    {
        $id   = (int) $id;
        $sets = [];
        $allowed = ['status','priority','assigned_to_user_id','description'];
        foreach ($allowed as $f) {
            if (!array_key_exists($f, $fields)) continue;
            $v = mysqli_real_escape_string($this->db, (string)$fields[$f]);
            $sets[] = "$f = '$v'";
        }
        if (empty($sets)) return false;
        $setStr = implode(', ', $sets);
        $r = mysqli_query($this->db, "UPDATE work_orders SET $setStr WHERE id = $id");
        if ($r) $this->logAction($id, 'updated');
        return (bool) $r;
    }

    
    public function technicianCompleteWO(int $id, array $data): bool
    {
        $id      = (int) $id;
        $work    = mysqli_real_escape_string($this->db, $data['work_performed'] ?? '');
        $parts   = mysqli_real_escape_string($this->db, json_encode($data['parts_used'] ?? []));
        $minutes = (int) ($data['time_spent_minutes'] ?? 0);

        $r = mysqli_query($this->db,
            "UPDATE work_orders
             SET    status = 'completed', work_performed = '$work',
                    parts_used = '$parts', time_spent_minutes = $minutes,
                    completed_at = NOW()
             WHERE  id = $id AND status IN ('open','in_progress','pending_parts')");
        if ($r) $this->logAction($id, 'completed_by_technician');
        return (bool) $r;
    }

    
    public function supervisorCloseWO(int $id, int $supervisorId): array
    {
        $id  = (int) $id;
        $sid = (int) $supervisorId;

        $wo = $this->find($id);
        if (!$wo) return ['success' => false, 'error' => 'Work order not found.'];
        if (empty($wo['work_performed'])) {
            return ['success' => false, 'error' => 'Cannot close: work_performed is required.'];
        }

        mysqli_query($this->db,
            "UPDATE work_orders
             SET    status = 'closed', supervisor_id = $sid, closed_at = NOW()
             WHERE  id = $id AND status = 'completed'");

        
        if (!empty($wo['room_id'])) {
            mysqli_query($this->db,
                "UPDATE rooms SET status = 'available' WHERE id = {$wo['room_id']} AND status = 'out_of_order'");
        }

        $this->logAction($id, 'closed_by_supervisor');
        return ['success' => true];
    }

    
    public function reject(int $id, string $reason): bool
    {
        $id     = (int) $id;
        $reason = mysqli_real_escape_string($this->db, $reason);
        if (!$reason) return false;
        $r = mysqli_query($this->db,
            "UPDATE work_orders
             SET    status = 'rejected', rejection_reason = '$reason'
             WHERE  id = $id AND status NOT IN ('closed','rejected')");
        if ($r) $this->logAction($id, 'rejected', $reason);
        return (bool) $r;
    }

    
    public function updateProgress(int $id, string $notes): bool
    {
        $id    = (int) $id;
        $notes = mysqli_real_escape_string($this->db, $notes);
        $r     = mysqli_query($this->db,
            "UPDATE work_orders SET status = 'in_progress' WHERE id = $id AND status = 'open'");
        $this->logAction($id, 'progress_update', $notes);
        return (bool) $r;
    }

    
    public function createEmergency(array $data): array
    {
        
        $data['type']     = 'emergency';
        $data['priority'] = 'emergency';
        $woId = $this->createWorkOrder($data);

        $severity  = in_array($data['severity'] ?? '', ['low','medium','high','safety_critical'])
                     ? $data['severity'] : 'high';
        $isCrit    = (int) ($severity === 'safety_critical');

        
        mysqli_query($this->db,
            "INSERT INTO emergency_flags (work_order_id, severity, is_safety_critical)
             VALUES ($woId, '$severity', $isCrit)");

        
        if (!empty($data['room_id'])) {
            $rid = (int) $data['room_id'];
            mysqli_query($this->db, "UPDATE rooms SET status = 'out_of_order' WHERE id = $rid");
        }

        
        $propAlert   = false;
        $isSafetyRisk = !empty($data['immediate_safety_risk']);
        if ($isCrit || $isSafetyRisk) {
            $alertType = $isCrit ? 'safety_critical_emergency' : 'immediate_safety_risk';
            $prefix    = $isCrit ? 'SAFETY CRITICAL' : 'IMMEDIATE SAFETY RISK';
            $msg = mysqli_real_escape_string($this->db,
                "$prefix: " . ($data['description'] ?? '') . " — Work Order #$woId");
            mysqli_query($this->db,
                "INSERT INTO property_wide_alerts (alert_type, message, triggered_by_work_order_id)
                 VALUES ('$alertType', '$msg', $woId)");
            mysqli_query($this->db,
                "UPDATE emergency_flags SET property_alert_triggered = 1 WHERE work_order_id = $woId");
            $propAlert = true;
        }

        
        $this->checkRecurringEmergency($data, $woId);

        return ['work_order_id' => $woId, 'property_alert' => $propAlert];
    }

    private function checkRecurringEmergency(array $data, int $woId): void
    {
        $cond = [];
        if (!empty($data['room_id'])) {
            $rid   = (int) $data['room_id'];
            $cond[] = "room_id = $rid";
        }
        if (!empty($data['asset_id'])) {
            $aid   = (int) $data['asset_id'];
            $cond[] = "asset_id = $aid";
        }
        if (empty($cond)) return;

        $w = implode(' OR ', $cond);
        $r = mysqli_query($this->db,
            "SELECT COUNT(*) AS cnt FROM work_orders
             WHERE  type = 'emergency'
               AND  created_at >= NOW() - INTERVAL 30 DAY
               AND  ($w)");
        $row = $r ? mysqli_fetch_assoc($r) : null;
        if (!$row || (int)$row['cnt'] < 3) return;

        
        $rid   = !empty($data['room_id'])  ? (int)$data['room_id']  : 'NULL';
        $aid   = !empty($data['asset_id']) ? (int)$data['asset_id'] : 'NULL';
        $count = (int) $row['cnt'];
        $existing = mysqli_query($this->db,
            "SELECT id FROM replacement_review_flags
             WHERE  room_id <=> $rid AND asset_id <=> $aid AND reviewed = 0 LIMIT 1");
        if ($existing && mysqli_num_rows($existing) > 0) return; // already flagged

        mysqli_query($this->db,
            "INSERT INTO replacement_review_flags (room_id, asset_id, emergency_count)
             VALUES ($rid, $aid, $count)");
    }

    
    public function checkAvailability(int $roomId, string $date): array
    {
        $rid  = (int) $roomId;
        $d    = mysqli_real_escape_string($this->db, $date);

        
        $rr = mysqli_query($this->db,
            "SELECT id FROM reservations
             WHERE  room_id = $rid AND status IN ('confirmed','checked_in')
               AND  check_in_date <= '$d' AND check_out_date >= '$d'
             LIMIT  1");
        $resConflict = $rr && mysqli_num_rows($rr) > 0;

        
        $rw = mysqli_query($this->db,
            "SELECT wo.id FROM work_orders wo
             JOIN   preventative_schedules ps ON ps.work_order_id = wo.id
             WHERE  wo.room_id = $rid AND ps.scheduled_date = '$d'
               AND  wo.status NOT IN ('closed','rejected')
             LIMIT  1");
        $woConflict = $rw && mysqli_num_rows($rw) > 0;

        $conflict = $resConflict || $woConflict;

        
        $alternatives = [];
        if ($conflict) {
            $checkDate = new DateTime($date);
            for ($i = 1; $i <= 14 && count($alternatives) < 3; $i++) {
                $checkDate->modify('+1 day');
                $altDate = $checkDate->format('Y-m-d');
                $result  = $this->checkAvailability($rid, $altDate);
                if (!$result['conflict']) {
                    $alternatives[] = $altDate;
                }
            }
        }

        return compact('conflict', 'alternatives');
    }

    
    public function createPreventative(array $data): array
    {
        $data['type'] = 'preventative';
        $woId = $this->createWorkOrder($data);

        $assetId   = !empty($data['asset_id'])  ? (int)$data['asset_id']  : 'NULL';
        $roomId    = !empty($data['room_id'])   ? (int)$data['room_id']   : 'NULL';
        $maintType = mysqli_real_escape_string($this->db, $data['maintenance_type'] ?? 'other');
        $schedDate = mysqli_real_escape_string($this->db, $data['scheduled_date'] ?? date('Y-m-d'));
        $estMins   = (int) ($data['estimated_minutes'] ?? 60);
        $isRecur   = (int) !empty($data['is_recurring']);
        $freq      = $data['recurrence_frequency'] ?? null;
        $freqSql   = $freq ? "'".mysqli_real_escape_string($this->db, $freq)."'" : 'NULL';
        $nextDue   = $isRecur ? "'".mysqli_real_escape_string($this->db, $this->calcNextDue($schedDate, $freq))."'" : 'NULL';

        mysqli_query($this->db,
            "INSERT INTO preventative_schedules
                 (work_order_id, asset_id, room_id, maintenance_type, scheduled_date,
                  estimated_minutes, is_recurring, recurrence_frequency, next_due_date)
             VALUES ($woId, $assetId, $roomId, '$maintType', '$schedDate',
                     $estMins, $isRecur, $freqSql, $nextDue)");

        
        if (!empty($data['room_id'])) {
            $rid = (int) $data['room_id'];
            mysqli_query($this->db, "UPDATE rooms SET status = 'out_of_order' WHERE id = $rid");
        }

        return ['work_order_id' => $woId, 'is_recurring' => (bool)$isRecur];
    }

    private function calcNextDue(string $date, ?string $freq): string
    {
        $d = new DateTime($date);
        switch ($freq) {
            case 'weekly':    $d->modify('+1 week');    break;
            case 'monthly':   $d->modify('+1 month');   break;
            case 'quarterly': $d->modify('+3 months');  break;
            case 'yearly':    $d->modify('+1 year');    break;
        }
        return $d->format('Y-m-d');
    }

    
    public function getTechnicians(): array
    {
        
        $r = mysqli_query($this->db,
            "SELECT u.id, u.name FROM users u
             JOIN   roles r ON u.role_id = r.id
             WHERE  r.name IN ('housekeeper','manager') AND u.is_active = 1
             ORDER  BY u.name");
        if (!$r) return [];
        return mysqli_fetch_all($r, MYSQLI_ASSOC);
    }

    
    public function getAssets(): array
    {
        $r = mysqli_query($this->db,
            "SELECT * FROM assets WHERE status != 'decommissioned' ORDER BY name");
        if (!$r) return [];
        return mysqli_fetch_all($r, MYSQLI_ASSOC);
    }

    
    public function getRooms(): array
    {
        $r = mysqli_query($this->db,
            "SELECT id, room_number, floor, status FROM rooms ORDER BY room_number");
        if (!$r) return [];
        return mysqli_fetch_all($r, MYSQLI_ASSOC);
    }

    
    public function escalateOverdue(): array
    {
        $today = date('Y-m-d');

        
        $r = mysqli_query($this->db,
            "SELECT wo.id
             FROM   work_orders wo
             JOIN   preventative_schedules ps ON ps.work_order_id = wo.id
             WHERE  wo.type   = 'preventative'
               AND  wo.status = 'open'
               AND  ps.scheduled_date < '$today'
               AND  wo.priority != 'high'");
        if (!$r) return [];

        $escalated = [];
        while ($row = mysqli_fetch_assoc($r)) {
            $woId = (int) $row['id'];
            mysqli_query($this->db,
                "UPDATE work_orders SET priority = 'high' WHERE id = $woId");
            $this->logAction($woId, 'overdue_escalated',
                "Scheduled date passed — priority escalated to HIGH by system.");
            $escalated[] = $woId;
        }

        
        if (!empty($escalated)) {
            $uid  = (int) ($_SESSION['user_id'] ?? 0);
            $ids  = implode(',', $escalated);
            $note = mysqli_real_escape_string($this->db,
                "Overdue escalation: WO IDs [$ids] set to HIGH priority.");
            mysqli_query($this->db,
                "INSERT INTO audit_log (user_id, action, target_type, target_id, new_value)
                 VALUES ($uid, 'preventative_overdue_escalation', 'work_orders', 0, '$note')");
        }

        return $escalated;
    }

    
    public function cancelAssetTasks(int $assetId, string $reason): int
    {
        $assetId = (int) $assetId;
        $reason  = mysqli_real_escape_string($this->db, $reason);

        
        $r = mysqli_query($this->db,
            "SELECT id FROM work_orders
             WHERE  asset_id = $assetId AND status IN ('open','in_progress','pending_parts')");
        if (!$r) return 0;

        $count = 0;
        while ($row = mysqli_fetch_assoc($r)) {
            $woId = (int) $row['id'];
            mysqli_query($this->db,
                "UPDATE work_orders
                 SET    status = 'rejected', rejection_reason = '$reason'
                 WHERE  id = $woId");
            $this->logAction($woId, 'cancelled_asset_decommissioned', $reason);
            $count++;
        }

        
        mysqli_query($this->db,
            "DELETE ps FROM preventative_schedules ps
             JOIN   work_orders wo ON ps.work_order_id = wo.id
             WHERE  ps.asset_id = $assetId AND wo.status = 'rejected'");

        return $count;
    }

    
    public function decommissionAsset(int $assetId, string $reason): array
    {
        $assetId = (int) $assetId;
        mysqli_query($this->db,
            "UPDATE assets SET status = 'decommissioned' WHERE id = $assetId");
        $cancelled = $this->cancelAssetTasks($assetId, $reason ?: 'Asset decommissioned.');
        return ['asset_id' => $assetId, 'cancelled_work_orders' => $cancelled];
    }

    

    private function logAction(int $woId, string $action, string $notes = ''): void
    {
        $uid   = (int) ($_SESSION['user_id'] ?? 0);
        $act   = mysqli_real_escape_string($this->db, $action);
        $notes = mysqli_real_escape_string($this->db, $notes);
        mysqli_query($this->db,
            "INSERT INTO work_order_logs (work_order_id, action, performed_by_user_id, notes)
             VALUES ($woId, '$act', $uid, '$notes')");
    }

    
    public function getLogs(int $woId): array
    {
        $id = (int) $woId;
        $r  = mysqli_query($this->db,
            "SELECT wol.*, u.name AS performed_by_name
             FROM   work_order_logs wol
             LEFT   JOIN users u ON wol.performed_by_user_id = u.id
             WHERE  wol.work_order_id = $id
             ORDER  BY wol.created_at ASC");
        if (!$r) return [];
        return mysqli_fetch_all($r, MYSQLI_ASSOC);
    }
}
