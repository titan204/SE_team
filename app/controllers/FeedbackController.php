<?php
class FeedbackController extends Controller
{
    // ── Helpers ───────────────────────────────────────────────

    private function resolveGuest(): ?array
    {
        $email = trim((string)($_SESSION['user_email'] ?? ''));
        if ($email === '') return null;
        $m = new Guest();
        return $m->findByEmail($email) ?: null;
    }

    private function json(int $status, array $payload): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE);
        exit;
    }

    private function isAjax(): bool
    {
        return strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';
    }

    // ── Guest: Feedback Form ──────────────────────────────────

    public function create(): void
    {
        $this->requireRole('guest');

        $guest = $this->resolveGuest();
        if (!$guest) {
            $_SESSION['error'] = 'Guest profile not found.';
            $this->redirect('home/index');
        }

        $fbModel  = new Feedback();
        $eligible = $fbModel->eligibleReservations((int)$guest['id']);

        $this->view('feedback/create', [
            'pageTitle' => 'Leave Feedback',
            'guest'     => $guest,
            'eligible'  => $eligible,
            'errors'    => $_SESSION['feedback_errors'] ?? [],
            'old'       => $_SESSION['feedback_old']    ?? [],
            'success'   => $_SESSION['feedback_success'] ?? null,
        ]);

        unset($_SESSION['feedback_errors'], $_SESSION['feedback_old'], $_SESSION['feedback_success']);
    }

    // ── Guest: Submit Feedback ────────────────────────────────

    public function store(): void
    {
        $this->requireRole('guest');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('feedback/create');
        }

        $guest = $this->resolveGuest();
        if (!$guest) { $this->redirect('feedback/create'); }

        $guestId = (int)$guest['id'];
        $resId   = (int)($_POST['reservation_id']    ?? 0);
        $overall = (int)($_POST['overall_rating']    ?? 0);
        $clean   = (int)($_POST['cleanliness_rating'] ?? 0);
        $staff   = (int)($_POST['staff_rating']       ?? 0);
        $food    = (int)($_POST['food_rating']        ?? 0);
        $fac     = (int)($_POST['facilities_rating']  ?? 0);
        $comment = trim($_POST['comment']             ?? '');
        $rec     = ($_POST['recommend_hotel'] ?? 'no') === 'yes' ? 1 : 0;

        // ── Validation ────────────────────────────────────────
        $errors = [];
        if ($resId <= 0) $errors['reservation_id'] = 'Please select a reservation.';

        foreach ([
            'overall_rating'     => ['Overall rating',     $overall],
            'cleanliness_rating' => ['Cleanliness rating', $clean],
            'staff_rating'       => ['Staff service',      $staff],
            'food_rating'        => ['Food quality',       $food],
            'facilities_rating'  => ['Facilities',         $fac],
        ] as $field => [$label, $val]) {
            if ($val < 1 || $val > 5) $errors[$field] = "$label must be 1–5.";
        }

        if ($comment === '') {
            $errors['comment'] = 'Please write a comment about your stay (min 10 characters).';
        } elseif (mb_strlen($comment) < 10) {
            $errors['comment'] = 'Comment must be at least 10 characters.';
        } elseif (mb_strlen($comment) > 2000) {
            $errors['comment'] = 'Comment must be 2000 characters or fewer.';
        }

        $fbModel = new Feedback();

        if (empty($errors['reservation_id'])) {
            if (!$fbModel->reservationBelongsToGuest($resId, $guestId)) {
                $errors['reservation_id'] = 'That reservation is not eligible for feedback.';
            } elseif ($fbModel->hasFeedbackForReservation($resId, $guestId)) {
                $errors['reservation_id'] = 'Feedback already submitted for this reservation.';
            }
        }

        if (!empty($errors)) {
            $_SESSION['feedback_errors'] = $errors;
            $_SESSION['feedback_old']    = $_POST;
            $this->redirect('feedback/create');
        }

        $newId = $fbModel->createFeedback([
            'guest_id'           => $guestId,
            'guest_name'         => $guest['name'] ?? '',
            'reservation_id'     => $resId,
            'overall_rating'     => $overall,
            'cleanliness_rating' => $clean,
            'staff_rating'       => $staff,
            'food_rating'        => $food,
            'facilities_rating'  => $fac,
            'comment'            => $comment,
            'recommend_hotel'    => $rec,
        ]);

        $_SESSION[$newId ? 'feedback_success' : 'feedback_errors'] = $newId
            ? 'Thank you! Your feedback has been submitted successfully.'
            : ['general' => 'Could not save feedback. Please try again.'];

        $this->redirect('feedback/create');
    }

    // ── Guest: My Feedback History ────────────────────────────

    public function myFeedback(): void
    {
        $this->requireRole('guest');

        $guest = $this->resolveGuest();
        if (!$guest) { $this->redirect('home/index'); }

        $fbModel = new Feedback();

        $this->view('feedback/my_feedback', [
            'pageTitle'    => 'My Feedback',
            'guest'        => $guest,
            'feedbackList' => $fbModel->getGuestFeedback((int)$guest['id']),
        ]);
    }

    // ── Admin: All Feedback ───────────────────────────────────

    public function index(): void
    {
        $this->requireRole('admin');

        $filters = array_filter([
            'rating'      => $_GET['rating']      ?? '',
            'date_from'   => $_GET['date_from']   ?? '',
            'date_to'     => $_GET['date_to']     ?? '',
            'guest_id'    => $_GET['guest_id']    ?? '',
            'is_resolved' => $_GET['is_resolved'] ?? '',
        ], fn($v) => $v !== '');

        $fbModel = new Feedback();

        $this->view('feedback/index', [
            'pageTitle'    => 'Guest Feedback',
            'feedbackList' => $fbModel->getAllFeedback($filters),
            'averages'     => $fbModel->calculateAverageRatings($filters),
            'filters'      => $filters,
            'guests'       => (new Guest())->all(),
            'success'      => $_SESSION['feedback_admin_success'] ?? null,
        ]);

        unset($_SESSION['feedback_admin_success']);
    }

    // ── Admin: Resolve ────────────────────────────────────────

    public function resolve($id): void
    {
        $this->requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('feedback/index');
        }

        $userId  = (int)($_SESSION['user_id'] ?? 0);
        $fbModel = new Feedback();
        $ok      = $fbModel->markAsResolved((int)$id, $userId);

        if ($this->isAjax()) {
            $this->json($ok ? 200 : 422, [
                'ok'      => $ok,
                'message' => $ok ? 'Marked as resolved.' : 'Already resolved or not found.',
            ]);
        }

        $_SESSION['feedback_admin_success'] = $ok
            ? 'Feedback #' . (int)$id . ' marked as resolved.'
            : 'Could not update that feedback.';

        $this->redirect('feedback/index');
    }
}
