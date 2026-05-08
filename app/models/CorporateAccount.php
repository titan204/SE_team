<?php
// ============================================================
//  CorporateAccount Model — Company accounts with contracted rates
//  Table: corporate_accounts
//
//  Usage: $corp = new CorporateAccount();
// ============================================================

class CorporateAccount extends AbstractModel
{
    protected $id;
    protected $company_name;
    protected $contact_email;
    protected $contact_phone;
    protected $contracted_rate;
    protected $created_at;

    public function __construct($db = null, array $aggregates = [])
    {
        parent::__construct($db, $aggregates);
        $this->registerAggregate('guests', Guest::class);
    }

    public function all()
    {
       $query = "SELECT * FROM corporate_accounts";
       $result = mysqli_query($this->db, $query);

       return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
    
    public function find($id)
    {
       $id = (int)$id;

       $query = "SELECT * FROM corporate_accounts WHERE id = $id";
       $result = mysqli_query($this->db, $query);

       return mysqli_fetch_assoc($result);
    }    
    public function create($data)
    {
       $company_name    = mysqli_real_escape_string($this->db, $data['company_name']);
       $contact_email   = mysqli_real_escape_string($this->db, $data['contact_email'] ?? '');
       $contact_phone   = mysqli_real_escape_string($this->db, $data['contact_phone'] ?? '');
       $contracted_rate = (float)$data['contracted_rate'];

       $query = "
        INSERT INTO corporate_accounts 
        (company_name, contact_email, contact_phone, contracted_rate)
        VALUES 
        ('$company_name', '$contact_email', '$contact_phone', $contracted_rate) ";

       mysqli_query($this->db, $query);

       return mysqli_insert_id($this->db);
    }
    public function update($id, $data)
   { 
       $id = (int)$id;
       $company_name    = mysqli_real_escape_string($this->db, $data['company_name']);
       $contact_email   = mysqli_real_escape_string($this->db, $data['contact_email'] ?? '');
       $contact_phone   = mysqli_real_escape_string($this->db, $data['contact_phone'] ?? '');
       $contracted_rate = (float)$data['contracted_rate'];

       $query = "
        UPDATE corporate_accounts 
        SET company_name = '$company_name',
            contact_email = '$contact_email',
            contact_phone = '$contact_phone',
            contracted_rate = $contracted_rate
        WHERE id = $id ";

        mysqli_query($this->db, $query);

    return true;
    }   
    public function delete($id)
   {
       $id = (int)$id;
       $query = "DELETE FROM corporate_accounts WHERE id = $id";
       mysqli_query($this->db, $query);

    return true;
   }

    public function guests() {
        $corpId = (int)$this->id;

        $query = "
        SELECT g.*
        FROM guests g
        JOIN guest_corporate gc ON gc.guest_id = g.id
        WHERE gc.corporate_id = $corpId ";

        $result = mysqli_query($this->db, $query);

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}
?>
