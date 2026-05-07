<?php

class Payment extends AbstractBilling
{
    protected $id;
    protected $folio_id;
    protected $amount;
    protected $method;
    protected $reference;
    protected $processed_by;
    protected $processed_at;

    public function __construct($db = null, $invoice = null, array $aggregates = [])
    {
        parent::__construct($db, $invoice, $aggregates);
        $this->setBillingSubject('folio_payment');
        $this->registerAggregate('folio', Folio::class);
    }

    public function all() { /* TODO: mysqli_query($this->db, "SELECT * FROM payments") */ }
    public function find($id) { /* TODO: SELECT * FROM payments WHERE id = ? */ }
    public function findByFolio($folioId) { /* TODO: WHERE folio_id = ? */ }
    public function create($data) { /* TODO: INSERT INTO payments */ }
    public function update($id, $data) { /* TODO: UPDATE payments */ }
    public function delete($id) { /* TODO: DELETE FROM payments */ }

    public function folio() { /* TODO: Return parent folio */ }
    public function processRefund($paymentId, $amount) { /* TODO: Refund logic + audit_log */ }
    public function holdDeposit($folioId, $amount) { /* TODO: Pre-Authorization Manager */ }
}
