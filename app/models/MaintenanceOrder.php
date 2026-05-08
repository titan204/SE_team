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

}
