<?php
// ============================================================
//  GuestBilling Model — UC38: Manage Guest Billing
//  Tables: billing_items, billing_adjustments, final_invoices
//  Caller: BillingController (front desk actor)
// ============================================================

class GuestBilling extends Model
{
    const TAX_RATE = 0.10; // 10% — configurable

    // ── Step 1: Load full billing state ──────────────────────

    /**
     * Load reservation + guest info for a billing page.
     */
    public function getReservationWithGuest(int $reservationId): ?array
    {
        $id = (int) $reservationId;
        $r  = mysqli_query($this->db,
            "SELECT res.*, g.name AS guest_name, g.email AS guest_email,
                    g.phone AS guest_phone, g.loyalty_points,
                    rm.room_number, rt.name AS room_type_name,
                    rt.base_price AS daily_rate,
                    DATEDIFF(res.check_out_date, res.check_in_date) AS nights
             FROM   reservations res
             JOIN   guests g  ON res.guest_id = g.id
             JOIN   rooms  rm ON res.room_id  = rm.id
             JOIN   room_types rt ON rm.room_type_id = rt.id
             WHERE  res.id = $id LIMIT 1");
        if (!$r) return null;
        return mysqli_fetch_assoc($r) ?: null;
    }

    /**
     * Get all non-voided billing items for a reservation, grouped by type.
     */
    public function getBillingItems(int $reservationId): array
    {
        $id = (int) $reservationId;
        $r  = mysqli_query($this->db,
            "SELECT bi.*, u.name AS added_by_name
             FROM   billing_items bi
             LEFT   JOIN users u ON bi.added_by_user_id = u.id
             WHERE  bi.reservation_id = $id
             ORDER  BY bi.added_at ASC");
        if (!$r) return [];
        return mysqli_fetch_all($r, MYSQLI_ASSOC);
    }

    /**
     * Get all adjustments (discounts, surcharges, loyalty redemptions).
     */
    public function getAdjustments(int $reservationId): array
    {
        $id = (int) $reservationId;
        $r  = mysqli_query($this->db,
            "SELECT ba.*, u.name AS applied_by_name
             FROM   billing_adjustments ba
             LEFT   JOIN users u ON ba.applied_by_user_id = u.id
             WHERE  ba.reservation_id = $id
             ORDER  BY ba.created_at ASC");
        if (!$r) return [];
        return mysqli_fetch_all($r, MYSQLI_ASSOC);
    }

    /**
     * Get the final_invoice for a reservation (if finalized).
     */
    public function getFinalInvoice(int $reservationId): ?array
    {
        $id = (int) $reservationId;
        $r  = mysqli_query($this->db,
            "SELECT * FROM final_invoices WHERE reservation_id = $id LIMIT 1");
        if (!$r) return null;
        return mysqli_fetch_assoc($r) ?: null;
    }

    /**
     * Compute the running totals.
     * Returns: [ subtotal, roomTotal, taxAmount, discountTotal, grandTotal ]
     */
    public function computeTotals(array $reservation, array $items, array $adjustments): array
    {
        // Room charges: nights × daily_rate
        $nights    = max(1, (int) $reservation['nights']);
        $roomTotal = round((float)$reservation['daily_rate'] * $nights, 2);

        // Sum active billing items
        $itemsTotal = 0;
        foreach ($items as $item) {
            if ($item['is_voided']) continue;
            $itemsTotal += (float)$item['amount'] * (int)$item['quantity'];
        }

        $subtotal = round($roomTotal + $itemsTotal, 2);

        // Sum adjustments (discounts negative, surcharges positive)
        $discountTotal = 0;
        foreach ($adjustments as $adj) {
            if ($adj['type'] === 'discount' || $adj['type'] === 'loyalty_redemption') {
                $discountTotal += (float) $adj['value'];
            } else {
                $discountTotal -= (float) $adj['value']; // surcharge reduces discount bucket
            }
        }

        $afterDiscount = max(0, $subtotal - $discountTotal);
        $taxAmount     = round($afterDiscount * self::TAX_RATE, 2);
        $grandTotal    = round($afterDiscount + $taxAmount, 2);

        return compact('subtotal','roomTotal','itemsTotal','taxAmount','discountTotal','grandTotal');
    }

    // ── Step 2a: Add charge item ──────────────────────────────

    public function addItem(int $reservationId, array $data): int
    {
        $id          = (int)   $reservationId;
        $type        = mysqli_real_escape_string($this->db, $data['type'] ?? 'manual');
        $description = mysqli_real_escape_string($this->db, $data['description'] ?? '');
        $amount      = (float) ($data['amount'] ?? 0);
        $quantity    = max(1, (int)($data['quantity'] ?? 1));
        $userId      = (int)   ($_SESSION['user_id'] ?? 0);

        mysqli_query($this->db,
            "INSERT INTO billing_items
                 (reservation_id, item_type, description, amount, quantity, added_by_user_id)
             VALUES ($id, '$type', '$description', $amount, $quantity, $userId)");
        return (int) mysqli_insert_id($this->db);
    }

    // ── Step 2b: Void item (NEVER delete) ────────────────────

    public function voidItem(int $itemId, string $reason): bool
    {
        $itemId = (int) $itemId;
        $reason = mysqli_real_escape_string($this->db, $reason ?: 'Voided by staff');
        $result = mysqli_query($this->db,
            "UPDATE billing_items
             SET    is_voided = 1, void_reason = '$reason'
             WHERE  id = $itemId AND is_voided = 0");
        return $result && mysqli_affected_rows($this->db) > 0;
    }

    // ── Step 2c: Apply discount / surcharge ──────────────────

    public function applyAdjustment(int $reservationId, array $data): int
    {
        $id     = (int)   $reservationId;
        $type   = mysqli_real_escape_string($this->db, $data['type'] ?? 'discount');
        $value  = (float) ($data['value'] ?? 0);
        $reason = mysqli_real_escape_string($this->db, $data['reason'] ?? '');
        $userId = (int)   ($_SESSION['user_id'] ?? 0);

        mysqli_query($this->db,
            "INSERT INTO billing_adjustments (reservation_id, type, value, applied_by_user_id, reason)
             VALUES ($id, '$type', $value, $userId, '$reason')");
        return (int) mysqli_insert_id($this->db);
    }

    // ── Step 2d: Loyalty redemption ───────────────────────────

    /**
     * Redeem loyalty points as a discount.
     * 1 point = $0.01 (configurable).
     * Returns [ 'success'=>bool, 'error'=>string|null, 'discount'=>float ]
     */
    public function redeemPoints(int $reservationId, int $guestId, int $points): array
    {
        $guestId = (int) $guestId;
        $points  = (int) $points;

        // Check guest has enough points
        $r   = mysqli_query($this->db,
            "SELECT loyalty_points FROM guests WHERE id = $guestId LIMIT 1");
        $row = mysqli_fetch_assoc($r);
        $available = (int)($row['loyalty_points'] ?? 0);

        if ($points > $available) {
            return [
                'success' => false,
                'error'   => "Guest only has $available points. Reduce redemption amount.",
            ];
        }

        $dollarValue = round($points * 0.01, 2); // 1pt = $0.01

        // Deduct points from guest
        mysqli_query($this->db,
            "UPDATE guests SET loyalty_points = loyalty_points - $points WHERE id = $guestId");

        // Create loyalty_redemption adjustment
        $this->applyAdjustment($reservationId, [
            'type'   => 'loyalty_redemption',
            'value'  => $dollarValue,
            'reason' => "Loyalty redemption: {$points} pts = \${$dollarValue}",
        ]);

        return ['success' => true, 'discount' => $dollarValue, 'error' => null];
    }

    // ── Step 3: Finalize ─────────────────────────────────────

    /**
     * Lock the billing. Creates/updates final_invoices row.
     * Returns the final_invoice ID.
     */
    public function finalize(int $reservationId, array $totals): int
    {
        $id            = (int)   $reservationId;
        $grandTotal    = (float) $totals['grandTotal'];
        $taxAmount     = (float) $totals['taxAmount'];
        $discountTotal = (float) $totals['discountTotal'];

        // Upsert final_invoices
        mysqli_query($this->db,
            "INSERT INTO final_invoices
                 (reservation_id, total_amount, tax_amount, discount_amount, is_finalized, issued_at)
             VALUES ($id, $grandTotal, $taxAmount, $discountTotal, 1, NOW())
             ON DUPLICATE KEY UPDATE
                 total_amount    = $grandTotal,
                 tax_amount      = $taxAmount,
                 discount_amount = $discountTotal,
                 is_finalized    = 1,
                 issued_at       = NOW()");

        $invoiceId = (int) mysqli_insert_id($this->db);

        // Audit log
        $userId = (int) ($_SESSION['user_id'] ?? 0);
        $msg    = mysqli_real_escape_string($this->db,
            "Bill finalized for reservation #$id. Grand total: $grandTotal");
        mysqli_query($this->db,
            "INSERT INTO audit_log (user_id, action, target_type, target_id, old_value, new_value)
             VALUES ($userId, 'bill_finalized', 'reservation', $id, 'open', '$msg')");

        return $invoiceId;
    }

    /**
     * Check if a reservation's bill is already finalized.
     */
    public function isFinalized(int $reservationId): bool
    {
        $id = (int) $reservationId;
        $r  = mysqli_query($this->db,
            "SELECT is_finalized FROM final_invoices
             WHERE  reservation_id = $id AND is_finalized = 1 LIMIT 1");
        return $r && mysqli_num_rows($r) > 0;
    }

    // ── Step 4: Split payment ─────────────────────────────────

    /**
     * Process a multi-method payment via UC12 PaymentService.
     * $payments = [ ['method'=>'card','amount'=>X], ['method'=>'cash','amount'=>Y] ]
     * Returns [ 'success'=>bool, 'errors'=>[] ]
     */
    public function processSplitPayment(int $reservationId, int $guestId, array $payments, float $grandTotal): array
    {
        $sum = array_sum(array_column($payments, 'amount'));
        if (round($sum, 2) !== round($grandTotal, 2)) {
            return ['success' => false, 'errors' => ['Payment total must equal grand total.']];
        }

        $svc    = new PaymentService();
        $errors = [];
        foreach ($payments as $p) {
            $method = $p['method'] ?? 'card';
            $amount = (float) $p['amount'];
            if ($amount <= 0) continue;

            if ($method === 'card') {
                $key    = $reservationId . '_checkout_' . $amount . '_' . uniqid();
                $result = $svc->chargeGuestCard($guestId, $amount, 'checkout_balance', $reservationId, $key);
                if (isset($result['error'])) {
                    $errors[] = "Card payment failed: " . $result['error'];
                }
            } else {
                // Cash / other methods: just log
                $userId = (int) ($_SESSION['user_id'] ?? 0);
                $m      = mysqli_real_escape_string($this->db, $method);
                mysqli_query($this->db,
                    "INSERT INTO audit_log (user_id, action, target_type, target_id, old_value, new_value)
                     VALUES ($userId, 'payment_recorded', 'reservation', $reservationId, '$m', '$amount')");
            }
        }

        return ['success' => empty($errors), 'errors' => $errors];
    }
}
