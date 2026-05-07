<?php


class MaintenanceOrder extends AbstractModel
{
    protected $id;
    protected $room_id;
    protected $reported_by;
    protected $assigned_to;
    protected $description;
    protected $priority;      // low, medium, high, critical
    protected $status;        // open, in_progress, resolved, escalated
    protected $resolved_at;
    protected $created_at;
    protected $updated_at;

    public function __construct($db = null, array $aggregates = [])
    {
        parent::__construct($db, $aggregates);
        $this->registerAggregate('room', Room::class);
        $this->registerAggregate('reporter', User::class);
        $this->registerAggregate('assignedStaff', User::class);
    }

    public function all() { /* TODO: mysqli_query($this->db, "SELECT * FROM maintenance_orders") */ }
    public function find($id) { /* TODO: WHERE id = ? */ }
    public function create($data) { /* TODO: INSERT INTO maintenance_orders */ }
    public function update($id, $data) { /* TODO: UPDATE maintenance_orders */ }
    public function delete($id) { /* TODO: DELETE FROM maintenance_orders */ }

    public function room() { /* TODO: Return parent room */ }
    public function reporter() { /* TODO: Return the reporting user */ }
    public function assignedStaff() { /* TODO: Return the assigned user */ }

    public function escalate($id) {
        // TODO: Set status='escalated', set room status='out_of_order'
    }
    public function resolve($id) {
        // TODO: Set status='resolved', resolved_at=NOW()
    }
    public function findByPriority($priority) { /* TODO: WHERE priority = ? */ }
    public function findOverdue() {
        // TODO: Preventative Maintenance Scheduler — find tasks past due date
    }
}
