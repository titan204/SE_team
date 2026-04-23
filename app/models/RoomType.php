<?php
// ============================================================
//  RoomType Model — Categories of rooms (Standard, Deluxe, Suite)
//  Table: room_types
// ============================================================

class RoomType extends Model
{
    public $id;
    public $name;
    public $description;
    public $base_price;
    public $capacity;

    // ── CRUD ─────────────────────────────────────────────────

    public function all()
    {
        // TODO: Team will implement query logic here
        // SELECT * FROM room_types ORDER BY base_price
    }

    public function find($id)
    {
        // TODO: Team will implement query logic here
        // SELECT * FROM room_types WHERE id = ?
    }

    public function create($data)
    {
        // TODO: Team will implement query logic here
        // INSERT INTO room_types (name, description, base_price, capacity) VALUES (?, ?, ?, ?)
    }

    public function update($id, $data)
    {
        // TODO: Team will implement query logic here
        // UPDATE room_types SET name=?, description=?, base_price=?, capacity=? WHERE id = ?
    }

    public function delete($id)
    {
        // TODO: Team will implement query logic here
        // DELETE FROM room_types WHERE id = ?
    }

    // ── Relationships ────────────────────────────────────────

    public function rooms()
    {
        // TODO: Return all rooms of this type
        // SELECT * FROM rooms WHERE room_type_id = ?
    }
}
