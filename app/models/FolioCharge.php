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
        // $folioId = mysqli_real_escape_string($this->db, $folioId);
        // $result = mysqli_query($this->db, "SELECT * FROM folio_charges WHERE folio_id = '$folioId'");
        // return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function create($data)
    {
        // TODO: Team will implement query logic here
        // Use mysqli_real_escape_string() for each field, then INSERT
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
