<?php
// ============================================================
//  Front Controller — Single Entry Point
//  Every request goes through this file.
//
//  Apache: mod_rewrite or use ?url= query string directly.
// ============================================================

// 1. Load configuration (defines DB constants, paths, etc.)
require_once __DIR__ . '/../config/config.php';

// 2. Load core classes
require_once CORE_PATH . '/Database.php';
require_once CORE_PATH . '/Model.php';
require_once CORE_PATH . '/Controller.php';
require_once CORE_PATH . '/Router.php';

// 3. Start session
session_name(SESSION_NAME);
session_start();

// 4. Dispatch the request through the router
$router = new Router();
$router->dispatch();
