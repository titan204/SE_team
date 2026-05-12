<?php

class RoomType extends AbstractModel
{
    protected $id;
    protected $name;
    protected $description;
    protected $base_price;
    protected $capacity;

    public function __construct($db = null, array $aggregates = [])
    {
        parent::__construct($db, $aggregates);
        $this->registerAggregate('rooms', Room::class);
    }

   
    public function all()
    {
        $stmt = mysqli_prepare($this->db, "SELECT * FROM room_types ORDER BY base_price ASC");
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    
    public function find($id)
    {
        $stmt = mysqli_prepare($this->db, "SELECT * FROM room_types WHERE id = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($result);
    }

    
    public function create($data)
    {
        $stmt = mysqli_prepare(
            $this->db,
            "INSERT INTO room_types (name, description, base_price, capacity)
             VALUES (?, ?, ?, ?)"
        );
        $name        = $data['name'];
        $description = $data['description'] ?? '';
        $base_price  = $data['base_price'];
        $capacity    = $data['capacity'] ?? 2;
        mysqli_stmt_bind_param($stmt, 'ssdi', $name, $description, $base_price, $capacity);
        mysqli_stmt_execute($stmt);
        return mysqli_insert_id($this->db);
    }

    
    public function update($id, $data)
    {
        $stmt = mysqli_prepare(
            $this->db,
            "UPDATE room_types
             SET name = ?, description = ?, base_price = ?, capacity = ?
             WHERE id = ?"
        );
        $name        = $data['name'];
        $description = $data['description'] ?? '';
        $base_price  = $data['base_price'];
        $capacity    = $data['capacity'] ?? 2;
        mysqli_stmt_bind_param($stmt, 'ssdii', $name, $description, $base_price, $capacity, $id);
        mysqli_stmt_execute($stmt);
    }

    
    public function delete($id)
    {
        $stmt = mysqli_prepare($this->db, "SELECT COUNT(*) AS cnt FROM rooms WHERE room_type_id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        if ($row['cnt'] > 0) {
            throw new Exception("Cannot delete: {$row['cnt']} room(s) still use this type.");
        }

        $stmt = mysqli_prepare($this->db, "DELETE FROM room_types WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        return true;
    }

    
    public function rooms()
    {
        $stmt = mysqli_prepare($this->db, "SELECT * FROM rooms WHERE room_type_id = ? ORDER BY room_number ASC");
        mysqli_stmt_bind_param($stmt, 'i', $this->id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}
