<?php
// ============================================================
//  BillingController — Folios, charges, payments
//  Routes:
//    /billing              → index (list folios)
//    /billing/show/5       → show folio details
//    /billing/addCharge/5  → post a charge to folio #5
//    /billing/payment/5    → record payment on folio #5
//    /billing/invoice/5    → pro-forma invoice preview
// ============================================================

class BillingController extends Controller
{
    public function index()
    {
        $this->requireLogin();
        $this->requireRoles(['front_desk', 'manager']);
        $db = (new Model())->getDb();

        // ── Revenue Today (from actual completed payments today) ──
        $r = mysqli_query($db,
            "SELECT COALESCE(SUM(amount), 0) AS revenue_today
             FROM   payments
             WHERE  DATE(processed_at) = CURDATE()");
        $revenueToday = $r ? (float)mysqli_fetch_assoc($r)['revenue_today'] : 0;

        // ── Revenue This Month ──
        $r = mysqli_query($db,
            "SELECT COALESCE(SUM(amount), 0) AS revenue_month
             FROM   payments
             WHERE  MONTH(processed_at) = MONTH(CURDATE())
               AND  YEAR(processed_at)  = YEAR(CURDATE())");
        $revenueMonth = $r ? (float)mysqli_fetch_assoc($r)['revenue_month'] : 0;

        // ── Folio Counts ──
        $r = mysqli_query($db,
            "SELECT
                COUNT(*)                                                     AS total,
                SUM(CASE WHEN status='open'     THEN 1 ELSE 0 END)          AS open,
                SUM(CASE WHEN status='settled'  THEN 1 ELSE 0 END)          AS settled,
                SUM(CASE WHEN status='refunded' THEN 1 ELSE 0 END)          AS refunded,
                COALESCE(SUM(balance_due),0)                                 AS total_outstanding
             FROM folios");
        $folioStats = $r ? mysqli_fetch_assoc($r) : ['total'=>0,'open'=>0,'settled'=>0,'refunded'=>0,'total_outstanding'=>0];

        // ── Open Disputes ──
        $r = mysqli_query($db,
            "SELECT COUNT(*) AS cnt FROM billing_disputes WHERE status='open'");
        $openDisputes = $r ? (int)mysqli_fetch_assoc($r)['cnt'] : 0;

        // ── Payment Method Breakdown ──
        $r = mysqli_query($db,
            "SELECT method,
                    COUNT(*)          AS cnt,
                    SUM(amount)       AS total
             FROM   payments
             GROUP  BY method
             ORDER  BY total DESC");
        $paymentMethods = $r ? mysqli_fetch_all($r, MYSQLI_ASSOC) : [];

        // ── Folios List with Guest + Room ──
        $statusFilter = $_GET['status'] ?? '';
        $where = '1=1';
        if (in_array($statusFilter, ['open','settled','refunded'])) {
            $where .= " AND f.status = '$statusFilter'";
        }

        $r = mysqli_query($db,
            "SELECT f.id, f.status, f.total_amount, f.amount_paid, f.balance_due, f.created_at,
                    g.id   AS guest_id,
                    g.name AS guest_name,
                    r.room_number,
                    rt.name AS room_type,
                    res.check_in_date, res.check_out_date
             FROM   folios f
             JOIN   reservations res ON f.reservation_id = res.id
             LEFT   JOIN guests g   ON res.guest_id      = g.id
             LEFT   JOIN rooms  r   ON res.room_id       = r.id
             LEFT   JOIN room_types rt ON r.room_type_id = rt.id
             WHERE  $where
             ORDER  BY f.id DESC");
        $folios = $r ? mysqli_fetch_all($r, MYSQLI_ASSOC) : [];

        $this->view('billing/index', compact(
            'revenueToday','revenueMonth','folioStats',
            'openDisputes','paymentMethods','folios','statusFilter'
        ));
    }

    public function show($id)
    {
        $this->requireLogin();
        $this->requireRoles(['front_desk', 'manager']);
        $this->view('billing/show');
    }

    public function addCharge($id)
    {
        $this->requireLogin();
        $this->requireRoles(['front_desk', 'manager']);
        $this->redirect('billing/show/' . (int)$id);
    }

    public function payment($id)
    {
        $this->requireLogin();
        $this->requireRoles(['front_desk', 'manager']);
        $this->redirect('billing/show/' . (int)$id);
    }

    public function invoice($id)
    {
        $this->requireLogin();
        $this->requireRoles(['front_desk', 'manager']);
        $this->view('billing/invoice');
    }

    public function refund($id)
    {
        $this->requireLogin();
        $this->requireRoles(['front_desk', 'manager']);
        $this->redirect('billing/show/' . (int)$id);
    }

    /**
     * GET /billing/splitBill/{group_id}
     * Split preview: per-member breakdown with payment checkboxes.
     */
    public function splitBill($groupId)
    {
        $this->requireRoles(['front_desk', 'manager', 'revenue_manager']);

        $gb    = new GroupBilling();
        $group = $gb->findGroup($groupId);
        if (!$group) {
            die("Group #$groupId not found.");
        }

        // UC13 error handling: block if open disputes exist
        $disputes = $gb->getOpenDisputes((int)$groupId);

        $groupInvoice = $gb->findGroupInvoice($groupId);

        // Validate: invoice must exist and NOT be finalized
        $invoiceBlocked = !$groupInvoice || $groupInvoice['status'] !== 'draft';

        $members = $gb->getSplitPreviewMembers($groupId);

        $this->view('billing/split_preview', [
            'group'          => $group,
            'members'        => $members,
            'groupInvoice'   => $groupInvoice,
            'invoiceBlocked' => $invoiceBlocked,
            'disputes'       => $disputes,
        ]);
    }

    /**
     * POST /billing/splitProcess/{group_id}
     * UC13 Step 3 — Execute the split for selected member_ids.
     */
    public function splitProcess($groupId)
    {
        $this->requireRoles(['front_desk', 'manager', 'revenue_manager']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect("billing/splitBill/$groupId");
            return;
        }

        $gb    = new GroupBilling();
        $group = $gb->findGroup($groupId);
        if (!$group) die("Group #$groupId not found.");

        // Block if open disputes
        $disputes = $gb->getOpenDisputes((int)$groupId);
        if (!empty($disputes)) {
            $_SESSION['flash_error'] = 'Split is paused: ' . count($disputes) . ' open dispute(s) must be resolved first.';
            $this->redirect("billing/splitBill/$groupId");
            return;
        }

        // Validate: group invoice must be draft
        $groupInvoice = $gb->findGroupInvoice($groupId);
        if (!$groupInvoice || $groupInvoice['status'] !== 'draft') {
            $_SESSION['flash_error'] = 'Cannot split: invoice is already finalized or missing.';
            $this->redirect("billing/splitBill/$groupId");
            return;
        }

        // Selected reservation IDs to split
        $selectedResIds = array_map('intval', $_POST['member_ids'] ?? []);
        if (empty($selectedResIds)) {
            $_SESSION['flash_error'] = 'Select at least one member to split.';
            $this->redirect("billing/splitBill/$groupId");
            return;
        }

        $allActiveMembers = $gb->getSplitPreviewMembers($groupId);

        // Compute group-level subtotal + tax for proportional tax calculation
        $groupSubtotal = 0;
        $groupTax      = 0;
        foreach ($allActiveMembers as $m) {
            $groupSubtotal += (float)($m['member_subtotal'] ?? 0);
            $groupTax      += (float)$m['tax_charges'];
        }

        $originalTotal = (float) $groupInvoice['total_amount'];
        $manualQueue   = [];

        // Step 3b: generate individual invoices for selected members
        foreach ($allActiveMembers as $m) {
            if (!in_array((int)$m['reservation_id'], $selectedResIds)) continue;

            $invId = $gb->createSplitInvoice((int)$groupId, $m, $groupTax, $groupSubtotal);
            $gb->finalizeInvoice($invId);
            $gb->markMembersAsIndividual((int)$groupId, [(int)$m['reservation_id']]);

            // Error handling: no email → flag for manual delivery
            if (empty($m['guest_email'])) {
                $gb->flagManualDelivery($invId, (int)$m['reservation_id'], $m['guest_name']);
                $manualQueue[] = $m['guest_name'];
            } else {
                $gb->notifyMemberInvoice((int)$groupId, (int)$m['reservation_id'], (int)$invId, $m);
            }
        }

        // Steps 3c/d/e: update consolidated invoice
        $result = $gb->updateConsolidatedAfterSplit((int)$groupId, $selectedResIds, $allActiveMembers);

        // Step 3f: log the split
        $gb->logSplit((int)$groupId, $selectedResIds, $originalTotal);

        $msg = "Split complete. " . count($selectedResIds) . " individual invoice(s) generated.";
        if ($result === 'voided')  $msg .= " Consolidated invoice voided (all members split).";
        if (!empty($manualQueue))  $msg .= " Manual delivery queued for: " . implode(', ', $manualQueue) . ".";

        $_SESSION['flash_success'] = $msg;
        $this->redirect("billing/group/$groupId");
    }

    public function raiseDispute($groupId)
    {
        $this->requireRoles(['front_desk', 'manager', 'revenue_manager']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect("billing/splitBill/$groupId");
            return;
        }

        $reservationId = (int)($_POST['reservation_id'] ?? 0);
        $description   = trim($_POST['description'] ?? '');
        if ($reservationId <= 0 || $description === '') {
            $_SESSION['flash_error'] = 'Reservation and dispute description are required.';
            $this->redirect("billing/splitBill/$groupId");
            return;
        }

        $gb = new GroupBilling();
        $gb->createDispute((int)$groupId, $reservationId, $description);

        $_SESSION['flash_error'] = 'Dispute raised. Split billing is paused until it is resolved.';
        $this->redirect("billing/splitBill/$groupId");
    }

    // ── UC06: Group Billing ───────────────────────────────────

    /**
     * GET /billing/group/{group_id}
     * Displays all reservations in the group with per-member breakdown
     * and the consolidated total.
     */
    public function group($groupId)
    {
        $this->requireRoles(['front_desk', 'manager', 'revenue_manager']);

        $groupBilling = new GroupBilling();
        $group = $groupBilling->findGroup($groupId);
        if (!$group) {
            die("Group #$groupId not found.");
        }

        $members = $groupBilling->getMembersWithDetails($groupId);

        // Separate active from cancelled members
        $activeMembers    = [];
        $cancelledMembers = [];
        foreach ($members as $m) {
            if ($m['res_status'] === 'cancelled') {
                $cancelledMembers[] = $m;
            } else {
                $activeMembers[] = $m;
            }
        }

        // Consolidated totals (active members only, tax counted once)
        $consolidatedSubtotal = 0;
        $consolidatedTax      = 0;
        foreach ($activeMembers as $am) {
            $consolidatedSubtotal += (float) ($am['member_subtotal'] ?? 0);
            $consolidatedTax      += (float) $am['tax_charges'];
        }
        $discountAmount     = round($consolidatedSubtotal * ((float)$group['discount_percentage'] / 100), 2);
        $consolidatedTotal  = round($consolidatedSubtotal + $consolidatedTax - $discountAmount, 2);

        $existingInvoice = $groupBilling->findGroupInvoice($groupId);

        $this->view('billing/group_billing', [
            'group'             => $group,
            'activeMembers'     => $activeMembers,
            'cancelledMembers'  => $cancelledMembers,
            'consolidatedSubtotal' => $consolidatedSubtotal,
            'consolidatedTax'    => $consolidatedTax,
            'discountAmount'     => $discountAmount,
            'consolidatedTotal' => $consolidatedTotal,
            'existingInvoice'   => $existingInvoice,
        ]);
    }

    /**
     * GET /billing/group/{group_id}/invoice
     * Sums all charges, applies group discount, generates an invoice record.
     * PDF download option available in the view.
     */
    public function groupInvoice($groupId)
    {
        $this->requireRoles(['front_desk', 'manager', 'revenue_manager']);
        $this->view('billing/group_invoice', $this->buildGroupInvoiceData((int)$groupId));
    }

    /**
     * POST /billing/group/{group_id}/finalize
     * Finalizes the consolidated invoice and queues coordinator notification.
     * Also generates + finalizes individual invoices for split-billing members.
     */
    public function groupFinalize($groupId)
    {
        $this->requireRoles(['front_desk', 'manager', 'revenue_manager']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['flash_error'] = 'Finalize must be submitted with POST.';
            $this->redirect("billing/group/$groupId");
            return;
        }

        $groupBilling = new GroupBilling();
        $group = $groupBilling->findGroup($groupId);
        if (!$group) {
            die("Group #$groupId not found.");
        }

        $groupInvoice = $groupBilling->findDraftGroupInvoice($groupId);
        if (!$groupInvoice) {
            $_SESSION['flash_error'] = 'No draft group invoice is available to finalize.';
            $this->redirect("billing/group/$groupId");
            return;
        }

        $groupBilling->finalizeInvoice($groupInvoice['id']);
        $groupBilling->notifyCoordinatorInvoice((int)$groupId, (int)$groupInvoice['id'], $group);

        $members = $groupBilling->getMembersWithDetails($groupId);
        foreach ($members as $m) {
            if ($m['res_status'] === 'cancelled') continue;
            if ($m['billing_type'] !== 'individual') continue;

            $reason = $this->incompletePaymentReason($groupBilling, $m);
            if ($reason !== '') {
                $groupBilling->flagIncompletePayment((int)$m['reservation_id'], $reason, $m['guest_name']);
                $groupBilling->notifyCoordinatorPaymentFlag((int)$groupId, (int)$m['reservation_id'], $m['guest_name'], $reason);
                continue;
            }

            $memberTotal = round((float)($m['member_subtotal'] ?? 0) + (float)$m['tax_charges'], 2);
            $indvId = $groupBilling->createIndividualInvoice(
                (int)$groupId,
                (int)$m['reservation_id'],
                $memberTotal,
                (float)$m['tax_charges'],
                0
            );
            $groupBilling->finalizeInvoice($indvId);
            $groupBilling->notifyMemberInvoice((int)$groupId, (int)$m['reservation_id'], (int)$indvId, $m);
        }

        $groupBilling->logFinalizeEvent((int)$groupId);

        $_SESSION['flash_success'] = 'Group invoice finalized. Coordinator and split-member invoice notifications were queued.';
        $this->redirect("billing/group/$groupId");
    }

    public function groupInvoicePdf($groupId)
    {
        $this->requireRoles(['front_desk', 'manager', 'revenue_manager']);

        $data = $this->buildGroupInvoiceData((int)$groupId);
        $lines = [
            'Grand Hotel - Consolidated Group Invoice',
            'Invoice #' . $data['invoiceId'],
            'Group: ' . $data['group']['group_name'],
            'Coordinator: ' . $data['group']['coordinator_name'] . ' <' . $data['group']['coordinator_email'] . '>',
            'Subtotal: $' . number_format($data['subtotal'], 2),
            'Tax: $' . number_format($data['taxAmount'], 2),
            'Discount: $' . number_format($data['discountAmount'], 2),
            'Grand Total: $' . number_format($data['totalAfterDiscount'], 2),
            '',
            'Members:',
        ];

        foreach ($data['activeMembers'] as $m) {
            $lines[] = $m['guest_name']
                . ' | Room ' . $m['room_number']
                . ' | Nights ' . $m['room_nights']
                . ' | Total $' . number_format((float)$m['member_total'], 2);
        }

        if (!empty($data['flaggedMembers'])) {
            $lines[] = '';
            $lines[] = 'Flagged members excluded from this invoice:';
            foreach ($data['flaggedMembers'] as $m) {
                $lines[] = $m['guest_name'] . ' - ' . ($m['flag_reason'] ?? 'Incomplete payment info');
            }
        }

        $pdf = $this->buildSimplePdf($lines);
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="group-invoice-' . (int)$data['invoiceId'] . '.pdf"');
        header('Content-Length: ' . strlen($pdf));
        echo $pdf;
        exit;
    }

    public function groupCancel($groupId)
    {
        $this->requireRoles(['front_desk', 'manager', 'revenue_manager']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['flash_error'] = 'Group cancellation must be submitted with POST.';
            $this->redirect("billing/group/$groupId");
            return;
        }

        $groupBilling = new GroupBilling();
        $group = $groupBilling->findGroup($groupId);
        if (!$group) {
            die("Group #$groupId not found.");
        }

        $reservationModel = new Reservation();
        $members = $groupBilling->getMembersWithDetails($groupId);
        $cancelled = 0;

        foreach ($members as $m) {
            if (!in_array($m['res_status'], ['checked_in', 'checked_out', 'cancelled'], true)) {
                if ($reservationModel->cancel((int)$m['reservation_id'])) {
                    $cancelled++;
                }
            }

            $groupBilling->sendCancellationNotification(
                (int)$m['reservation_id'],
                ['id' => $m['guest_id'], 'name' => $m['guest_name'], 'email' => $m['guest_email']]
            );
        }

        $_SESSION['flash_success'] = "Group cancellation processed. $cancelled eligible reservation(s) cancelled and member notifications queued.";
        $this->redirect("billing/group/$groupId");
    }

    private function buildGroupInvoiceData(int $groupId): array
    {
        $groupBilling = new GroupBilling();
        $group = $groupBilling->findGroup($groupId);
        if (!$group) {
            die("Group #$groupId not found.");
        }

        $members = $groupBilling->getMembersWithDetails($groupId);
        $activeMembers = array_values(array_filter($members, fn($m) => $m['res_status'] !== 'cancelled'));

        $invoiceMembers = [];
        $flagged = [];
        $subtotal = 0;
        $taxAmount = 0;

        foreach ($activeMembers as $m) {
            $reason = $this->incompletePaymentReason($groupBilling, $m);
            if ($reason !== '') {
                $m['flag_reason'] = $reason;
                $groupBilling->flagIncompletePayment((int)$m['reservation_id'], $reason, $m['guest_name']);
                $groupBilling->notifyCoordinatorPaymentFlag($groupId, (int)$m['reservation_id'], $m['guest_name'], $reason);
                $flagged[] = $m;
                continue;
            }

            $invoiceMembers[] = $m;
            $subtotal += (float)($m['member_subtotal'] ?? 0);
            $taxAmount += (float)$m['tax_charges'];
        }

        $discountPercentage = (float)$group['discount_percentage'];
        $discountAmount = round($subtotal * ($discountPercentage / 100), 2);
        $totalAfterDiscount = round($subtotal + $taxAmount - $discountAmount, 2);

        $invoiceId = $groupBilling->createGroupInvoice(
            $groupId,
            $subtotal,
            $taxAmount,
            $discountPercentage
        );

        return [
            'group'              => $group,
            'activeMembers'      => $invoiceMembers,
            'flaggedMembers'     => $flagged,
            'subtotal'           => $subtotal,
            'taxAmount'          => $taxAmount,
            'discountPercentage' => $discountPercentage,
            'discountAmount'     => $discountAmount,
            'totalAfterDiscount' => $totalAfterDiscount,
            'invoiceId'          => $invoiceId,
        ];
    }

    private function incompletePaymentReason(GroupBilling $groupBilling, array $member): string
    {
        if (empty($member['folio_id'])) {
            return "No folio found for reservation #{$member['reservation_id']}";
        }

        if (!$groupBilling->hasDefaultPaymentMethod((int)$member['guest_id'])) {
            return "No default payment method on file for guest #{$member['guest_id']}";
        }

        return '';
    }

    private function buildSimplePdf(array $lines): string
    {
        $content = "BT\n/F1 11 Tf\n14 TL\n50 790 Td\n";
        foreach ($lines as $line) {
            $line = substr((string)$line, 0, 110);
            $content .= '(' . $this->pdfEscape($line) . ") Tj\nT*\n";
        }
        $content .= "ET";

        $objects = [
            "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n",
            "2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n",
            "3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>\nendobj\n",
            "4 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\n",
            "5 0 obj\n<< /Length " . strlen($content) . " >>\nstream\n$content\nendstream\nendobj\n",
        ];

        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        foreach ($objects as $object) {
            $offsets[] = strlen($pdf);
            $pdf .= $object;
        }

        $xref = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";
        for ($i = 1; $i <= count($objects); $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }
        $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n$xref\n%%EOF";

        return $pdf;
    }

    private function pdfEscape(string $value): string
    {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $value);
    }

    // ── UC38: Guest Billing ───────────────────────────────────

    /**
     * GET /billing/guestBill/{reservation_id}
     * Full billing view: charges grouped, running totals, adjustments.
     */
    public function guestBill($reservationId)
    {
        $this->requireLogin();
        $gb  = new GuestBilling();
        $res = $gb->getReservationWithGuest((int)$reservationId);
        if (!$res) die("Reservation #$reservationId not found.");

        $items       = $gb->getBillingItems((int)$reservationId);
        $adjustments = $gb->getAdjustments((int)$reservationId);
        $totals      = $gb->computeTotals($res, $items, $adjustments);
        $finalInvoice= $gb->getFinalInvoice((int)$reservationId);
        $isFinalized = $finalInvoice && $finalInvoice['is_finalized'];

        $this->view('billing/guest_bill', [
            'reservation'  => $res,
            'items'        => $items,
            'adjustments'  => $adjustments,
            'totals'       => $totals,
            'finalInvoice' => $finalInvoice,
            'isFinalized'  => $isFinalized,
        ]);
    }

    /**
     * POST /billing/addBillingItem/{reservation_id}
     * UC38 Step 2a — Add a charge item.
     */
    public function addBillingItem($reservationId)
    {
        $this->requireLogin();
        $gb = new GuestBilling();

        if ($gb->isFinalized((int)$reservationId)) {
            $_SESSION['bill_error'] = 'Bill is finalized. Contact manager to unlock.';
            $this->redirect("billing/guestBill/$reservationId");
            return;
        }

        $gb->addItem((int)$reservationId, [
            'type'        => $_POST['type']        ?? 'manual',
            'description' => $_POST['description'] ?? '',
            'amount'      => (float)($_POST['amount'] ?? 0),
            'quantity'    => (int)($_POST['quantity'] ?? 1),
        ]);

        $_SESSION['bill_success'] = 'Charge added.';
        $this->redirect("billing/guestBill/$reservationId");
    }

    /**
     * POST /billing/voidBillingItem/{item_id}
     * UC38 Step 2b — Void item (never deleted).
     */
    public function voidBillingItem($itemId)
    {
        $this->requireLogin();
        $gb     = new GuestBilling();
        $reason = trim($_POST['reason'] ?? '');
        $resId  = (int)($_POST['reservation_id'] ?? 0);

        if (!$reason) {
            $_SESSION['bill_error'] = 'A reason is required to void a charge.';
            $this->redirect("billing/guestBill/$resId");
            return;
        }

        $gb->voidItem((int)$itemId, $reason);
        $_SESSION['bill_success'] = 'Charge voided.';
        $this->redirect("billing/guestBill/$resId");
    }

    /**
     * POST /billing/applyAdjustment/{reservation_id}
     * UC38 Step 2c — Apply discount or surcharge.
     */
    public function applyAdjustment($reservationId)
    {
        $this->requireLogin();
        $gb = new GuestBilling();

        if ($gb->isFinalized((int)$reservationId)) {
            $_SESSION['bill_error'] = 'Bill is finalized. Contact manager to unlock.';
            $this->redirect("billing/guestBill/$reservationId");
            return;
        }

        $type  = $_POST['type']  ?? 'discount';
        $value = (float)($_POST['value'] ?? 0);

        if (!in_array($type, ['discount','surcharge'], true) || $value <= 0) {
            $_SESSION['bill_error'] = 'Invalid adjustment type or amount.';
            $this->redirect("billing/guestBill/$reservationId");
            return;
        }

        $gb->applyAdjustment((int)$reservationId, [
            'type'   => $type,
            'value'  => $value,
            'reason' => trim($_POST['reason'] ?? ''),
        ]);

        $_SESSION['bill_success'] = ucfirst($type) . " of \$$value applied.";
        $this->redirect("billing/guestBill/$reservationId");
    }

    /**
     * POST /billing/redeemPoints/{reservation_id}
     * UC38 Step 2d — Loyalty point redemption.
     */
    public function redeemPoints($reservationId)
    {
        $this->requireLogin();
        $gb     = new GuestBilling();
        $res    = $gb->getReservationWithGuest((int)$reservationId);
        if (!$res) die("Reservation not found.");

        $points = (int)($_POST['points'] ?? 0);
        $result = $gb->redeemPoints((int)$reservationId, (int)$res['guest_id'], $points);

        if (!$result['success']) {
            $_SESSION['bill_error'] = $result['error'];
        } else {
            $_SESSION['bill_success'] = "$points points redeemed (−\${$result['discount']}).";
        }
        $this->redirect("billing/guestBill/$reservationId");
    }

    /**
     * POST /billing/finalizeBill/{reservation_id}
     * UC38 Step 3 — Lock bill and generate final invoice.
     */
    public function finalizeBill($reservationId)
    {
        $this->requireLogin();
        $gb = new GuestBilling();

        if ($gb->isFinalized((int)$reservationId)) {
            $_SESSION['bill_error'] = 'Bill is finalized. Contact manager to unlock.';
            $this->redirect("billing/guestBill/$reservationId");
            return;
        }

        $res         = $gb->getReservationWithGuest((int)$reservationId);
        $items       = $gb->getBillingItems((int)$reservationId);
        $adjustments = $gb->getAdjustments((int)$reservationId);
        $totals      = $gb->computeTotals($res, $items, $adjustments);

        $gb->finalize((int)$reservationId, $totals);

        $_SESSION['bill_success'] = 'Bill finalized. Invoice issued to guest.';
        $this->redirect("billing/guestBill/$reservationId");
    }

    /**
     * POST /billing/payBill/{reservation_id}
     * UC38 Step 4 — Split payment (multi-method).
     */
    public function payBill($reservationId)
    {
        $this->requireLogin();
        $gb  = new GuestBilling();
        $res = $gb->getReservationWithGuest((int)$reservationId);
        if (!$res) die("Reservation not found.");

        $items       = $gb->getBillingItems((int)$reservationId);
        $adjustments = $gb->getAdjustments((int)$reservationId);
        $totals      = $gb->computeTotals($res, $items, $adjustments);

        // Expecting POST payments as JSON string or array
        $payments = json_decode($_POST['payments'] ?? '[]', true) ?: [];

        $result = $gb->processSplitPayment(
            (int)$reservationId,
            (int)$res['guest_id'],
            $payments,
            $totals['grandTotal']
        );

        if (!$result['success']) {
            $_SESSION['bill_error'] = implode(' ', $result['errors']);
        } else {
            $_SESSION['bill_success'] = 'Payment processed successfully.';
        }
        $this->redirect("billing/guestBill/$reservationId");
    }
}
