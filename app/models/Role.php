<?php
// ============================================================
//  Role Model — Access roles (manager, front_desk, housekeeper)
//  Table: roles
// ============================================================

class Role extends Model
{
    public $id;
    public $name;
    public $created_at;

    // ── CRUD ─────────────────────────────────────────────────

    public function all()
    {
        // TODO: Team will implement query logic here
        // SELECT * FROM roles
    }

    public function find($id)
    {
        // TODO: Team will implement query logic here
        // SELECT * FROM roles WHERE id = ?
    }

    public function create($data)
    {
        // TODO: Team will implement query logic here
        // INSERT INTO roles (name) VALUES (?)
    }

    public function update($id, $data)
    {
        // TODO: Team will implement query logic here
        // UPDATE roles SET name = ? WHERE id = ?
    }

    public function delete($id)
    {
        // TODO: Team will implement query logic here
        // DELETE FROM roles WHERE id = ?
    }

    // ── Relationships ────────────────────────────────────────

    public function users()
    {
        // TODO: Return all users that belong to this role
        // SELECT * FROM users WHERE role_id = ?
    }
}
