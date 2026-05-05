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
    private string $defaultController = 'HomeController';
    private string $defaultMethod     = 'index';

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

        // UC06 nested billing routes:
        // /billing/group/{group_id}/invoice
        // /billing/group/{group_id}/invoice/pdf
        // /billing/group/{group_id}/finalize
        // /billing/group/{group_id}/cancel
        if (
            strtolower($url[0] ?? '') === 'billing'
            && strtolower($url[1] ?? '') === 'group'
            && isset($url[2], $url[3])
        ) {
            $nestedAction = strtolower($url[3]);
            if ($nestedAction === 'invoice' && strtolower($url[4] ?? '') === 'pdf') {
                $method = 'groupInvoicePdf';
                $param  = $url[2];
            } elseif ($nestedAction === 'invoice') {
                $method = 'groupInvoice';
                $param  = $url[2];
            } elseif ($nestedAction === 'finalize') {
                $method = 'groupFinalize';
                $param  = $url[2];
            } elseif ($nestedAction === 'cancel') {
                $method = 'groupCancel';
                $param  = $url[2];
            }
        }

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
        // Support single-word, snake_case, kebab-case, and camelCase controller slugs.
        $normalized = trim($slug);
        $normalized = preg_replace('/([a-z])([A-Z])/', '$1 $2', $normalized);
        $normalized = str_replace(['-', '_'], ' ', $normalized);
        $normalized = preg_replace('/\s+/', ' ', strtolower($normalized));

        return str_replace(' ', '', ucwords($normalized)) . 'Controller';
    }

    /**
     * Renders the 404 page.
     */
    private function notFound(string $message = ''): void
    {
        http_response_code(404);
        echo "<h1 style='color:red;font-size:20px;font-weight:bold;'>404 — Page Not Found</h1>";
    }
}
