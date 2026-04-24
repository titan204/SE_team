<?php
// ============================================================
//  GuestPreference Model — Stores guest-specific preferences
//  Table: guest_preferences
//
//  Usage: $pref = new GuestPreference();
// ============================================================

class GuestPreference extends Model
{
    public $id;
    public $guest_id;
    public $pref_key;
    public $pref_value;

    public function all()
    {
        // TODO: $result = mysqli_query($this->db, "SELECT * FROM guest_preferences");
        // return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function find($id)
    {
        // TODO: $id = mysqli_real_escape_string($this->db, $id);
        // $result = mysqli_query($this->db, "SELECT * FROM guest_preferences WHERE id = '$id'");
        // return mysqli_fetch_assoc($result);
    }

    public function findByGuest($guestId)
    {
        // TODO: $guestId = mysqli_real_escape_string($this->db, $guestId);
        // $result = mysqli_query($this->db, "SELECT * FROM guest_preferences WHERE guest_id = '$guestId'");
        // return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function create($data)
    {
        // TODO: INSERT INTO guest_preferences (guest_id, pref_key, pref_value) VALUES (...)
    }

    public function update($id, $data)
    {
        // TODO: UPDATE guest_preferences SET pref_key=..., pref_value=... WHERE id = ...
    }

    public function delete($id)
    {
        // TODO: DELETE FROM guest_preferences WHERE id = ...
    }

    public function guest()
    {
        // TODO: Return the guest who owns this preference
    }
}
