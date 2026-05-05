<?php
// ============================================================
//  QAInspection Model — UC32: Manage Quality Assurance
//                        UC33: Submit Quality Score
//  Tables: qa_inspections, corrective_tasks,
//          quality_scores, housekeeper_performance
// ============================================================

class QAInspection extends Model
{
    // ── UC32 ─────────────────────────────────────────────────

    /**
     * Rooms pending inspection (status='clean', no inspection today).
     */
    public function getRoomsPendingInspection(): array
    {
        $today = date('Y-m-d');
        $r     = mysqli_query($this->db,
            "SELECT rm.id, rm.room_number, rm.floor, rm.status, rt.name AS room_type
             FROM   rooms rm
             JOIN   room_types rt ON rm.room_type_id = rt.id
             WHERE  rm.status IN ('available','dirty')
               AND  rm.id NOT IN (
                   SELECT room_id FROM qa_inspections WHERE inspection_date = '$today'
               )
             ORDER  BY rm.room_number ASC");
        if (!$r) return [];
        return mysqli_fetch_all($r, MYSQLI_ASSOC);
    }

    /**
     * Rooms flagged by low guest feedback (overall_score ≤ 2, flagged_for_qa=1).
     */
    public function getRoomsFlaggedByFeedback(): array
    {
        $r = mysqli_query($this->db,
            "SELECT DISTINCT rm.id, rm.room_number, rm.floor, rm.status,
                    rt.name AS room_type, f.rating AS feedback_rating, f.comments
             FROM   rooms rm
             JOIN   room_types rt ON rm.room_type_id = rt.id
             JOIN   reservations res ON res.room_id = rm.id
             JOIN   feedback f ON f.reservation_id = res.id
             WHERE  f.rating <= 2
             ORDER  BY f.rating ASC, rm.room_number ASC");
        if (!$r) return [];
        return mysqli_fetch_all($r, MYSQLI_ASSOC);
    }

    /**
     * Load checklist data + existing inspections for a room.
     */
    public function getInspectionData(int $roomId): array
    {
        $id = (int) $roomId;
        $rr = mysqli_query($this->db,
            "SELECT rm.*, rt.name AS room_type
             FROM   rooms rm JOIN room_types rt ON rm.room_type_id = rt.id
             WHERE  rm.id = $id LIMIT 1");
        $room = $rr ? mysqli_fetch_assoc($rr) : null;

        // Housekeepers list (for corrective assignment)
        $rh = mysqli_query($this->db,
            "SELECT id, name FROM users
             WHERE  role_id = (SELECT id FROM roles WHERE name = 'housekeeper') AND is_active = 1
             ORDER  BY name");
        $housekeepers = $rh ? mysqli_fetch_all($rh, MYSQLI_ASSOC) : [];

        return compact('room', 'housekeepers');
    }

    /**
     * UC32 POST /qa/inspections
     * $data keys: room_id, checklist_scores (assoc), overall_result, notes,
     *             corrective_assignments [{housekeeper_id, task_description, due_by}]
     *             is_critical (bool)
     */
    public function submitInspection(array $data): array
    {
        $roomId   = (int)   $data['room_id'];
        $inspector= (int)   ($_SESSION['user_id'] ?? 0);
        $result   = in_array($data['overall_result'] ?? '', ['pass','fail','corrective_action'])
                    ? $data['overall_result'] : 'pass';
        $scores   = mysqli_real_escape_string($this->db, json_encode($data['checklist_scores'] ?? []));
        $notes    = mysqli_real_escape_string($this->db, $data['notes'] ?? '');
        $today    = date('Y-m-d');

        mysqli_query($this->db,
            "INSERT INTO qa_inspections
                 (room_id, inspector_id, inspection_date, overall_result, checklist_scores, notes)
             VALUES ($roomId, $inspector, '$today', '$result', '$scores', '$notes')");
        $inspectionId = (int) mysqli_insert_id($this->db);

        $correctives = [];

        // Step b: create corrective tasks
        foreach ($data['corrective_assignments'] ?? [] as $ca) {
            $hkId = (int)   $ca['housekeeper_id'];
            $desc = mysqli_real_escape_string($this->db, $ca['task_description'] ?? '');
            $due  = mysqli_real_escape_string($this->db, $ca['due_by'] ?? '');
            $dueSql = $due ? "'$due'" : 'NULL';
            mysqli_query($this->db,
                "INSERT INTO corrective_tasks (qa_inspection_id, assigned_to_user_id, task_description, due_by)
                 VALUES ($inspectionId, $hkId, '$desc', $dueSql)");
            $correctives[] = (int) mysqli_insert_id($this->db);
        }

        // Step c: FAIL + critical → room out_of_order + maintenance request
        $isCritical = !empty($data['is_critical']);
        if ($result === 'fail' && $isCritical) {
            mysqli_query($this->db,
                "UPDATE rooms SET status = 'out_of_order' WHERE id = $roomId");
            // Auto-create a maintenance order for critical QA failure
            $mDesc = mysqli_real_escape_string($this->db,
                "Critical QA failure in room. Inspection #$inspectionId requires immediate maintenance.");
            $uid = $inspector;
            mysqli_query($this->db,
                "INSERT INTO maintenance_orders
                     (room_id, reported_by, description, priority, status)
                 VALUES ($roomId, $uid, '$mDesc', 'critical', 'open')");
        }

        // Step d: PASS → mark room inspected
        if ($result === 'pass') {
            mysqli_query($this->db,
                "UPDATE rooms SET status = 'available' WHERE id = $roomId");
        }

        return [
            'inspection_id' => $inspectionId,
            'correctives'   => $correctives,
            'room_ooo'      => ($result === 'fail' && $isCritical),
        ];
    }

    /**
     * UC32 GET /qa/trends — pass rates per housekeeper and floor.
     */
    public function getTrends(int $days = 30): array
    {
        $since = date('Y-m-d', strtotime("-{$days} days"));

        // Per inspector
        $ri = mysqli_query($this->db,
            "SELECT u.name AS inspector_name,
                    COUNT(*) AS total,
                    SUM(qi.overall_result = 'pass') AS passed,
                    ROUND(SUM(qi.overall_result = 'pass') * 100.0 / COUNT(*), 1) AS pass_rate
             FROM   qa_inspections qi
             JOIN   users u ON qi.inspector_id = u.id
             WHERE  qi.inspection_date >= '$since'
             GROUP  BY qi.inspector_id
             ORDER  BY pass_rate DESC");
        $byInspector = $ri ? mysqli_fetch_all($ri, MYSQLI_ASSOC) : [];

        // Per floor
        $rf = mysqli_query($this->db,
            "SELECT rm.floor,
                    COUNT(*) AS total,
                    SUM(qi.overall_result = 'pass') AS passed,
                    ROUND(SUM(qi.overall_result = 'pass') * 100.0 / COUNT(*), 1) AS pass_rate
             FROM   qa_inspections qi
             JOIN   rooms rm ON qi.room_id = rm.id
             WHERE  qi.inspection_date >= '$since'
             GROUP  BY rm.floor
             ORDER  BY rm.floor ASC");
        $byFloor = $rf ? mysqli_fetch_all($rf, MYSQLI_ASSOC) : [];

        return compact('byInspector', 'byFloor');
    }

    // ── UC33 ─────────────────────────────────────────────────

    /**
     * Load inspection for the scoring form.
     */
    public function findInspection(int $id): ?array
    {
        $id = (int) $id;
        $r  = mysqli_query($this->db,
            "SELECT qi.*, rm.room_number, u.name AS inspector_name
             FROM   qa_inspections qi
             JOIN   rooms rm ON qi.room_id = rm.id
             JOIN   users u  ON qi.inspector_id = u.id
             WHERE  qi.id = $id LIMIT 1");
        if (!$r) return null;
        return mysqli_fetch_assoc($r) ?: null;
    }

    /**
     * UC33 POST /qa/scores — submit quality score.
     */
    public function submitScore(array $data): array
    {
        $inspId = (int) $data['inspection_id'];
        $hkId   = (int) $data['housekeeper_id'];
        $roomId = (int) $data['room_id'];
        $c      = min(100, max(0, (int)($data['cleanliness']   ?? 0)));
        $p      = min(100, max(0, (int)($data['presentation']  ?? 0)));
        $co     = min(100, max(0, (int)($data['completeness']  ?? 0)));
        $s      = min(100, max(0, (int)($data['speed']         ?? 0)));
        $overall= round(($c + $p + $co + $s) / 4, 2);
        $notes  = mysqli_real_escape_string($this->db, $data['notes'] ?? '');
        $photos = mysqli_real_escape_string($this->db, json_encode($data['photo_urls'] ?? []));
        $uid    = (int) ($_SESSION['user_id'] ?? 0);

        mysqli_query($this->db,
            "INSERT INTO quality_scores
                 (inspection_id, housekeeper_id, room_id, cleanliness, presentation,
                  completeness, speed, overall_score, notes, photo_urls, submitted_by_user_id)
             VALUES ($inspId, $hkId, $roomId, $c, $p, $co, $s, $overall, '$notes', '$photos', $uid)");
        $scoreId = (int) mysqli_insert_id($this->db);

        // Step c: update housekeeper_performance
        $this->updatePerformance($hkId, $overall);

        // Step e: follow-up alert if overall < 60
        $followUp = false;
        if ($overall < 60) {
            $followUp = true;
            // Store as a qa_inspection note (simple approach without separate follow_up_alerts table)
            $msg = mysqli_real_escape_string($this->db,
                "Low quality score ($overall/100) for housekeeper #$hkId in room #$roomId.");
            mysqli_query($this->db,
                "INSERT INTO audit_log (user_id, action, target_type, target_id, old_value, new_value)
                 VALUES ($uid, 'qa_low_score_alert', 'quality_score', $scoreId, 'submitted', '$msg')");
        }

        return ['score_id' => $scoreId, 'overall' => $overall, 'follow_up_triggered' => $followUp];
    }

    /**
     * UC33 Step c — update running average + trend on housekeeper_performance.
     */
    private function updatePerformance(int $hkId, float $newScore): void
    {
        // Recalculate from all scores
        $r = mysqli_query($this->db,
            "SELECT AVG(overall_score) AS avg_s, COUNT(*) AS total
             FROM   quality_scores WHERE housekeeper_id = $hkId");
        $row = $r ? mysqli_fetch_assoc($r) : null;
        if (!$row) return;

        $avg   = round((float)$row['avg_s'], 2);
        $total = (int) $row['total'];

        // Trend: compare last 5 scores
        $rt = mysqli_query($this->db,
            "SELECT overall_score FROM quality_scores
             WHERE  housekeeper_id = $hkId ORDER BY created_at DESC LIMIT 5");
        $last5 = $rt ? array_column(mysqli_fetch_all($rt, MYSQLI_ASSOC), 'overall_score') : [];

        $trend = 'stable';
        if (count($last5) >= 3) {
            $first = (float)end($last5);
            $last  = (float)$last5[0];
            if ($last > $first + 3)    $trend = 'improving';
            elseif ($last < $first - 3) $trend = 'declining';
        }

        mysqli_query($this->db,
            "INSERT INTO housekeeper_performance (housekeeper_id, avg_score, total_inspections, trend)
             VALUES ($hkId, $avg, $total, '$trend')
             ON DUPLICATE KEY UPDATE
                 avg_score = $avg,
                 total_inspections = $total,
                 trend = '$trend',
                 updated_at = NOW()");
    }

    /**
     * UC33 PUT /qa/scores/{id}/dispute
     */
    public function disputeScore(int $scoreId, string $note): bool
    {
        $id   = (int) $scoreId;
        $note = mysqli_real_escape_string($this->db, $note);
        $r    = mysqli_query($this->db,
            "UPDATE quality_scores SET is_disputed = 1, dispute_resolution = '$note'
             WHERE  id = $id");
        return $r && mysqli_affected_rows($this->db) > 0;
    }
}
