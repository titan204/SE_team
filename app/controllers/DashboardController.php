<?php


class DashboardController extends Controller
{
    public function index()
    {
        $this->requireLogin();
        $role = strtolower($_SESSION['user_role'] ?? '');
        if ($role === 'guest') { $this->redirect('rooms/guest'); return; }
        $stats = $this->loadStats();
        $this->view('dashboard/index', $stats);
    }

    
    public function stats()
    {
        $this->requireLogin();
        header('Content-Type: application/json');
        echo json_encode($this->loadStats());
        exit;
    }

    

    private function loadStats(): array
    {
        $role = strtolower($_SESSION['user_role'] ?? '');
        $db   = (new Model())->getDb();

        if ($role === 'housekeeper') {
            return [
                'pending_hk_tasks' => $this->pendingHkTasks($db),
                'rooms_by_status'  => $this->roomsByStatus($db),
                'hk_my_tasks'      => $this->myHkTasks($db),
            ];
        }

        if ($role === 'front_desk') {
            return [
                'reservations_today' => $this->reservationsToday($db),
                'rooms_by_status'    => $this->roomsByStatus($db),
                'upcoming_checkins'  => $this->upcomingCheckins($db),
                'upcoming_checkouts' => $this->upcomingCheckouts($db),
                'vip_arrivals'       => $this->vipArrivals($db),
            ];
        }

        if ($role === 'revenue_manager') {
            return [
                'reservations_today' => $this->reservationsToday($db),
                'rooms_by_status'    => $this->roomsByStatus($db),
                'revenue_today'      => $this->revenueToday($db),
                'revenue_month'      => $this->revenueMonth($db),
                'revenue_prev_month' => $this->revenuePrevMonth($db),
                'occupancy_rate'     => $this->occupancyRate($db),
                'upcoming_checkins'  => $this->upcomingCheckins($db),
            ];
        }

        
        return [
            'reservations_today' => $this->reservationsToday($db),
            'rooms_by_status'    => $this->roomsByStatus($db),
            'pending_hk_tasks'   => $this->pendingHkTasks($db),
            'revenue_today'      => $this->revenueToday($db),
            'revenue_month'      => $this->revenueMonth($db),
            'upcoming_checkins'  => $this->upcomingCheckins($db),
            'upcoming_checkouts' => $this->upcomingCheckouts($db),
            'vip_arrivals'       => $this->vipArrivals($db),
            'open_work_orders'   => $this->openWorkOrders($db),
            'open_disputes'      => $this->openDisputes($db),
        ];
    }

    
    private function reservationsToday($db): int
    {
        $r = mysqli_query($db,
            "SELECT COUNT(*) AS cnt FROM reservations
             WHERE (check_in_date = CURDATE() OR check_out_date = CURDATE())
               AND status NOT IN ('cancelled','no_show')");
        return (int)(mysqli_fetch_assoc($r)['cnt'] ?? 0);
    }

    
    private function roomsByStatus($db): array
    {
        $r    = mysqli_query($db, "SELECT status, COUNT(*) AS cnt FROM rooms GROUP BY status");
        $data = ['available' => 0, 'occupied' => 0, 'dirty' => 0,
                 'cleaning'  => 0, 'inspecting' => 0, 'out_of_order' => 0];
        if ($r) { while ($row = mysqli_fetch_assoc($r)) { $data[$row['status']] = (int)$row['cnt']; } }
        $data['total'] = array_sum($data);
        return $data;
    }

    
    private function pendingHkTasks($db): int
    {
        $r = mysqli_query($db,
            "SELECT COUNT(*) AS cnt FROM housekeeping_tasks
             WHERE status IN ('pending','in_progress')");
        return (int)(mysqli_fetch_assoc($r)['cnt'] ?? 0);
    }

    
    private function myHkTasks($db): array
    {
        $uid = (int)($_SESSION['user_id'] ?? 0);
        $r   = mysqli_query($db,
            "SELECT ht.id, ht.task_type, ht.status, ht.notes, rm.room_number
             FROM   housekeeping_tasks ht
             LEFT   JOIN rooms rm ON ht.room_id = rm.id
             WHERE  ht.assigned_to = $uid AND ht.status NOT IN ('done','skipped')
             ORDER  BY ht.created_at DESC LIMIT 10");
        return $r ? mysqli_fetch_all($r, MYSQLI_ASSOC) : [];
    }

    
    private function revenueToday($db): float
    {
        
        $r = mysqli_query($db,
            "SELECT COALESCE(SUM(p.amount), 0) AS total
             FROM   payments p
             WHERE  DATE(p.processed_at) = CURDATE()");
        $fromPayments = (float)(mysqli_fetch_assoc($r)['total'] ?? 0);

        
        if ($fromPayments <= 0) {
            $r2 = mysqli_query($db,
                "SELECT COALESCE(SUM(f.amount_paid), 0) AS total
                 FROM   folios f
                 WHERE  DATE(f.updated_at) = CURDATE()
                   AND  f.amount_paid > 0");
            $fromFolios = (float)(mysqli_fetch_assoc($r2)['total'] ?? 0);
            return round($fromFolios, 2);
        }

        return round($fromPayments, 2);
    }

    
    private function revenueMonth($db): float
    {
        $r = mysqli_query($db,
            "SELECT COALESCE(SUM(amount), 0) AS total FROM payments
             WHERE  YEAR(processed_at)  = YEAR(CURDATE())
               AND  MONTH(processed_at) = MONTH(CURDATE())");
        $v = (float)(mysqli_fetch_assoc($r)['total'] ?? 0);

        
        if ($v <= 0) {
            $r2 = mysqli_query($db,
                "SELECT COALESCE(SUM(amount_paid), 0) AS total
                 FROM   folios
                 WHERE  YEAR(updated_at)  = YEAR(CURDATE())
                   AND  MONTH(updated_at) = MONTH(CURDATE())
                   AND  amount_paid > 0");
            $v = (float)(mysqli_fetch_assoc($r2)['total'] ?? 0);
        }
        return round($v, 2);
    }

    
    private function revenuePrevMonth($db): float
    {
        $r = mysqli_query($db,
            "SELECT COALESCE(SUM(amount), 0) AS total FROM payments
             WHERE  YEAR(processed_at)  = YEAR(CURDATE() - INTERVAL 1 MONTH)
               AND  MONTH(processed_at) = MONTH(CURDATE() - INTERVAL 1 MONTH)");
        $v = (float)(mysqli_fetch_assoc($r)['total'] ?? 0);

        if ($v <= 0) {
            $r2 = mysqli_query($db,
                "SELECT COALESCE(SUM(amount_paid), 0) AS total
                 FROM   folios
                 WHERE  YEAR(updated_at)  = YEAR(CURDATE() - INTERVAL 1 MONTH)
                   AND  MONTH(updated_at) = MONTH(CURDATE() - INTERVAL 1 MONTH)
                   AND  amount_paid > 0");
            $v = (float)(mysqli_fetch_assoc($r2)['total'] ?? 0);
        }
        return round($v, 2);
    }

    
    private function occupancyRate($db): float
    {
        $rTotal = mysqli_query($db, "SELECT COUNT(*) AS cnt FROM rooms");
        $total  = (int)(mysqli_fetch_assoc($rTotal)['cnt'] ?? 1);
        $rOcc   = mysqli_query($db, "SELECT COUNT(*) AS cnt FROM rooms WHERE status = 'occupied'");
        $occ    = (int)(mysqli_fetch_assoc($rOcc)['cnt'] ?? 0);
        return $total > 0 ? round($occ / $total * 100, 1) : 0.0;
    }

    
    private function upcomingCheckins($db): array
    {
        $r = mysqli_query($db,
            "SELECT r.id, g.name AS guest_name, rm.room_number, r.check_in_date,
                    r.adults, r.children, r.special_requests, g.is_vip, g.loyalty_tier
             FROM   reservations r
             JOIN   guests g  ON r.guest_id = g.id
             JOIN   rooms  rm ON r.room_id  = rm.id
             WHERE  r.check_in_date BETWEEN CURDATE() AND CURDATE() + INTERVAL 3 DAY
               AND  r.status IN ('confirmed','pending')
             ORDER  BY r.check_in_date ASC, g.name ASC
             LIMIT  20");
        return $r ? mysqli_fetch_all($r, MYSQLI_ASSOC) : [];
    }

    
    private function upcomingCheckouts($db): array
    {
        $r = mysqli_query($db,
            "SELECT r.id, g.name AS guest_name, rm.room_number,
                    r.check_out_date, r.total_price,
                    COALESCE(f.amount_paid, 0)  AS amount_paid,
                    COALESCE(f.balance_due, 0)  AS balance_due,
                    f.status AS folio_status
             FROM   reservations r
             JOIN   guests g  ON r.guest_id = g.id
             JOIN   rooms  rm ON r.room_id  = rm.id
             LEFT   JOIN folios f ON f.reservation_id = r.id
             WHERE  r.check_out_date BETWEEN CURDATE() AND CURDATE() + INTERVAL 1 DAY
               AND  r.status IN ('checked_in','confirmed')
             ORDER  BY r.check_out_date ASC, g.name ASC
             LIMIT  20");
        return $r ? mysqli_fetch_all($r, MYSQLI_ASSOC) : [];
    }

    
    private function vipArrivals($db): array
    {
        $r = mysqli_query($db,
            "SELECT r.id, g.name AS guest_name, g.loyalty_tier,
                    rm.room_number, r.check_in_date, r.special_requests
             FROM   reservations r
             JOIN   guests g  ON r.guest_id = g.id
             JOIN   rooms  rm ON r.room_id  = rm.id
             WHERE  r.check_in_date BETWEEN CURDATE() AND CURDATE() + INTERVAL 2 DAY
               AND  r.status IN ('confirmed','pending','checked_in')
               AND  (g.is_vip = 1 OR g.loyalty_tier IN ('gold','platinum'))
             ORDER  BY g.loyalty_tier DESC, r.check_in_date ASC
             LIMIT  10");
        return $r ? mysqli_fetch_all($r, MYSQLI_ASSOC) : [];
    }

    
    private function openWorkOrders($db): int
    {
        
        $r = mysqli_query($db,
            "SELECT COUNT(*) AS cnt FROM work_orders
             WHERE  status IN ('open','in_progress','pending_parts')");
        $count = (int)(mysqli_fetch_assoc($r)['cnt'] ?? 0);

        
        if ($count === 0) {
            $r2 = mysqli_query($db,
                "SELECT COUNT(*) AS cnt FROM maintenance_orders
                 WHERE  status IN ('open','in_progress','escalated')");
            $count = (int)(mysqli_fetch_assoc($r2)['cnt'] ?? 0);
        }

        return $count;
    }

    
    private function openDisputes($db): int
    {
        $r = mysqli_query($db,
            "SELECT COUNT(*) AS cnt FROM billing_disputes WHERE status = 'open'");
        return (int)(mysqli_fetch_assoc($r)['cnt'] ?? 0);
    }
}
