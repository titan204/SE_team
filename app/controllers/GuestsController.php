<?php
// ============================================================
//  GuestsController — Guest profile CRUD
//  Routes:
//    /guests            → index (list all guests)
//    /guests/show/5     → show guest #5
//    /guests/create     → new guest form
//    /guests/store      → save new guest
//    /guests/edit/5     → edit guest #5
//    /guests/update/5   → save edits
//    /guests/delete/5   → delete guest #5
// ============================================================

class GuestsController extends Controller
{
   public function index()
{
    $guestModel = new Guest();
    $search = trim($_GET['search'] ?? '');
    $filter = trim($_GET['filter'] ?? '');  
    if (!empty($search)) {
        $guests = $guestModel->search($search);
    } elseif ($filter === 'vip') {
        $guests = $guestModel->filterByVip();
    } elseif ($filter === 'blacklist') {
        $guests = $guestModel->filterByBlacklist();
    } else {
        $guests = $guestModel->all();
    }

    $this->view('guests/index', [
        'guests' => $guests,
        'search' => $search,
    ]);
}
    public function show($id)
    {
        $guestModel = new Guest();
        $guest = $guestModel->find($id);

        if (!$guest) {
            $this->view('errors/404');
            return;
        }

        // ── Reservation history — use Reservation model so room names are joined ──
        // Guest::reservations() returns raw room_id integers; Reservation::findByGuest()
        // runs the proper JOIN and returns room_number + room_type_name.
        try {
            $reservationModel = new Reservation();
            $reservations = $reservationModel->findByGuest((int) $guest['id']);
            if (!is_array($reservations)) $reservations = [];
        } catch (\Throwable $e) {
            $reservations = [];
        }

        // ── Preferences — guard against missing table or unexpected return type ──
        $guestModel->id = $id;
        try {
            $raw = $guestModel->preferences();
            $preferences = is_array($raw) ? $raw : [];
        } catch (\Throwable $e) {
            $preferences = [];
        }

        // ── Feedback ──────────────────────────────────────────────────────────────
        try {
            $raw = $guestModel->feedback();
            $feedback = is_array($raw) ? $raw : [];
        } catch (\Throwable $e) {
            $feedback = [];
        }

        // ── Lifetime value ────────────────────────────────────────────────────────
        try {
            $ltv = $guestModel->calculateLifetimeValue() ?? 0;
            if (!is_numeric($ltv)) $ltv = 0;
        } catch (\Throwable $e) {
            $ltv = 0;
        }

        $this->view('guests/show', [
            'guest'        => $guest,
            'reservations' => $reservations,
            'preferences'  => $preferences,
            'feedback'     => $feedback,
            'ltv'          => $ltv,
        ]);
    }

    public function create()
    {
        // No extra data needed — "Referred By" was removed; password fields are plain inputs.
        $this->view('guests/create', [
            'errors' => $_SESSION['errors'] ?? [],
            'old'    => $_SESSION['old'] ?? [],
        ]);
        unset($_SESSION['errors'], $_SESSION['old']);
    }

    public function store()
    {
        $name    = trim($_POST['name']    ?? '');
        $email   = trim($_POST['email']   ?? '');
        $phone   = trim($_POST['phone']   ?? '');
        $password        = $_POST['password']         ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // ── Validation ────────────────────────────────────────
        $errors = [];

        if ($name === '')                                         $errors['name']  = 'Name is required.';
        if ($email === '')                                        $errors['email'] = 'Email is required.';
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL))       $errors['email'] = 'Invalid email address.';

        if ($password === '')                                     $errors['password'] = 'Password is required.';
        elseif (strlen($password) < 6)                           $errors['password'] = 'Password must be at least 6 characters.';

        if ($confirmPassword === '')                              $errors['confirm_password'] = 'Please confirm your password.';
        elseif ($password !== '' && $password !== $confirmPassword) $errors['confirm_password'] = 'Passwords do not match.';

        // Check email uniqueness across both tables
        if (!isset($errors['email'])) {
            $userModel  = new User();
            $guestModel = new Guest();
            if ($userModel->findByEmail($email) || $guestModel->findByEmail($email)) {
                $errors['email'] = 'This email is already registered.';
            }
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old']    = [
                'name'  => $name,
                'email' => $email,
                'phone' => $phone,
                'national_id'   => $_POST['national_id']   ?? '',
                'nationality'   => $_POST['nationality']   ?? '',
                'date_of_birth' => $_POST['date_of_birth'] ?? '',
            ];
            $this->redirect('guests/create');
            return;
        }

        // ── Persist ───────────────────────────────────────────
        // 1. Insert into guests table
        $guestModel  = new Guest();
        $guestId = $guestModel->create([
            'name'          => $name,
            'email'         => $email,
            'phone'         => $phone,
            'national_id'   => $_POST['national_id']   ?? '',
            'nationality'   => $_POST['nationality']   ?? '',
            'date_of_birth' => $_POST['date_of_birth'] ?? null,
        ]);

        // 2. Resolve guest role ID
        $guestRole = (new Role())->findByName('guest');
        if (!$guestRole) {
            // Roll back and abort if guest role is missing
            $guestModel->delete($guestId);
            $_SESSION['errors'] = ['role' => 'Guest role not found in the database. Cannot create account.'];
            $this->redirect('guests/create');
            return;
        }

        // 3. Create the users record — User::create() bcrypt-hashes the password
        $userModel = new User();
        $userId = $userModel->create([
            'name'     => $name,
            'email'    => $email,
            'password' => $password,    // plaintext — User::create() hashes with PASSWORD_DEFAULT
            'role_id'  => $guestRole['id'],
        ]);

        if ((int) $userId <= 0) {
            // Roll back guests row and bail
            $guestModel->delete($guestId);
            $_SESSION['errors'] = ['db' => 'Could not create login account. Please try again.'];
            $this->redirect('guests/create');
            return;
        }

        $this->redirect('guests');
    }

    public function edit($id)
    {
        $guestModel = new Guest();
        $guest = $guestModel->find($id);

        if (!$guest) {
            $this->view('errors/404');
            return;
        }

        $this->view('guests/edit', ['guest' => $guest]);
    }

    public function update($id)
    {
        $name  = trim($_POST['name']  ?? '');
        $email = trim($_POST['email'] ?? '');

        $errors = [];
        if (empty($name))                                $errors[] = 'Name is required.';
        if (empty($email))                               $errors[] = 'Email is required.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))  $errors[] = 'Invalid email.';

        if (!empty($errors)) {
            $this->view('guests/edit', ['errors' => $errors, 'old' => $_POST]);
            return;
        }

        $guestModel = new Guest();
        $guestModel->update($id, [
            'name'          => $name,
            'email'         => $email,
            'phone'         => $_POST['phone']         ?? '',
            'nationality'   => $_POST['nationality']   ?? '',
            'date_of_birth' => $_POST['date_of_birth'] ?? null,
        ]);

        $this->redirect('guests/show/' . $id);
    }

    public function delete($id)
    {
        $guestModel = new Guest();
        $guestModel->delete($id);

        $this->redirect('guests');
    }

    // ── Special Actions ──────────────────────────────────────

    public function blacklist($id)
    {
        $reason = trim($_POST['reason'] ?? 'No reason provided.');

        $guestModel     = new Guest();
        $guestModel->id = $id;
        $guestModel->blacklist($reason);

        $this->redirect('guests/show/' . $id);
    }

    public function anonymize($id)
    {
        $guestModel     = new Guest();
        $guestModel->id = $id;
        $guestModel->anonymize();

        $this->redirect('guests');
    }

    public function flagVip($id)
    {
        $guestModel     = new Guest();
        $guestModel->id = $id;
        $guestModel->flagAsVip();

        $this->redirect('guests/show/' . $id);
    }
    public function profile()
{
   
    if (empty($_SESSION['user_id'])) {
        $this->redirect('auth/login');
    }
 
    $userModel = new User();
    $guest = $userModel->find($_SESSION['user_id']);
 
    if (!$guest) {
        $this->redirect('');
    }
 
    $this->view('guests/profile', [
        'pageTitle' => 'My Profile',
        'guest'     => $guest,
    ]);
}
}
?>