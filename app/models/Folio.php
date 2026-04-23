<?php
// ============================================================
//  Folio Model — Billing folio for a reservation
//  Table: folios
// ============================================================

class Folio extends Model
{
    public $id;
    public $reservation_id;
    public $total_amount;
    public $amount_paid;
    public $balance_due;    // computed column
    public $status;         // open, settled, refunded
    public $created_at;
    public $updated_at;

    // ── CRUD ─────────────────────────────────────────────────

    public function all()
    {
        // TODO: Team will implement query logic here
        // SELECT f.*, r.id AS res_id, g.name AS guest_name
        // FROM folios f
        // JOIN reservations r ON f.reservation_id = r.id
        // JOIN guests g ON r.guest_id = g.id
    }

    public function find($id)
    {
        // TODO: Team will implement query logic here
    }

    public function findByReservation($reservationId)
    {
        // TODO: Team will implement query logic here
        // SELECT * FROM folios WHERE reservation_id = ?
    }

    public function create($data)
    {
        // TODO: Team will implement query logic here
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

    public function reservation()
    {
        // TODO: Return the reservation for this folio
    }

    public function charges()
    {
        // TODO: Return all charges on this folio
        // SELECT * FROM folio_charges WHERE folio_id = ?
    }

    public function payments()
    {
        // TODO: Return all payments on this folio
        // SELECT * FROM payments WHERE folio_id = ?
    }

    // ── Business Logic ───────────────────────────────────────

    public function recalculateTotal()
    {
        // TODO: Sum all charges, update total_amount
    }

    public function settle()
    {
        // TODO: Set status = 'settled' when balance_due = 0
    }

    public function generateProFormaInvoice($id)
    {
        // TODO: Create a preview bill (Pro-Forma Invoice)
    }

    public function splitBill($chargeIds, $splitRatio)
    {
        // TODO: Shared-Expense Split-Bill logic
    }
}
