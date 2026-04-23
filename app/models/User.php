<?php
// ============================================================
//  User Model — Staff accounts (Manager, Front-Desk, Housekeeper)
//  Table: users
// ============================================================

class User extends Model
{
    public $id;
    public $role_id;
    public $name;
    public $email;
    public $password;
    public $is_active;
    public $created_at;
    public $updated_at;

    // ── CRUD ─────────────────────────────────────────────────

    public function all()
    {
        // TODO: Team will implement query logic here
        // SELECT * FROM users JOIN roles ON users.role_id = roles.id
    }

    public function find($id)
    {
        // TODO: Team will implement query logic here
        // SELECT * FROM users WHERE id = ?
    }

    public function findByEmail($email)
    {
        // TODO: Team will implement query logic here
        // SELECT * FROM users WHERE email = ?
    }

    public function create($data)
    {
        // TODO: Team will implement query logic here
        // INSERT INTO users (role_id, name, email, password) VALUES (?, ?, ?, ?)
    }

    public function update($id, $data)
    {
        // TODO: Team will implement query logic here
        // UPDATE users SET name=?, email=?, role_id=? WHERE id = ?
    }

    public function delete($id)
    {
        // TODO: Team will implement query logic here
        // DELETE FROM users WHERE id = ?
    }

    // ── Relationships ────────────────────────────────────────

    public function role()
    {
        // TODO: Return the role record for this user
        // SELECT * FROM roles WHERE id = this->role_id
    }

    // ── Authentication Helpers ───────────────────────────────

    public function authenticate($email, $password)
    {
        // TODO: Find user by email, verify password with password_verify()
        // Return user data on success, false on failure
    }
}
