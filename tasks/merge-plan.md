# 🔀 Merge Plan — Pull Request Order & Conflict Resolution

## Merge Order (STRICT — Follow This Exactly)

Merges into `develop` happen in this exact order. Each merge must be reviewed and tested before the next one.

### Phase 1 — Foundation (End of Day 2)

| Order | Branch | Developer | Reviewer | Prerequisite |
|---|---|---|---|---|
| **1st** | `feature/core-auth` | Dev 1 | Dev 4 or Dev 7 | None — merges first |

### Phase 2 — Core Modules (End of Day 3)

| Order | Branch | Developer | Reviewer | Prerequisite |
|---|---|---|---|---|
| **2nd** | `feature/rooms` | Dev 2 | Dev 1 | Phase 1 merged |
| **3rd** | `feature/guests` | Dev 3 | Dev 1 | Phase 1 merged |

> Dev 2 and Dev 3 can merge in any order — they don't conflict.

### Phase 3 — Reservations (End of Day 4)

| Order | Branch | Developer | Reviewer | Prerequisite |
|---|---|---|---|---|
| **4th** | `feature/reservations` | Dev 4 | Dev 2 + Dev 3 | Phase 2 merged |

### Phase 4 — Downstream Modules (End of Day 5)

| Order | Branch | Developer | Reviewer | Prerequisite |
|---|---|---|---|---|
| **5th** | `feature/billing` | Dev 5 | Dev 4 | Phase 3 merged |
| **6th** | `feature/housekeeping` | Dev 6 | Dev 2 | Phase 3 merged |
| **7th** | `feature/dashboard` | Dev 7 | Dev 1 | Phase 3 merged |

> Dev 5, 6, 7 can merge in any order — they own separate files.

### Phase 5 — Final (End of Day 7)

| Order | Branch | Developer | Reviewer | Prerequisite |
|---|---|---|---|---|
| **8th** | `develop` → `main` | Dev 1 (lead) | ALL team | All features merged + tested |

---

## Pull Request Process

### Before Creating a PR:

```bash
# 1. Make sure your branch is up to date with develop
git checkout develop
git pull origin develop
git checkout feature/your-branch
git rebase develop

# 2. Fix any conflicts locally
# 3. Test your code works after rebase
# 4. Push your branch
git push origin feature/your-branch --force-with-lease
```

### PR Checklist (include in PR description):

```
## PR Checklist
- [ ] All my functions are implemented (no empty TODO methods)
- [ ] I tested all my routes in the browser
- [ ] No PHP errors or warnings
- [ ] I did not modify files outside my ownership
- [ ] I rebased from latest develop
- [ ] My reviewer has been assigned
```

### Reviewing a PR:

1. Pull the branch locally
2. Test the routes listed in the developer's task file
3. Check that no shared files were modified
4. Check for SQL injection vulnerabilities (all queries should use prepared statements)
5. Approve or request changes

---

## Code Review Assignments

| Developer | Reviews PRs From | Reason |
|---|---|---|
| Dev 1 | Dev 2, Dev 3, Dev 7 | Understands core, can verify integration |
| Dev 2 | Dev 4, Dev 6 | Rooms are used by reservations and HK |
| Dev 3 | Dev 4 | Guests are used by reservations |
| Dev 4 | Dev 5 | Reservations connect to billing |
| Dev 5 | Dev 4 | Cross-verification of reservation-billing link |
| Dev 6 | Dev 2 | HK needs rooms to work |
| Dev 7 | Dev 1 | Dashboard pulls from everything |

---

## Conflict Resolution

### If Two Developers Modified the Same File:

> This should NOT happen if everyone follows their file ownership list. But if it does:

1. **STOP** — don't force-push
2. Identify who was supposed to own the file (check `master-plan.md`)
3. The non-owner reverts their changes from that file
4. The owner's version is kept
5. If both had legitimate changes, they sit together and merge manually

### Common Conflict Points:

| File | Risk | Resolution |
|---|---|---|
| `app/views/layouts/main.php` | Dev 7 owns it, but others might add links | Only Dev 7 modifies. Others request changes via team chat. |
| `public/assets/css/style.css` | Multiple devs might want to add styles | Only Dev 7 modifies. Others use inline styles temporarily, then tell Dev 7 to add proper CSS. |
| `public/assets/js/app.js` | Multiple devs might add JS functions | Only Dev 7 modifies. Others can create separate `.js` files in `public/assets/js/` with their name (e.g., `billing.js`). |
| `database.sql` | Someone might want to add/change a column | FROZEN after Day 2. Any DB changes must go through Dev 1 and entire team approval. |

### Emergency Conflict Protocol:

1. Both developers create a backup of their version
2. Team lead (Dev 1) decides which version to keep
3. Lost changes are re-applied by hand
4. Document what happened to avoid repeating
hello this is my pc 