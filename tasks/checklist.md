# ✅ Final Pre-Submission Checklist

Use this checklist on **Day 7** before submitting. Every item must be checked by the assigned person.

---

## 🔧 Infrastructure (Dev 1 verifies)

- [ ] `database.sql` imports without errors on a fresh MySQL database
- [ ] `config/config.php` has correct default values documented
- [ ] `public/index.php` loads without PHP errors
- [ ] `.htaccess` URL rewriting works (clean URLs)
- [ ] All `core/` files are unmodified from the agreed version
- [ ] `.gitignore` is present and excludes IDE files

## 🔐 Authentication (Dev 1 verifies)

- [ ] Login page renders correctly at `/auth/login`
- [ ] Valid credentials → redirects to dashboard
- [ ] Invalid credentials → shows error, stays on login page
- [ ] Logout destroys session and redirects to login
- [ ] Accessing any page without login redirects to `/auth/login`
- [ ] Manager-only pages (Users, Reports) reject non-manager roles
- [ ] Session persists across all pages during a single visit

## 🏨 Rooms (Dev 2 verifies)

- [ ] Room types CRUD works (list, add, edit, delete)
- [ ] Rooms CRUD works (list, add, edit, delete)
- [ ] Room status updates work (available ↔ occupied ↔ dirty, etc.)
- [ ] Room availability search by date works
- [ ] Rooms display correct type name and status badge

## 👤 Guests (Dev 3 verifies)

- [ ] Guests CRUD works (list, add, edit, delete)
- [ ] Guest search by name/email works
- [ ] Guest profile shows preferences, reservation history, feedback
- [ ] VIP flagging works
- [ ] Blacklist function works
- [ ] Loyalty tier displays correctly
- [ ] Lifetime value calculation works
- [ ] GDPR anonymize function works

## 📋 Reservations (Dev 4 verifies)

- [ ] Create reservation with guest + room selection works
- [ ] Reservation list shows all bookings with correct status
- [ ] Reservation detail page shows all info
- [ ] Check-in: status changes to `checked_in`, room becomes `occupied`
- [ ] Check-out: status changes to `checked_out`, room becomes `dirty`
- [ ] Cancel: status changes to `cancelled`
- [ ] No-show: status changes to `no_show`, penalty applied
- [ ] Date range filtering works
- [ ] Status filtering works
- [ ] Group booking (multiple rooms under one group) works
- [ ] Early check-in eligibility check works

## 💰 Billing (Dev 5 verifies)

- [ ] Folio auto-created when reservation is created
- [ ] Folio list displays with correct totals
- [ ] Add charge to folio works (all charge types)
- [ ] Record payment works (all payment methods)
- [ ] Folio total auto-recalculates after adding charges
- [ ] Balance due updates after payments
- [ ] Folio settles when fully paid (balance = 0)
- [ ] Pro-forma invoice renders correctly
- [ ] Split bill function works
- [ ] Refund function works
- [ ] Audit log records price overrides and refunds

## 🧹 Housekeeping (Dev 6 verifies)

- [ ] HK task list displays with status grouping
- [ ] Create HK task with room + assignee works
- [ ] Complete task: quality score saved, room status updated
- [ ] Minibar consumption logging works
- [ ] Check-out auto-creates HK cleaning task for the room
- [ ] Priority arrival rooms flagged for front desk

## 🔧 Maintenance (Dev 6 verifies)

- [ ] Maintenance order list displays with priority badges
- [ ] Create work order works
- [ ] Resolve order: status updates, resolved_at timestamp set
- [ ] Escalate order: status changes, room marked `out_of_order`
- [ ] Filter by priority and status works

## 📦 Lost & Found (Dev 6 verifies)

- [ ] Can log a found item with room and description
- [ ] Can link item to a guest
- [ ] Claim function updates status

## 📊 Dashboard (Dev 7 verifies)

- [ ] Dashboard shows today's reservation count
- [ ] Dashboard shows room availability summary
- [ ] Dashboard shows pending HK tasks count
- [ ] Dashboard shows today's revenue
- [ ] Upcoming check-ins table shows correct data
- [ ] Upcoming check-outs table shows correct data

## 📈 Reports (Dev 7 verifies)

- [ ] Reports menu page loads with all report links
- [ ] Occupancy report with date filter works
- [ ] Revenue report with date filter works
- [ ] Audit log displays with filtering (by user, action, date)
- [ ] Audit log is read-only (no edit/delete)

## 🎨 Frontend / UI (Dev 7 verifies)

- [ ] All pages use the shared Bootstrap layout
- [ ] Navbar shows correct links based on user role
- [ ] Navbar shows logged-in user name
- [ ] Status badges use correct colors everywhere
- [ ] Forms have Bootstrap validation styling
- [ ] Delete actions show confirmation dialog
- [ ] No broken links or 404 pages
- [ ] Pages are responsive (work on mobile)
- [ ] No raw PHP errors displayed to users

## 🔍 Code Quality (All verify)

- [ ] All SQL queries use prepared statements (no SQL injection)
- [ ] All passwords stored with `password_hash()`
- [ ] No hardcoded database credentials (use `config.php` constants)
- [ ] No `var_dump()` or `print_r()` left in production code
- [ ] No commented-out debug code
- [ ] Commit messages are clear and prefixed with developer number
- [ ] All branches merged into `develop`
- [ ] `develop` merged into `main`

## 📄 Final Submission

- [ ] README.md exists in project root with setup instructions
- [ ] Database can be imported from `database.sql` on a clean machine
- [ ] Project runs on a fresh XAMPP/WAMP installation
- [ ] All team members' names listed in project documentation
- [ ] Project folder is clean (no temp files, no node_modules)
