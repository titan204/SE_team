<?php
// ============================================================
//  Feedback Model - hotel_management.feedback table
// ============================================================
class Feedback extends AbstractReport
{
    private $columnCache = null;

    public function __construct($db = null, array $aggregates = [])
    {
        parent::__construct($db, $aggregates);
        $this->setReportScope('guest_feedback');
        $this->setReportInputs(['guest_id', 'rating_min', 'rating_max', 'status']);
        $this->registerAggregate('guests', Guest::class);
        $this->registerAggregate('reservations', Reservation::class);
    }

    /**
     * Submit new feedback for a reservation.
     * Returns new ID on success, false on duplicate or DB error.
     */
    public function createFeedback(array $data)
    {
        if ($this->hasFeedbackForReservation((int)$data['reservation_id'], (int)$data['guest_id'])) {
            return false;
        }

        $guestId   = (int) $data['guest_id'];
        $resId     = (int) $data['reservation_id'];
        $guestName = mysqli_real_escape_string($this->db, trim($data['guest_name'] ?? ''));
        $overall   = (int) $data['overall_rating'];
        $clean     = (int) $data['cleanliness_rating'];
        $staff     = (int) $data['staff_rating'];
        $food      = (int) $data['food_rating'];
        $fac       = (int) $data['facilities_rating'];
        $comment   = mysqli_real_escape_string($this->db, trim($data['comment'] ?? ''));
        $recommend = !empty($data['recommend_hotel']) ? 1 : 0;

        $columns = [];
        $values  = [];

        $this->addInsertValue($columns, $values, 'guest_id', (string)$guestId);
        $this->addInsertValue($columns, $values, 'reservation_id', (string)$resId);
        $this->addInsertValue($columns, $values, 'guest_name', "'$guestName'");
        $this->addInsertValue($columns, $values, 'overall_rating', (string)$overall);
        $this->addInsertValue($columns, $values, 'cleanliness_rating', (string)$clean);
        $this->addInsertValue($columns, $values, 'staff_rating', (string)$staff);
        $this->addInsertValue($columns, $values, 'food_rating', (string)$food);
        $this->addInsertValue($columns, $values, 'facilities_rating', (string)$fac);
        $this->addInsertValue($columns, $values, 'comment', "'$comment'");
        $this->addInsertValue($columns, $values, 'comments', "'$comment'");
        $this->addInsertValue($columns, $values, 'recommend_hotel', (string)$recommend);
        $this->addInsertValue($columns, $values, 'rating', (string)$overall);
        $this->addInsertValue($columns, $values, 'overall_score', (string)$overall);
        $this->addInsertValue($columns, $values, 'flagged_for_qa', $overall <= 2 ? '1' : '0');

        if (empty($columns)) {
            return false;
        }

        $sql = "INSERT INTO feedback (" . implode(', ', $columns) . ")
                VALUES (" . implode(', ', $values) . ")";

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
        $guestId     = (int)$guestId;
        $created     = $this->createdAtExpression('f');
        $ratingExpr  = $this->ratingExpression('f');
        $commentExpr = $this->commentExpression('f');

        $result = mysqli_query($this->db,
            "SELECT f.*,
                    $ratingExpr AS overall_rating,
                    $commentExpr AS comment,
                    $created AS created_at,
                    r.check_in_date, r.check_out_date, rm.room_number
             FROM   feedback f
             INNER  JOIN reservations r  ON r.id  = f.reservation_id
             LEFT   JOIN rooms rm        ON rm.id = r.room_id
             WHERE  f.guest_id = $guestId
             ORDER  BY $created DESC");

        return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    }

    /**
     * All feedback with guest/room info + optional filters.
     *
     * @param array $filters Keys: rating, date_from, date_to, guest_id, is_resolved
     */
    public function getAllFeedback(array $filters = []): array
    {
        $where      = ['1=1'];
        $ratingExpr = $this->ratingExpression('f');
        $created    = $this->createdAtExpression('f');
        $commentExpr = $this->commentExpression('f');

        if (!empty($filters['rating'])) {
            $where[] = "$ratingExpr = " . (int)$filters['rating'];
        }
        if (!empty($filters['date_from'])) {
            $where[] = "DATE($created) >= '" . mysqli_real_escape_string($this->db, $filters['date_from']) . "'";
        }
        if (!empty($filters['date_to'])) {
            $where[] = "DATE($created) <= '" . mysqli_real_escape_string($this->db, $filters['date_to']) . "'";
        }
        if (!empty($filters['guest_id'])) {
            $where[] = 'f.guest_id = ' . (int)$filters['guest_id'];
        }
        if (isset($filters['is_resolved']) && $filters['is_resolved'] !== '') {
            $where[] = $this->hasColumn('is_resolved')
                ? 'f.is_resolved = ' . (int)$filters['is_resolved']
                : ((int)$filters['is_resolved'] === 0 ? '1=1' : '1=0');
        }

        $w = implode(' AND ', $where);
        $resolveSelect = $this->hasColumn('is_resolved')
            ? 'f.is_resolved, f.resolved_at, f.resolved_by'
            : '0 AS is_resolved, NULL AS resolved_at, NULL AS resolved_by';
        $resolverJoin = $this->hasColumn('resolved_by')
            ? 'LEFT   JOIN users        u  ON u.id  = f.resolved_by'
            : '';
        $resolverName = $this->hasColumn('resolved_by') ? 'u.name' : 'NULL';

        $result = mysqli_query($this->db,
            "SELECT f.*,
                    $ratingExpr AS overall_rating,
                    $commentExpr AS comment,
                    $created AS created_at,
                    $resolveSelect,
                    g.name AS guest_name, g.email AS guest_email,
                    r.check_in_date, r.check_out_date,
                    rm.room_number, rt.name AS room_type_name,
                    $resolverName AS resolved_by_name
             FROM   feedback f
             INNER  JOIN guests       g  ON g.id  = f.guest_id
             INNER  JOIN reservations r  ON r.id  = f.reservation_id
             LEFT   JOIN rooms        rm ON rm.id = r.room_id
             LEFT   JOIN room_types   rt ON rt.id = rm.room_type_id
             $resolverJoin
             WHERE  $w
             ORDER  BY $created DESC");

        return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    }

    /**
     * Average ratings across all (filtered) feedback.
     */
    public function calculateAverageRatings(array $filters = []): array
    {
        $where      = ['1=1'];
        $ratingExpr = $this->ratingExpression('f');
        $created    = $this->createdAtExpression('f');

        if (!empty($filters['rating'])) {
            $where[] = "$ratingExpr = " . (int)$filters['rating'];
        }
        if (!empty($filters['date_from'])) {
            $where[] = "DATE($created) >= '" . mysqli_real_escape_string($this->db, $filters['date_from']) . "'";
        }
        if (!empty($filters['date_to'])) {
            $where[] = "DATE($created) <= '" . mysqli_real_escape_string($this->db, $filters['date_to']) . "'";
        }
        if (!empty($filters['guest_id'])) {
            $where[] = 'f.guest_id = ' . (int)$filters['guest_id'];
        }

        $w = implode(' AND ', $where);
        $recommendSum = $this->hasColumn('recommend_hotel') ? 'SUM(recommend_hotel)' : '0';
        $cleanExpr = $this->ratingCategoryExpression('cleanliness_rating', 'f');
        $staffExpr = $this->ratingCategoryExpression('staff_rating', 'f');
        $foodExpr  = $this->ratingCategoryExpression('food_rating', 'f');
        $facExpr   = $this->ratingCategoryExpression('facilities_rating', 'f');

        $result = mysqli_query($this->db,
            "SELECT COUNT(*) AS total,
                    $recommendSum AS would_recommend,
                    ROUND(AVG($ratingExpr), 2) AS avg_overall,
                    ROUND(AVG($cleanExpr), 2) AS avg_cleanliness,
                    ROUND(AVG($staffExpr), 2) AS avg_staff,
                    ROUND(AVG($foodExpr), 2) AS avg_food,
                    ROUND(AVG($facExpr), 2) AS avg_facilities
             FROM   feedback f
             WHERE  $w");

        if (!$result) {
            return [];
        }

        $row = mysqli_fetch_assoc($result);
        if (!$row || (int)$row['total'] === 0) {
            return [
                'total' => 0,
                'avg_overall' => 0,
                'avg_cleanliness' => 0,
                'avg_staff' => 0,
                'avg_food' => 0,
                'avg_facilities' => 0,
                'recommend_pct' => 0,
            ];
        }

        $row['recommend_pct'] = round(((float)$row['would_recommend'] / max((int)$row['total'], 1)) * 100);
        return $row;
    }

    /**
     * Mark feedback as resolved.
     */
    public function markAsResolved(int $id, int $resolvedBy): bool
    {
        if (!$this->hasColumn('is_resolved') || !$this->hasColumn('resolved_at') || !$this->hasColumn('resolved_by')) {
            return false;
        }

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
        $id          = (int)$id;
        $ratingExpr  = $this->ratingExpression('f');
        $commentExpr = $this->commentExpression('f');
        $created     = $this->createdAtExpression('f');
        $r  = mysqli_query($this->db,
            "SELECT f.*,
                    $ratingExpr AS overall_rating,
                    $commentExpr AS comment,
                    $created AS created_at,
                    g.name AS guest_name, g.email AS guest_email
             FROM feedback f
             INNER JOIN guests g ON g.id=f.guest_id
             WHERE f.id=$id
             LIMIT 1");

        if (!$r) {
            return null;
        }

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

    private function addInsertValue(array &$columns, array &$values, string $column, string $value): void
    {
        if (!$this->hasColumn($column)) {
            return;
        }

        $columns[] = $column;
        $values[]  = $value;
    }

    private function getExistingColumns(): array
    {
        if ($this->columnCache !== null) {
            return $this->columnCache;
        }

        $result = mysqli_query($this->db, 'SHOW COLUMNS FROM feedback');
        if (!$result) {
            $this->columnCache = ['reservation_id', 'guest_id', 'rating', 'comments', 'submitted_at'];
            return $this->columnCache;
        }

        $columns = [];
        while ($row = mysqli_fetch_assoc($result)) {
            if (!empty($row['Field'])) {
                $columns[] = $row['Field'];
            }
        }

        $this->columnCache = $columns ?: ['reservation_id', 'guest_id', 'rating', 'comments', 'submitted_at'];
        return $this->columnCache;
    }

    private function hasColumn(string $column): bool
    {
        return in_array($column, $this->getExistingColumns(), true);
    }

    private function ratingExpression(string $alias = 'f'): string
    {
        if ($this->hasColumn('overall_rating')) {
            return "$alias.overall_rating";
        }

        if ($this->hasColumn('overall_score')) {
            return "COALESCE($alias.overall_score, $alias.rating)";
        }

        return "$alias.rating";
    }

    private function ratingCategoryExpression(string $column, string $alias = 'f'): string
    {
        return $this->hasColumn($column) ? "$alias.$column" : $this->ratingExpression($alias);
    }

    private function commentExpression(string $alias = 'f'): string
    {
        if ($this->hasColumn('comment') && $this->hasColumn('comments')) {
            return "COALESCE($alias.comment, $alias.comments)";
        }

        if ($this->hasColumn('comment')) {
            return "$alias.comment";
        }

        return "$alias.comments";
    }

    private function createdAtExpression(string $alias = 'f'): string
    {
        return $this->hasColumn('created_at') ? "$alias.created_at" : "$alias.submitted_at";
    }
}
