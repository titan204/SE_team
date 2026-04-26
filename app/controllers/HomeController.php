<?php
class HomeController extends Controller
{
    public function index(): void
    {
        $pageTitle   = 'Welcome to our Hotel';
        $guestName   = htmlspecialchars($_SESSION['guest_name'] ?? '');
        $checkinDate = $_SESSION['checkin_date'] ?? null;
        $roomNumber  = $_SESSION['room_number'] ?? null;

        ob_start();
        require VIEW_PATH . '/home/index.php';
        $content = ob_get_clean();
        require VIEW_PATH . '/layouts/main.php';
    }

    public function guestprofile()
{
    if (empty($_SESSION['user_id'])) {
        $this->redirect('auth/login');
    }

    $userModel = new User();
    $guest = $userModel->find($_SESSION['user_id']);

    if (!$guest) {
        $this->redirect('');
    }

    $this->view('home/guestprofile', [
        'pageTitle' => 'My Profile',
        'guest'     => $guest,
    ]);
}
}