<?php
// ============================================================
//  Application Configuration
//  Edit these values to match your local environment
// ============================================================

// ── Database ────────────────────────────────────────────────
define('DB_HOST',     'localhost');
define('DB_NAME',     'hotel_management');
define('DB_USER',     'root');
define('DB_PASS',     '');
define('DB_CHARSET',  'utf8mb4');

// ── Application ─────────────────────────────────────────────
define('APP_NAME',    'Hotel Management System');
define('APP_URL',     'http://localhost/SE_project/public');
define('APP_VERSION', '1.0.0');

// ── Session ──────────────────────────────────────────────────
define('SESSION_NAME', 'hotel_session');

// ── Paths ────────────────────────────────────────────────────
define('ROOT_PATH',   dirname(__DIR__));
define('APP_PATH',    ROOT_PATH . '/app');
define('VIEW_PATH',   APP_PATH  . '/views');
define('CORE_PATH',   ROOT_PATH . '/core');

// ── Error Reporting (set to 0 in production) ─────────────────
error_reporting(E_ALL);
ini_set('display_errors', 1);
