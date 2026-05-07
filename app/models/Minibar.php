<?php


class Minibar extends Model
{
    // ── UC29 Step 1: Inventory list for a room ───────────────

    /**
     * Return all active minibar items with their current stock for the given room.
     */
    public function getInventoryForRoom(int $roomId): array
    {
        $id = (int) $roomId;
        $r  = mysqli_query($this->db,
            "SELECT mi.id AS inventory_id, it.id AS item_id,
                    it.name, it.sku, it.price, it.reorder_threshold,
                    mi.current_stock
             FROM   minibar_inventory mi
             JOIN   minibar_items it ON mi.item_id = it.id
             WHERE  mi.room_id = $id AND it.is_active = 1
             ORDER  BY it.name ASC");
        if (!$r) return [];
        return mysqli_fetch_all($r, MYSQLI_ASSOC);
    }

    // ── UC29 Step 2: POST /minibar/log ───────────────────────

    /**
     * Find the active checked-in reservation for a room.
     */
    public function findActiveReservation(int $roomId): ?array
    {
        $id = (int) $roomId;
        $r  = mysqli_query($this->db,
            "SELECT id, guest_id FROM reservations
             WHERE  room_id = $id AND status = 'checked_in'
             LIMIT  1");
        if (!$r) return null;
        return mysqli_fetch_assoc($r) ?: null;
    }

    /**
     * Process minibar consumption log.
     * $items = [ ['item_id'=>X, 'quantity'=>Y], ... ]
     * Returns [ 'success'=>bool, 'total'=>float, 'log_id'=>int,
     *           'billing_queued'=>bool, 'low_stock_items'=>[] ]
     */
    public function logConsumption(int $roomId, array $items, int $housekeeperId): array
    {
        $reservation = $this->findActiveReservation($roomId);

        // Filter to items with qty > 0
        $consumed = array_filter($items, fn($i) => (int)($i['quantity'] ?? 0) > 0);
        if (empty($consumed)) {
            return ['success' => false, 'error' => 'No quantities entered.'];
        }

        $totalAmount  = 0;
        $logItems     = [];
        $billingLines = [];
        $lowStockItemIds = [];

        foreach ($consumed as $row) {
            $itemId   = (int) $row['item_id'];
            $qty      = (int) $row['quantity'];

            // Load item price
            $ir    = mysqli_query($this->db,
                "SELECT id, name, price, reorder_threshold FROM minibar_items WHERE id = $itemId AND is_active = 1 LIMIT 1");
            $item  = $ir ? mysqli_fetch_assoc($ir) : null;
            if (!$item) continue; // skip unknown

            $lineTotal    = round((float)$item['price'] * $qty, 2);
            $totalAmount += $lineTotal;

            $logItems[]     = ['item_id' => $itemId, 'name' => $item['name'], 'quantity' => $qty, 'unit_price' => $item['price'], 'line_total' => $lineTotal];
            $billingLines[] = ['description' => "Minibar — {$item['name']} x{$qty}", 'amount' => (float)$item['price'], 'quantity' => $qty];

            // Step f: update stock
            $newStock = $this->deductStock($roomId, $itemId, $qty);

            // Step g: check threshold
            if ($newStock !== null && $newStock < (int)$item['reorder_threshold']) {
                $lowStockItemIds[] = $itemId;
            }
        }

        $itemsJson    = mysqli_real_escape_string($this->db, json_encode($logItems));
        $resId        = $reservation ? (int)$reservation['id'] : 'NULL';
        $hkId         = (int) $housekeeperId;
        $totalEscaped = (float) $totalAmount;

        // Step e: insert into minibar_logs
        mysqli_query($this->db,
            "INSERT INTO minibar_logs (room_id, reservation_id, housekeeper_id, items, total_amount)
             VALUES ($roomId, $resId, $hkId, '$itemsJson', $totalEscaped)");
        $logId = (int) mysqli_insert_id($this->db);

        // Step c/d: insert billing_items (or queue on failure)
        $billingQueued = false;
        if ($reservation) {
            $resIdInt  = (int) $reservation['id'];
            $userId    = (int) ($_SESSION['user_id'] ?? 0);
            foreach ($billingLines as $bl) {
                $desc  = mysqli_real_escape_string($this->db, $bl['description']);
                $amt   = (float) $bl['amount'];
                $qty   = (int)   $bl['quantity'];
                $ok    = mysqli_query($this->db,
                    "INSERT INTO billing_items (reservation_id, item_type, description, amount, quantity, added_by_user_id)
                     VALUES ($resIdInt, 'minibar', '$desc', $amt, $qty, $userId)");
                if (!$ok) {
                    // Error handling: queue for retry
                    mysqli_query($this->db,
                        "INSERT INTO billing_retry_queue (reservation_id, description, amount, quantity)
                         VALUES ($resIdInt, '$desc', $amt, $qty)");
                    $billingQueued = true;
                }
            }
        }

        return [
            'success'         => true,
            'total'           => $totalAmount,
            'log_id'          => $logId,
            'billing_queued'  => $billingQueued,
            'low_stock_items' => $lowStockItemIds,
            'has_reservation' => (bool) $reservation,
        ];
    }

    /**
     * Reduce stock for a room/item pair. Returns new stock level or null.
     */
    private function deductStock(int $roomId, int $itemId, int $qty): ?int
    {
        mysqli_query($this->db,
            "UPDATE minibar_inventory
             SET    current_stock = GREATEST(0, current_stock - $qty)
             WHERE  room_id = $roomId AND item_id = $itemId");
        $r = mysqli_query($this->db,
            "SELECT current_stock FROM minibar_inventory
             WHERE  room_id = $roomId AND item_id = $itemId LIMIT 1");
        if (!$r) return null;
        $row = mysqli_fetch_assoc($r);
        return $row ? (int) $row['current_stock'] : null;
    }

    /**
     * Manual entry for an item not in the system.
     * Returns billing_item id (flagged for manager review via is_voided=0 but type='manual').
     */
    public function addManualItem(int $reservationId, string $description, float $unitPrice, int $qty): int
    {
        $resId = (int)   $reservationId;
        $desc  = mysqli_real_escape_string($this->db, $description);
        $amt   = (float) $unitPrice;
        $qty   = max(1, (int) $qty);
        $uid   = (int)   ($_SESSION['user_id'] ?? 0);

        mysqli_query($this->db,
            "INSERT INTO billing_items (reservation_id, item_type, description, amount, quantity, added_by_user_id)
             VALUES ($resId, 'manual', '$desc [MANUAL—manager review]', $amt, $qty, $uid)");
        return (int) mysqli_insert_id($this->db);
    }
}
