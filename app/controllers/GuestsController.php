<?php
// ============================================================
//  GuestsController — Guest profile CRUD
//  Routes:
//    /guests            → index (list all guests)
//    /guests/show/5     → show guest #5
//    /guests/create     → new guest form
//    /guests/store      → save new guest
//    /guests/edit/5     → edit guest #5
//    /guests/update/5   → save edits
//    /guests/delete/5   → delete guest #5
// ============================================================

class GuestsController extends Controller
{
    public function index()
    {
        // TODO: Require login
        // TODO: Load all guests from Guest model
        // TODO: Pass to guests/index view
        $this->view('guests/index');
    }

    public function show($id)
    {
        // TODO: Load guest by ID
        // TODO: Load guest preferences, reservations, feedback
        // TODO: Calculate Lifetime Value (LTV)
        // TODO: Pass to guests/show view
        $this->view('guests/show');
    }

    public function create()
    {
        // TODO: Show empty form
        $this->view('guests/create');
    }

    public function store()
    {
        // TODO: Validate $_POST data
        // TODO: Call Guest model create()
        // TODO: Redirect to guests/index
    }

    public function edit($id)
    {
        // TODO: Load guest by ID
        // TODO: Pass to guests/edit view
        $this->view('guests/edit');
    }

    public function update($id)
    {
        // TODO: Validate $_POST data
        // TODO: Call Guest model update()
        // TODO: Redirect to guests/show/$id
    }

    public function delete($id)
    {
        // TODO: Call Guest model delete()
        // TODO: Redirect to guests/index
    }

    // ── Special Actions ──────────────────────────────────────

    public function blacklist($id)
    {
        // TODO: Call Guest model blacklist() with reason
        // TODO: Log to audit_log
    }

    public function anonymize($id)
    {
        // TODO: GDPR Right to be Forgotten
        // TODO: Call Guest model anonymize()
    }

    public function flagVip($id)
    {
        // TODO: Call Guest model flagAsVip()
        // TODO: Alert front desk
    }
}
