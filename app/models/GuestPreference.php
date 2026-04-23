<?php
// ============================================================
//  GuestPreference Model — Stores guest-specific preferences
//  Table: guest_preferences
// ============================================================

class GuestPreference extends Model
{
    public $id;
    public $guest_id;
    public $pref_key;
    public $pref_value;

    // ── CRUD ─────────────────────────────────────────────────

    public function all()
    {
        // TODO: Team will implement query logic here
        // SELECT * FROM guest_preferences
    }

    public function find($id)
    {
        // TODO: Team will implement query logic here
        // SELECT * FROM guest_preferences WHERE id = ?
    }

    public function findByGuest($guestId)
    {
        // TODO: Team will implement query logic here
        // SELECT * FROM guest_preferences WHERE guest_id = ?
    }

    public function create($data)
    {
        // TODO: Team will implement query logic here
        // INSERT INTO guest_preferences (guest_id, pref_key, pref_value) VALUES (?, ?, ?)
    }

    public function update($id, $data)
    {
        // TODO: Team will implement query logic here
        // UPDATE guest_preferences SET pref_key=?, pref_value=? WHERE id = ?
    }

    public function delete($id)
    {
        // TODO: Team will implement query logic here
        // DELETE FROM guest_preferences WHERE id = ?
    }

    // ── Relationships ────────────────────────────────────────

    public function guest()
    {
        // TODO: Return the guest who owns this preference
        // SELECT * FROM guests WHERE id = this->guest_id
    }
}
