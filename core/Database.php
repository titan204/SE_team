<?php
// ============================================================
//  Database — mysqli Connection (Singleton)
//  Provides a single shared database connection for the app.
// ============================================================

class Database
{
    // Holds the one shared mysqli instance
    private static $instance = null;

    // Private constructor — prevents direct instantiation
    private function __construct() {}

    /**
     * Returns the shared mysqli connection.
     * Creates it on the first call (Singleton pattern).
     */
    public static function getConnection()
    {
        if (self::$instance === null) {
            self::$instance = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
            if (!self::$instance) {
                // TODO: Replace with a proper error page in production
                die('Database connection failed: ' . mysqli_connect_error());
            }

            // Set charset to utf8mb4
            mysqli_set_charset(self::$instance, DB_CHARSET);
        }

        return self::$instance;
    }
}
