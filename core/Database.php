<?php
// ============================================================
//  Database — PDO Singleton
//  Provides a single shared database connection for the app.
// ============================================================

class Database
{
    // Holds the one shared PDO instance
    private static ?PDO $instance = null;

    // Private constructor — prevents direct instantiation
    private function __construct() {}

    /**
     * Returns the shared PDO connection.
     * Creates it on the first call (Singleton pattern).
     */
    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $dsn = 'mysql:host=' . DB_HOST
                 . ';dbname='   . DB_NAME
                 . ';charset='  . DB_CHARSET;

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                // TODO: Replace with a proper error page in production
                die('Database connection failed: ' . $e->getMessage());
            }
        }

        return self::$instance;
    }
}
