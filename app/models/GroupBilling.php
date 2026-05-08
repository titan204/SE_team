<?php
// ============================================================
//  GroupBilling Model — UC06: Manage Group Billing
//  Tables: group_reservations, group_members, invoices
// ============================================================

class GroupBilling extends AbstractBilling
{
    public function __construct($db = null, $invoice = null, array $aggregates = [])
    {
        parent::__construct($db, $invoice, $aggregates);
        $this->setBillingSubject('group_reservation');
        $this->registerAggregate('reservations', Reservation::class);
        $this->registerAggregate('guests', Guest::class);
        $this->registerAggregate('folios', Folio::class);
        $this->registerAggregate('paymentProcessor', PaymentService::class);
    }

    // ── Group Reservation Queries ────────────────────────────

    /**
     * Find a group_reservation record by its ID.
     * Returns the group row including the coordinator guest's name and email.
     */
    public function findGroup($groupId)
    {
        $groupId = (int) $groupId;
        $sql = "SELECT gr.*,
                       g.name  AS coordinator_name,
                       g.email AS coordinator_email
                FROM   group_reservations gr
                JOIN   guests g ON gr.coordinator_guest_id = g.id
                WHERE  gr.id = $groupId
                LIMIT  1";
        $result = mysqli_query($this->db, $sql);
        if (!$result) die("Query Failed: " . mysqli_error($this->db));
        return mysqli_fetch_assoc($result);
    }

    /**
     * Returns all members of a group with their reservation details,
     * guest info, room info, folio totals, and per-member charge breakdown.
     * Cancelled reservations are flagged but still returned (excluded at invoice level).
     */
    public function getMembersWithDetails($groupId)
    {
        $groupId = (int) $groupId;
        $sql = "SELECT gm.id          AS member_id,
                       gm.billing_type,
                       gm.reservation_id,
                       r.status       AS res_status,
                       r.check_in_date,
                       r.check_out_date,
                       GREATEST(DATEDIFF(r.check_out_date, r.check_in_date), 0) AS room_nights,
                       r.total_price,
                       g.id           AS guest_id,
                       g.name         AS guest_name,
                       g.email        AS guest_email,
                       rm.room_number,
                       rt.name        AS room_type_name,
                       COALESCE(SUM(CASE WHEN fc.charge_type = 'room_rate'  THEN fc.amount ELSE 0 END), 0) AS room_charges,
                       COALESCE(SUM(CASE WHEN fc.charge_type IN ('service','spa','restaurant') THEN fc.amount ELSE 0 END), 0) AS service_charges,
                       COALESCE(SUM(CASE WHEN fc.charge_type = 'minibar'    THEN fc.amount ELSE 0 END), 0) AS minibar_charges,
                       COALESCE(SUM(CASE WHEN fc.charge_type = 'tax'        THEN fc.amount ELSE 0 END), 0) AS tax_charges,
                       COALESCE(SUM(CASE WHEN fc.charge_type NOT IN ('room_rate','service','spa','restaurant','minibar','tax') THEN fc.amount ELSE 0 END), 0) AS other_charges,
                       COALESCE(SUM(CASE WHEN fc.charge_type <> 'tax' THEN fc.amount ELSE 0 END), 0) AS member_subtotal,
                       COALESCE(SUM(fc.amount), 0)                                                          AS member_total,
                       f.id           AS folio_id,
                       f.amount_paid,
                       f.balance_due
                FROM   group_members gm
                JOIN   reservations r  ON gm.reservation_id  = r.id
                JOIN   guests       g  ON r.guest_id          = g.id
                JOIN   rooms        rm ON r.room_id           = rm.id
                JOIN   room_types   rt ON rm.room_type_id     = rt.id
                LEFT   JOIN folios      f  ON f.reservation_id = r.id
                LEFT   JOIN folio_charges fc ON fc.folio_id   = f.id
                WHERE  gm.group_reservation_id = $groupId
                GROUP  BY gm.id, gm.billing_type, gm.reservation_id,
                          r.status, r.check_in_date, r.check_out_date, r.total_price,
                          g.id, g.name, g.email, rm.room_number, rt.name,
                          f.id, f.amount_paid, f.balance_due
                ORDER  BY g.name ASC";
        $result = mysqli_query($this->db, $sql);
        if (!$result) die("Query Failed: " . mysqli_error($this->db));
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function hasDefaultPaymentMethod($guestId): bool
    {
        $guestId = (int) $guestId;
        $result = mysqli_query($this->db,
            "SELECT id FROM payment_methods
             WHERE guest_id = $guestId AND is_default = 1
             LIMIT 1");
        return $result && mysqli_num_rows($result) > 0;
    }

    public function markMembersAsIndividual(int $groupId, array $reservationIds): void
    {
        $groupId = (int) $groupId;
        $ids = array_values(array_filter(array_map('intval', $reservationIds)));
        if (empty($ids)) return;

        $idList = implode(',', $ids);
        mysqli_query($this->db,
            "UPDATE group_members
             SET billing_type = 'individual'
             WHERE group_reservation_id = $groupId
               AND reservation_id IN ($idList)");
    }

    // ── Invoice Queries ──────────────────────────────────────

    /**
     * Find the latest group-level invoice for a group, if one exists.
     */
    public function findGroupInvoice($groupId)
    {
        $groupId = (int) $groupId;
        $sql = "SELECT * FROM invoices
                WHERE  group_id = $groupId AND invoice_type = 'group'
                ORDER  BY generated_at DESC
                LIMIT  1";
        $result = mysqli_query($this->db, $sql);
        if (!$result) die("Query Failed: " . mysqli_error($this->db));
        return mysqli_fetch_assoc($result);
    }

    /**
     * Find the current draft group invoice for a group.
     */
    public function findDraftGroupInvoice($groupId)
    {
        $groupId = (int) $groupId;
        $sql = "SELECT * FROM invoices
                WHERE group_id = $groupId
                  AND invoice_type = 'group'
                  AND status = 'draft'
                ORDER BY generated_at DESC
                LIMIT 1";
        $result = mysqli_query($this->db, $sql);
        if (!$result) die("Query Failed: " . mysqli_error($this->db));
        return mysqli_fetch_assoc($result);
    }

    /**
     * Generate or update a consolidated group invoice draft.
     * Applies the group discount percentage from group_reservations.
     * Only includes non-cancelled members in the total.
     *
     * @param  int   $groupId
     * @param  float $subtotal           Sum of active members' non-tax charges
     * @param  float $taxAmount
     * @param  float $discountPercentage From group_reservations.discount_percentage
     * @return int   Draft invoice ID
     */
    public function createGroupInvoice($groupId, $subtotal, $taxAmount, $discountPercentage)
    {
        $groupId            = (int)   $groupId;
        $subtotal           = (float) $subtotal;
        $taxAmount          = (float) $taxAmount;
        $discountPercentage = (float) $discountPercentage;

        $discountAmount = round($subtotal * ($discountPercentage / 100), 2);
        $totalAmount    = round($subtotal + $taxAmount - $discountAmount, 2);

        $draft = $this->findDraftGroupInvoice($groupId);
        if ($draft) {
            $invoiceId = (int) $draft['id'];
            $sql = "UPDATE invoices
                    SET total_amount = $totalAmount,
                        tax_amount = $taxAmount,
                        discount_amount = $discountAmount,
                        generated_at = NOW()
                    WHERE id = $invoiceId";
            $result = mysqli_query($this->db, $sql);
            if (!$result) die("Invoice Update Failed: " . mysqli_error($this->db));
            return $invoiceId;
        }

        $sql = "INSERT INTO invoices
                    (group_id, reservation_id, invoice_type, total_amount, tax_amount, discount_amount, status, generated_at)
                VALUES
                    ($groupId, NULL, 'group', $totalAmount, $taxAmount, $discountAmount, 'draft', NOW())";
        $result = mysqli_query($this->db, $sql);
        if (!$result) die("Invoice Insert Failed: " . mysqli_error($this->db));
        return mysqli_insert_id($this->db);
    }

    /**
     * Find the latest individual invoice for a member reservation in a group.
     */
    public function findIndividualInvoice($groupId, $reservationId)
    {
        $groupId       = (int) $groupId;
        $reservationId = (int) $reservationId;
        $sql = "SELECT * FROM invoices
                WHERE group_id = $groupId
                  AND reservation_id = $reservationId
                  AND invoice_type = 'individual'
                  AND status <> 'void'
                ORDER BY generated_at DESC
                LIMIT 1";
        $result = mysqli_query($this->db, $sql);
        if (!$result) die("Query Failed: " . mysqli_error($this->db));
        return mysqli_fetch_assoc($result);
    }

    /**
     * Create an individual invoice for a split-billing member.
     *
     * @param  int   $groupId
     * @param  int   $reservationId
     * @param  float $totalAmount
     * @param  float $taxAmount
     * @param  float $discountAmount
     * @return int   New invoice ID
     */
    public function createIndividualInvoice($groupId, $reservationId, $totalAmount, $taxAmount, $discountAmount)
    {
        $groupId       = (int)   $groupId;
        $reservationId = (int)   $reservationId;
        $totalAmount   = (float) $totalAmount;
        $taxAmount     = (float) $taxAmount;
        $discountAmount= (float) $discountAmount;

        $existing = $this->findIndividualInvoice($groupId, $reservationId);
        if ($existing) {
            return (int) $existing['id'];
        }

        $sql = "INSERT INTO invoices
                    (group_id, reservation_id, invoice_type, total_amount, tax_amount, discount_amount, status, generated_at)
                VALUES
                    ($groupId, $reservationId, 'individual', $totalAmount, $taxAmount, $discountAmount, 'draft', NOW())";
        $result = mysqli_query($this->db, $sql);
        if (!$result) die("Invoice Insert Failed: " . mysqli_error($this->db));
        return mysqli_insert_id($this->db);
    }

    /**
     * Finalize an invoice (set status = 'finalized').
     */
    public function finalizeInvoice($invoiceId)
    {
        $invoiceId = (int) $invoiceId;
        $result    = mysqli_query($this->db,
            "UPDATE invoices SET status = 'finalized'
             WHERE id = $invoiceId AND status = 'draft'");
        return (bool) $result;
    }

    /**
     * Get all invoices (group + individual) for a group.
     */
    public function getInvoicesForGroup($groupId)
    {
        $groupId = (int) $groupId;
        $sql = "SELECT i.*,
                       r.id AS res_id,
                       g.name AS guest_name, g.email AS guest_email
                FROM   invoices i
                LEFT JOIN reservations r ON i.reservation_id = r.id
                LEFT JOIN guests       g ON r.guest_id       = g.id
                WHERE  i.group_id = $groupId
                ORDER  BY i.invoice_type DESC, i.generated_at DESC";
        $result = mysqli_query($this->db, $sql);
        if (!$result) die("Query Failed: " . mysqli_error($this->db));
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    // ── Cancellation helper (UC11 delegation) ────────────────

    /**
     * Logs a cancellation-notification audit entry for each cancelled member.
     * This is the hook point for UC11 sendCancellationNotification().
     * Actual email delivery is handled externally; this logs the intent.
     *
     * @param  int   $reservationId
     * @param  array $guestRow        Must contain 'id' and 'name'
     * @return bool
     */
    public function sendCancellationNotification($reservationId, array $guestRow)
    {
        $reservationId = (int) $reservationId;
        $guestId       = (int) $guestRow['id'];
        $guestName     = mysqli_real_escape_string($this->db, $guestRow['name'] ?? '');
        $guestEmail    = mysqli_real_escape_string($this->db, $guestRow['email'] ?? '');
        $userId        = (int) ($_SESSION['user_id'] ?? 0);

        if ($this->auditLogExists('cancellation_notify', 'reservation', $reservationId, (string)$guestId)) {
            return true;
        }

        $message = mysqli_real_escape_string($this->db,
            "Group billing cancellation notification queued for {$guestName} "
            . "({$guestEmail}, guest #{$guestId}) for reservation #{$reservationId}."
        );

        $result = mysqli_query($this->db,
            "INSERT INTO audit_log (user_id, action, target_type, target_id, old_value, new_value)
             VALUES ($userId, 'cancellation_notify', 'reservation', $reservationId,
                     '$guestId', '$message')");
        return (bool) $result;
    }

    // ── Incomplete payment flag ───────────────────────────────

    /**
     * Flag a member reservation as having incomplete payment info.
     * Logs to audit_log so the coordinator can be notified.
     *
     * @param  int    $reservationId
     * @param  string $reason
     * @return bool
     */
    public function flagIncompletePayment($reservationId, $reason = '', $guestName = '')
    {
        $reservationId = (int) $reservationId;
        $reason        = mysqli_real_escape_string($this->db, $reason ?: 'Incomplete payment information');
        $guestName     = mysqli_real_escape_string($this->db, $guestName);
        $userId        = (int) ($_SESSION['user_id'] ?? 0);

        if ($this->auditLogExists('payment_flag', 'reservation', $reservationId, 'incomplete')) {
            return true;
        }

        $message = $guestName !== ''
            ? "Member {$guestName}: {$reason}"
            : $reason;

        $result = mysqli_query($this->db,
            "INSERT INTO audit_log (user_id, action, target_type, target_id, old_value, new_value)
             VALUES ($userId, 'payment_flag', 'reservation', $reservationId,
                     'incomplete', '$message')");
        return (bool) $result;
    }

    // ── Audit log ─────────────────────────────────────────────

    /**
     * Write an audit log entry for the group finalize event.
     *
     * @param  int  $groupId
     * @return bool
     */
    public function logFinalizeEvent($groupId)
    {
        $groupId = (int) $groupId;
        $userId  = (int) ($_SESSION['user_id'] ?? 0);

        if ($this->auditLogExists('group_invoice_finalized', 'group_reservation', $groupId, 'draft')) {
            return true;
        }

        $result = mysqli_query($this->db,
            "INSERT INTO audit_log (user_id, action, target_type, target_id, old_value, new_value)
             VALUES ($userId, 'group_invoice_finalized', 'group_reservation', $groupId, 'draft', 'finalized')");
        return (bool) $result;
    }

    /**
     * The project uses audit_log as its notification dispatch mechanism.
     */
    public function notifyCoordinatorInvoice($groupId, $invoiceId, array $group): bool
    {
        $groupId   = (int) $groupId;
        $invoiceId = (int) $invoiceId;
        $userId    = (int) ($_SESSION['user_id'] ?? 0);

        if ($this->auditLogExists('group_invoice_coordinator_notified', 'group_reservation', $groupId, (string)$invoiceId)) {
            return true;
        }

        $email = mysqli_real_escape_string($this->db, $group['coordinator_email'] ?? '');
        $name  = mysqli_real_escape_string($this->db, $group['coordinator_name'] ?? '');
        $msg   = mysqli_real_escape_string($this->db,
            "Consolidated group invoice #{$invoiceId} notification queued for coordinator {$name} ({$email})."
        );

        $result = mysqli_query($this->db,
            "INSERT INTO audit_log (user_id, action, target_type, target_id, old_value, new_value)
             VALUES ($userId, 'group_invoice_coordinator_notified', 'group_reservation', $groupId,
                     '$invoiceId', '$msg')");
        return (bool) $result;
    }

    public function notifyMemberInvoice($groupId, $reservationId, $invoiceId, array $member): bool
    {
        $groupId       = (int) $groupId;
        $reservationId = (int) $reservationId;
        $invoiceId     = (int) $invoiceId;
        $guestId       = (int) ($member['guest_id'] ?? 0);
        $userId        = (int) ($_SESSION['user_id'] ?? 0);

        if ($this->auditLogExists('individual_invoice_member_notified', 'reservation', $reservationId, (string)$invoiceId)) {
            return true;
        }

        $name  = mysqli_real_escape_string($this->db, $member['guest_name'] ?? '');
        $email = mysqli_real_escape_string($this->db, $member['guest_email'] ?? '');
        $msg   = mysqli_real_escape_string($this->db,
            "Individual invoice #{$invoiceId} notification queued for {$name} ({$email}, guest #{$guestId}) in group #{$groupId}."
        );

        $result = mysqli_query($this->db,
            "INSERT INTO audit_log (user_id, action, target_type, target_id, old_value, new_value)
             VALUES ($userId, 'individual_invoice_member_notified', 'reservation', $reservationId,
                     '$invoiceId', '$msg')");
        return (bool) $result;
    }

    public function notifyCoordinatorPaymentFlag($groupId, $reservationId, $guestName, $reason): bool
    {
        $groupId       = (int) $groupId;
        $reservationId = (int) $reservationId;
        $userId        = (int) ($_SESSION['user_id'] ?? 0);

        if ($this->auditLogExists('payment_flag_coordinator_notified', 'reservation', $reservationId, 'incomplete')) {
            return true;
        }

        $guestName = mysqli_real_escape_string($this->db, $guestName);
        $reason    = mysqli_real_escape_string($this->db, $reason);
        $msg       = mysqli_real_escape_string($this->db,
            "Coordinator notification queued: {$guestName} on reservation #{$reservationId} has incomplete payment info ({$reason})."
        );

        $result = mysqli_query($this->db,
            "INSERT INTO audit_log (user_id, action, target_type, target_id, old_value, new_value)
             VALUES ($userId, 'payment_flag_coordinator_notified', 'reservation', $reservationId,
                     'incomplete', '$msg')");
        return (bool) $result;
    }

    private function auditLogExists(string $action, string $targetType, int $targetId, string $oldValue): bool
    {
        $action     = mysqli_real_escape_string($this->db, $action);
        $targetType = mysqli_real_escape_string($this->db, $targetType);
        $oldValue   = mysqli_real_escape_string($this->db, $oldValue);
        $targetId   = (int) $targetId;

        $result = mysqli_query($this->db,
            "SELECT id FROM audit_log
             WHERE action = '$action'
               AND target_type = '$targetType'
               AND target_id = $targetId
               AND old_value = '$oldValue'
             LIMIT 1");
        return $result && mysqli_num_rows($result) > 0;
    }

    // ── UC13: Split Group Billing ─────────────────────────────

    /**
     * Get member rows eligible for split (non-cancelled, with folio data).
     * Returns same structure as getMembersWithDetails() — reuses existing query.
     */
    public function getSplitPreviewMembers($groupId)
    {
        $members = $this->getMembersWithDetails($groupId);
        return array_values(array_filter($members, fn($m) => $m['res_status'] !== 'cancelled'));
    }

    /**
     * UC13 Step 3b: Generate an individual invoice for one split member.
     * Calculates proportional tax from the group total.
     *
     * @param int   $groupId
     * @param array $member    Row from getMembersWithDetails()
     * @param float $groupTax  Total tax of the group invoice (for proportional split)
     * @param float $groupSubtotal  Subtotal before tax (for tax ratio)
     * @return int  New invoice_id
     */
    public function createSplitInvoice(int $groupId, array $member, float $groupTax, float $groupSubtotal): int
    {
        $memberSubtotal = (float) ($member['member_subtotal'] ?? 0);

        // Proportional tax: member_subtotal / group_subtotal * group_tax
        $proportionalTax = $groupSubtotal > 0
            ? round(($memberSubtotal / $groupSubtotal) * $groupTax, 2)
            : (float) $member['tax_charges'];

        $memberTotal = round($memberSubtotal + $proportionalTax, 2);

        $invoiceId = $this->createIndividualInvoice(
            $groupId,
            $member['reservation_id'],
            $memberTotal,
            $proportionalTax,
            0
        );

        // Insert invoice_items for the audit trail
        $this->insertInvoiceItems($invoiceId, $member, $proportionalTax);

        return $invoiceId;
    }

    /**
     * Insert per-charge line items into invoice_items for one member's invoice.
     */
    private function insertInvoiceItems(int $invoiceId, array $m, float $proportionalTax): void
    {
        $resId     = (int) $m['reservation_id'];
        $invoiceId = (int) $invoiceId;

        $lines = [
            ['Room charges',   (float)$m['room_charges'],    'room_rate'],
            ['Services',       (float)$m['service_charges'], 'service'],
            ['Minibar',        (float)$m['minibar_charges'], 'minibar'],
            ['Other charges',   (float)($m['other_charges'] ?? 0), 'other'],
            ['Tax (prorated)', $proportionalTax,             'tax'],
        ];

        foreach ($lines as [$desc, $amount, $type]) {
            if ($amount <= 0) continue;
            $desc   = mysqli_real_escape_string($this->db, $desc);
            $type   = mysqli_real_escape_string($this->db, $type);
            $amount = (float) $amount;
            mysqli_query($this->db,
                "INSERT INTO invoice_items (invoice_id, description, amount, item_type, reservation_id)
                 VALUES ($invoiceId, '$desc', $amount, '$type', $resId)");
        }
    }

    /**
     * UC13 Step 3c/d/e: Update the consolidated group invoice after splitting.
     *
     * If ALL active members are splitting → void the consolidated invoice.
     * If partial split → recalculate and update the consolidated total.
     *
     * @param int   $groupId
     * @param array $splitMemberIds  reservation_ids being split out
     * @param array $allActiveMembers All non-cancelled members
     * @return string 'voided' | 'updated'
     */
    public function updateConsolidatedAfterSplit(int $groupId, array $splitMemberIds, array $allActiveMembers): string
    {
        $groupInvoice = $this->findGroupInvoice($groupId);
        if (!$groupInvoice) return 'no_invoice';

        $invoiceId = (int) $groupInvoice['id'];

        // Check if all active members are splitting
        $activeResIds = array_column($allActiveMembers, 'reservation_id');
        $remaining    = array_diff($activeResIds, $splitMemberIds);

        if (empty($remaining)) {
            // All split → void consolidated
            mysqli_query($this->db,
                "UPDATE invoices SET status = 'void' WHERE id = $invoiceId");
            return 'voided';
        }

        // Partial split → recalculate consolidated total for remaining members
        $newSubtotal = 0;
        $newTax      = 0;
        foreach ($allActiveMembers as $m) {
            if (in_array($m['reservation_id'], $splitMemberIds)) continue;
            $newSubtotal += (float) ($m['member_subtotal'] ?? 0);
            $newTax      += (float) $m['tax_charges'];
        }

        // Re-apply group discount
        $group          = $this->findGroup($groupId);
        $discountPct    = (float) ($group['discount_percentage'] ?? 0);
        $discountAmount = round($newSubtotal * ($discountPct / 100), 2);
        $newTotal       = round($newSubtotal + $newTax - $discountAmount, 2);

        mysqli_query($this->db,
            "UPDATE invoices
             SET total_amount = $newTotal, tax_amount = $newTax,
                 discount_amount = $discountAmount, status = 'draft'
             WHERE id = $invoiceId");
        return 'updated';
    }

    /**
     * UC13 Step 3f: Log the split event to billing_split_log.
     */
    public function logSplit(int $groupId, array $splitMemberIds, float $originalTotal): void
    {
        $groupId       = (int)   $groupId;
        $originalTotal = (float) $originalTotal;
        $userId        = (int)   ($_SESSION['user_id'] ?? 0);
        $membersJson   = mysqli_real_escape_string($this->db, json_encode($splitMemberIds));

        mysqli_query($this->db,
            "INSERT INTO billing_split_log
                 (group_id, split_by_user_id, members_split, original_consolidated_total)
             VALUES ($groupId, $userId, '$membersJson', $originalTotal)");
    }

    /**
     * Create a billing dispute record — pauses split until resolved.
     */
    public function createDispute(int $groupId, int $reservationId, string $description): int
    {
        $groupId       = (int) $groupId;
        $reservationId = (int) $reservationId;
        $description   = mysqli_real_escape_string($this->db, $description);
        $userId        = (int) ($_SESSION['user_id'] ?? 0);

        mysqli_query($this->db,
            "INSERT INTO billing_disputes
                 (group_id, reservation_id, raised_by_user_id, description, status)
             VALUES ($groupId, $reservationId, $userId, '$description', 'open')");
        return (int) mysqli_insert_id($this->db);
    }

    /**
     * Returns any open disputes for a group (blocks split if any exist).
     */
    public function getOpenDisputes(int $groupId): array
    {
        $groupId = (int) $groupId;
        $r = mysqli_query($this->db,
            "SELECT * FROM billing_disputes
             WHERE group_id = $groupId AND status = 'open'");
        if (!$r) return [];
        return mysqli_fetch_all($r, MYSQLI_ASSOC);
    }

    /**
     * Flag an invoice for manual delivery (member has no email).
     * Inserts into front_desk_queue.
     */
    public function flagManualDelivery(int $invoiceId, int $reservationId, string $guestName): void
    {
        $invoiceId     = (int) $invoiceId;
        $reservationId = (int) $reservationId;
        $guestName     = mysqli_real_escape_string($this->db, $guestName);

        mysqli_query($this->db,
            "INSERT INTO front_desk_queue (invoice_id, reservation_id, reason, guest_name)
             VALUES ($invoiceId, $reservationId, 'no_email_manual_delivery', '$guestName')");
    }
}
