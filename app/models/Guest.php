<?php
// ============================================================
//  Guest Model — Hotel guest profiles and CRM data
//  Table: guests
//
//  Usage:
//    $guest = new Guest();
//    $all   = $guest->all();
//    $one   = $guest->find(5);
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
        $query = " SELECT * from guests order by name ";
        $result = mysqli_query($this->db, $query);
        if (!$result) {

            die("Query Failed: " . mysqli_error($this->db)); // if query fails, output error message and stop execution
        }

        return mysqli_fetch_all($result, MYSQLI_ASSOC); //return an array of all guests
    }

    public function find($id)
    {
        $id = mysqli_real_escape_string($this->db, $id);
        $query = " SELECT * from guests where id = '$id' ";
        $result = mysqli_query($this->db, $query);
        if (!$result) {

            die("Query Failed: " . mysqli_error($this->db)); // if query fails, output error message and stop execution
        }  

        return mysqli_fetch_array($result, MYSQLI_ASSOC); //return an array of the guest with the specified id
    }

    public function create($data)
    {
        $name = mysqli_real_escape_string($this->db, $data['name']);
        $email = mysqli_real_escape_string($this->db, $data['email']);
        $phone = mysqli_real_escape_string($this->db, $data['phone'] ?? '');
        $national_id = mysqli_real_escape_string($this->db, $data['national_id'] ?? '');
        $nationality = mysqli_real_escape_string($this->db, $data['nationality'] ?? '');
        $date_of_birth = mysqli_real_escape_string($this->db, $data['date_of_birth'] ?? null);
        $referred_by = !empty($data['referred_by']) ? (int)$data['referred_by'] : "NULL";


        $query = " 
        INSERT INTO guests (name, email, phone, national_id, nationality, date_of_birth, referred_by) 
            VALUES ('$name', '$email', '$phone', '$national_id', '$nationality', 
            " . ($date_of_birth ? "'$date_of_birth'" : "NULL") . ", 
            " . ($referred_by !== "NULL" ? $referred_by : "NULL") . ") "; // can be NULL if not provided 

        $result = mysqli_query($this->db, $query);

        if (!$result) {
           die("Insert Failed: " . mysqli_error($this->db));
        }

        return mysqli_insert_id($this->db);
    }

    public function update($id, $data)
    {
        $id = (int)$id;
        $name = mysqli_real_escape_string($this->db, $data['name']);
        $email = mysqli_real_escape_string($this->db, $data['email']);
        $phone = mysqli_real_escape_string($this->db, $data['phone'] ?? '');
        $nationality = mysqli_real_escape_string($this->db, $data['nationality'] ?? '');
        $date_of_birth = mysqli_real_escape_string($this->db, $data['date_of_birth'] ?? null);

        $query = "
        UPDATE guests SET
            name = '$name',
            email = '$email',
            phone = '$phone',
            nationality = '$nationality',
            date_of_birth = " . ($date_of_birth ? "'$date_of_birth'" : "NULL") . "
        WHERE id = $id ";

        $result = mysqli_query($this->db, $query);

        if (!$result) {
           die("Update Failed: " . mysqli_error($this->db)); 
        }

        return true;
    }

    public function delete($id)
    {
        $id = (int)$id;
        $query = "DELETE FROM guests WHERE id = $id";
        $result = mysqli_query($this->db, $query);

        if (!$result) {
          die("Delete Failed: " . mysqli_error($this->db)); 
        }

        return true;
    }

    // ── Relationships ────────────────────────────────────────

    public function reservations()
    {
        $guestId = (int)$this->id;
        $query = "SELECT * FROM reservations WHERE guest_id = $guestId";
        $result = mysqli_query($this->db, $query);

        if (!$result) {
          die("Query Failed: " . mysqli_error($this->db));
        }

        return mysqli_fetch_all($result, MYSQLI_ASSOC);
     
    }

    public function preferences()
    {
        $guestId = (int)$this->id;
        $query = "SELECT * FROM guest_preferences WHERE guest_id = $guestId";
        $result = mysqli_query($this->db, $query);

        if (!$result) {
           die("Query Failed: " . mysqli_error($this->db));
      }

        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function corporateAccount()
    {
        $guestId = (int)$this->id;

        $sql = "
        SELECT ca.*
        FROM corporate_accounts ca
        JOIN guest_corporate gc ON gc.corporate_id = ca.id
        WHERE gc.guest_id = $guestId
        LIMIT 1 ";

       $result = mysqli_query($this->db, $sql);

       if (!$result) {
          die("Query Failed: " . mysqli_error($this->db));
        }

        return mysqli_fetch_assoc($result);
    }

    public function feedback()
    {
        $guestId = (int)$this->id;
        $query = "SELECT * FROM feedback WHERE guest_id = $guestId";
        $result = mysqli_query($this->db, $query);

        if (!$result) {
           die("Query Failed: " . mysqli_error($this->db));
        }

        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    // ── Business Logic Prototypes ────────────────────────────

    public function calculateLifetimeValue()
    {
        $guestId = (int)$this->id;
        $query = "
        SELECT SUM(total_price) AS lifetime_value
        FROM reservations
        WHERE guest_id = $guestId ";

       $result = mysqli_query($this->db, $query);

       if (!$result) {
        die("Query Failed: " . mysqli_error($this->db));
       }

       $row = mysqli_fetch_assoc($result);

       return $row['lifetime_value'] ?? 0;
    }

    public function updateLoyaltyTier()
    {
       $guestId = (int)$this->id;

       $query = " SELECT lifetime_nights FROM guests WHERE id = $guestId";
       $result = mysqli_query($this->db, $query);

       $row = mysqli_fetch_assoc($result);
       $nights = (int)$row['lifetime_nights'];

       if ($nights >= 50) {
         $tier = 'platinum';
       } 
       elseif ($nights >= 25) {
         $tier = 'gold';
       } 
       elseif ($nights >= 10) {
         $tier = 'silver';
       } 
       else {
         $tier = 'standard';
       }

       $update = "
        UPDATE guests 
        SET loyalty_tier = '$tier'
        WHERE id = $guestId";

       mysqli_query($this->db, $update);
    }

    public function flagAsVip()
    {
        $guestId = (int)$this->id;

        mysqli_query($this->db, "
            UPDATE guests 
            SET is_vip = 1  WHERE id = $guestId ");

        mysqli_query($this->db, "
        INSERT INTO audit_log (guest_id, action)
        VALUES ($guestId, 'Marked as VIP') ");
    }

    public function blacklist($reason)
    {
        $guestId = (int)$this->id;
        $reason = mysqli_real_escape_string($this->db, $reason);

        mysqli_query($this->db, "
          UPDATE guests 
          SET is_blacklisted = 1,
          blacklist_reason = '$reason'
          WHERE id = $guestId ");
    }

    public function anonymize()
    {
        $guestId = (int)$this->id;

        mysqli_query($this->db, "
           UPDATE guests 
            SET 
            name = 'ANONYMIZED',
            email = NULL,
            phone = NULL,
            national_id = NULL,
            nationality = NULL,
            gdpr_anonymized = 1
        WHERE id = $guestId");
    }

    public function referrals()
    {
        $guestId = (int)$this->id;
        $query = "SELECT * FROM guests WHERE referred_by = $guestId";
        $result = mysqli_query($this->db, $query);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function search($keyword)
   {
    $keyword = '%' . mysqli_real_escape_string($this->db, $keyword) . '%';

    $query = "
        SELECT * FROM guests
        WHERE name  LIKE '$keyword'
        OR    email LIKE '$keyword'
        ORDER BY name
    ";

    $result = mysqli_query($this->db, $query);

    if (!$result) {
        die("Search Failed: " . mysqli_error($this->db));
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function filterByVip()
    {
    $result = mysqli_query($this->db, 
        "SELECT * FROM guests WHERE is_vip = 1 ORDER BY name");
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
   }

   public function filterByBlacklist()
   {
    $result = mysqli_query($this->db, 
        "SELECT * FROM guests WHERE is_blacklisted = 1 ORDER BY name");
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
   }
}
?>
