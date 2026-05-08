<?php
// ============================================================
//  RevenueManagerController - Revenue oversight scaffold for
//  billing summaries, folio hooks, and reporting placeholders.
//  Routes:
//    /revenue_manager              -> index
//    /revenue_manager/create       -> create
//    /revenue_manager/store        -> store
//    /revenue_manager/show/5       -> show
//    /revenue_manager/edit/5       -> edit
//    /revenue_manager/update/5     -> update
//    /revenue_manager/delete/5     -> delete
// ============================================================

class RevenueManagerController extends Controller
{
    public function index()
    {
        $this->requireLogin();
        $this->requireRoles(['revenue_manager', 'manager']);

        $db = (new Model())->getDb();

        // Get first available group so workspace buttons always link to real data
        $r = mysqli_query($db, "SELECT id FROM group_reservations ORDER BY id LIMIT 1");
        $firstGroup = $r ? mysqli_fetch_assoc($r) : null;
        $firstGroupId = $firstGroup ? (int)$firstGroup['id'] : 1;

        // Also get first reservation for guestBill demo link
        $r2 = mysqli_query($db, "SELECT id FROM reservations WHERE status NOT IN ('cancelled','no_show') ORDER BY id LIMIT 1");
        $firstRes = $r2 ? mysqli_fetch_assoc($r2) : null;
        $firstResId = $firstRes ? (int)$firstRes['id'] : 1;

        $this->view('revenue_manager/index', compact('firstGroupId', 'firstResId'));
    }

    
}
