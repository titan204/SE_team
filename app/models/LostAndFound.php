<?php


class LostAndFound extends Model
{
    public $id;
    public $guest_id;
    public $room_id;
    public $found_by;
    public $description;
    public $status;       // found, claimed, donated, discarded
    public $found_at;

    public function all() { /* TODO: mysqli_query($this->db, "SELECT * FROM lost_and_found") */ }
    public function find($id) { /* TODO: WHERE id = ? */ }
    public function create($data) { /* TODO: INSERT INTO lost_and_found */ }
    public function update($id, $data) { /* TODO: UPDATE lost_and_found */ }
    public function delete($id) { /* TODO: DELETE FROM lost_and_found */ }

    public function guest() { /* TODO: Return linked guest */ }
    public function room() { /* TODO: Return room where found */ }
    public function foundByStaff() { /* TODO: Return the staff member who found the item */ }

    public function claim($id, $guestId) {
        // TODO: Set status='claimed', link to guest_id
    }
}
