# 🔑 Developer 1 — Mission File
# Core Framework, Authentication & Staff Management

---

## Your Main Responsibility

You are the **foundation builder and team lead**. You set up the database, verify the core framework files, implement the full authentication system (login/logout/sessions), and build the staff management module. Every other developer depends on your work being done first.

## Why Your Work Is Important

You are the **first link in the chain**. If login doesn't work, nobody can test their modules. If the database isn't imported correctly, nobody's queries will run. If the session variables aren't standardized, every module will break. **You must finish on time — the entire team is blocked until you do.**

## Start Day / Time

📅 **Day 1, Morning** — You start immediately on Day 1.

## Deadline

📅 **Day 2, End of Day** — Authentication MUST be merged into `develop` by end of Day 2.
📅 **Day 4, End of Day** — Staff management (Users module) fully complete.

## Estimated Hours Needed

- Database setup + core verification: **2 hours**
- Authentication system: **6 hours**
- Staff management (Users CRUD + RBAC): **6 hours**
- Integration support (Days 5-7): **8 hours**
- **Total: ~22 hours**

## Git Branch

```
feature/core-auth
```

## Files You Own

### Core Files (verify + protect — no one else touches these):
- `database.sql`
- `config/config.php`
- `core/Database.php`
- `core/Model.php`
- `core/Controller.php`
- `core/Router.php`
- `public/index.php`
- `public/.htaccess`

### Model Files:
- `app/models/User.php`
- `app/models/Role.php`

### Controller Files:
- `app/controllers/AuthController.php`
- `app/controllers/UsersController.php`

### View Files:
- `app/views/auth/login.php`
- `app/views/users/index.php`
- `app/views/users/create.php`
- `app/views/users/edit.php`

---

## Functions You Must Implement

### In `app/models/Role.php`:
| Function | What It Does |
|---|---|
| `all()` | SELECT all roles from the `roles` table |
| `find($id)` | SELECT one role by its ID |
| `create($data)` | INSERT a new role |
| `update($id, $data)` | UPDATE a role name |
| `delete($id)` | DELETE a role |
| `users()` | SELECT all users that have this role_id |

### In `app/models/User.php`:
| Function | What It Does |
|---|---|
| `all()` | SELECT all users JOINed with their role name |
| `find($id)` | SELECT one user by ID |
| `findByEmail($email)` | SELECT one user by email (used for login) |
| `create($data)` | INSERT a new user (hash password with `password_hash()`) |
| `update($id, $data)` | UPDATE user details |
| `delete($id)` | Soft-delete: SET `is_active = 0` (don't actually DELETE) |
| `role()` | SELECT the role record for this user's `role_id` |
| `authenticate($email, $password)` | Find user by email, then verify password with `password_verify()`. Return user data on success, `false` on failure |

### In `app/controllers/AuthController.php`:
| Function | What It Does |
|---|---|
| `login()` | Render the login form view |
| `doLogin()` | Read `$_POST`, call `User::authenticate()`, set session variables, redirect |
| `logout()` | Call `session_destroy()`, redirect to login page |

### In `app/controllers/UsersController.php`:
| Function | What It Does |
|---|---|
| `index()` | Require manager role. Load all users. Render users/index view |
| `create()` | Require manager role. Load roles for dropdown. Render users/create view |
| `store()` | Validate POST. Hash password. Call `User::create()`. Redirect |
| `edit($id)` | Load user + roles. Render users/edit view |
| `update($id)` | Validate POST. Call `User::update()`. Redirect |
| `delete($id)` | Soft-delete user. Redirect |

---

## Requirements Covered

| # | Requirement | Status |
|---|---|---|
| 38 | Role-Based Access Control (RBAC) | ✅ You implement this |
| 39 | System Audit Trail (partial — audit log model is Dev 7) | ✅ You set the foundation |
| — | Staff authentication & session management | ✅ You implement this |
| — | Staff account CRUD (Manager only) | ✅ You implement this |

---

## Dependencies

**None** — You start first with zero dependencies.

## Developers Waiting For You

| Developer | What They Need From You | When |
|---|---|---|
| **Dev 2** | Working database, login system, base Controller/Model classes | End of Day 2 |
| **Dev 3** | Working database, login system, base Controller/Model classes | End of Day 2 |
| **Dev 4** | Everything above + session variables standardized | End of Day 2 |
| **Dev 5** | Everything above | End of Day 2 |
| **Dev 6** | Everything above | End of Day 2 |
| **Dev 7** | Everything above + working auth to test navbar | End of Day 2 |

---

## Step By Step Work Plan

### Step 1: Import Database (Day 1, first 30 minutes)
- Open phpMyAdmin or MySQL CLI
- Create database: `CREATE DATABASE hotel_management`
- Import `database.sql`
- Verify all 17 tables are created
- Verify the 3 seed roles exist: manager, front_desk, housekeeper

### Step 2: Verify Core Framework (Day 1, next 1 hour)
- Open `config/config.php` — set your local DB credentials
- Open `public/index.php` in browser — should load without errors
- Test the Router: visit `?url=auth/login` — should reach AuthController
- If `.htaccess` rewriting works, `/auth/login` should also work

### Step 3: Implement User Model — authenticate() (Day 1, next 2 hours)
- Start with `findByEmail()` — this is the simplest query
- Then implement `authenticate()`:
  1. Call `findByEmail($email)` to get user row
  2. If no user found, return `false`
  3. If user found, call `password_verify($password, $user['password'])`
  4. If password matches, return the user array
  5. If not, return `false`

### Step 4: Implement AuthController (Day 1 afternoon)
- `login()` — just calls `$this->view('auth/login')`
- `doLogin()`:
  1. Read `$_POST['email']` and `$_POST['password']`
  2. Create User model: `$userModel = $this->model('User')`
  3. Call `$userModel->authenticate($email, $password)`
  4. If result is `false` → redirect back to login with error
  5. If result is user data → set session variables:
     - `$_SESSION['user_id'] = $user['id']`
     - `$_SESSION['user_name'] = $user['name']`
     - `$_SESSION['user_role'] = $roleName` (query the role name)
  6. Redirect to dashboard
- `logout()`:
  1. Call `session_unset()` then `session_destroy()`
  2. Redirect to `auth/login`

### Step 5: Build Login View (Day 1 evening)
- Build `app/views/auth/login.php` with a Bootstrap card form
- Include email input, password input, submit button
- Show error message if login failed (use `$_SESSION['error']` or `$_GET['error']`)

### Step 6: Insert a Test User into Database (Day 1)
- Manually insert a user for testing:
  ```sql
  INSERT INTO users (role_id, name, email, password)
  VALUES (1, 'Admin', 'admin@hotel.com', '$2y$10$...');
  ```
- Generate the hash with PHP: `echo password_hash('password123', PASSWORD_DEFAULT);`

### Step 7: Test Full Login Flow (Day 2 morning)
- Go to `/auth/login`
- Enter test credentials
- Verify redirect to dashboard
- Verify session variables are set
- Test logout — verify session is destroyed

### Step 8: Implement Remaining User Model Methods (Day 2)
- `all()`, `find()`, `create()`, `update()`, `delete()`
- Remember: `create()` must hash the password before INSERT
- Remember: `delete()` should SET `is_active = 0`, not DELETE

### Step 9: Implement UsersController (Days 3-4)
- Add `$this->requireRole('manager')` at the top of every method
- Build each method following the TODO comments
- Build all 3 user views

### Step 10: Add Seed Data (Day 2)
- Insert at least one user per role for team testing:
  - Admin (manager): admin@hotel.com / password123
  - Front Desk: frontdesk@hotel.com / password123
  - Housekeeper: housekeeper@hotel.com / password123

---

## Day By Day Schedule

| Day | What You Do |
|---|---|
| **Day 1** | Import DB. Verify core. Implement `User::authenticate()` + `User::findByEmail()`. Build AuthController login/logout. Build login view. Insert test users. |
| **Day 2** | Test and fix login flow. Implement full User model CRUD. Implement Role model. Push + merge `feature/core-auth` into `develop`. Share test credentials with team. |
| **Day 3** | Implement `UsersController` (index, create, store). Build users/index and users/create views. Add RBAC checks. |
| **Day 4** | Implement `UsersController` (edit, update, delete). Build users/edit view. Test all role-based access. Complete staff management module. |
| **Day 5** | **Integration support.** Help other devs debug. Verify auth works in every module. Check RBAC is enforced. |
| **Day 6** | Full integration testing. Test login → every module → logout. Fix any session bugs. |
| **Day 7** | Final bugs. Add missing seed data. Help with final merge to main. |

---

## Implementation Guidance

### Authentication Flow (Conceptual):

```
User visits /auth/login
    → AuthController::login() shows the form
    → User submits email + password
    → AuthController::doLogin() receives POST data
    → Creates User model, calls authenticate(email, password)
    → User model queries DB for that email
    → If found: compares hashed password using password_verify()
    → If match: sets $_SESSION variables and redirects to /dashboard
    → If no match: redirects back to /auth/login with error message
```

### Session Variables (STANDARDIZED — tell the whole team):
```
$_SESSION['user_id']    → int    (the user's database ID)
$_SESSION['user_name']  → string (the user's display name)
$_SESSION['user_role']  → string (the role NAME: 'manager', 'front_desk', 'housekeeper')
```

### Password Security:
- NEVER store plain-text passwords
- Always use `password_hash($password, PASSWORD_DEFAULT)` when creating users
- Always use `password_verify($inputPassword, $hashedPassword)` when checking login
- Never use MD5 or SHA1 for passwords

### RBAC (Role-Based Access Control):
- The base `Controller.php` already has `requireLogin()` and `requireRole()` methods
- `requireLogin()` checks if `$_SESSION['user_id']` exists
- `requireRole('manager')` checks if `$_SESSION['user_role'] === 'manager'`
- Call these at the START of controller methods that need protection

---

## What To Test Before Merge

- [ ] Database imports cleanly from `database.sql`
- [ ] Visiting any page without login redirects to `/auth/login`
- [ ] Login with valid credentials → redirects to dashboard
- [ ] Login with invalid credentials → shows error, stays on login
- [ ] Session shows user_id, user_name, user_role after login
- [ ] Logout destroys session and redirects to login
- [ ] Users list page works (manager only)
- [ ] Create new staff member works (password is hashed)
- [ ] Edit staff member works
- [ ] Deactivate staff member works (soft delete)
- [ ] Non-manager trying to access /users → gets "Access denied"

## Risks To Avoid

1. **Don't forget to hash passwords** — this is the #1 security mistake
2. **Don't call `session_start()` anywhere** — it's already called in `public/index.php`
3. **Don't use different session variable names** — stick to `user_id`, `user_name`, `user_role` exactly
4. **Don't hard-delete users** — always soft-delete with `is_active = 0`
5. **Don't forget to share test credentials with the team on Day 2**

## Definition of Done

- [ ] Login and logout work end-to-end
- [ ] 3 test users exist (one per role)
- [ ] Session variables are set correctly
- [ ] `requireLogin()` and `requireRole()` work
- [ ] Users CRUD is complete (manager only)
- [ ] All 4 user views render correctly
- [ ] Branch merged into `develop`
- [ ] Team notified with test credentials

## Handover Notes

Tell all developers:
1. **Test credentials**: admin@hotel.com / password123, frontdesk@hotel.com / password123, housekeeper@hotel.com / password123
2. **Session variables**: Use `$_SESSION['user_id']`, `$_SESSION['user_name']`, `$_SESSION['user_role']`
3. **To protect a page**: Call `$this->requireLogin()` or `$this->requireRole('manager')` at the start of your method
4. **To get the logged-in user's ID**: `$_SESSION['user_id']`
