<?php
// ============================================================
//  FolioCharge Model — Individual charges on a folio
//  Table: folio_charges
//
//  Usage:
//    $charge = new FolioCharge();
// ============================================================

class FolioCharge extends AbstractModel
{
    protected $id;
    protected $folio_id;
    protected $charge_type;   // room_rate, service, minibar, spa, restaurant, penalty, tax, other
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
