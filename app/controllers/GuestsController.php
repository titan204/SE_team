<?php

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

        
        try {
            $reservationModel = new Reservation();
            $reservations = $reservationModel->findByGuest((int) $guest['id']);
            if (!is_array($reservations)) $reservations = [];
        } catch (\Throwable $e) {
            $reservations = [];
        }

        
        $guestModel->id = $id;
        try {
            $raw = $guestModel->preferences();
            $preferences = is_array($raw) ? $raw : [];
        } catch (\Throwable $e) {
            $preferences = [];
        }

        
        try {
            $raw = $guestModel->feedback();
            $feedback = is_array($raw) ? $raw : [];
        } catch (\Throwable $e) {
            $feedback = [];
        }

    
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

        
        $errors = [];

        if ($name === '')                                         $errors['name']  = 'Name is required.';
        if ($email === '')                                        $errors['email'] = 'Email is required.';
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL))       $errors['email'] = 'Invalid email address.';

        if ($password === '')                                     $errors['password'] = 'Password is required.';
        elseif (strlen($password) < 6)                           $errors['password'] = 'Password must be at least 6 characters.';

        if ($confirmPassword === '')                              $errors['confirm_password'] = 'Please confirm your password.';
        elseif ($password !== '' && $password !== $confirmPassword) $errors['confirm_password'] = 'Passwords do not match.';

        
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

        
        $guestModel  = new Guest();
        $guestId = $guestModel->create([
            'name'          => $name,
            'email'         => $email,
            'phone'         => $phone,
            'national_id'   => $_POST['national_id']   ?? '',
            'nationality'   => $_POST['nationality']   ?? '',
            'date_of_birth' => $_POST['date_of_birth'] ?? null,
        ]);

        
        $guestRole = (new Role())->findByName('guest');
        if (!$guestRole) {
            
            $guestModel->delete($guestId);
            $_SESSION['errors'] = ['role' => 'Guest role not found in the database. Cannot create account.'];
            $this->redirect('guests/create');
            return;
        }

        
        $userModel = new User();
        $userId = $userModel->create([
            'name'     => $name,
            'email'    => $email,
            'password' => $password,    
            'role_id'  => $guestRole['id'],
        ]);

        if ((int) $userId <= 0) {
            
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