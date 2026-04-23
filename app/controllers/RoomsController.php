<?php
// ============================================================
//  RoomsController — Room inventory management
//  Routes:
//    /rooms            → index
//    /rooms/show/5     → show room details
//    /rooms/create     → new room form
//    /rooms/store      → save new room
//    /rooms/edit/5     → edit room
//    /rooms/update/5   → save edits
//    /rooms/delete/5   → delete room
// ============================================================

class RoomsController extends Controller
{
    public function index()
    {
        // TODO: Require login
        // TODO: Load all rooms with their types
        // TODO: Pass to rooms/index view
        $this->view('rooms/index');
    }

    public function show($id)
    {
        // TODO: Load room by ID with type, reservations, HK tasks, maintenance
        // TODO: Pass to rooms/show view
        $this->view('rooms/show');
    }

    public function create()
    {
        // TODO: Load room types for dropdown
        // TODO: Show rooms/create view
        $this->view('rooms/create');
    }

    public function store()
    {
        // TODO: Validate $_POST data
        // TODO: Call Room model create()
        // TODO: Redirect to rooms/index
    }

    public function edit($id)
    {
        // TODO: Load room by ID and room types
        // TODO: Pass to rooms/edit view
        $this->view('rooms/edit');
    }

    public function update($id)
    {
        // TODO: Validate $_POST data
        // TODO: Call Room model update()
        // TODO: Redirect to rooms/show/$id
    }

    public function delete($id)
    {
        // TODO: Call Room model delete()
        // TODO: Redirect to rooms/index
    }

    public function updateStatus($id)
    {
        // TODO: Update room status (state machine)
        // TODO: Log to audit_log
    }
}
