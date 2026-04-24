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
        if (!empty($_SESSION['user_id'])) {
            $this->redirect($this->getRedirectPath([
                'role_id'   => $_SESSION['user_role_id'] ?? null,
                'role_name' => $_SESSION['user_role'] ?? null,
            ]));
        }

        $data = [
            'message' => $_SESSION['error'] ?? null,
            'errors'  => $_SESSION['errors'] ?? [],
            'old'     => $_SESSION['old'] ?? [],
        ];

        unset($_SESSION['error'], $_SESSION['errors'], $_SESSION['old']);

        $this->view('auth/login', $data);
    }

    public function doLogin()
    {


        $userModel = new User();

        // 1. Get data from form
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $errors = [];

        if ($email === '') {
            $errors['email'] = 'Email address is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
        }

        if ($password === '') {
            $errors['password'] = 'Password is required.';
        }

        $_SESSION['old'] = ['email' => $email];

        if (!empty($errors)) {
            $_SESSION['error'] = 'Please correct the highlighted fields.';
            $_SESSION['errors'] = $errors;
            $this->redirect('auth/login');
        }

        // 3. Try to authenticate user
        $user = $userModel->authenticate($email, $password);

        // 4. If login failed
        if (!$user) {
            $_SESSION['error'] = 'Invalid email or password.';
            $_SESSION['errors'] = [];
            $this->redirect('auth/login');
        }


        $_SESSION['user_id']      = $user['id'];
        $_SESSION['user_name']    = $user['name'];
        $_SESSION['user_email']   = $user['email'];
        $_SESSION['user_role_id'] = $user['role_id'];
        $_SESSION['user_role']    = $user['role_name'] ?? null;

        unset($_SESSION['error'], $_SESSION['errors'], $_SESSION['old']);

        $this->redirect($this->getRedirectPath($user));
    }

    public function logout()
    {
        $_SESSION = [];

        session_destroy();
        $this->redirect('auth/login');
    }

    private function getRedirectPath($user)
    {
        $roleId = (int) ($user['role_id'] ?? 0);
        $roleName = strtolower((string) ($user['role_name'] ?? ''));

        if ($roleName === 'manager' || $roleId === 1) {
            return 'Dashboard/index';
        }

        if ($roleName === 'front_desk' || $roleName === 'frontdesk' || $roleId === 2) {
            return 'Frontdesk/index';
        }

        if ($roleName === 'housekeeper' || $roleId === 3) {
            return 'Housekeeping/index';
        }

        if ($roleName === 'guest' || $roleId === 4) {
            return 'Guests/index';
        }

        return '';
    }
}
