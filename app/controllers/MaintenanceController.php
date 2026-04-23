<?php
// ============================================================
//  MaintenanceController — Work orders for repairs
//  Routes:
//    /maintenance            → index
//    /maintenance/show/5     → order details
//    /maintenance/create     → new work order
//    /maintenance/store      → save order
//    /maintenance/resolve/5  → mark resolved
//    /maintenance/escalate/5 → escalate issue
// ============================================================

class MaintenanceController extends Controller
{
    public function index()
    {
        // TODO: Require login
        // TODO: Load all maintenance orders
        // TODO: Pass to maintenance/index view
        $this->view('maintenance/index');
    }

    public function show($id)
    {
        // TODO: Load order with room and staff details
        $this->view('maintenance/show');
    }

    public function create()
    {
        // TODO: Load rooms for dropdown
        $this->view('maintenance/create');
    }

    public function store()
    {
        // TODO: Validate $_POST data
        // TODO: Call MaintenanceOrder model create()
        // TODO: Redirect to maintenance/index
    }

    public function resolve($id)
    {
        // TODO: Call MaintenanceOrder model resolve()
        // TODO: Update room status if was out_of_order
    }

    public function escalate($id)
    {
        // TODO: Call MaintenanceOrder model escalate()
        // TODO: Set room to 'out_of_order'
    }
}
