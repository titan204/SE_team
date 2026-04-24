<?php
// ============================================================
//  HousekeepingTask Model — Cleaning & inspection tasks
//  Table: housekeeping_tasks
//
//  Usage: $task = new HousekeepingTask();
// ============================================================

class HousekeepingTask extends Model
{
    public $id;
    public $room_id;
    public $assigned_to;
    public $task_type;      // cleaning, turndown, inspection, deep_clean, minibar_check
    public $status;         // pending, in_progress, done, skipped
    public $notes;
    public $quality_score;  // 1-5
    public $created_at;
    public $updated_at;

    public function all() { /* TODO: mysqli_query($this->db, "SELECT * FROM housekeeping_tasks") */ }
    public function find($id) { /* TODO: WHERE id = ? */ }
    public function create($data) { /* TODO: INSERT INTO housekeeping_tasks */ }
    public function update($id, $data) { /* TODO: UPDATE housekeeping_tasks */ }
    public function delete($id) { /* TODO: DELETE FROM housekeeping_tasks */ }

    // ── Relationships ────────────────────────────────────────
    public function room() { /* TODO: Return parent room */ }
    public function assignedStaff() { /* TODO: Return the housekeeper user */ }

    // ── Filters ──────────────────────────────────────────────
    public function findByRoom($roomId) { /* TODO: WHERE room_id = ? */ }
    public function findByStatus($status) { /* TODO: WHERE status = ? */ }
    public function findByAssignee($userId) { /* TODO: WHERE assigned_to = ? */ }

    // ── Business Logic ───────────────────────────────────────
    public function markComplete($id, $qualityScore) {
        // TODO: Set status='done', quality_score, update room status to 'available'
        // TODO: Deduct linen/consumable inventory
    }

    public function createTurndownTask($roomId) {
        // TODO: Turn-Down Service Coordinator — evening preparation task
    }

    public function alertFrontDesk($roomId) {
        // TODO: HK-to-Front-Desk Instant Alert for priority arrivals
    }

    public function logMinibarConsumption($roomId, $items) {
        // TODO: Minibar Consumption Logger — record items + post charge to folio
    }
}
