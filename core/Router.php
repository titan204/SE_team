<?php
// ============================================================
//  Router — Simple URL Dispatcher
//  Maps URL segments to Controller@method
//
//  URL format:  /public/index.php?url=controller/method/param
//  Examples:
//    /public/index.php?url=reservations          → ReservationController@index
//    /public/index.php?url=reservations/create   → ReservationController@create
//    /public/index.php?url=reservations/edit/5   → ReservationController@edit(5)
// ============================================================

class Router
{
    // Default controller and method when no URL is given
    private string $defaultController = 'AuthController';
    private string $defaultMethod     = 'login';

    /**
     * Reads the ?url= query string, splits it into parts,
     * then loads and calls the matching controller method.
     */
    public function dispatch(): void
    {
        // Get the URL string, sanitize it, and split into segments
        $url = $this->getUrl();

        // Segment 0 → controller name  (e.g. "reservations")
        // Segment 1 → method name      (e.g. "create")
        // Segment 2 → optional param   (e.g. "5")

        $controllerName = isset($url[0]) && $url[0] !== ''
            ? $this->toControllerName($url[0])
            : $this->defaultController;

        $method = isset($url[1]) && $url[1] !== ''
        ? $url[1]
            : (isset($url[0]) && $url[0] !== '' ? 'index' : $this->defaultMethod);
        $param = $url[2] ?? null;

        // Load the controller file
        $file = APP_PATH . '/controllers/' . $controllerName . '.php';

        if (!file_exists($file)) {
            $this->notFound("Controller not found: {$controllerName}");
            return;
        }

        require_once $file;

        // Make sure the class exists
        if (!class_exists($controllerName)) {
            $this->notFound("Class not found: {$controllerName}");
            return;
        }

        $controller = new $controllerName();

        // Make sure the method exists on the controller
        if (!method_exists($controller, $method)) {
            $this->notFound("Method not found: {$controllerName}@{$method}");
            return;
        }

        // Call the method with or without a parameter
        if ($param !== null) {
            $controller->$method($param);
        } else {
            $controller->$method();
        }
    }

    // ── Helpers ──────────────────────────────────────────────

    /**
     * Reads and sanitizes the ?url= query parameter.
     * Returns an array of URL segments.
     */
    private function getUrl(): array
    {
        $raw = $_GET['url'] ?? '';
        // Remove any dangerous characters
        $clean = filter_var(rtrim($raw, '/'), FILTER_SANITIZE_URL);
        return explode('/', $clean);
    }

    /**
     * Converts a URL slug to a PascalCase controller class name.
     * Example: "reservations" → "ReservationController"
     *          "auth"         → "AuthController"
     */
    private function toControllerName(string $slug): string
    {
        // Convert slug to PascalCase then append "Controller"
        return ucfirst(strtolower($slug)) . 'Controller';
    }

    /**
     * Renders the 404 page.
     */
    private function notFound(string $message = ''): void
    {
        http_response_code(404);
    
        echo "<h1 style = 'color: red ; fontsize: 20px ; font-weight: bold; '>404 — Page Not Found</h1>";
   
    }
}
