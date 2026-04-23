<?php
// ============================================================
//  ReportsController — Reporting & analytics (Manager only)
//  Routes:
//    /reports            → index (report menu)
//    /reports/occupancy  → occupancy report
//    /reports/revenue    → revenue report
//    /reports/audit      → audit log viewer
// ============================================================

class ReportsController extends Controller
{
    public function index()
    {
        // TODO: Require 'manager' role
        // TODO: Show reports menu
        $this->view('reports/index');
    }

    public function occupancy()
    {
        // TODO: Calculate occupancy rates by date range
        // TODO: Pass to reports/occupancy view
        $this->view('reports/occupancy');
    }

    public function revenue()
    {
        // TODO: Aggregate revenue from folios by date range
        // TODO: Pass to reports/revenue view
        $this->view('reports/revenue');
    }

    public function audit()
    {
        // TODO: Load audit_log entries
        // TODO: Support filtering by user, action, date
        // TODO: Pass to reports/audit view
        $this->view('reports/audit');
    }
}
