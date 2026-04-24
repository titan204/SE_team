<?php
// ============================================================
//  Front Controller — Single Entry Point
//  Every request goes through this file.
//
//  Apache: mod_rewrite or use ?url= query string directly.
// ============================================================

// 1. Load configuration (defines DB constants, paths, etc.)
require_once __DIR__ . '/config/config.php';

// 2. Load core classes
require_once CORE_PATH . '/Database.php';
require_once CORE_PATH . '/Model.php';
require_once CORE_PATH . '/Controller.php';
require_once CORE_PATH . '/Router.php';

// 3. Load all model files so you can use: new Guest(), new Room(), etc.
foreach (glob(APP_PATH . '/models/*.php') as $modelFile) {
    require_once $modelFile;
}

// 4. Start session
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (!empty($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443);

session_name(SESSION_NAME);
ini_set('session.use_strict_mode', '1');

session_start();

// 5. Dispatch the request through the router
$router = new Router();
$router->dispatch();
