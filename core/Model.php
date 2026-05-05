<?php
// ============================================================
//  Base Model
//  All models extend this class to get the DB connection.
//  Usage: $guest = new Guest();
// ============================================================

class Model
{
    // Shared mysqli connection available to all child models
    protected $db;

    public function __construct()
    {
        // Grab the single shared connection from the Database singleton
        $this->db = Database::getConnection();
    }

    /** Expose the DB connection for raw queries in controllers. */
    public function getDb()
    {
        return $this->db;
    }
}
