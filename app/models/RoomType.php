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
        // TODO: $result = mysqli_query($this->db, "SELECT * FROM room_types ORDER BY base_price");
        // return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function find($id)
    {
        // TODO: $id = mysqli_real_escape_string($this->db, $id);
        // $result = mysqli_query($this->db, "SELECT * FROM room_types WHERE id = '$id'");
        // return mysqli_fetch_assoc($result);
    }

    public function create($data)
    {
        // TODO: INSERT INTO room_types (name, description, base_price, capacity) VALUES (...)
    }

    public function update($id, $data)
    {
        // TODO: UPDATE room_types SET ... WHERE id = ...
    }

    public function delete($id)
    {
        // TODO: DELETE FROM room_types WHERE id = ...
    }

    public function rooms()
    {
        // TODO: Return all rooms of this type
        // $result = mysqli_query($this->db, "SELECT * FROM rooms WHERE room_type_id = '$this->id'");
        // return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}
