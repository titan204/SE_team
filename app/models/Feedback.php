<?php
// ============================================================
//  Feedback Model — hotel_management.feedback table
// ============================================================
class Feedback extends Model
{
    /**
     * Submit new feedback for a reservation.
     * Returns new ID on success, false on duplicate or DB error.
     */
    public function createFeedback(array $data)
    {
        if ($this->hasFeedbackForReservation((int)$data['reservation_id'], (int)$data['guest_id'])) {
            return false;
        }

        $guestId    = (int)  $data['guest_id'];
        $resId      = (int)  $data['reservation_id'];
        $guestName  = mysqli_real_escape_string($this->db, trim($data['guest_name'] ?? ''));
        $overall    = (int)  $data['overall_rating'];
        $clean      = (int)  $data['cleanliness_rating'];
        $staff      = (int)  $data['staff_rating'];
        $food       = (int)  $data['food_rating'];
        $fac        = (int)  $data['facilities_rating'];
        $comment    = mysqli_real_escape_string($this->db, trim($data['comment'] ?? ''));
        $recommend  = !empty($data['recommend_hotel']) ? 1 : 0;

        // `rating` is the legacy column (NOT NULL) — mirror overall_rating into it
        $sql = "INSERT INTO feedback
                    (guest_id, guest_name, reservation_id,
                     overall_rating, cleanliness_rating, staff_rating,
                     food_rating, facilities_rating,
                     comment, recommend_hotel,
                     rating)
                VALUES
                    ($guestId, '$guestName', $resId,
                     $overall, $clean, $staff,
                     $food, $fac,
                     '$comment', $recommend,
                     $overall)";

        $ok = mysqli_query($this->db, $sql);

        if (!$ok) {
            error_log('[Feedback::createFeedback] MySQL error: ' . mysqli_error($this->db));
            return false;
        }

        return (int) mysqli_insert_id($this->db);
    }

    /**
     * All feedback rows for one guest, newest first.
     */
    public function getGuestFeedback(int $guestId): array
    {
        $guestId = (int)$guestId;
        $result  = mysqli_query($this->db,
            "SELECT f.*, r.check_in_date, r.check_out_date, rm.room_number
             FROM   feedback f
             INNER  JOIN reservations r  ON r.id  = f.reservation_id
             LEFT   JOIN rooms rm        ON rm.id = r.room_id
             WHERE  f.guest_id = $guestId
             ORDER  BY f.created_at DESC");
        return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    }

    /**
     * All feedback with guest/room info + optional filters.
     *
     * @param array $filters  Keys: rating, date_from, date_to, guest_id, is_resolved
     */
    public function getAllFeedback(array $filters = []): array
    {
        $where = ['1=1'];

        if (!empty($filters['rating'])) {
            $where[] = 'f.overall_rating = ' . (int)$filters['rating'];
        }
        if (!empty($filters['date_from'])) {
            $where[] = "DATE(f.created_at) >= '" . mysqli_real_escape_string($this->db, $filters['date_from']) . "'";
        }
        if (!empty($filters['date_to'])) {
            $where[] = "DATE(f.created_at) <= '" . mysqli_real_escape_string($this->db, $filters['date_to']) . "'";
        }
        if (!empty($filters['guest_id'])) {
            $where[] = 'f.guest_id = ' . (int)$filters['guest_id'];
        }
        if (isset($filters['is_resolved']) && $filters['is_resolved'] !== '') {
            $where[] = 'f.is_resolved = ' . (int)$filters['is_resolved'];
        }

        $w = implode(' AND ', $where);

        $result = mysqli_query($this->db,
            "SELECT f.*,
                    g.name  AS guest_name, g.email AS guest_email,
                    r.check_in_date, r.check_out_date,
                    rm.room_number, rt.name AS room_type_name,
                    u.name AS resolved_by_name
             FROM   feedback f
             INNER  JOIN guests       g  ON g.id  = f.guest_id
             INNER  JOIN reservations r  ON r.id  = f.reservation_id
             LEFT   JOIN rooms        rm ON rm.id = r.room_id
             LEFT   JOIN room_types   rt ON rt.id = rm.room_type_id
             LEFT   JOIN users        u  ON u.id  = f.resolved_by
             WHERE  $w
             ORDER  BY f.created_at DESC");

        return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    }

    /**
     * Average ratings across all (filtered) feedback.
     */
    public function calculateAverageRatings(array $filters = []): array
    {
        $where = ['1=1'];
        if (!empty($filters['rating']))    $where[] = 'f.overall_rating = '  . (int)$filters['rating'];
        if (!empty($filters['date_from'])) $where[] = "DATE(f.created_at) >= '" . mysqli_real_escape_string($this->db, $filters['date_from']) . "'";
        if (!empty($filters['date_to']))   $where[] = "DATE(f.created_at) <= '" . mysqli_real_escape_string($this->db, $filters['date_to'])   . "'";
        if (!empty($filters['guest_id']))  $where[] = 'f.guest_id = '         . (int)$filters['guest_id'];

        $w = implode(' AND ', $where);

        $result = mysqli_query($this->db,
            "SELECT COUNT(*) AS total,
                    SUM(recommend_hotel)                AS would_recommend,
                    ROUND(AVG(overall_rating),    2)    AS avg_overall,
                    ROUND(AVG(cleanliness_rating),2)    AS avg_cleanliness,
                    ROUND(AVG(staff_rating),      2)    AS avg_staff,
                    ROUND(AVG(food_rating),       2)    AS avg_food,
                    ROUND(AVG(facilities_rating), 2)    AS avg_facilities
             FROM   feedback f WHERE $w");

        if (!$result) return [];
        $row = mysqli_fetch_assoc($result);
        if (!$row || (int)$row['total'] === 0) {
            return ['total'=>0,'avg_overall'=>0,'avg_cleanliness'=>0,
                    'avg_staff'=>0,'avg_food'=>0,'avg_facilities'=>0,'recommend_pct'=>0];
        }
        $row['recommend_pct'] = round(($row['would_recommend'] / max((int)$row['total'],1)) * 100);
        return $row;
    }

    /**
     * Mark feedback as resolved.
     */
    public function markAsResolved(int $id, int $resolvedBy): bool
    {
        $id         = (int)$id;
        $resolvedBy = (int)$resolvedBy;
        $ok = mysqli_query($this->db,
            "UPDATE feedback
             SET is_resolved=1, resolved_at=NOW(), resolved_by=$resolvedBy
             WHERE id=$id AND is_resolved=0");
        return $ok && mysqli_affected_rows($this->db) > 0;
    }

    /**
     * Single feedback row with guest name/email.
     */
    public function find(int $id): ?array
    {
        $id = (int)$id;
        $r  = mysqli_query($this->db,
            "SELECT f.*, g.name AS guest_name, g.email AS guest_email
             FROM feedback f INNER JOIN guests g ON g.id=f.guest_id
             WHERE f.id=$id LIMIT 1");
        if (!$r) return null;
        $row = mysqli_fetch_assoc($r);
        return $row ?: null;
    }

    /**
     * Checks whether THIS guest already submitted feedback for a reservation.
     * Scoped by guest_id so old seed data from other guests doesn't block new submissions.
     */
    public function hasFeedbackForReservation(int $resId, int $guestId = 0): bool
    {
        $resId   = (int)$resId;
        $guestId = (int)$guestId;

        // If guest_id provided, check for THIS guest only
        $guestClause = $guestId > 0 ? "AND guest_id = $guestId" : '';

        $result = mysqli_query($this->db,
            "SELECT id FROM feedback
             WHERE reservation_id = $resId $guestClause
             LIMIT 1");
        return $result && mysqli_num_rows($result) > 0;
    }

    /**
     * Checks that a reservation belongs to the guest AND is checked_out.
     */
    public function reservationBelongsToGuest(int $resId, int $guestId): bool
    {
        $resId   = (int)$resId;
        $guestId = (int)$guestId;
        $result  = mysqli_query($this->db,
            "SELECT id FROM reservations
             WHERE id=$resId AND guest_id=$guestId AND status='checked_out'
             LIMIT 1");
        return $result && mysqli_num_rows($result) > 0;
    }

    /**
     * Checked-out reservations that have NO feedback from THIS guest yet.
     * The JOIN is scoped to guest_id so seed/other-guest feedback doesn't
     * accidentally hide eligible reservations.
     */
    public function eligibleReservations(int $guestId): array
    {
        $guestId = (int)$guestId;
        $sql     = "SELECT r.id, r.check_in_date, r.check_out_date,
                           rm.room_number, rt.name AS room_type_name
                    FROM   reservations r
                    LEFT JOIN rooms      rm ON rm.id = r.room_id
                    LEFT JOIN room_types rt ON rt.id = rm.room_type_id
                    LEFT JOIN feedback   f  ON f.reservation_id = r.id
                                           AND f.guest_id = r.guest_id
                    WHERE  r.guest_id = $guestId
                      AND  r.status   = 'checked_out'
                      AND  f.id IS NULL
                    ORDER  BY r.check_out_date DESC";

        $result = mysqli_query($this->db, $sql);
        if (!$result) {
            error_log('[Feedback::eligibleReservations] MySQL error: ' . mysqli_error($this->db));
            return [];
        }
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}
