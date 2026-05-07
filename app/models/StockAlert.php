<?php


class StockAlert extends Model
{
    /**
     * UC31: Check stock levels. If item_id given → check that item only.
     * Otherwise check ALL active items.
     * Inserts low_stock_alerts for new low-stock items not already alerted.
     * Returns array of newly created alert records.
     */
    public function checkStockLevels(?int $itemId = null): array
    {
        if ($itemId !== null) {
            $where = "WHERE si.item_id = " . (int)$itemId . " AND sit.is_active = 1";
        } else {
            $where = "WHERE sit.is_active = 1";
        }

        $r = mysqli_query($this->db,
            "SELECT si.item_id, si.location, si.current_stock,
                    sit.min_threshold, sit.name AS item_name
             FROM   supply_inventory si
             JOIN   supply_items sit ON si.item_id = sit.id
             $where
             HAVING si.current_stock < sit.min_threshold");
        if (!$r) return [];

        $lowItems = mysqli_fetch_all($r, MYSQLI_ASSOC);
        $newAlerts = [];

        foreach ($lowItems as $row) {
            $iid  = (int)    $row['item_id'];
            $loc  = mysqli_real_escape_string($this->db, $row['location']);
            $curr = (int)    $row['current_stock'];
            $min  = (int)    $row['min_threshold'];

            // Check if an active alert already exists for this item+location
            $existing = mysqli_query($this->db,
                "SELECT id FROM low_stock_alerts
                 WHERE  item_id = $iid AND location = '$loc' AND status = 'active'
                 LIMIT  1");
            if ($existing && mysqli_num_rows($existing) > 0) continue;

            // Create the alert
            mysqli_query($this->db,
                "INSERT INTO low_stock_alerts (item_id, location, current_stock, min_threshold)
                 VALUES ($iid, '$loc', $curr, $min)");
            $alertId    = (int) mysqli_insert_id($this->db);
            $newAlerts[] = array_merge($row, ['alert_id' => $alertId]);
        }

        return $newAlerts;
    }

    /**
     * Also check minibar_inventory against minibar_items.reorder_threshold.
     * Called from UC29 for specific item after stock deduction.
     */
    public function checkMinibarStock(int $minibarItemId): bool
    {
        $id = (int) $minibarItemId;
        $r  = mysqli_query($this->db,
            "SELECT mi.current_stock, it.reorder_threshold
             FROM   minibar_inventory mi
             JOIN   minibar_items it ON mi.item_id = it.id
             WHERE  mi.item_id = $id
             LIMIT  1");
        if (!$r) return false;
        $row = mysqli_fetch_assoc($r);
        return $row && (int)$row['current_stock'] < (int)$row['reorder_threshold'];
    }

    /**
     * GET /alerts — return all active low-stock alerts with item names.
     */
    public function getActiveAlerts(): array
    {
        $r = mysqli_query($this->db,
            "SELECT lsa.*, si.name AS item_name, si.unit
             FROM   low_stock_alerts lsa
             JOIN   supply_items si ON lsa.item_id = si.id
             WHERE  lsa.status = 'active'
             ORDER  BY lsa.created_at DESC");
        if (!$r) return [];
        return mysqli_fetch_all($r, MYSQLI_ASSOC);
    }

    /**
     * PUT /alerts/{id}/acknowledge
     */
    public function acknowledge(int $alertId): bool
    {
        $id  = (int) $alertId;
        $uid = (int) ($_SESSION['user_id'] ?? 0);
        $r   = mysqli_query($this->db,
            "UPDATE low_stock_alerts
             SET    status = 'acknowledged', acknowledged_by = $uid, acknowledged_at = NOW()
             WHERE  id = $id AND status = 'active'");
        return $r && mysqli_affected_rows($this->db) > 0;
    }

    /**
     * Escalate unacknowledged alerts older than 2 hours.
     */
    public function escalateOverdue(): int
    {
        $r = mysqli_query($this->db,
            "UPDATE low_stock_alerts
             SET    escalated = 1
             WHERE  status = 'active'
               AND  escalated = 0
               AND  created_at < NOW() - INTERVAL 2 HOUR");
        return $r ? (int) mysqli_affected_rows($this->db) : 0;
    }

    /**
     * POST /stock/requisitions — create restocking requisition.
     * $alertIds  = [int, ...]
     * $items     = [['item_id'=>X, 'quantity_needed'=>Y], ...]
     */
    public function createRequisition(array $alertIds, array $items): int
    {
        $uid       = (int) ($_SESSION['user_id'] ?? 0);
        $itemsJson = mysqli_real_escape_string($this->db, json_encode($items));

        mysqli_query($this->db,
            "INSERT INTO restocking_requisitions (items, requested_by_user_id)
             VALUES ('$itemsJson', $uid)");
        $reqId = (int) mysqli_insert_id($this->db);

        // Resolve linked alerts
        foreach ($alertIds as $aid) {
            $aid = (int) $aid;
            mysqli_query($this->db,
                "UPDATE low_stock_alerts SET status = 'resolved' WHERE id = $aid");
        }

        return $reqId;
    }
}
