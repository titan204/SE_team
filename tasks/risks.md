# ⚠️ Risks & Mitigation — Known Project Risks

## Risk Matrix

| # | Risk | Likelihood | Impact | Owner | Mitigation |
|---|---|---|---|---|---|
| R1 | Dev 1 delayed → entire team blocked | 🔴 High | 🔴 Critical | Dev 1 | Dev 1 focuses ONLY on core auth Days 1-2. No distractions. Team helps if needed. |
| R2 | Database schema change mid-week | 🟡 Medium | 🔴 Critical | Dev 1 | Schema is FROZEN after Day 2. Any change needs full team vote. |
| R3 | Two devs edit the same file | 🟡 Medium | 🟠 High | All | Strict file ownership. See `master-plan.md` shared resources table. |
| R4 | Merge conflicts on `develop` | 🟡 Medium | 🟠 High | All | Follow merge order in `merge-plan.md`. Rebase before PR. |
| R5 | Route naming conflicts | 🟢 Low | 🟡 Medium | Dev 1 | Each controller uses unique URL prefix. Router maps by first URL segment. |
| R6 | SQL injection vulnerabilities | 🟡 Medium | 🔴 Critical | All | ALL queries MUST use PDO prepared statements. Never concatenate user input. |
| R7 | Session/auth breaks across modules | 🟡 Medium | 🟠 High | Dev 1 | Dev 1 tests auth on every module during Day 5-6 integration. |
| R8 | A developer doesn't finish on time | 🟡 Medium | 🟠 High | All | Day 5 has buffer. Days 6-7 are for integration and polish only. |
| R9 | Frontend looks broken/inconsistent | 🟢 Low | 🟡 Medium | Dev 7 | Dev 7 owns ALL CSS and layout. Others use Bootstrap classes only. |
| R10 | Folio totals don't match charges | 🟡 Medium | 🟠 High | Dev 5 | Dev 5 must use database-level computed column + PHP recalculation. |

---

## Detailed Risk Analysis

### R1: Dev 1 Bottleneck (HIGHEST RISK)

**Why it's dangerous:** Every other developer depends on Dev 1's work. If login doesn't work by Day 2, the entire project is delayed.

**Prevention:**
- Dev 1 should work on auth ONLY — no side tasks
- Dev 1 starts on Day 1 morning, not afternoon
- If Dev 1 is stuck, Dev 4 or Dev 7 helps immediately
- Absolute deadline: Auth must be merged into `develop` by **Day 2, 6:00 PM**

**Fallback:** If auth is not ready, other devs temporarily bypass auth by commenting out `$this->requireLogin()` calls and using hardcoded session data for testing.

---

### R2: Database Schema Changes

**Why it's dangerous:** If someone adds/changes a column after Day 2, it breaks everyone's SQL queries and test data.

**Prevention:**
- `database.sql` is reviewed by entire team on Day 1
- After Day 2, schema is FROZEN
- If a change is truly needed, it must be:
  1. Discussed in team chat
  2. Approved by Dev 1 + affected developers
  3. Applied as a separate `.sql` migration file (e.g., `database_patch_v2.sql`)
  4. Everyone must re-import their database

---

### R3: File Ownership Violations

**Why it's dangerous:** If two people edit the same file, Git can't auto-merge PHP files well. This causes broken code and wasted hours.

**Prevention:**
- Every file has ONE owner (listed in each devX.md)
- If you need something changed in another dev's file, ask them to do it
- Exception: Each dev can add `require_once` statements in views they own to include the shared layout

**Common violations to watch for:**
- Adding navbar links → Only Dev 7
- Adding CSS classes → Only Dev 7
- Adding JS functions → Only Dev 7 (others create separate files)
- Changing `config.php` → Only Dev 1

---

### R4: Merge Conflicts

**Prevention:**
- Follow the strict merge order in `merge-plan.md`
- Always rebase from `develop` before creating a PR
- Commit frequently with small changes (not one giant commit)
- Never commit IDE/editor config files (`.vscode/`, `.idea/`)

**Add this to `.gitignore` on Day 1:**
```
.vscode/
.idea/
*.swp
*.swo
.DS_Store
Thumbs.db
```

---

### R5: Route Naming Conflicts

**Why it could happen:** Two controllers might accidentally respond to the same URL.

**Why it's LOW risk:** The Router maps the FIRST URL segment to a controller name. Since each controller has a unique name, conflicts are impossible:

| URL Prefix | Controller | Owner |
|---|---|---|
| `/auth` | AuthController | Dev 1 |
| `/dashboard` | DashboardController | Dev 7 |
| `/guests` | GuestsController | Dev 3 |
| `/rooms` | RoomsController | Dev 2 |
| `/reservations` | ReservationsController | Dev 4 |
| `/billing` | BillingController | Dev 5 |
| `/housekeeping` | HousekeepingController | Dev 6 |
| `/maintenance` | MaintenanceController | Dev 6 |
| `/users` | UsersController | Dev 1 |
| `/reports` | ReportsController | Dev 7 |

No overlaps possible.

---

### R6: SQL Injection

**This is a security risk AND a grading risk.**

**Rule:** Every single SQL query must use prepared statements:

```php
// ✅ CORRECT — always do this
$stmt = $this->db->prepare("SELECT * FROM guests WHERE id = ?");
$stmt->execute([$id]);

// ❌ WRONG — never do this
$result = $this->db->query("SELECT * FROM guests WHERE id = $id");
```

**Code review must check for this in every PR.**

---

### R7: Session Breaking Across Modules

**Why it could happen:** A developer might accidentally destroy the session, use different session variable names, or forget to call `session_start()`.

**Prevention:**
- `session_start()` is called ONCE in `public/index.php` — nobody calls it elsewhere
- Session variable names are standardized (set by Dev 1):
  - `$_SESSION['user_id']`
  - `$_SESSION['user_name']`
  - `$_SESSION['user_role']`
- Nobody creates new session variables without team agreement

---

### R8: Developer Falls Behind

**Warning signs:**
- Developer has no commits by end of their start day
- Developer can't demo their routes during checkpoint
- Developer asks basic PHP/SQL questions repeatedly

**Mitigation:**
- Pair them with a stronger developer
- Reduce their scope (move some functions to another dev)
- Use Day 6-7 buffer time for catch-up

---

### R10: Billing Calculation Errors

**Why it's dangerous:** If folio totals, tax, or payment balance don't add up correctly, the billing module is broken.

**Prevention:**
- `balance_due` is a computed column in MySQL — it auto-calculates
- Dev 5 must also recalculate in PHP when adding charges
- Always use `DECIMAL(10,2)` for money — never `FLOAT`
- Test with known amounts (e.g., room rate $100 × 3 nights = $300 total)
