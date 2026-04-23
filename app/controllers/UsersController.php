<?php
// ============================================================
//  UsersController — Staff account management (Manager only)
//  Routes:
//    /users            → index
//    /users/create     → new staff form
//    /users/store      → save staff
//    /users/edit/5     → edit staff
//    /users/update/5   → save edits
//    /users/delete/5   → deactivate staff
// ============================================================

class UsersController extends Controller
{
    public function index()
    {
        // TODO: Require 'manager' role
        // TODO: Load all users with roles
        // TODO: Pass to users/index view
        $this->view('users/index');
    }

    public function create()
    {
        // TODO: Require 'manager' role
        // TODO: Load roles for dropdown
        $this->view('users/create');
    }

    public function store()
    {
        // TODO: Require 'manager' role
        // TODO: Validate $_POST data
        // TODO: Hash password with password_hash()
        // TODO: Call User model create()
        // TODO: Redirect to users/index
    }

    public function edit($id)
    {
        // TODO: Require 'manager' role
        // TODO: Load user and roles
        $this->view('users/edit');
    }

    public function update($id)
    {
        // TODO: Require 'manager' role
        // TODO: Validate $_POST data
        // TODO: Call User model update()
        // TODO: Log to audit_log
    }

    public function delete($id)
    {
        // TODO: Require 'manager' role
        // TODO: Deactivate user (set is_active = 0), don't hard-delete
        // TODO: Log to audit_log
    }
}
