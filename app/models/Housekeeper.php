<?php

class Housekeeper extends AbstractStaffWorkspace
{
    protected $id;
    protected $user_id;
    protected $room_id;
    protected $task_id;
    protected $shift_code;
    protected $readiness_status;
    protected $inspection_status;
    protected $notes;
    protected $created_at;
    protected $updated_at;

    public function __construct($db = null, array $aggregates = [])
    {
        parent::__construct($db, $aggregates);
        $this->setAssignmentContext(['room_id', 'task_id', 'user_id']);
        $this->registerAggregate('rooms', Room::class);
        $this->registerAggregate('tasks', HousekeepingTask::class);
        $this->registerAggregate('maintenanceOrders', MaintenanceOrder::class);
    }

}
