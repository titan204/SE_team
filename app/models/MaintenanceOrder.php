<?php
// ============================================================
//  MaintenanceOrder Model — Repair work orders
//  Table: maintenance_orders
//
//  Usage: $order = new MaintenanceOrder();
// ============================================================

class MaintenanceOrder extends Model
{
    public $id;
    public $room_id;
    public $reported_by;
    public $assigned_to;
    public $description;
    public $priority;      // low, medium, high, critical
    public $status;        // open, in_progress, resolved, escalated
    public $resolved_at;
    public $created_at;
    public $updated_at;

    public function all() { /* TODO: mysqli_query($this->db, "SELECT * FROM maintenance_orders") */ }
    public function find($id) { /* TODO: WHERE id = ? */ }
    public function create($data) { /* TODO: INSERT INTO maintenance_orders */ }
    public function update($id, $data) { /* TODO: UPDATE maintenance_orders */ }
    public function delete($id) { /* TODO: DELETE FROM maintenance_orders */ }

    public function room() { /* TODO: Return parent room */ }
    public function reporter() { /* TODO: Return the reporting user */ }
    public function assignedStaff() { /* TODO: Return the assigned user */ }

    public function escalate($id) {
        // TODO: Set status='escalated', set room status='out_of_order'
    }
    public function resolve($id) {
        // TODO: Set status='resolved', resolved_at=NOW()
    }
    public function findByPriority($priority) { /* TODO: WHERE priority = ? */ }
    public function findOverdue() {
        // TODO: Preventative Maintenance Scheduler — find tasks past due date
    }
}
