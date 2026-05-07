<?php


class LostAndFound extends AbstractModel
{
    protected $id;
    protected $guest_id;
    protected $room_id;
    protected $found_by;
    protected $description;
    protected $status;       // found, claimed, donated, discarded
    protected $found_at;

    public function __construct($db = null, array $aggregates = [])
    {
        parent::__construct($db, $aggregates);
        $this->registerAggregate('guest', Guest::class);
        $this->registerAggregate('room', Room::class);
        $this->registerAggregate('foundByStaff', User::class);
    }

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
