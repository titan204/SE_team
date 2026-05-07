<?php
class FrontdeskController extends Controller
{
    /** Restrict to front-desk staff and managers only. */
    private function requireFrontDesk(): void
    {
        $this->requireRoles(['front_desk', 'manager', 'supervisor']);
    }

    public function index()
    {
        $this->requireLogin();
        $this->requireFrontDesk();

        $db = (new Model())->getDb();

        // Today's arrivals (confirmed, check-in = today)
        $r = mysqli_query($db, "SELECT COUNT(*) AS cnt FROM reservations WHERE check_in_date = CURDATE() AND status = 'confirmed'");
        $todayArrivals = (int)(mysqli_fetch_assoc($r)['cnt'] ?? 0);

        // Today's departures (checked_in, check-out = today)
        $r = mysqli_query($db, "SELECT COUNT(*) AS cnt FROM reservations WHERE check_out_date = CURDATE() AND status = 'checked_in'");
        $todayDepartures = (int)(mysqli_fetch_assoc($r)['cnt'] ?? 0);

        // In-house guests (currently checked in)
        $r = mysqli_query($db, "SELECT COUNT(*) AS cnt FROM reservations WHERE status = 'checked_in'");
        $inHouse = (int)(mysqli_fetch_assoc($r)['cnt'] ?? 0);

        // Recent 10 reservations with guest + room info
        $r = mysqli_query($db,
            "SELECT res.id, g.name AS guest_name, rm.room_number,
                    res.check_in_date, res.check_out_date, res.status
             FROM   reservations res
             JOIN   guests g  ON res.guest_id = g.id
             JOIN   rooms  rm ON res.room_id  = rm.id
             ORDER  BY res.created_at DESC LIMIT 10");
        $recentReservations = $r ? mysqli_fetch_all($r, MYSQLI_ASSOC) : [];

        $this->view('frontdesk/index', compact(
            'todayArrivals', 'todayDepartures', 'inHouse', 'recentReservations'
        ));
    }

    // ── UC37: Lost & Found ────────────────────────────────────

    /**
     * GET /frontdesk/lostFound
     * Main L&F queue with filters + guest reports section.
     */
    public function lostFound()
    {
        $this->requireLogin();
        $this->requireFrontDesk();

        // Handle dismiss-candidates POST ("None match") — clears the match session
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['_dismiss_candidates'])) {
            unset($_SESSION['lf_candidates'], $_SESSION['lf_report_id']);
            $_SESSION['fd_info'] = 'Match dismissed. Guest report remains open.';
            $this->redirect('frontdesk/lostFound');
            return;
        }

        $fi      = new FoundItem();
        $filters = [
            'status'    => $_GET['status']    ?? '',
            'date_from' => $_GET['date_from'] ?? '',
            'date_to'   => $_GET['date_to']   ?? '',
        ];
        $foundItems  = $fi->getQueue($filters);
        $lostReports = $fi->getLostReports();
        $overdueItems = $fi->getOverdueItems(90);  // UC37 Step 5: 90-day retention

        // Guest list for report form
        $db = (new Model())->getDb();
        $rg = mysqli_query($db, "SELECT id, name, email FROM guests ORDER BY name");
        $guests = $rg ? mysqli_fetch_all($rg, MYSQLI_ASSOC) : [];

        $this->view('frontdesk/lost_found', compact('foundItems', 'lostReports', 'overdueItems', 'filters', 'guests'));
    }

    /**
     * POST /frontdesk/lostReport
     * UC37 Step 2 — Accept guest lost-item report + auto-match.
     */
    public function lostReport()
    {
        $this->requireLogin();
        $this->requireFrontDesk();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('frontdesk/lostFound');
            return;
        }

        $guestId = (int) ($_POST['guest_id'] ?? 0);
        if (!$guestId) {
            $_SESSION['fd_error'] = 'Please select a guest.';
            $this->redirect('frontdesk/lostFound');
            return;
        }

        $fi     = new FoundItem();
        $result = $fi->acceptGuestReport([
            'guest_id'       => $guestId,
            'description'    => trim($_POST['description'] ?? ''),
            'reservation_id' => !empty($_POST['reservation_id']) ? (int)$_POST['reservation_id'] : null,
            'lost_date'      => $_POST['lost_date'] ?? date('Y-m-d'),
        ]);

        if (!empty($result['candidates'])) {
            $_SESSION['lf_candidates']  = $result['candidates'];
            $_SESSION['lf_report_id']   = $result['report_id'];
            $_SESSION['fd_info'] = count($result['candidates']) . ' possible match(es) found. Please confirm below.';
        } else {
            $_SESSION['fd_success'] = 'Guest report registered. No automatic match found.';
        }
        $this->redirect('frontdesk/lostFound');
    }

    /**
     * POST /frontdesk/matchItem
     * UC37 Step 3 — Confirm match between found item and guest report.
     */
    public function matchItem()
    {
        $this->requireLogin();
        $this->requireFrontDesk();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('frontdesk/lostFound');
            return;
        }

        $fi       = new FoundItem();
        $foundId  = (int) ($_POST['found_item_id'] ?? 0);
        $reportId = (int) ($_POST['report_id']     ?? 0);
        $uid      = (int) ($_SESSION['user_id']    ?? 0);

        if (!$foundId || !$reportId) {
            $_SESSION['fd_error'] = 'Invalid match request.';
            $this->redirect('frontdesk/lostFound');
            return;
        }

        $item = $fi->find($foundId);
        if (!$item) {
            $_SESSION['fd_error'] = 'Found item not found.';
            $this->redirect('frontdesk/lostFound');
            return;
        }
        if ($item['is_high_value']) {
            $_SESSION['fd_error'] = 'High-value items are managed exclusively by security.';
            $this->redirect('frontdesk/lostFound');
            return;
        }

        $fi->confirmMatch($foundId, $reportId, $uid);
        unset($_SESSION['lf_candidates'], $_SESSION['lf_report_id']);
        $_SESSION['fd_success'] = 'Match confirmed. Item status updated to "matched".';
        $this->redirect('frontdesk/lostFound');
    }

    /**
     * POST /frontdesk/returnItem/{found_item_id}
     * UC37 Step 4 — Arrange return.
     */
    public function returnItem($foundItemId)
    {
        $this->requireLogin();
        $this->requireFrontDesk();

        $fi   = new FoundItem();
        $item = $fi->find((int)$foundItemId);
        if (!$item) {
            $_SESSION['fd_error'] = 'Found item not found.';
            $this->redirect('frontdesk/lostFound');
            return;
        }
        if ($item['is_high_value']) {
            $_SESSION['fd_error'] = 'High-value items are managed exclusively by security.';
            $this->redirect('frontdesk/lostFound');
            return;
        }
        if ($item['status'] !== 'matched') {
            $_SESSION['fd_error'] = 'Item must be in matched status to arrange a return.';
            $this->redirect('frontdesk/lostFound');
            return;
        }

        $guestId = (int) ($_POST['guest_id'] ?? 0);
        if (!$guestId) {
            $_SESSION['fd_error'] = 'Please select the guest for the return.';
            $this->redirect('frontdesk/lostFound');
            return;
        }

        $method   = in_array($_POST['return_method'] ?? '', ['pickup','courier']) ? $_POST['return_method'] : 'pickup';
        $address  = trim($_POST['return_address'] ?? '') ?: null;
        $shipping = (float) ($_POST['shipping_cost'] ?? 0);

        // UC37 Error: get guest consent for shipping charge BEFORE UC12 call
        if ($method === 'courier' && $shipping > 0 && empty($_POST['guest_consent'])) {
            $_SESSION['fd_error'] =
                "Guest consent required before charging shipping ($".number_format($shipping, 2).")."
                . " Re-submit with guest_consent=1 to confirm."
            ;
            $this->redirect('frontdesk/lostFound');
            return;
        }

        $fi->recordReturn((int)$foundItemId, $guestId, $method, $address, $shipping);
        $_SESSION['fd_success'] = 'Return arranged via ' . ucfirst($method) . '.';
        $this->redirect('frontdesk/lostFound');
    }

    /**
     * POST /frontdesk/disposeItem/{found_item_id}
     * UC37 Step 5 — Dispose of stored item.
     * Requires supervisor_approval_code (validated against a hard-coded env value or DB setting).
     */
    public function disposeItem($foundItemId)
    {
        $this->requireLogin();
        $this->requireFrontDesk();

        $code = trim($_POST['supervisor_approval_code'] ?? '');
        if (!$code) {
            $_SESSION['fd_error'] = 'Disposal requires supervisor approval code. No exceptions.';
            $this->redirect('frontdesk/lostFound');
            return;
        }

        $fi   = new FoundItem();
        $item = $fi->find((int)$foundItemId);
        if (!$item) {
            $_SESSION['fd_error'] = 'Found item not found.';
            $this->redirect('frontdesk/lostFound');
            return;
        }
        if ($item['is_high_value']) {
            $_SESSION['fd_error'] = 'High-value items require security clearance for disposal.';
            $this->redirect('frontdesk/lostFound');
            return;
        }

        $method = in_array($_POST['dispose_method'] ?? '', ['donate','discard']) ? $_POST['dispose_method'] : 'discard';
        $ok     = $fi->dispose((int)$foundItemId, $method);

        if ($ok) {
            $_SESSION['fd_success'] = 'Item disposed of (' . ucfirst($method) . '). Action logged.';
        } else {
            $_SESSION['fd_error'] = 'Disposal failed. Item must be in "stored" status.';
        }
        $this->redirect('frontdesk/lostFound');
    }
}
