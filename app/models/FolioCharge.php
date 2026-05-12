<?php


class FolioCharge extends AbstractModel
{
    protected $id;
    protected $folio_id;
    protected $charge_type;   
    protected $description;
    protected $amount;
    protected $posted_by;
    protected $posted_at;

    public function __construct($db = null, array $aggregates = [])
    {
        parent::__construct($db, $aggregates);
        $this->registerAggregate('folio', Folio::class);
    }

    
}
