<?php

class HousekeepingTask extends AbstractModel
{
    protected $id;
    protected $room_id;
    protected $assigned_to;
    protected $task_type;      
    protected $status;         
    protected $notes;
    protected $quality_score;  
    protected $created_at;
    protected $updated_at;

    public function __construct($db = null, array $aggregates = [])
    {
        parent::__construct($db, $aggregates);
        $this->registerAggregate('room', Room::class);
        $this->registerAggregate('assignedStaff', Housekeeper::class);
    }

}
