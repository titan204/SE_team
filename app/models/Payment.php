<?php

class Payment extends Model
{
    public $id;
    public $folio_id;
    public $amount;
    public $method;
    public $reference;
    public $processed_by;
    public $processed_at;

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
