<?php
// ============================================================
//  AuditLog Model — System audit trail
//  Table: audit_log
//
//  Usage: $log = new AuditLog();
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

    public function all()
    { /* TODO: mysqli_query($this->db, "SELECT * FROM audit_log ORDER BY created_at DESC") */
    }
    public function find($id)
    { /* TODO: WHERE id = ? */
    }
    public function create($data)
    {
        $user_id = mysqli_real_escape_string($this->db, $data['user_id']);
        $action = mysqli_real_escape_string($this->db, $data['action']);
        $target_type = mysqli_real_escape_string($this->db, $data['target_type']);
        $target_id = mysqli_real_escape_string($this->db, $data['target_id']);
        $old_value = $data['old_value'] ? mysqli_real_escape_string($this->db, $data['old_value']) : 'NULL';
        $new_value = $data['new_value'] ? mysqli_real_escape_string($this->db, $data['new_value']) : 'NULL';

        $old_val_sql = $old_value === 'NULL' ? 'NULL' : "'$old_value'";
        $new_val_sql = $new_value === 'NULL' ? 'NULL' : "'$new_value'";

        $query = "INSERT INTO audit_log (user_id, action, target_type, target_id, old_value, new_value) 
                  VALUES ('$user_id', '$action', '$target_type', '$target_id', $old_val_sql, $new_val_sql)";
        return mysqli_query($this->db, $query);
    }

    // Audit logs should never be updated or deleted
    public function update($id, $data)
    { /* Not allowed — audit logs are immutable */
    }
    public function delete($id)
    { /* Not allowed — audit logs are immutable */
    }

    public function findByUser($userId)
    { /* TODO: WHERE user_id = ? */
    }
    public function findByAction($action)
    { /* TODO: WHERE action = ? */
    }
    public function findByTarget($type, $id)
    { /* TODO: WHERE target_type=? AND target_id=? */
    }

    public static function log($userId, $action, $targetType, $targetId, $oldValue = null, $newValue = null)
    {
        $log = new self();
        $data = [
            'user_id' => $userId,
            'action' => $action,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'old_value' => $oldValue,
            'new_value' => $newValue,
        ];
        $log->create($data);
    }
}
