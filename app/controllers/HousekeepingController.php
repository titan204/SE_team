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
    public function index()
    {
        // TODO: Require login
        // TODO: Load tasks grouped by status (pending, in_progress, done)
        // TODO: Pass to housekeeping/index view
        $this->view('housekeeping/index');
    }

    public function show($id)
    {
        // TODO: Load task with room and assigned staff details
        // TODO: Pass to housekeeping/show view
        $this->view('housekeeping/show');
    }

    public function create()
    {
        // TODO: Load rooms and housekeepers for dropdowns
        // TODO: Show housekeeping/create view
        $this->view('housekeeping/create');
    }

    public function store()
    {
        // TODO: Validate $_POST data
        // TODO: Call HousekeepingTask model create()
        // TODO: Redirect to housekeeping/index
    }

    public function complete($id)
    {
        // TODO: Set quality score from supervisor
        // TODO: Call HousekeepingTask model markComplete()
        // TODO: Update room status to 'available'
        // TODO: Alert front desk if priority arrival room
        // TODO: Redirect to housekeeping/index
    }

    public function minibar($id)
    {
        // TODO: Log minibar consumption
        // TODO: Post charges to guest folio
    }
}
