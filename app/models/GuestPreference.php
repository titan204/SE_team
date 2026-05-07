<?php

class GuestPreference extends Model
{
    public $id;
    public $guest_id;
    public $pref_key;
    public $pref_value;

    public function all()
    {
        $query = "SELECT * FROM guest_preferences";
        $result = mysqli_query($this->db, $query);

        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function find($id)
    {
        $id = (int)$id;
        $query = "SELECT * FROM guest_preferences WHERE id = $id";
        $result = mysqli_query($this->db, $query);

        return mysqli_fetch_assoc($result);
    }

    public function findByGuest($guestId)
{
    $guestId = (int)$guestId;
    $query = "SELECT pref_key AS preference_type, pref_value AS preference_value 
              FROM guest_preferences 
              WHERE guest_id = $guestId";
    $result = mysqli_query($this->db, $query);

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

    public function create($data)
    {
        $guest_id   = (int)$data['guest_id'];
        $pref_key   = mysqli_real_escape_string($this->db, $data['pref_key']);
        $pref_value = mysqli_real_escape_string($this->db, $data['pref_value']);

        $query = "
        INSERT INTO guest_preferences (guest_id, pref_key, pref_value)
        VALUES ($guest_id, '$pref_key', '$pref_value') ";

        mysqli_query($this->db, $query);

        return mysqli_insert_id($this->db);
    }

    public function update($id, $data)
    {
        $id = (int)$id;
        $pref_key   = mysqli_real_escape_string($this->db, $data['pref_key']);
        $pref_value = mysqli_real_escape_string($this->db, $data['pref_value']);

        $query = "
        UPDATE guest_preferences 
        SET pref_key = '$pref_key',
            pref_value = '$pref_value'
        WHERE id = $id";

        mysqli_query($this->db, $query);

        return true;
    }

    public function delete($id)
    {
        $id = (int)$id;
        $query = "DELETE FROM guest_preferences WHERE id = $id";
        mysqli_query($this->db, $query);

        return true;
    }

    public function guest()
    {
        $query = "
        SELECT g.*
        FROM guests g
        JOIN guest_preferences gp ON gp.guest_id = g.id
        WHERE gp.id = $this->id
        LIMIT 1
    ";

        $result = mysqli_query($this->db, $query);

        return mysqli_fetch_assoc($result);
    }
}
?>
