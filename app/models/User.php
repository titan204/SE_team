<?php


class User extends AbstractUser implements AuthenticatableInterface
{
    protected $id;
    protected $role_id;
    protected $name;
    protected $email;
    protected $password;
    protected $is_active;
    protected $created_at;
    protected $updated_at;

    public function __construct($db = null, $profileData = null, array $aggregates = [])
    {
        parent::__construct($db, $profileData, $aggregates);
        $this->registerAggregate('role', Role::class);
        $this->registerAggregate('auditLogs', AuditLog::class);
    }

    // ── CRUD ─────────────────────────────────────────────────

    public function all()
    {
        $result = mysqli_query(
            $this->db,
            "SELECT users.id, users.role_id, users.name, users.email,
                    users.is_active, users.created_at, users.updated_at,
                    roles.name AS role_name
             FROM users
             JOIN roles ON users.role_id = roles.id"
        );
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function find($id)
    {
        $id     = mysqli_real_escape_string($this->db, $id);
        $result = mysqli_query(
            $this->db,
            "SELECT id, role_id, name, email, is_active, created_at, updated_at
             FROM users WHERE id = '$id'"
        );
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
        $this->hydrateProfileData($data);
        $name    = mysqli_real_escape_string($this->db, $data['name']);
        $email   = mysqli_real_escape_string($this->db, $data['email']);
        $role_id = mysqli_real_escape_string($this->db, $data['role_id']);

        // Always hash through the central helper — callers MUST pass plaintext here.
        // This guarantees bcrypt (PASSWORD_DEFAULT) regardless of which controller
        // creates the user (self-registration, Admin, Front Desk, etc.).
        $password = mysqli_real_escape_string($this->db, $this->hashPassword($data['password']));

        mysqli_query($this->db,
            "INSERT INTO users (role_id, name, email, password, is_active)
             VALUES ('$role_id', '$name', '$email', '$password', 1)"
        );
        return mysqli_insert_id($this->db);
    }

    /**
     * Updates allowed profile fields for a user.
     * To change a password use updatePassword() instead.
     *
     * Allowed keys: name, email, role_id, is_active
     */
    public function update($id, $data)
    {
        $this->hydrateProfileData($data);
        $id = mysqli_real_escape_string($this->db, $id);

        $updates = [];
        foreach ($data as $key => $value) {
            if (in_array($key, ['name', 'email', 'role_id', 'is_active'], true)) {
                $value     = mysqli_real_escape_string($this->db, $value);
                $updates[] = "`$key` = '$value'";
            }
        }

        if (empty($updates)) {
            return;
        }

        $query = "UPDATE users SET " . implode(", ", $updates) . " WHERE id = '$id'";
        mysqli_query($this->db, $query);
    }

    /**
     * Updates a user's password using the canonical bcrypt hashing workflow.
     *
     * Accepts a PLAIN-TEXT password. Never call this with a pre-hashed value.
     * This is the single source of truth for password changes across every
     * role: self-registration, Admin, Front Desk, and Manager.
     *
     * @param  int|string $id          User ID
     * @param  string     $plainPassword  Plain-text password (≥ 6 chars expected)
     * @return bool  true on success, false if the query failed
     */
    public function updatePassword($id, $plainPassword): bool
    {
        $id   = mysqli_real_escape_string($this->db, $id);
        $hash = mysqli_real_escape_string($this->db, $this->hashPassword($plainPassword));
        return (bool) mysqli_query(
            $this->db,
            "UPDATE users SET password = '$hash' WHERE id = '$id'"
        );
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
        $user  = $this->findByEmail($email);

        if (!$user || (int) $user['is_active'] !== 1) {
            return false;
        }

        if (!$this->passwordMatches($password, $user['password'] ?? '')) {
            return false;
        }

        // Strip the password hash — it must NEVER leave the model layer
        unset($user['password']);

        return $user;
    }

    private function passwordMatches($plainPassword, $storedPassword)
    {
        if ($storedPassword === '') {
            return false;
        }

        return password_verify($plainPassword, $storedPassword);
    }
}
