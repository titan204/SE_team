<?php

class Guest extends AbstractUser
{
    protected $id;
    protected $name;
    protected $email;
    protected $phone;
    protected $national_id;
    protected $nationality;
    protected $date_of_birth;
    protected $loyalty_tier;
    protected $lifetime_nights;
    protected $lifetime_value;
    protected $is_blacklisted;
    protected $blacklist_reason;
    protected $is_vip;
    protected $gdpr_anonymized;
    protected $referred_by;
    protected $created_at;
    protected $updated_at;
    private $columnCache = null;

    public function __construct($db = null, $profileData = null, array $aggregates = [])
    {
        parent::__construct($db, $profileData, $aggregates);
        $this->registerAggregate('reservations', Reservation::class);
        $this->registerAggregate('preferences', GuestPreference::class);
        $this->registerAggregate('corporateAccount', CorporateAccount::class);
        $this->registerAggregate('feedback', Feedback::class);
    }

    public function all()
    {
        $query = "SELECT * from guests order by name";
        $result = mysqli_query($this->db, $query);
        if (!$result) die("Query Failed: " . mysqli_error($this->db));
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function find($id)
    {
        $id = mysqli_real_escape_string($this->db, $id);
        $query = "SELECT * from guests where id = '$id'";
        $result = mysqli_query($this->db, $query);
        if (!$result) die("Query Failed: " . mysqli_error($this->db));
        return mysqli_fetch_assoc($result);
    }

    public function findByEmail($email)
    {
        $email = mysqli_real_escape_string($this->db, trim($email));
        $query = "SELECT * FROM guests WHERE email = '$email' LIMIT 1";
        $result = mysqli_query($this->db, $query);
        if (!$result) die("Query Failed: " . mysqli_error($this->db));
        return mysqli_fetch_assoc($result);
    }

    public function create($data)
    {
        $this->hydrateProfileData($data);
        $supportedColumns = array_flip($this->getExistingColumns());
        $allowedColumns = ['name', 'email', 'phone', 'national_id', 'nationality', 'date_of_birth', 'referred_by'];
        $columns = [];
        $values = [];

        foreach ($allowedColumns as $column) {
            if (!isset($supportedColumns[$column]) || !array_key_exists($column, $data)) continue;
            if ($column === 'referred_by') {
                if ($data[$column] === '' || $data[$column] === null || !is_numeric($data[$column])) continue;
                $columns[] = $column;
                $values[]  = (string)((int)$data[$column]);
                continue;
            }
            if ($column === 'date_of_birth') {
                $columns[] = $column;
                $values[]  = !empty($data[$column])
                    ? "'" . mysqli_real_escape_string($this->db, $data[$column]) . "'"
                    : "NULL";
                continue;
            }
            $columns[] = $column;
            $values[]  = "'" . mysqli_real_escape_string($this->db, (string)($data[$column] ?? '')) . "'";
        }

        if (!in_array('name', $columns, true) || !in_array('email', $columns, true))
            die("Insert Failed: guests table is missing required columns.");

        $query  = "INSERT INTO guests (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ")";
        $result = mysqli_query($this->db, $query);
        if (!$result) die("Insert Failed: " . mysqli_error($this->db));
        return mysqli_insert_id($this->db);
    }

    public function getExistingColumns()
    {
        if ($this->columnCache !== null) return $this->columnCache;
        $result  = mysqli_query($this->db, "SHOW COLUMNS FROM guests");
        if (!$result) { $this->columnCache = ['name', 'email']; return $this->columnCache; }
        $columns = [];
        while ($row = mysqli_fetch_assoc($result)) {
            if (!empty($row['Field'])) $columns[] = $row['Field'];
        }
        if (empty($columns)) $columns = ['name', 'email'];
        $this->columnCache = $columns;
        return $this->columnCache;
    }

    public function update($id, $data)
    {
        $id            = (int)$id;
        $name          = mysqli_real_escape_string($this->db, $data['name']);
        $email         = mysqli_real_escape_string($this->db, $data['email']);
        $phone         = mysqli_real_escape_string($this->db, $data['phone'] ?? '');
        $nationality   = mysqli_real_escape_string($this->db, $data['nationality'] ?? '');
        $date_of_birth = mysqli_real_escape_string($this->db, $data['date_of_birth'] ?? '');

        $query = "UPDATE guests SET
            name = '$name', email = '$email', phone = '$phone',
            nationality = '$nationality',
            date_of_birth = " . ($date_of_birth ? "'$date_of_birth'" : "NULL") . "
            WHERE id = $id";

        $result = mysqli_query($this->db, $query);
        if (!$result) die("Update Failed: " . mysqli_error($this->db));
        return true;
    }

    public function delete($id)
{
    $id = (int)$id;

    // حذف البيانات المرتبطة أولاً
    mysqli_query($this->db, "DELETE FROM feedback WHERE guest_id = $id");
    mysqli_query($this->db, "DELETE FROM service_bookings WHERE guest_id = $id");
    mysqli_query($this->db, "DELETE FROM guest_preferences WHERE guest_id = $id");
    mysqli_query($this->db, "DELETE FROM guest_corporate WHERE guest_id = $id");
    mysqli_query($this->db, "DELETE FROM lost_and_found WHERE guest_id = $id");

    // حذف الـ folios المرتبطة بالـ reservations
    $res = mysqli_query($this->db, "SELECT id FROM reservations WHERE guest_id = $id");
    while ($row = mysqli_fetch_assoc($res)) {
        $rid = (int)$row['id'];
        mysqli_query($this->db, "DELETE FROM folio_charges WHERE folio_id IN (SELECT id FROM folios WHERE reservation_id = $rid)");
        mysqli_query($this->db, "DELETE FROM payments WHERE folio_id IN (SELECT id FROM folios WHERE reservation_id = $rid)");
        mysqli_query($this->db, "DELETE FROM folios WHERE reservation_id = $rid");
        mysqli_query($this->db, "DELETE FROM billing_items WHERE reservation_id = $rid");
        mysqli_query($this->db, "DELETE FROM billing_adjustments WHERE reservation_id = $rid");
    }

   
    mysqli_query($this->db, "DELETE FROM reservations WHERE guest_id = $id");

    
    $result = mysqli_query($this->db, "DELETE FROM guests WHERE id = $id");
    if (!$result) die("Delete Failed: " . mysqli_error($this->db));
    return true;
}
    public function reservations()
    {
        $guestId = (int)$this->id;
        $result  = mysqli_query($this->db, "SELECT * FROM reservations WHERE guest_id = $guestId");
        if (!$result) die("Query Failed: " . mysqli_error($this->db));
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function preferences()
    {
        $guestId = (int)$this->id;
        $query   = "SELECT id, guest_id, pref_key, pref_value
                    FROM guest_preferences WHERE guest_id = $guestId
                    ORDER BY id ASC";
        $result  = mysqli_query($this->db, $query);
        if (!$result) die("Query Failed: " . mysqli_error($this->db));
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function corporateAccount()
    {
        $guestId = (int)$this->id;
        $sql     = "SELECT ca.* FROM corporate_accounts ca
                    JOIN guest_corporate gc ON gc.corporate_id = ca.id
                    WHERE gc.guest_id = $guestId LIMIT 1";
        $result  = mysqli_query($this->db, $sql);
        if (!$result) die("Query Failed: " . mysqli_error($this->db));
        return mysqli_fetch_assoc($result);
    }

    public function feedback()
    {
        $guestId = (int)$this->id;
        $result  = mysqli_query($this->db, "SELECT * FROM feedback WHERE guest_id = $guestId");
        if (!$result) die("Query Failed: " . mysqli_error($this->db));
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function calculateLifetimeValue()
    {
        $guestId = (int)$this->id;
        $result  = mysqli_query($this->db,
            "SELECT SUM(total_price) AS lifetime_value FROM reservations WHERE guest_id = $guestId");
        if (!$result) die("Query Failed: " . mysqli_error($this->db));
        $row = mysqli_fetch_assoc($result);
        return $row['lifetime_value'] ?? 0;
    }

    public function updateLoyaltyTier()
    {
        $guestId = (int)$this->id;
        $result  = mysqli_query($this->db, "SELECT lifetime_nights FROM guests WHERE id = $guestId");
        $row     = mysqli_fetch_assoc($result);
        $nights  = (int)$row['lifetime_nights'];
        if      ($nights >= 50) $tier = 'platinum';
        elseif  ($nights >= 25) $tier = 'gold';
        elseif  ($nights >= 10) $tier = 'silver';
        else                    $tier = 'standard';
        mysqli_query($this->db, "UPDATE guests SET loyalty_tier = '$tier' WHERE id = $guestId");
    }

    public function flagAsVip()
    {
        $guestId = (int)$this->id;
        $userId  = (int)($_SESSION['user_id'] ?? 0);
        mysqli_query($this->db, "UPDATE guests SET is_vip = 1 WHERE id = $guestId");
        mysqli_query($this->db,
            "INSERT INTO audit_log (user_id, action, target_type, target_id, old_value, new_value)
             VALUES ($userId, 'vip_flag', 'guest', $guestId, '0', '1')");
    }

    public function blacklist($reason)
    {
        $guestId = (int)$this->id;
        $reason  = mysqli_real_escape_string($this->db, $reason);
        mysqli_query($this->db,
            "UPDATE guests SET is_blacklisted = 1, blacklist_reason = '$reason' WHERE id = $guestId");
    }

    public function anonymize()
    {
        $guestId = (int)$this->id;
        mysqli_query($this->db,
            "UPDATE guests SET name='ANONYMIZED', email=NULL, phone=NULL,
             national_id=NULL, nationality=NULL, gdpr_anonymized=1 WHERE id = $guestId");
    }

    public function referrals()
    {
        $guestId = (int)$this->id;
        $result  = mysqli_query($this->db, "SELECT * FROM guests WHERE referred_by = $guestId");
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function search($keyword)
    {
        $keyword = '%' . mysqli_real_escape_string($this->db, $keyword) . '%';
        $result  = mysqli_query($this->db,
            "SELECT * FROM guests WHERE name LIKE '$keyword' OR email LIKE '$keyword' ORDER BY name");
        if (!$result) die("Search Failed: " . mysqli_error($this->db));
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function filterByVip()
    {
        $result = mysqli_query($this->db, "SELECT * FROM guests WHERE is_vip = 1 ORDER BY name");
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function filterByBlacklist()
    {
        $result = mysqli_query($this->db, "SELECT * FROM guests WHERE is_blacklisted = 1 ORDER BY name");
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function getProfileWithRelations(int $guestId): ?array
    {
        $guestId = (int)$guestId;
        $r1      = mysqli_query($this->db, "SELECT * FROM guests WHERE id = $guestId LIMIT 1");
        if (!$r1) return null;
        $guest = mysqli_fetch_assoc($r1);
        if (!$guest) return null;

        $r2          = mysqli_query($this->db,
    "SELECT pref_key, pref_value
     FROM guest_preferences WHERE guest_id = $guestId ORDER BY id ASC");
$preferences = $r2 ? mysqli_fetch_all($r2, MYSQLI_ASSOC) : [];

        $r3 = mysqli_query($this->db,
    "SELECT r.id, r.check_in_date, r.check_out_date,
            r.status, r.total_price, rm.room_number, rt.name AS room_type
     FROM reservations r
     LEFT JOIN rooms rm ON rm.id = r.room_id
     LEFT JOIN room_types rt ON rt.id = rm.room_type_id
     WHERE r.guest_id = $guestId ORDER BY r.check_in_date DESC");
        $reservations = $r3 ? mysqli_fetch_all($r3, MYSQLI_ASSOC) : [];

        return ['guest' => $guest, 'preferences' => $preferences, 'reservations' => $reservations];
    }

    public function saveAllPreferences(int $guestId, array $prefs): bool
    {
        $guestId = (int)$guestId;
        foreach ($prefs as $key => $value) {
            $key      = mysqli_real_escape_string($this->db, (string)$key);
            $value    = trim((string)$value);
            $existing = mysqli_query($this->db,
                "SELECT id FROM guest_preferences WHERE guest_id = $guestId AND pref_key = '$key' LIMIT 1");
            $row      = $existing ? mysqli_fetch_assoc($existing) : null;
            if ($value === '') {
                if ($row) mysqli_query($this->db,
                    "DELETE FROM guest_preferences WHERE id = {$row['id']} AND guest_id = $guestId");
            } else {
                $v = mysqli_real_escape_string($this->db, $value);
                if ($row)
                    mysqli_query($this->db,
                        "UPDATE guest_preferences SET pref_value = '$v' WHERE id = {$row['id']} AND guest_id = $guestId");
                else
                    mysqli_query($this->db,
                        "INSERT INTO guest_preferences (guest_id, pref_key, pref_value) VALUES ($guestId, '$key', '$v')");
            }
        }
        return true;
    }

    public function addPreference(int $guestId, array $data): ?int
    {
        $guestId = (int)$guestId;
        $key     = trim($data['pref_key']   ?? '');
        $value   = trim($data['pref_value'] ?? '');
        if ($key === '' || $value === '') return null;
        $stmt = mysqli_prepare($this->db,
            'INSERT INTO guest_preferences (guest_id, pref_key, pref_value) VALUES (?, ?, ?)');
        if (!$stmt) return null;
        mysqli_stmt_bind_param($stmt, 'iss', $guestId, $key, $value);
        $ok    = mysqli_stmt_execute($stmt);
        $newId = $ok ? (int)mysqli_insert_id($this->db) : null;
        mysqli_stmt_close($stmt);
        return $newId;
    }

    public function updatePreference(int $prefId, int $guestId, array $data): bool
    {
        $prefId  = (int)$prefId;
        $guestId = (int)$guestId;
        $key     = trim($data['pref_key']   ?? '');
        $value   = trim($data['pref_value'] ?? '');
        if ($key === '' || $value === '') return false;
        $stmt = mysqli_prepare($this->db,
            'UPDATE guest_preferences SET pref_key = ?, pref_value = ? WHERE id = ? AND guest_id = ?');
        if (!$stmt) return false;
        mysqli_stmt_bind_param($stmt, 'ssii', $key, $value, $prefId, $guestId);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok && mysqli_affected_rows($this->db) > 0;
    }

    public function deletePreference(int $prefId, int $guestId): bool
    {
        $prefId  = (int)$prefId;
        $guestId = (int)$guestId;
        $stmt    = mysqli_prepare($this->db,
            'DELETE FROM guest_preferences WHERE id = ? AND guest_id = ?');
        if (!$stmt) return false;
        mysqli_stmt_bind_param($stmt, 'ii', $prefId, $guestId);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok && mysqli_affected_rows($this->db) > 0;
    }

    public function cancelReservation(int $reservationId, int $guestId): bool
    {
        $reservationId = (int)$reservationId;
        $guestId       = (int)$guestId;
        $stmt          = mysqli_prepare($this->db,
            "UPDATE reservations SET status = 'cancelled' WHERE id = ? AND guest_id = ? AND status = 'confirmed'");
        if (!$stmt) return false;
        mysqli_stmt_bind_param($stmt, 'ii', $reservationId, $guestId);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok && mysqli_affected_rows($this->db) > 0;
    }

    public function updateProfile(int $guestId, array $data): array
    {
        $this->hydrateProfileData($data);
        $guestId = (int)$guestId;
        $errors  = [];
        $name    = trim($data['name']  ?? '');
        $email   = trim($data['email'] ?? '');
        if ($name  === '') $errors['name']  = 'Full name is required.';
        if ($email === '') $errors['email'] = 'Email address is required.';
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Invalid email.';
        if (!empty($errors)) return ['ok' => false, 'errors' => $errors];

        $phone       = trim($data['phone']         ?? '');
        $nationality = trim($data['nationality']   ?? '');
        $dob         = trim($data['date_of_birth'] ?? '');
        $dobParam    = $dob !== '' ? $dob : null;

        $stmt = mysqli_prepare($this->db,
            'UPDATE guests SET name=?, email=?, phone=?, nationality=?, date_of_birth=? WHERE id=?');
        if (!$stmt) return ['ok' => false, 'errors' => ['db' => 'Database error.']];
        mysqli_stmt_bind_param($stmt, 'sssssi', $name, $email, $phone, $nationality, $dobParam, $guestId);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok ? ['ok' => true] : ['ok' => false, 'errors' => ['db' => 'Update failed.']];
    }
}
?>
