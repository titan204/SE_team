<?php
// ============================================================
//  DashboardController — Main landing page after login
//  Route: /dashboard → index
// ============================================================

class DashboardController extends Controller
{
    public function index()
    {
        // TODO: Require login
        // TODO: Load summary statistics:
        //   - Total reservations today
        //   - Rooms available / occupied / dirty
        //   - Pending housekeeping tasks
        //   - Revenue today
        //   - Upcoming check-ins / check-outs
        // TODO: Pass data to dashboard view
        $this->view('dashboard/index');
    }
}
