<?php


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
        $result = mysqli_query($this->db, "SELECT * FROM users JOIN roles ON users.role_id = roles.id");
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function find($id)
    {
        $id = mysqli_real_escape_string($this->db, $id);
        $result = mysqli_query($this->db, "SELECT * FROM users WHERE id = '$id'");
        return mysqli_fetch_assoc($result);
    }

    public function findByEmail($email)
    {
        $email = mysqli_real_escape_string($this->db, $email);
        $result = mysqli_query(
            $this->db,
            "SELECT users.*, roles.name AS role_name
             FROM users
             LEFT JOIN roles ON users.role_id = roles.id
             WHERE users.email = '$email'
             LIMIT 1"
        );
        return mysqli_fetch_assoc($result);
    }

    public function hashPassword($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public function create($data)
    {
        $name     = mysqli_real_escape_string($this->db, $data['name']);
        $email    = mysqli_real_escape_string($this->db, $data['email']);
        $password = mysqli_real_escape_string($this->db, $data['password']);
        $role_id  = mysqli_real_escape_string($this->db, $data['role_id']);
        mysqli_query($this->db, "INSERT INTO users (role_id, name, email, password, is_active) VALUES ('$role_id', '$name', '$email', '$password', 1)");
        return mysqli_insert_id($this->db);
    }

    public function update($id, $data)
    {
        $id = mysqli_real_escape_string($this->db, $id);

        $updates = [];
        foreach ($data as $key => $value) {
            if (in_array($key, ['name', 'email', 'role_id', 'is_active'])) {
                $value = mysqli_real_escape_string($this->db, $value);
                $updates[] = "`$key` = '$value'";
            }
        }

        if (empty($updates)) {
            return;
        }

        $query = "UPDATE users SET " . implode(", ", $updates) . " WHERE id = '$id'";
        mysqli_query($this->db, $query);
    }

    public function delete($id)
    {
        // Soft delete: set is_active = 0
        $id = mysqli_real_escape_string($this->db, $id);
        mysqli_query($this->db, "UPDATE users SET is_active = 0 WHERE id = '$id'");
    }

    // ── Relationships ────────────────────────────────────────

    public function role()
    {
        $result = mysqli_query($this->db, "SELECT * FROM roles WHERE id = '$this->role_id'");
        return mysqli_fetch_assoc($result);
    }

    // ── Authentication Helpers ───────────────────────────────
    public function authenticate($email, $password)
    {
        $email = trim($email);
        $user = $this->findByEmail($email);

        if (!$user || (int)$user['is_active'] !== 1) {
            return false;
        }

        if ($this->passwordMatches($password, $user['password'] ?? '')) {
            return $user;
        }

        return false;
    }

    private function passwordMatches($plainPassword, $storedPassword)
    {
        if ($storedPassword === '') {
            return false;
        }

        if (password_verify($plainPassword, $storedPassword)) {
            return true;
        }

        return $plainPassword === $storedPassword;
    }
}
