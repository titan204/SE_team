<?php
// ============================================================
//  Guest Model — Hotel guest profiles and CRM data
//  Table: guests
// ============================================================

class Guest extends Model
{
    public $id;
    public $name;
    public $email;
    public $phone;
    public $national_id;
    public $nationality;
    public $date_of_birth;
    public $loyalty_tier;
    public $lifetime_nights;
    public $lifetime_value;
    public $is_blacklisted;
    public $blacklist_reason;
    public $is_vip;
    public $gdpr_anonymized;
    public $referred_by;
    public $created_at;
    public $updated_at;

    // ── CRUD ─────────────────────────────────────────────────

    public function all()
    {
        // TODO: Team will implement query logic here
        // SELECT * FROM guests ORDER BY name
    }

    public function find($id)
    {
        // TODO: Team will implement query logic here
        // SELECT * FROM guests WHERE id = ?
    }

    public function create($data)
    {
        // TODO: Team will implement query logic here
        // INSERT INTO guests (name, email, phone, ...) VALUES (?, ?, ?, ...)
    }

    public function update($id, $data)
    {
        // TODO: Team will implement query logic here
        // UPDATE guests SET name=?, email=?, ... WHERE id = ?
    }

    public function delete($id)
    {
        // TODO: Team will implement query logic here
        // DELETE FROM guests WHERE id = ?
    }

    // ── Relationships ────────────────────────────────────────

    public function reservations()
    {
        // TODO: Return all reservations for this guest
        // SELECT * FROM reservations WHERE guest_id = ?
    }

    public function preferences()
    {
        // TODO: Return guest preferences
        // SELECT * FROM guest_preferences WHERE guest_id = ?
    }

    public function corporateAccount()
    {
        // TODO: Return the corporate account linked to this guest
        // SELECT ca.* FROM corporate_accounts ca
        // JOIN guest_corporate gc ON gc.corporate_id = ca.id
        // WHERE gc.guest_id = ?
    }

    public function feedback()
    {
        // TODO: Return all feedback from this guest
        // SELECT * FROM feedback WHERE guest_id = ?
    }

    // ── Business Logic Prototypes ────────────────────────────

    public function calculateLifetimeValue()
    {
        // TODO: Sum all folio totals across all reservations for this guest
    }

    public function updateLoyaltyTier()
    {
        // TODO: Based on lifetime_nights, update loyalty_tier
        // e.g., 0-9 = standard, 10-24 = silver, 25-49 = gold, 50+ = platinum
    }

    public function flagAsVip()
    {
        // TODO: Set is_vip = 1, log to audit_log
    }

    public function blacklist($reason)
    {
        // TODO: Set is_blacklisted = 1, blacklist_reason = $reason
    }

    public function anonymize()
    {
        // TODO: GDPR "Right to be Forgotten" — replace PII with anonymized data
    }

    public function referrals()
    {
        // TODO: Return all guests referred by this guest
        // SELECT * FROM guests WHERE referred_by = ?
    }
}
