<?php
// ============================================================
//  AuditLog Model — System audit trail
//  Table: audit_log
// ============================================================

class AuditLog extends Model
{
    public $id;
    public $user_id;
    public $action;
    public $target_type;
    public $target_id;
    public $old_value;
    public $new_value;
    public $created_at;

    public function all() { /* TODO: SELECT * FROM audit_log ORDER BY created_at DESC */ }
    public function find($id) { /* TODO: WHERE id = ? */ }
    public function create($data) { /* TODO: INSERT INTO audit_log */ }

    // Audit logs should never be updated or deleted
    public function update($id, $data) { /* Not allowed — audit logs are immutable */ }
    public function delete($id) { /* Not allowed — audit logs are immutable */ }

    public function findByUser($userId) { /* TODO: WHERE user_id = ? */ }
    public function findByAction($action) { /* TODO: WHERE action = ? */ }
    public function findByTarget($type, $id) { /* TODO: WHERE target_type=? AND target_id=? */ }

    public static function log($userId, $action, $targetType, $targetId, $oldValue = null, $newValue = null) {
        // TODO: Convenience method to create an audit entry from anywhere
    }
}
