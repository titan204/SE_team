<?php
// ============================================================
//  RoomType Model — Categories of rooms (Standard, Deluxe, Suite)
//  Table: room_types
//
//  Usage: $roomType = new RoomType();
// ============================================================

class RoomType extends Model
{
    public $id;
    public $name;
    public $description;
    public $base_price;
    public $capacity;

    public function all()
    {
        $result = mysqli_query(
            $this->db,
            "SELECT * FROM room_types ORDER BY base_price ASC"
        );
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function find($id)
    {
         $id     = mysqli_real_escape_string($this->db, $id);
        $result = mysqli_query(
            $this->db,
            "SELECT * FROM room_types WHERE id = '$id' LIMIT 1"
        );
        return mysqli_fetch_assoc($result);
    }

    public function create($data)
    {
        $name        = mysqli_real_escape_string($this->db, $data['name']);
        $description = mysqli_real_escape_string($this->db, $data['description'] ?? '');
        $base_price  = mysqli_real_escape_string($this->db, $data['base_price']);
        $capacity    = mysqli_real_escape_string($this->db, $data['capacity'] ?? 2);
 
        mysqli_query(
            $this->db,
            "INSERT INTO room_types (name, description, base_price, capacity)
             VALUES ('$name', '$description', '$base_price', '$capacity')"
        );
        return mysqli_insert_id($this->db);
    }

    public function update($id, $data)
    {
         $id          = mysqli_real_escape_string($this->db, $id);
        $name        = mysqli_real_escape_string($this->db, $data['name']);
        $description = mysqli_real_escape_string($this->db, $data['description'] ?? '');
        $base_price  = mysqli_real_escape_string($this->db, $data['base_price']);
        $capacity    = mysqli_real_escape_string($this->db, $data['capacity'] ?? 2);
 
        mysqli_query(
            $this->db,
            "UPDATE room_types
             SET name        = '$name',
                 description = '$description',
                 base_price  = '$base_price',
                 capacity    = '$capacity'
             WHERE id = '$id'"
        );
    }

    public function delete($id)
    {
        $id = mysqli_real_escape_string($this->db, $id);
 
        $check = mysqli_query(
            $this->db,
            "SELECT COUNT(*) AS cnt FROM rooms WHERE room_type_id = '$id'"
        );
        $row = mysqli_fetch_assoc($check);
 
        if ($row['cnt'] > 0) {
            return "Cannot delete: {$row['cnt']} room(s) still use this type.";
        }
 
        mysqli_query($this->db, "DELETE FROM room_types WHERE id = '$id'");
        return true;
    }

    public function rooms()
    {
        $id     = mysqli_real_escape_string($this->db, $this->id);
        $result = mysqli_query(
            $this->db,
            "SELECT * FROM rooms WHERE room_type_id = '$id' ORDER BY room_number ASC"
        );
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}
