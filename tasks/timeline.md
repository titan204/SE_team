# 📅 Timeline — 7-Day Development Schedule

## Overview

| Day | Focus | Key Milestone |
|---|---|---|
| Day 1 | Foundation | DB imported, core verified, auth started |
| Day 2 | Core + Parallel Start | Auth done, Rooms & Guests started |
| Day 3 | Module Development | Rooms & Guests done, Reservations started |
| Day 4 | Module Development | Reservations working, Billing/HK/Dashboard started |
| Day 5 | Module Completion | All modules individually complete |
| Day 6 | Integration | Full system integration + testing |
| Day 7 | Polish & Submit | Bug fixes, final testing, submission |

---

## Day 1 (Monday) — Foundation

| Developer | Task | Deliverable |
|---|---|---|
| **Dev 1** | Import `database.sql` into MySQL, verify all tables created. Test `Database.php` connection. Verify `Router.php` dispatches correctly. Begin `AuthController` login/logout. | DB running, routing works |
| **Dev 2** | Study Room/RoomType models. Plan SQL queries. Set up local environment. | Environment ready, queries drafted |
| **Dev 3** | Study Guest/GuestPreference models. Plan SQL queries. Set up local environment. | Environment ready, queries drafted |
| **Dev 4** | Study Reservation model. Map out the check-in/check-out workflow on paper. | Workflow documented |
| **Dev 5** | Study Folio/FolioCharge/Payment models. Understand the billing flow. | Billing flow documented |
| **Dev 6** | Study HousekeepingTask/MaintenanceOrder/LostAndFound models. | Task flow documented |
| **Dev 7** | Study layout template. Plan dashboard widgets. Draft CSS improvements. | UI plan ready |

### ✅ Day 1 Checkpoint (End of Day):
- [ ] Database imported and verified (Dev 1)
- [ ] `public/index.php` loads without errors (Dev 1)
- [ ] All devs have local environment running
- [ ] All devs have created their Git branches

---

## Day 2 (Tuesday) — Auth Complete + Parallel Start

| Developer | Task | Deliverable |
|---|---|---|
| **Dev 1** | Complete `AuthController` (login, doLogin, logout). Implement `User::authenticate()` and `User::findByEmail()`. Session management working. Test login flow end-to-end. | ✅ Login/logout fully working |
| **Dev 2** | Implement `RoomType` model CRUD (all, find, create, update, delete). Implement `Room` model CRUD (all, find, create, update, delete). | Models return real data |
| **Dev 3** | Implement `Guest` model CRUD (all, find, create, update, delete). Implement `GuestPreference` model CRUD. | Models return real data |
| **Dev 4** | Wait for Dev 2 + Dev 3. Start implementing `Reservation::all()` and `Reservation::find()` using static test data if needed. | Basic queries ready |
| **Dev 5** | Wait for Dev 4. Study folio schema deeply. Draft all SQL queries on paper. | All queries drafted |
| **Dev 6** | Wait for Dev 2. Study housekeeping schema. Draft all SQL queries. | All queries drafted |
| **Dev 7** | Implement `app/views/layouts/main.php` fully — working navbar with session-aware links. Style the login page view. Add base CSS. | Layout + login page polished |

### ✅ Day 2 Checkpoint (End of Day):
- [ ] Can log in and log out successfully (Dev 1)
- [ ] Session shows user name and role in navbar (Dev 1 + Dev 7)
- [ ] `RoomType` and `Room` models tested with real data (Dev 2)
- [ ] `Guest` model tested with real data (Dev 3)
- [ ] **Dev 1 merges `feature/core-auth` into `develop`**

---

## Day 3 (Wednesday) — Module Development

| Developer | Task | Deliverable |
|---|---|---|
| **Dev 1** | Implement `Role` model CRUD. Implement `User` model full CRUD (all, find, create, update, delete). Build `UsersController` fully (index, create, store, edit, update, delete). Build users views (index, create, edit). | Staff management working |
| **Dev 2** | Build `RoomsController` fully (index, show, create, store, edit, update, delete, updateStatus). Build rooms views (index, show, create, edit). Implement `Room::updateStatus()` state machine. Implement `Room::findAvailable()`. | ✅ Rooms module fully working |
| **Dev 3** | Build `GuestsController` fully (index, show, create, store, edit, update, delete, blacklist, anonymize, flagVip). Build guests views (index, show, create, edit). Implement loyalty tier calculation. | ✅ Guests module fully working |
| **Dev 4** | Pull latest `develop` (now has auth + rooms + guests). Implement `Reservation` model full CRUD. Implement `Reservation::findByDateRange()`, `findByStatus()`, `findByGuest()`. | Reservation queries working |
| **Dev 5** | Continue drafting. Start `Folio::create()` and `Folio::findByReservation()`. | Folio model started |
| **Dev 6** | Pull latest `develop`. Start `HousekeepingTask` model CRUD. Start `MaintenanceOrder` model CRUD. | HK model started |
| **Dev 7** | Build `app/views/auth/login.php` with full Bootstrap form. Start `app/views/dashboard/index.php` layout (stat cards, tables). Add CSS for status badges, cards. | Login + Dashboard UI ready |

### ✅ Day 3 Checkpoint (End of Day):
- [ ] Can create, view, edit, delete rooms in the browser (Dev 2)
- [ ] Can create, view, edit, delete guests in the browser (Dev 3)
- [ ] Room status changes work (Dev 2)
- [ ] Guest VIP flagging and blacklist work (Dev 3)
- [ ] **Dev 2 merges `feature/rooms` into `develop`**
- [ ] **Dev 3 merges `feature/guests` into `develop`**

---

## Day 4 (Thursday) — Reservations + Downstream Start

| Developer | Task | Deliverable |
|---|---|---|
| **Dev 1** | Build users views fully. Implement RBAC checks (`requireRole`) in UsersController. Test role-based access. Help others with bugs. | ✅ Staff management complete |
| **Dev 2** | Help Dev 4 with room availability queries. Fix any room bugs. Implement `Room::suggestUpgrade()`. | Room module polished |
| **Dev 3** | Help Dev 4 with guest lookups. Fix guest bugs. Implement `Guest::calculateLifetimeValue()`, `Guest::referrals()`. | Guest CRM polished |
| **Dev 4** | Build `ReservationsController` fully (index, show, create, store, edit, update, delete, checkin, checkout, noshow). Build reservations views (index, show, create, edit). Implement `Reservation::confirm()`, `checkIn()`, `checkOut()`, `cancel()`, `markNoShow()`. | ✅ Reservations core working |
| **Dev 5** | Pull latest. Implement `Folio` model full CRUD. Implement `FolioCharge` model CRUD. Implement `Payment` model CRUD. Start `BillingController`. | Billing models working |
| **Dev 6** | Pull latest. Implement `HousekeepingTask` full CRUD. Build `HousekeepingController` (index, show, create, store, complete, minibar). Build housekeeping views. | HK tasks working |
| **Dev 7** | Implement `DashboardController::index()` with real statistics queries. Wire dashboard view to show real data. Start `AuditLog` model. | Dashboard shows real data |

### ✅ Day 4 Checkpoint (End of Day):
- [ ] Can create a reservation, assign guest + room (Dev 4)
- [ ] Can check-in and check-out a reservation (Dev 4)
- [ ] Room status auto-updates on check-in/check-out (Dev 4 + Dev 2)
- [ ] **Dev 4 merges `feature/reservations` into `develop`**

---

## Day 5 (Friday) — Module Completion

| Developer | Task | Deliverable |
|---|---|---|
| **Dev 1** | **Integration support role.** Help debug other devs' issues. Verify auth + sessions work across all modules. Ensure RBAC is enforced correctly everywhere. | System-wide auth verified |
| **Dev 2** | Integration testing for rooms. Verify rooms work with reservations flow. Fix any bugs. | ✅ Rooms fully integrated |
| **Dev 3** | Integration testing for guests. Verify guest data appears correctly in reservations + billing. Implement `Guest::anonymize()` for GDPR. | ✅ Guests fully integrated |
| **Dev 4** | Implement group booking logic (`findGroupReservations`, `is_group` handling). Implement `checkEarlyCheckInEligibility()`. Polish reservation views with status badges. | ✅ Reservations fully complete |
| **Dev 5** | Build `BillingController` fully (index, show, addCharge, payment, invoice, refund, splitBill). Build billing views (index, show, invoice, split). Implement `Folio::recalculateTotal()`, `Folio::settle()`. Implement `Payment::processRefund()`. | ✅ Billing fully working |
| **Dev 6** | Implement `MaintenanceOrder` full CRUD. Build `MaintenanceController` (index, show, create, store, resolve, escalate). Build maintenance views. Implement `LostAndFound` model + basic UI. Wire HK task completion to room status update. | ✅ Operations fully working |
| **Dev 7** | Implement `ReportsController` (occupancy, revenue, audit). Build report views. Implement `AuditLog` model CRUD. Implement `Feedback` model CRUD. Polish all CSS. | ✅ Reports fully working |

### ✅ Day 5 Checkpoint (End of Day):
- [ ] All 7 modules individually working
- [ ] **Dev 5 merges `feature/billing` into `develop`**
- [ ] **Dev 6 merges `feature/housekeeping` into `develop`**
- [ ] **Dev 7 merges `feature/dashboard` into `develop`**
- [ ] **Dev 1 merges `feature/core-auth` final updates into `develop`**

---

## Day 6 (Saturday) — Integration Day

| Developer | Task | Deliverable |
|---|---|---|
| **ALL** | Pull latest `develop`. Full integration testing. | All code merged |
| **Dev 1** | Test login → dashboard → all modules flow. Verify session persistence. | Auth flow verified |
| **Dev 2** | Test: Create room → Reserve it → Check-in → Room becomes occupied → Check-out → Room becomes dirty → HK cleans → Room available | Room lifecycle verified |
| **Dev 3** | Test: Create guest → Make reservation → Check-out → Lifetime value updates → Loyalty tier updates → View guest profile | Guest CRM flow verified |
| **Dev 4** | Test: Full reservation workflow including no-show and cancellation. Test group bookings. | Reservation flows verified |
| **Dev 5** | Test: Reservation → Auto-create folio → Add charges → Make payment → Settle folio → Print invoice | Billing flow verified |
| **Dev 6** | Test: Check-out creates HK task → HK completes → Room available. Test maintenance escalation → Room out of order. | Operations flow verified |
| **Dev 7** | Test: Dashboard shows correct stats. Reports filter correctly. Audit log records all actions. Verify all pages look good. | UI/Reports verified |

### ✅ Day 6 Checkpoint (End of Day):
- [ ] Complete system flow works end-to-end
- [ ] No broken pages or PHP errors
- [ ] All roles (manager, front_desk, housekeeper) tested
- [ ] `develop` branch is stable

---

## Day 7 (Sunday) — Polish & Submit

| Developer | Task | Deliverable |
|---|---|---|
| **ALL** | Bug fixes from Day 6 testing | All bugs fixed |
| **Dev 1** | Final database cleanup. Add any missing seed data. | Clean DB |
| **Dev 2 + Dev 3** | Final data validation in forms. Test edge cases. | Forms validated |
| **Dev 4 + Dev 5** | Test billing accuracy. Verify totals are correct. | Numbers verified |
| **Dev 6** | Test HK + Maintenance edge cases. | Edge cases handled |
| **Dev 7** | Final CSS polish. Responsive testing. Screenshots for documentation. | UI polished |
| **ALL (2:00 PM)** | Merge `develop` → `main`. Final smoke test. | ✅ PROJECT SUBMITTED |

### ✅ Day 7 Final Checkpoint:
- [ ] `main` branch has all code
- [ ] Database imports cleanly
- [ ] Login works with seeded user
- [ ] All CRUD operations work
- [ ] Reservation full workflow works
- [ ] Billing calculates correctly
- [ ] Reports display data
- [ ] No PHP errors or warnings
- [ ] All pages render correctly
