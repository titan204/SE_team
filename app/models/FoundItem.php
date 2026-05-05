<?php
// ============================================================
//  FoundItem Model — UC30: Log Found Item / UC37: Manage L&F
//  Tables: found_items, security_alerts, lost_item_reports,
//          item_returns
// ============================================================

class FoundItem extends Model
{
    // ── UC30: Housekeeper logs a found item ──────────────────

    /**
     * Generate next L&F reference: LF-YYYY-#####
     */
    private function generateReference(): string
    {
        $year = date('Y');
        $r    = mysqli_query($this->db,
            "SELECT COUNT(*) AS cnt FROM found_items WHERE lf_reference LIKE 'LF-$year-%'");
        $row  = $r ? mysqli_fetch_assoc($r) : null;
        $seq  = ($row ? (int)$row['cnt'] : 0) + 1;
        return sprintf('LF-%s-%05d', $year, $seq);
    }

    /**
     * UC30 duplicate check: same room + similar description within 2 hours.
     */
    public function checkDuplicate(string $roomNumber, string $description): array
    {
        $rm   = mysqli_real_escape_string($this->db, $roomNumber);
        $desc = mysqli_real_escape_string($this->db, $description);
        $r    = mysqli_query($this->db,
            "SELECT id, lf_reference, description, found_at
             FROM   found_items
             WHERE  room_number = '$rm'
               AND  found_at > NOW() - INTERVAL 2 HOUR
               AND  LOWER(description) LIKE LOWER('%$desc%')
             LIMIT  3");
        if (!$r) return [];
        return mysqli_fetch_all($r, MYSQLI_ASSOC);
    }

    /**
     * UC30 POST /found-items — insert record, trigger security if high-value.
     * $data keys: description, location_type, room_number, public_area,
     *             condition, photo_url, is_high_value
     * Returns [ 'id'=>int, 'lf_reference'=>string ]
     */
    public function create(array $data): array
    {
        $ref         = $this->generateReference();
        $desc        = mysqli_real_escape_string($this->db, $data['description'] ?? '');
        $locType     = in_array($data['location_type'] ?? '', ['room','public']) ? $data['location_type'] : 'room';
        $roomNum     = mysqli_real_escape_string($this->db, $data['room_number'] ?? '');
        $pubArea     = $data['public_area'] ?? null;
        $pubAreaSql  = $pubArea ? "'".mysqli_real_escape_string($this->db, $pubArea)."'" : 'NULL';
        $cond        = in_array($data['condition'] ?? '', ['good','damaged','fragile']) ? $data['condition'] : 'good';
        $photoUrl    = mysqli_real_escape_string($this->db, $data['photo_url'] ?? '');
        $photoSql    = $photoUrl ? "'$photoUrl'" : 'NULL';
        $isHV        = (int) !empty($data['is_high_value']);
        $userId      = (int) ($_SESSION['user_id'] ?? 0);

        mysqli_query($this->db,
            "INSERT INTO found_items
                 (lf_reference, description, location_type, room_number, public_area,
                  `condition`, photo_url, is_high_value, found_by_user_id)
             VALUES ('$ref', '$desc', '$locType', '$roomNum', $pubAreaSql,
                     '$cond', $photoSql, $isHV, $userId)");
        $id = (int) mysqli_insert_id($this->db);

        // Step d: high-value → security alert
        if ($isHV) {
            $msg = mysqli_real_escape_string($this->db,
                "High-value item found: $desc. Ref: $ref. Please secure immediately.");
            mysqli_query($this->db,
                "INSERT INTO security_alerts (alert_type, message, related_found_item_id, priority)
                 VALUES ('high_value_found_item', '$msg', $id, 'urgent')");
            mysqli_query($this->db,
                "UPDATE found_items SET escalated_to_security = 1 WHERE id = $id");
        }

        return ['id' => $id, 'lf_reference' => $ref];
    }

    // ── UC37: Front desk manages L&F queue ───────────────────

    /**
     * List all found_items with filters.
     */
    public function getQueue(array $filters = []): array
    {
        $where = ['1=1'];
        if (!empty($filters['status'])) {
            $s = mysqli_real_escape_string($this->db, $filters['status']);
            $where[] = "fi.status = '$s'";
        }
        if (!empty($filters['date_from'])) {
            $d = mysqli_real_escape_string($this->db, $filters['date_from']);
            $where[] = "DATE(fi.found_at) >= '$d'";
        }
        if (!empty($filters['date_to'])) {
            $d = mysqli_real_escape_string($this->db, $filters['date_to']);
            $where[] = "DATE(fi.found_at) <= '$d'";
        }
        $w = implode(' AND ', $where);
        $r = mysqli_query($this->db,
            "SELECT fi.*, u.name AS found_by_name
             FROM   found_items fi
             LEFT   JOIN users u ON fi.found_by_user_id = u.id
             WHERE  $w
             ORDER  BY fi.found_at DESC");
        if (!$r) return [];
        return mysqli_fetch_all($r, MYSQLI_ASSOC);
    }

    /**
     * Get all guest lost-item reports.
     */
    public function getLostReports(): array
    {
        $r = mysqli_query($this->db,
            "SELECT lir.*, g.name AS guest_name, g.email AS guest_email,
                    fi.lf_reference AS matched_ref
             FROM   lost_item_reports lir
             JOIN   guests g ON lir.guest_id = g.id
             LEFT   JOIN found_items fi ON lir.matched_found_item_id = fi.id
             ORDER  BY lir.created_at DESC");
        if (!$r) return [];
        return mysqli_fetch_all($r, MYSQLI_ASSOC);
    }

    /**
     * UC37 Step 2 — Accept guest report, try auto-match.
     */
    public function acceptGuestReport(array $data): array
    {
        $guestId  = (int)   $data['guest_id'];
        $desc     = mysqli_real_escape_string($this->db, $data['description'] ?? '');
        $resId    = !empty($data['reservation_id']) ? (int)$data['reservation_id'] : 'NULL';
        $lostDate = mysqli_real_escape_string($this->db, $data['lost_date'] ?? date('Y-m-d'));

        mysqli_query($this->db,
            "INSERT INTO lost_item_reports (guest_id, description, reservation_id, lost_date)
             VALUES ($guestId, '$desc', $resId, '$lostDate')");
        $reportId = (int) mysqli_insert_id($this->db);

        // Auto-match: similar description within ±3 days of lost_date
        $r = mysqli_query($this->db,
            "SELECT id, lf_reference, description, found_at, photo_url
             FROM   found_items
             WHERE  status = 'stored'
               AND  DATE(found_at) BETWEEN '$lostDate' - INTERVAL 3 DAY
                                       AND '$lostDate' + INTERVAL 3 DAY
               AND  LOWER(description) LIKE LOWER('%$desc%')
             LIMIT  5");
        $candidates = $r ? mysqli_fetch_all($r, MYSQLI_ASSOC) : [];

        return ['report_id' => $reportId, 'candidates' => $candidates];
    }

    /**
     * UC37 Step 3 — Confirm match between found_item and guest report.
     */
    public function confirmMatch(int $foundItemId, int $reportId, int $confirmedByUserId): bool
    {
        $fid  = (int) $foundItemId;
        $rid  = (int) $reportId;
        $uid  = (int) $confirmedByUserId;

        mysqli_query($this->db,
            "UPDATE found_items SET status = 'matched' WHERE id = $fid");
        mysqli_query($this->db,
            "UPDATE lost_item_reports
             SET    status = 'matched', matched_found_item_id = $fid
             WHERE  id = $rid");
        return true;
    }

    /**
     * UC37 Step 4 — Record item return.
     * method: 'pickup' or 'courier'
     */
    public function recordReturn(int $foundItemId, int $guestId, string $method, ?string $address, float $shippingCost): int
    {
        $fid    = (int)   $foundItemId;
        $gid    = (int)   $guestId;
        $meth   = mysqli_real_escape_string($this->db, $method);
        $addr   = $address ? "'".mysqli_real_escape_string($this->db, $address)."'" : 'NULL';
        $ship   = (float) $shippingCost;

        $newStatus = $method === 'pickup' ? 'claimed' : 'shipped';
        $retAt     = $method === 'pickup' ? "NOW()" : 'NULL';

        mysqli_query($this->db,
            "INSERT INTO item_returns (found_item_id, guest_id, return_method, return_address, shipping_cost, returned_at)
             VALUES ($fid, $gid, '$meth', $addr, $ship, $retAt)");
        $retId = (int) mysqli_insert_id($this->db);

        mysqli_query($this->db,
            "UPDATE found_items SET status = '$newStatus' WHERE id = $fid");

        return $retId;
    }

    /**
     * UC37 Step 5 — Dispose of item (requires supervisor_approval_code validation upstream).
     */
    public function dispose(int $foundItemId, string $method): bool
    {
        $fid  = (int) $foundItemId;
        $meth = in_array($method, ['donate','discard']) ? $method : 'discard';
        $r    = mysqli_query($this->db,
            "UPDATE found_items SET status = 'disposed' WHERE id = $fid AND status = 'stored'");
        return $r && mysqli_affected_rows($this->db) > 0;
    }

    public function find(int $id): ?array
    {
        $id = (int) $id;
        $r  = mysqli_query($this->db, "SELECT * FROM found_items WHERE id = $id LIMIT 1");
        if (!$r) return null;
        return mysqli_fetch_assoc($r) ?: null;
    }

    /**
     * UC37 Step 5 — Items past retention period (default 90 days), still in 'stored' status.
     * Returned to front desk/supervisor for action (dispose or extend).
     */
    public function getOverdueItems(int $retentionDays = 90): array
    {
        $r = mysqli_query($this->db,
            "SELECT * FROM found_items
             WHERE  status = 'stored'
               AND  found_at <= NOW() - INTERVAL $retentionDays DAY
             ORDER  BY found_at ASC");
        if (!$r) return [];
        return mysqli_fetch_all($r, MYSQLI_ASSOC);
    }
}
