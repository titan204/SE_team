<?php


class UsersController extends Controller
{
    public function index()
    {
        $this->requireRole('manager');
        $db = (new Model())->getDb();

        $roleFilter = $_GET['role'] ?? '';
        $where = '1=1';
        if ($roleFilter) {
            $safe = mysqli_real_escape_string($db, $roleFilter);
            $where .= " AND r.name = '$safe'";
        }

        
        $r = mysqli_query($db,
            "SELECT u.id, u.name, u.email, u.is_active, u.created_at,
                    r.name AS role, r.id AS role_id
             FROM   users u
             JOIN   roles r ON u.role_id = r.id
             WHERE  r.name != 'guest' AND $where
             ORDER  BY FIELD(r.name,'manager','front_desk','housekeeper','revenue_manager'),
                       u.name ASC");
        $users = $r ? mysqli_fetch_all($r, MYSQLI_ASSOC) : [];

        
        $rCounts = mysqli_query($db,
            "SELECT r.name AS role, COUNT(*) AS cnt
             FROM   users u JOIN roles r ON u.role_id = r.id
             WHERE  r.name != 'guest'
             GROUP  BY r.name");
        $roleCounts = [];
        if ($rCounts) { while ($c = mysqli_fetch_assoc($rCounts)) $roleCounts[$c['role']] = (int)$c['cnt']; }

        $message = $_SESSION['message'] ?? null;
        unset($_SESSION['message']);

        $this->view('users/index', compact('users','roleCounts','roleFilter','message'));
    }

    public function create()
    {
        $this->requireRole('manager');

        $roleModel = new Role();
        $roles = $roleModel->all();

        $data = [
            'roles' => $roles,
            'errors' => $_SESSION['errors'] ?? [],
            'old' => $_SESSION['old'] ?? [],
        ];

        unset($_SESSION['errors'], $_SESSION['old']);

        $this->view('users/create', $data);
    }

    public function store()
    {
        $this->requireRole('manager');

        
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role_id = $_POST['role_id'] ?? '';
        $errors = [];

        
        if ($name === '') {
            $errors['name'] = 'Name is required.';
        }

        if ($email === '') {
            $errors['email'] = 'Email is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
        }

        if ($password === '') {
            $errors['password'] = 'Password is required.';
        } elseif (strlen($password) < 6) {
            $errors['password'] = 'Password must be at least 6 characters.';
        }

        if ($role_id === '' || !is_numeric($role_id)) {
            $errors['role_id'] = 'Role is required.';
        }

        $_SESSION['old'] = ['name' => $name, 'email' => $email, 'role_id' => $role_id];

        // 3. If validation failed
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $this->redirect('users/create');
        }

    
        $userModel = new User();
        $userId = $userModel->create([
            'name'     => $name,
            'email'    => $email,
            'password' => $password,
            'role_id'  => $role_id,
        ]);

        
        AuditLog::log(
            $_SESSION['user_id'],
            'CREATE',
            'user',
            $userId,
            null,
            json_encode([
                'name' => $name,
                'email' => $email,
                'role_id' => $role_id
            ])
        );

        
        $_SESSION['message'] = 'User created successfully.';
        $this->redirect('users/index');
    }

    public function edit($id)
    {
        $this->requireRole('manager');

        $userModel = new User();
        $user = $userModel->find($id);

        if (!$user) {
            die('User not found');
        }

        $roleModel = new Role();
        $roles = $roleModel->all();

        $data = [
            'user' => $user,
            'roles' => $roles,
            'errors' => $_SESSION['errors'] ?? [],
        ];

        unset($_SESSION['errors']);

        $this->view('users/edit', $data);
    }

    public function update($id)
    {
        $this->requireRole('manager');

        $userModel = new User();
        $oldUser = $userModel->find($id);

        if (!$oldUser) {
            die('User not found');
        }

        
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $role_id = $_POST['role_id'] ?? '';
        $errors = [];

        
        if ($name === '') {
            $errors['name'] = 'Name is required.';
        }

        if ($email === '') {
            $errors['email'] = 'Email is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
        }

        if ($role_id === '' || !is_numeric($role_id)) {
            $errors['role_id'] = 'Role is required.';
        }

        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $this->redirect('users/edit/' . $id);
        }

        
        $updateData = [
            'name'      => $name,
            'email'     => $email,
            'role_id'   => (int)$role_id,
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];

        
        $newPassword = trim($_POST['password'] ?? '');
        if ($newPassword !== '') {
            if (strlen($newPassword) < 6) {
                $_SESSION['errors'] = ['password' => 'Password must be at least 6 characters.'];
                $this->redirect('users/edit/' . $id);
                return;
            }
            
            $userModel->updatePassword($id, $newPassword);
        }

        
        $userModel->update($id, $updateData);

        
        AuditLog::log(
            $_SESSION['user_id'],
            'UPDATE',
            'user',
            $id,
            json_encode([
                'name' => $oldUser['name'],
                'email' => $oldUser['email'],
                'role_id' => $oldUser['role_id']
            ]),
            json_encode($updateData)
        );

        
        $_SESSION['message'] = 'User updated successfully.';
        $this->redirect('users/index');
    }

    public function delete($id)
    {
        $this->requireRole('manager');

        $userModel = new User();
        $oldUser = $userModel->find($id);

        if (!$oldUser) {
            die('User not found');
        }

        
        $userModel->update($id, ['is_active' => 0]);

        
        AuditLog::log(
            $_SESSION['user_id'],
            'DELETE',
            'user',
            $id,
            json_encode([
                'name' => $oldUser['name'],
                'email' => $oldUser['email'],
                'is_active' => $oldUser['is_active']
            ]),
            json_encode([
                'name' => $oldUser['name'],
                'email' => $oldUser['email'],
                'is_active' => 0
            ])
        );

        
        $_SESSION['message'] = 'User deactivated successfully.';
        $this->redirect('users/index');
    }
}
