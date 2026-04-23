<?php
// ============================================================
//  FolioCharge Model — Individual charges on a folio
//  Table: folio_charges
// ============================================================

class FolioCharge extends Model
{
    public $id;
    public $folio_id;
    public $charge_type;   // room_rate, service, minibar, spa, restaurant, penalty, tax, other
    public $description;
    public $amount;
    public $posted_by;
    public $posted_at;

    // ── CRUD ─────────────────────────────────────────────────

    public function all()
    {
        // TODO: Team will implement query logic here
    }

    public function find($id)
    {
        // TODO: Team will implement query logic here
    }

    public function findByFolio($folioId)
    {
        // TODO: Team will implement query logic here
        // SELECT * FROM folio_charges WHERE folio_id = ?
    }

    public function create($data)
    {
        // TODO: Team will implement query logic here
        // INSERT INTO folio_charges (folio_id, charge_type, description, amount, posted_by) VALUES (?, ?, ?, ?, ?)
    }

    public function update($id, $data)
    {
        // TODO: Team will implement query logic here
    }

    public function delete($id)
    {
        // TODO: Team will implement query logic here
    }

    // ── Relationships ────────────────────────────────────────

    public function folio()
    {
        // TODO: Return the folio this charge belongs to
    }

    // ── POS Bridge ───────────────────────────────────────────

    public function postToRoom($guestId, $chargeType, $amount, $description)
    {
        // TODO: External Service POS Bridge
        // Verify guest is checked in, then post charge to their active folio
    }

    public function applyCancellationFee($folioId, $amount)
    {
        // TODO: Service Cancellation Fee Logic
    }
}
