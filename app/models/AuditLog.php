<?php

class AuditLog extends AbstractReport
{
    public function __construct($db = null, array $aggregates = [])
    {
        parent::__construct($db, $aggregates);
        $this->setReportScope('audit_log');
        $this->setReportInputs(['user_id', 'action', 'target_type', 'start', 'end']);
        $this->registerAggregate('users', User::class);
    }

    
    public function all(array $filters = [], int $limit = 500, int $offset = 0): array
    {
        $where = ['1=1'];
        if (!empty($filters['user_id']))    $where[] = "al.user_id    = " . (int)$filters['user_id'];
        if (!empty($filters['action']))     $where[] = "al.action     LIKE '%" . mysqli_real_escape_string($this->db, $filters['action']) . "%'";
        if (!empty($filters['target_type']))$where[] = "al.target_type = '" . mysqli_real_escape_string($this->db, $filters['target_type']) . "'";
        if (!empty($filters['start']))      $where[] = "DATE(al.created_at) >= '" . mysqli_real_escape_string($this->db, $filters['start']) . "'";
        if (!empty($filters['end']))        $where[] = "DATE(al.created_at) <= '" . mysqli_real_escape_string($this->db, $filters['end']) . "'";

        $w = implode(' AND ', $where);
        $r = mysqli_query($this->db,
            "SELECT al.id, al.user_id, al.action, al.target_type, al.target_id,
                    al.old_value, al.new_value, al.ip_address, al.user_agent,
                    al.created_at, u.name AS user_name
             FROM   audit_log al
             LEFT   JOIN users u ON al.user_id = u.id
             WHERE  $w
             ORDER  BY al.created_at DESC
             LIMIT  $limit OFFSET $offset");
        return $r ? mysqli_fetch_all($r, MYSQLI_ASSOC) : [];
    }

    
    public function find($id): ?array
    {
        $id = (int) $id;
        $r  = mysqli_query($this->db, "SELECT * FROM audit_log WHERE id = $id LIMIT 1");
        if (!$r) return null;
        return mysqli_fetch_assoc($r) ?: null;
    }

    
    public function create(array $data): int
    {
        $uid    = !empty($data['user_id'])    ? (int)$data['user_id']  : 'NULL';
        $act    = mysqli_real_escape_string($this->db, $data['action']      ?? '');
        $ttype  = mysqli_real_escape_string($this->db, $data['target_type'] ?? '');
        $tid    = !empty($data['target_id'])  ? (int)$data['target_id'] : 'NULL';
        $oldVal = isset($data['old_value']) && $data['old_value'] !== null
                    ? "'" . mysqli_real_escape_string($this->db, is_array($data['old_value']) ? json_encode($data['old_value']) : (string)$data['old_value']) . "'"
                    : 'NULL';
        $newVal = isset($data['new_value']) && $data['new_value'] !== null
                    ? "'" . mysqli_real_escape_string($this->db, is_array($data['new_value']) ? json_encode($data['new_value']) : (string)$data['new_value']) . "'"
                    : 'NULL';
        
        $ip        = mysqli_real_escape_string($this->db,
                        $data['ip_address'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '');
        $ua        = mysqli_real_escape_string($this->db,
                        $data['user_agent'] ?? $_SERVER['HTTP_USER_AGENT'] ?? '');

        mysqli_query($this->db,
            "INSERT INTO audit_log
                 (user_id, action, target_type, target_id, old_value, new_value, ip_address, user_agent, created_at)
             VALUES ($uid, '$act', '$ttype', $tid, $oldVal, $newVal, '$ip', '$ua', NOW())");
        return (int) mysqli_insert_id($this->db);
    }

    
    public function findByUser(int $userId, int $limit = 50): array
    {
        $uid = (int) $userId;
        $r   = mysqli_query($this->db,
            "SELECT * FROM audit_log WHERE user_id = $uid ORDER BY created_at DESC LIMIT $limit");
        return $r ? mysqli_fetch_all($r, MYSQLI_ASSOC) : [];
    }

    
    public function findByTarget(string $type, int $id): array
    {
        $t  = mysqli_real_escape_string($this->db, $type);
        $id = (int) $id;
        $r  = mysqli_query($this->db,
            "SELECT * FROM audit_log WHERE target_type = '$t' AND target_id = $id ORDER BY created_at DESC");
        return $r ? mysqli_fetch_all($r, MYSQLI_ASSOC) : [];
    }

    
    public static function log(
        ?int $userId,
        string $action,
        string $targetType = '',
        ?int $targetId = null,
        $oldValue = null,
        $newValue = null
    ): void {
        $instance = new self();
        $instance->create([
            'user_id'     => $userId,
            'action'      => $action,
            'target_type' => $targetType,
            'target_id'   => $targetId,
            'old_value'   => $oldValue,
            'new_value'   => $newValue,
        ]);
    }

    
    public function update($id, $data = []): never
    {
        throw new RuntimeException('AuditLog records are immutable. Update is forbidden.');
    }

    public function delete($id): never
    {
        throw new RuntimeException('AuditLog records cannot be deleted for compliance.');
    }
}
