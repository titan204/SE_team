# 📊 Developer 7 — Mission File --> mohmoud didamon (from 2 to 3 )
# Dashboard, Reports, Audit Log & Frontend Polish

---

## Your Main Responsibility

You own the **dashboard** (main landing page with live statistics), **reports** (occupancy, revenue, audit log), the **feedback** and **audit log** models, the **shared layout template**, and **all CSS/JS assets**. You are the visual face of the system — every page the user sees passes through your layout.

## Why Your Work Is Important

The dashboard is the **first thing staff see after login** — it must show useful real-time data. Reports give managers the analytics they need to run the hotel. And your layout/CSS work affects **every single page** in the system. If the UI looks broken, the whole project looks broken regardless of how well the backend works.

## Start Day / Time
📅 **Day 2, Morning** — You start the layout and login page styling on Day 2 (same time as Dev 2 and Dev 3). Dashboard data wiring starts Day 4 after other modules have data.

## Deadline
📅 **Day 5, End of Day** — Dashboard + Reports merged into `develop`.
📅 **Day 7** — Final UI polish complete.

## Estimated Hours Needed
- Layout + login page styling: **4 hours**
- Dashboard controller + view with statistics: **6 hours**
- Reports (3 report pages): **6 hours**
- AuditLog + Feedback models: **3 hours**
- CSS polish + JS functions: **4 hours**
- **Total: ~23 hours**

## Git Branch
```
feature/dashboard
```

## Files You Own

### Model Files:
- `app/models/AuditLog.php`
- `app/models/Feedback.php`

### Controller Files:
- `app/controllers/DashboardController.php`
- `app/controllers/ReportsController.php`

### View Files:
- `app/views/layouts/main.php` ⚠️ **SHARED LAYOUT — only YOU modify this**
- `app/views/auth/login.php` ⚠️ **Login page styling**
- `app/views/dashboard/index.php`
- `app/views/reports/index.php`
- `app/views/reports/occupancy.php`
- `app/views/reports/revenue.php`
- `app/views/reports/audit.php`

### Asset Files:
- `public/assets/css/style.css` ⚠️ **Only YOU modify CSS**
- `public/assets/js/app.js` ⚠️ **Only YOU modify main JS**

**Total: 14 files**

---

## Functions You Must Implement

### `app/models/AuditLog.php` (7 functions):
| Function | What It Does |
|---|---|
| `all()` | SELECT all audit logs ORDER BY created_at DESC. JOIN users for user name. |
| `find($id)` | SELECT one log entry |
| `create($data)` | INSERT log (user_id, action, target_type, target_id, old_value, new_value) |
| `findByUser($userId)` | SELECT logs WHERE user_id = ? |
| `findByAction($action)` | SELECT logs WHERE action = ? |
| `findByTarget($type, $id)` | SELECT logs WHERE target_type=? AND target_id=? |
| `log($userId, $action, $targetType, $targetId, $oldVal, $newVal)` | Static convenience method — shortcut for create() |

> **Note:** `update()` and `delete()` should remain empty or throw errors — audit logs are **immutable**.

### `app/models/Feedback.php` (7 functions):
| Function | What It Does |
|---|---|
| `all()` | SELECT all feedback JOIN guests + reservations |
| `find($id)` | SELECT one feedback entry |
| `create($data)` | INSERT feedback (reservation_id, guest_id, rating, comments) |
| `update($id, $data)` | UPDATE feedback |
| `delete($id)` | DELETE feedback |
| `reservation()` | Return parent reservation |
| `guest()` | Return the guest who wrote this |
| `averageRating()` | SELECT AVG(rating) FROM feedback |

### `app/controllers/DashboardController.php` (1 function):
| Function | What It Does |
|---|---|
| `index()` | Require login. Query statistics from multiple tables. Pass to dashboard view. |

**Statistics to query:**
1. Total reservations today: `SELECT COUNT(*) FROM reservations WHERE check_in_date = CURDATE()`
2. Rooms by status: `SELECT status, COUNT(*) FROM rooms GROUP BY status`
3. Pending HK tasks: `SELECT COUNT(*) FROM housekeeping_tasks WHERE status='pending'`
4. Revenue today: `SELECT SUM(amount) FROM payments WHERE DATE(processed_at) = CURDATE()`
5. Upcoming check-ins (next 24h): `SELECT r.*, g.name FROM reservations r JOIN guests g ... WHERE check_in_date = CURDATE() AND status IN ('pending','confirmed')`
6. Upcoming check-outs (next 24h): `SELECT r.*, g.name FROM reservations r JOIN guests g ... WHERE check_out_date = CURDATE() AND status = 'checked_in'`
7. VIP arrivals today: `SELECT g.name FROM guests g JOIN reservations r ... WHERE g.is_vip=1 AND r.check_in_date = CURDATE()`

### `app/controllers/ReportsController.php` (4 functions):
| Function | What It Does |
|---|---|
| `index()` | Require manager role. Show reports menu → reports/index |
| `occupancy()` | Require manager. Calculate occupancy by date range → reports/occupancy |
| `revenue()` | Require manager. Aggregate revenue by date range → reports/revenue |
| `audit()` | Require manager. Load audit logs with filters → reports/audit |

**Occupancy Report queries:**
- Total rooms: `SELECT COUNT(*) FROM rooms WHERE status != 'out_of_order'`
- Occupied rooms in date range: `SELECT COUNT(DISTINCT room_id) FROM reservations WHERE status='checked_in' AND check_in_date <= ? AND check_out_date >= ?`
- Occupancy rate = (occupied / total) × 100
- Breakdown by room type: GROUP BY room_type_id

**Revenue Report queries:**
- Total revenue: `SELECT SUM(amount) FROM payments WHERE processed_at BETWEEN ? AND ?`
- Revenue by charge type: `SELECT charge_type, SUM(amount) FROM folio_charges WHERE posted_at BETWEEN ? AND ? GROUP BY charge_type`
- Daily breakdown: `SELECT DATE(processed_at), SUM(amount) FROM payments GROUP BY DATE(processed_at)`

---

## Requirements Covered
- (39) System Audit Trail → AuditLog model
- (21) Post-Stay Feedback Loop → Feedback model
- (38) Role-Based Access Control (report access) → requireRole('manager')
- (11) Check-Out Queue Optimizer → upcoming check-outs on dashboard
- (14) Automated VIP Flagging (display) → VIP alerts on dashboard

## Dependencies
| What | From | When |
|---|---|---|
| Auth system (session, login page) | Dev 1 | Day 2 morning |
| Room data for dashboard stats | Dev 2 | Day 3 |
| Guest data for VIP alerts | Dev 3 | Day 3 |
| Reservation data for dashboard tables | Dev 4 | Day 4 |
| Payment/Folio data for revenue | Dev 5 | Day 5 |
| HK task counts for dashboard | Dev 6 | Day 5 |

## Developers Waiting For You
| Dev | Needs | When |
|---|---|---|
| **ALL** | Working layout template (navbar, footer, Bootstrap) | Day 2 |
| **ALL** | CSS classes for status badges | Day 3 |
| Dev 1 | Login page styled | Day 2 |

---

## Step By Step Work Plan

**Step 1** (Day 2, 4 hrs): Build the shared layout and login page.
- `app/views/layouts/main.php`: Full HTML5 document with:
  - Bootstrap 5 CDN (CSS + JS)
  - Bootstrap Icons CDN
  - Custom CSS link to style.css
  - Navbar with conditional links (show/hide based on session)
  - Main content area using `<?= $content ?? '' ?>`
  - Footer with copyright
  - Custom JS link to app.js
- `app/views/auth/login.php`: Centered Bootstrap card with email/password form
- Make navbar session-aware: if `$_SESSION['user_id']` exists, show all links + logout. If not, show only login link.
- Show/hide "Staff" and "Reports" links only for manager role.

**Step 2** (Day 2-3, 2 hrs): Write base CSS.
- Status badge classes: `.badge-available`, `.badge-occupied`, `.badge-dirty`, etc.
- Card shadows, table header styling, form improvements
- Responsive adjustments
- Footer always at bottom (sticky footer)

**Step 3** (Day 3, 2 hrs): Write JS function stubs with basic implementations.
- `validateLoginForm()`: Check email format, password not empty
- `confirmDelete(entityName)`: Show confirm dialog
- `confirmCheckOut(id)`: Show confirm before checkout
- `formatCurrency(amount)`: Format as $X.XX
- `formatDate(dateString)`: Format for display
- DOMContentLoaded: Initialize Bootstrap tooltips

**Step 4** (Day 4, 3 hrs): Implement AuditLog model.
- Simple CRUD but `update()` and `delete()` should do nothing (immutable)
- `log()` static method: create a new AuditLog instance, call create() with the provided data. This is the convenience method other devs call.

**Step 5** (Day 4, 2 hrs): Implement Feedback model.
- Standard CRUD + `averageRating()` using `SELECT AVG(rating)`

**Step 6** (Day 4-5, 6 hrs): Build DashboardController + view.
- Run all 7 statistics queries (listed above)
- Pass all data to the dashboard view
- Dashboard view layout:
  - Row 1: 4 stat cards (Reservations Today, Rooms Available, Pending HK, Revenue Today)
  - Row 2: Upcoming Check-ins table (left) + Upcoming Check-outs table (right)
  - Row 3: VIP arrival alerts (if any)
- Use Bootstrap cards with colored headers for stat cards
- Since some tables might have no data early on, handle empty states gracefully

**Step 7** (Day 5, 6 hrs): Build ReportsController + all 3 report views.
- All report methods require `$this->requireRole('manager')`
- Reports menu page: 3 cards with icons linking to each report
- Occupancy report: date range picker + results table showing occupancy %
- Revenue report: date range picker + total + breakdown by charge type
- Audit log: table with User, Action, Target, Old Value, New Value, Timestamp. Filter by user, action, date.

**Step 8** (Day 6-7): Final CSS polish across all pages.
- Visit every page and ensure consistent styling
- Fix any layout issues
- Ensure responsive design works
- Add any missing Bootstrap classes

## Day By Day Schedule
| Day | Task |
|---|---|
| Day 1 | Study layout needs. Plan dashboard widgets. Draft CSS. |
| Day 2 | Build layouts/main.php (navbar, footer). Style login page. Write base CSS. |
| Day 3 | Write JS functions. Polish CSS. Add status badge classes. |
| Day 4 | Implement AuditLog + Feedback models. Start DashboardController with real queries. |
| Day 5 | Finish dashboard. Build ReportsController + 3 report views. **Merge to develop.** |
| Day 6 | Visit every page. Fix styling. Ensure consistency. Integration testing. |
| Day 7 | Final CSS polish. Responsive testing. Bug fixes. |

## Implementation Guidance

**Dashboard Statistics (conceptual)**:
- You're running queries directly against multiple tables — this is fine for a dashboard.
- Use `$this->model('Room')` won't give you aggregate counts easily. Instead, use `Database::getConnection()` directly in the DashboardController to run raw aggregate queries. This is acceptable for dashboards.
- Example approach in `DashboardController::index()`:
  ```
  $db = Database::getConnection();
  
  // Count today's reservations
  $stmt = $db->prepare("SELECT COUNT(*) as count FROM reservations WHERE check_in_date = CURDATE()");
  $stmt->execute();
  $todayReservations = $stmt->fetch()['count'];
  
  // ... repeat for each stat ...
  
  $this->view('dashboard/index', [
      'todayReservations' => $todayReservations,
      'roomStats' => $roomStats,
      // etc.
  ]);
  ```

**Navbar Session-Awareness**:
```
In the navbar, use PHP conditions:
- if $_SESSION['user_id'] is set → show nav links + logout
- if $_SESSION['user_role'] === 'manager' → show Staff + Reports links
- Show current user name from $_SESSION['user_name']
```

**Report Date Filtering**: Read `$_GET['from']` and `$_GET['to']` parameters. Default to current month if not provided. Pass dates to your queries as prepared statement parameters.

**Audit Log Display**: Show old_value and new_value side by side. For long values, truncate in the table and show full value on hover (Bootstrap tooltip) or in a modal.

## What To Test Before Merge
- [ ] Layout renders correctly on all pages (navbar, footer, responsive)
- [ ] Navbar shows correct links based on role
- [ ] Login page is styled and functional
- [ ] Dashboard shows all 7 statistics correctly
- [ ] Dashboard handles zero data gracefully (no PHP errors)
- [ ] Reports menu page renders
- [ ] Occupancy report with date filter works
- [ ] Revenue report with date filter works
- [ ] Audit log with filters works
- [ ] CSS status badges display correct colors
- [ ] JS confirmDelete() works on all delete buttons
- [ ] Pages look good on mobile (responsive)

## Risks To Avoid
1. **Layout changes affect EVERYONE** — test thoroughly before pushing
2. **Dashboard queries might fail if other modules aren't merged yet** — handle empty results gracefully, don't let the page crash
3. **Reports must check for manager role** — non-managers must be denied
4. **Don't break the navbar** — if you change a link, test all pages
5. **CSS changes can break other pages** — use specific selectors, not broad ones
6. **Don't add external JS libraries** — vanilla JS only per project rules

## Definition of Done
- [ ] Layout template works with navbar, footer, session awareness
- [ ] Login page styled with Bootstrap card
- [ ] Dashboard shows real-time statistics from database
- [ ] All 3 reports work with date filtering
- [ ] AuditLog model is immutable (no update/delete)
- [ ] Feedback model works with averageRating()
- [ ] CSS is clean and consistent across all pages
- [ ] JS validation functions work
- [ ] All pages responsive on mobile
- [ ] Branch merged into develop

## Handover Notes
Tell ALL developers:
1. **Layout**: Every view file should set `$pageTitle` before including the layout
2. **To use status badges**: Add class `badge-available`, `badge-occupied`, etc. on `<span class="badge">`
3. **To trigger audit log**: Call `AuditLog::log($userId, 'action', 'target_type', $targetId, $oldVal, $newVal)` from your controller after any important change
4. **Delete confirmations**: Add `onclick="return confirmDelete('guest')"` to delete buttons
5. **If you need a new CSS class**: Ask Dev 7 — don't write inline styles
