<?php

class HousekeepingTask extends AbstractModel
{
    protected $id;
    protected $room_id;
    protected $assigned_to;
    protected $task_type;      // cleaning, turndown, inspection, deep_clean, minibar_check
    protected $status;         // pending, in_progress, done, skipped
    protected $notes;
    protected $quality_score;  // 1-5
    protected $created_at;
    protected $updated_at;

    public function __construct($db = null, array $aggregates = [])
    {
        parent::__construct($db, $aggregates);
        $this->registerAggregate('room', Room::class);
        $this->registerAggregate('assignedStaff', Housekeeper::class);
    }

}
