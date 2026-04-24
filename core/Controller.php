<?php
// ============================================================
//  Base Controller
//  All controllers extend this class.
//
//  To use a model, just create it with new:
//    $guest = new Guest();
//    $room  = new Room();
// ============================================================

class Controller
{
    /**
     * Renders a view file and passes data to it.
     *
     * Usage inside a controller:
     *   $this->view('reservations/index', ['reservations' => $data]);
     */
    protected function view(string $viewPath, array $data = []): void
    {
        // Make every key in $data available as its own variable inside the view
        extract($data);

        $file = VIEW_PATH . '/' . $viewPath . '.php';

        if (!file_exists($file)) {
            die("View not found: {$viewPath}");
        }

        require $file;
    }

    /**
     * Redirects the browser to a given URL path.
     *
     * Usage:
     *   $this->redirect('reservations');
     */
    protected function redirect(string $path): void
    {
        header('Location: ' . APP_URL . '/' . ltrim($path, '/'));
        exit;
    }

    /**
     * Checks whether the current user is logged in.
     * Redirects to login page if not.
     */
    protected function requireLogin(): void
    {
        if (empty($_SESSION['user_id'])) {
            $this->redirect('auth/login');
        }
    }

    /**
     * Checks whether the logged-in user has a specific role.
     *
     * Usage:
     *   $this->requireRole('manager');
     */
    protected function requireRole(string $role): void
    {
        $this->requireLogin();

        if ($_SESSION['user_role'] !== $role) {
        $this->view("forbidden/index");
        die('Access denied. Required role: ' . $role);
        }
    }
}
