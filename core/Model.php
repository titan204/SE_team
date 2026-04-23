<?php
// ============================================================
//  Base Model
//  All models extend this class to get the DB connection.
// ============================================================

class Model
{
    // Shared PDO connection available to all child models
    protected PDO $db;

    public function __construct()
    {
        // Grab the single shared connection from the Database singleton
        $this->db = Database::getConnection();
    }
}
