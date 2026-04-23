# 🗺️ Master Plan — Hotel Management System

## Team Overview

| Dev | Role | Module | Priority |
|---|---|---|---|
| **Dev 1** | Core & Auth Lead | Database setup, Core framework, Authentication | 🔴 CRITICAL — starts first |
| **Dev 2** | Room Management | Room types, Room CRUD, Room status state machine | 🟠 HIGH |
| **Dev 3** | Guest Management | Guest CRUD, Preferences, CRM (VIP, Loyalty, Blacklist) | 🟠 HIGH |
| **Dev 4** | Reservations Lead | Reservation CRUD, Check-in/out workflow, Group bookings | 🟠 HIGH |
| **Dev 5** | Billing & Payments | Folios, Charges, Payments, Invoices, Split bills | 🟡 MEDIUM |
| **Dev 6** | Operations | Housekeeping tasks, Maintenance orders, Lost & Found | 🟡 MEDIUM |
| **Dev 7** | Dashboard & Reports | Dashboard stats, Reports, Audit log, Frontend polish | 🟢 STANDARD |

---

## Dependency Chain

```
Day 1-2:  Dev 1 (Core + Auth + DB)
              │
              ├──────────────────┐
              ▼                  ▼
Day 2-4:  Dev 2 (Rooms)     Dev 3 (Guests)
              │                  │
              └────────┬─────────┘
                       ▼
Day 3-5:          Dev 4 (Reservations)
                       │
              ┌────────┼─────────┐
              ▼        ▼         ▼
Day 4-6:  Dev 5    Dev 6     Dev 7
         (Billing) (HK/Maint) (Dashboard)
                       │
                       ▼
Day 6-7:     Integration & Testing (ALL)
```

### What This Means:

1. **Dev 1 MUST finish core + auth by end of Day 2** — everyone else is blocked until the database is set up and login works
2. **Dev 2 and Dev 3 start on Day 2** — they can work in parallel because rooms and guests are independent
3. **Dev 4 starts on Day 3** — needs both rooms (for room assignment) and guests (for guest selection) to be working
4. **Dev 5, Dev 6, Dev 7 start on Day 4** — they depend on reservations being partially functional
5. **Days 6-7** — everyone focuses on integration testing and bug fixes

---

## Git Workflow

### Branch Strategy

```
main (production — NEVER commit directly)
  │
  └── develop (integration branch)
        │
        ├── feature/core-auth        (Dev 1)
        ├── feature/rooms            (Dev 2)
        ├── feature/guests           (Dev 3)
        ├── feature/reservations     (Dev 4)
        ├── feature/billing          (Dev 5)
        ├── feature/housekeeping     (Dev 6)
        └── feature/dashboard        (Dev 7)
```

### Git Rules

1. **Create your branch from `develop`:**
   ```
   git checkout develop
   git pull origin develop
   git checkout -b feature/your-branch
   ```

2. **Commit often with clear messages:**
   ```
   git commit -m "Dev2: Implement Room::all() with PDO query"
   git commit -m "Dev3: Add guest search by name in GuestsController"
   ```
   Always prefix commits with your Dev number.

3. **Push daily:**
   ```
   git push origin feature/your-branch
   ```

4. **Before merging, always rebase from develop:**
   ```
   git checkout develop
   git pull origin develop
   git checkout feature/your-branch
   git rebase develop
   ```

5. **Merge order follows the dependency chain** (see `merge-plan.md`)

---

## Daily Standup Process

Every day at **9:00 AM**, all 7 developers meet for 15 minutes.

Each person answers 3 questions:

1. **What did I finish yesterday?**
2. **What am I working on today?**
3. **Am I blocked by anything?**

### Critical Checkpoints

| Day | Checkpoint | Who Reviews |
|---|---|---|
| Day 2 end | Auth system working, DB imported | All team |
| Day 3 end | Rooms + Guests CRUD functional | Dev 4 verifies |
| Day 4 end | Reservations create + list working | Dev 5, 6, 7 verify |
| Day 5 end | All modules independently working | All team |
| Day 6 end | Integration test — full flow works | All team |
| Day 7 end | Final testing, bugs fixed, submitted | All team |

---

## Shared Resources — DO NOT MODIFY

These files are set up by Dev 1 and should **NOT be changed** by anyone else without team agreement:

| File | Owner | Rule |
|---|---|---|
| `config/config.php` | Dev 1 | Nobody changes this |
| `core/Database.php` | Dev 1 | Nobody changes this |
| `core/Model.php` | Dev 1 | Nobody changes this |
| `core/Controller.php` | Dev 1 | Nobody changes this |
| `core/Router.php` | Dev 1 | Nobody changes this |
| `public/index.php` | Dev 1 | Nobody changes this |
| `public/.htaccess` | Dev 1 | Nobody changes this |
| `app/views/layouts/main.php` | Dev 7 | Only Dev 7 modifies layout |
| `public/assets/css/style.css` | Dev 7 | Only Dev 7 modifies CSS |
| `public/assets/js/app.js` | Dev 7 | Only Dev 7 modifies JS |
| `database.sql` | Dev 1 | Nobody changes after Day 2 |

---

## Communication Protocol

- **Slack/WhatsApp group** for quick questions
- **If you need to modify a shared file**, post in the group and wait for approval
- **If you find a bug in someone else's code**, create a note — don't fix it yourself
- **If you are blocked**, immediately notify the team — don't wait until standup
hello this is test 