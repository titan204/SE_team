<?php
class HomeController extends Controller
{
    public function index(): void
    {
        $guestName   = htmlspecialchars($_SESSION['user_name']    ?? '');
        $checkinDate = $_SESSION['checkin_date'] ?? null;
        $roomNumber  = $_SESSION['room_number']  ?? null;

        ob_start();
        require VIEW_PATH . '/home/index.php';
        $content = ob_get_clean();
        echo $content; // standalone page — has its own full layout
    }

    public function guestprofile()
    {
        // Redirect to the full self-service profile controller.
        // Kept for backward-compatibility with existing bookmarks/links.
        $this->redirect('guestProfile/index');
    }

    public function externalServices()
    {
        $this->requireRole('guest');

        $guest = $this->currentGuest();

        if (!$guest) {
            $_SESSION['external_service_booking_errors'] = [
                'guest' => 'Guest profile could not be found for this account.',
            ];
            $this->redirect('home/index');
        }

        $serviceModel = new ExternalService();
        $bookingModel = new ServiceBooking();

        $this->view('home/external_services', [
            'pageTitle' => 'External Services Booking',
            'services' => $serviceModel->all(),
            'bookings' => $bookingModel->findByGuest($guest['id']),
            'errors' => $_SESSION['external_service_booking_errors'] ?? [],
            'old' => $_SESSION['external_service_booking_old'] ?? [],
            'message' => $_SESSION['external_service_booking_success'] ?? null,
        ]);

        unset(
            $_SESSION['external_service_booking_errors'],
            $_SESSION['external_service_booking_old'],
            $_SESSION['external_service_booking_success']
        );
    }

    public function storeServiceBooking()
    {
        $this->requireRole('guest');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('home/externalServices');
        }

        $guest = $this->currentGuest();

        if (!$guest) {
            $_SESSION['external_service_booking_errors'] = [
                'guest' => 'Guest profile could not be found for this account.',
            ];
            $this->redirect('home/externalServices');
        }

        $serviceId = trim((string) ($_POST['service_id'] ?? ''));
        $bookingDate = trim((string) ($_POST['booking_date'] ?? ''));
        $bookingTime = trim((string) ($_POST['booking_time'] ?? ''));
        $errors = [];

        if ($serviceId === '' || !ctype_digit($serviceId)) {
            $errors['service_id'] = 'Please choose a valid service.';
        }

        $serviceModel = new ExternalService();
        $service = empty($errors['service_id']) ? $serviceModel->find((int) $serviceId) : null;

        if ($serviceId !== '' && empty($errors['service_id']) && !$service) {
            $errors['service_id'] = 'Please choose a valid service.';
        }

        $dateObject = DateTime::createFromFormat('Y-m-d', $bookingDate);
        if ($bookingDate === '' || !$dateObject || $dateObject->format('Y-m-d') !== $bookingDate) {
            $errors['booking_date'] = 'Please choose a valid booking date.';
        }

        $timeObject = DateTime::createFromFormat('H:i', $bookingTime);
        if ($bookingTime === '' || !$timeObject || $timeObject->format('H:i') !== $bookingTime) {
            $errors['booking_time'] = 'Please choose a valid booking time.';
        }

        $_SESSION['external_service_booking_old'] = [
            'service_id' => $serviceId,
            'booking_date' => $bookingDate,
            'booking_time' => $bookingTime,
        ];

        if (!empty($errors)) {
            $_SESSION['external_service_booking_errors'] = $errors;
            $this->redirect('home/externalServices');
        }

        $bookingModel = new ServiceBooking();
        $bookingModel->create([
            'guest_id' => $guest['id'],
            'service_id' => (int) $serviceId,
            'booking_date' => $bookingDate,
            'booking_time' => $bookingTime,
            'status' => 'pending',
        ]);

        unset($_SESSION['external_service_booking_old']);
        $_SESSION['external_service_booking_success'] = 'Your service booking has been submitted and is now pending.';

        $this->redirect('home/externalServices');
    }

    private function currentGuest()
    {
        $email = trim((string) ($_SESSION['user_email'] ?? ''));

        if ($email === '') {
            return null;
        }

        $guestModel = new Guest();
        return $guestModel->findByEmail($email);
    }
}
