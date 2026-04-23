# 📋 Tasks Folder — Team Execution Plan

## What Is This Folder?

This folder contains the **complete project management plan** for implementing the Hotel Management System. It divides the work across **7 developers** over **7 days**.

## How To Use This Folder

### For the Team Leader / Project Manager:

1. Read `master-plan.md` first — understand the full strategy
2. Read `timeline.md` — know what happens each day
3. Share each `developers/devX.md` file with the respective developer
4. Use `checklist.md` before final submission
5. Follow `merge-plan.md` when combining everyone's code
6. Monitor `risks.md` throughout the week

### For Each Developer:

1. Open **your own file** in `developers/devX.md`
2. Read your **Main Responsibility** section
3. Check your **Files Owned** — these are YOUR files, no one else touches them
4. Follow the **Functions To Implement** list one by one
5. Check **Depends On** — wait for that developer to finish before you start dependent work
6. Follow the **Git Branch** naming exactly
7. Read **What To Be Careful About** to avoid breaking teammates' work
8. Use **Implementation Guidance** for how to write the logic
9. Check off **Deliverables** when done

### For Code Reviews:

1. Follow the merge order in `merge-plan.md`
2. Review only files listed under the developer's ownership
3. Test the specific routes listed in their task file

## File Index

| File | Purpose |
|---|---|
| `master-plan.md` | Overall strategy, dependencies, Git workflow |
| `timeline.md` | Day-by-day schedule for all 7 developers |
| `merge-plan.md` | Pull request order, conflict resolution |
| `risks.md` | Known risks and how to avoid them |
| `checklist.md` | Final pre-submission checklist |
| `developers/dev1.md` | Dev 1 — Core, Auth & Database Setup |
| `developers/dev2.md` | Dev 2 — Room Management |
| `developers/dev3.md` | Dev 3 — Guest Management & CRM |
| `developers/dev4.md` | Dev 4 — Reservation System |
| `developers/dev5.md` | Dev 5 — Billing & Payments |
| `developers/dev6.md` | Dev 6 — Housekeeping & Maintenance |
| `developers/dev7.md` | Dev 7 — Dashboard, Reports & Frontend |

## Golden Rules

1. **Never edit a file you don't own** — check your `Files Owned` list
2. **Always pull from `develop` before starting work each day**
3. **Commit frequently** — at least 2-3 commits per day
4. **Test your own routes before pushing**
5. **If you're stuck for more than 30 minutes, ask the team**
