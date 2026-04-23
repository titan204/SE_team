<?php
// ============================================================
//  AuthController — Login / Logout / Session Management
//  Routes:
//    /auth/login   → login form
//    /auth/doLogin → process login
//    /auth/logout  → destroy session
// ============================================================

class AuthController extends Controller
{
    public function login()
    {
        // TODO: Show login view
        $this->view('auth/login');
    }

    public function doLogin()
    {
        // TODO: Validate $_POST['email'] and $_POST['password']
        // TODO: Call User model authenticate()
        // TODO: Set $_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['user_role']
        // TODO: Redirect to dashboard on success
        // TODO: Redirect back to login with error on failure
    }

    public function logout()
    {
        // TODO: Destroy session
        // TODO: Redirect to login page
    }
}
