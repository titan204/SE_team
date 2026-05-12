<?php

class RevenueManagerController extends Controller
{
    public function index()
    {
        $this->requireLogin();
        $this->requireRoles(['revenue_manager', 'manager']);

        $db = (new Model())->getDb();

        
        $r = mysqli_query($db, "SELECT id FROM group_reservations ORDER BY id LIMIT 1");
        $firstGroup = $r ? mysqli_fetch_assoc($r) : null;
        $firstGroupId = $firstGroup ? (int)$firstGroup['id'] : 1;

        
        $r2 = mysqli_query($db, "SELECT id FROM reservations WHERE status NOT IN ('cancelled','no_show') ORDER BY id LIMIT 1");
        $firstRes = $r2 ? mysqli_fetch_assoc($r2) : null;
        $firstResId = $firstRes ? (int)$firstRes['id'] : 1;

        $this->view('revenue_manager/index', compact('firstGroupId', 'firstResId'));
    }

    
}
