<?php
// ============================================================
//  FrontdeskController - Front desk landing page
//  Route: /frontdesk -> index
// ============================================================

class FrontdeskController extends Controller
{
    public function index()
    {
        $this->requireLogin();

        $this->view('frontdesk/index');
    }
}
