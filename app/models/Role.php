<?php
class Role extends Model
{
    public $id;
    public $name;
    public $created_at;

    public function all()
    {
        $result = mysqli_query($this->db, "SELECT * FROM roles");
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function find($id)
    {
        $id = mysqli_real_escape_string($this->db, $id);
        $result = mysqli_query($this->db, "SELECT * FROM roles WHERE id = '$id'");
        return mysqli_fetch_assoc($result);
    }

    public function findByName($name)
    {
        $name = mysqli_real_escape_string($this->db, strtolower(trim($name)));
        $result = mysqli_query($this->db, "SELECT * FROM roles WHERE LOWER(name) = '$name' LIMIT 1");
        return mysqli_fetch_assoc($result);
    }

    public function create($data)
    {
        $name = mysqli_real_escape_string($this->db, $data['name']);
        mysqli_query($this->db, "INSERT INTO roles (name) VALUES ('$name')");
        return mysqli_insert_id($this->db);
    }

    public function update($id, $data)
    {
        $id = mysqli_real_escape_string($this->db, $id);
        $name = mysqli_real_escape_string($this->db, $data['name']);
        mysqli_query($this->db, "UPDATE roles SET name = '$name' WHERE id = '$id'");
    }

    public function delete($id)
    {
        $id = mysqli_real_escape_string($this->db, $id);
        mysqli_query($this->db, "DELETE FROM roles WHERE id = '$id'");
    }

    public function users()
    {
        $roleId = mysqli_real_escape_string($this->db, $this->id);
        $result = mysqli_query($this->db, "SELECT * FROM users WHERE role_id = '$roleId'");
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}
