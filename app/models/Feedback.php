<?php
// ============================================================
//  Feedback Model — Post-stay guest surveys
//  Table: feedback
// ============================================================

class Feedback extends Model
{
    public $id;
    public $reservation_id;
    public $guest_id;
    public $rating;
    public $comments;
    public $submitted_at;

    public function all() { /* TODO: SELECT * FROM feedback */ }
    public function find($id) { /* TODO: WHERE id = ? */ }
    public function create($data) { /* TODO: INSERT INTO feedback */ }
    public function update($id, $data) { /* TODO: UPDATE feedback */ }
    public function delete($id) { /* TODO: DELETE FROM feedback */ }

    public function reservation() { /* TODO: Return parent reservation */ }
    public function guest() { /* TODO: Return the guest */ }
    public function averageRating() { /* TODO: SELECT AVG(rating) FROM feedback */ }
}
