<?php
// ============================================================
//  Folio Model — Billing folio for a reservation
//  Table: folios
//
//  Usage:
//    $folio = new Folio();
// ============================================================

class Folio extends AbstractBilling
{
    protected $id;
    protected $reservation_id;
    protected $total_amount;
    protected $amount_paid;
    protected $balance_due;    // computed column
    protected $status;         // open, settled, refunded
    protected $created_at;
    protected $updated_at;

    public function __construct($db = null, $invoice = null, array $aggregates = [])
    {
        parent::__construct($db, $invoice, $aggregates);
        $this->setBillingSubject('reservation_folio');
        $this->registerAggregate('reservation', Reservation::class);
        $this->registerAggregate('charges', FolioCharge::class);
        $this->registerAggregate('payments', Payment::class);
    }


}
