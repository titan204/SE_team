<?php
// ============================================================
//  CorporateAccount Model — Company accounts with contracted rates
//  Table: corporate_accounts
//
//  Usage: $corp = new CorporateAccount();
// ============================================================

class CorporateAccount extends Model
{
    public $id;
    public $company_name;
    public $contact_email;
    public $contact_phone;
    public $contracted_rate;
    public $created_at;

    public function all() { /* TODO: mysqli_query($this->db, "SELECT * FROM corporate_accounts") */ }
    public function find($id) { /* TODO: WHERE id = ? */ }
    public function create($data) { /* TODO: INSERT INTO corporate_accounts */ }
    public function update($id, $data) { /* TODO: UPDATE corporate_accounts */ }
    public function delete($id) { /* TODO: DELETE FROM corporate_accounts */ }

    public function guests() {
        // TODO: Return all guests linked to this corporate account
        // JOIN guest_corporate
    }
}
