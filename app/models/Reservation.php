<?php
// ============================================================
//  Reservation Model — Bookings / check-in / check-out
//  Table: reservations
// ============================================================

class Reservation extends Model
{
    public $id;
    public $guest_id;
    public $room_id;
    public $assigned_by;
    public $check_in_date;
    public $check_out_date;
    public $actual_check_in;
    public $actual_check_out;
    public $status;         // pending, confirmed, checked_in, checked_out, cancelled, no_show
    public $adults;
    public $children;
    public $special_requests;
    public $deposit_amount;
    public $deposit_paid;
    public $is_group;
    public $group_id;
    public $total_price;
    public $created_at;
    public $updated_at;

    // ── CRUD ─────────────────────────────────────────────────

    public function all()
    {
        // TODO: Team will implement query logic here
        // SELECT r.*, g.name AS guest_name, rm.room_number
        // FROM reservations r
        // JOIN guests g ON r.guest_id = g.id
        // JOIN rooms rm ON r.room_id = rm.id
    }

    public function find($id)
    {
        // TODO: Team will implement query logic here
        // SELECT * FROM reservations WHERE id = ?
    }

    public function create($data)
    {
        // TODO: Team will implement query logic here
        // INSERT INTO reservations (...) VALUES (...)
    }

    public function update($id, $data)
    {
        // TODO: Team will implement query logic here
        // UPDATE reservations SET ... WHERE id = ?
    }

    public function delete($id)
    {
        // TODO: Team will implement query logic here
        // DELETE FROM reservations WHERE id = ?
    }

    // ── Relationships ────────────────────────────────────────

    public function guest()
    {
        // TODO: Return the guest for this reservation
    }

    public function room()
    {
        // TODO: Return the room for this reservation
    }

    public function folio()
    {
        // TODO: Return the folio for this reservation
    }

    // ── Workflow Methods ─────────────────────────────────────

    public function confirm($id)
    {
        // TODO: Set status = 'confirmed'
    }

    public function checkIn($id)
    {
        // TODO: Set status = 'checked_in', actual_check_in = NOW()
        // TODO: Update room status to 'occupied'
    }

    public function checkOut($id)
    {
        // TODO: Set status = 'checked_out', actual_check_out = NOW()
        // TODO: Update room status to 'dirty'
        // TODO: Create housekeeping task
    }

    public function cancel($id)
    {
        // TODO: Set status = 'cancelled'
        // TODO: Handle cancellation fee logic
    }

    public function markNoShow($id)
    {
        // TODO: Set status = 'no_show'
        // TODO: Trigger No-Show Penalty (charge card, release room)
    }

    // ── Search / Filter ──────────────────────────────────────

    public function findByDateRange($from, $to)
    {
        // TODO: Return reservations within a date range
    }

    public function findByStatus($status)
    {
        // TODO: Return reservations with given status
    }

    public function findByGuest($guestId)
    {
        // TODO: Return all reservations for a specific guest
    }

    public function findGroupReservations($groupId)
    {
        // TODO: Return all reservations sharing the same group_id
    }

    // ── Early Check-In ───────────────────────────────────────

    public function checkEarlyCheckInEligibility($id)
    {
        // TODO: Cross-reference housekeeping status against guest arrival time
    }
}
