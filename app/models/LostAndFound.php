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

}
