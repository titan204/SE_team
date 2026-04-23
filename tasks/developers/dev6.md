# 🧹 Developer 6 — Mission File--> mostafa A turki (from 2 to 3 )
# Housekeeping, Maintenance & Lost and Found

---

## Your Main Responsibility

You own the **operations layer**: housekeeping tasks (room cleaning, turndown, inspections, minibar), maintenance work orders (repairs, escalation), and lost & found management. You keep the physical hotel running smoothly.

## Why Your Work Is Important

Without housekeeping, rooms never become "available" again after checkout. Without maintenance, broken rooms stay broken. Your module directly feeds into room status (Dev 2's state machine) and billing (Dev 5's minibar charges).

## Start Day / Time
📅 **Day 4, Morning** — After Dev 2 (rooms) and Dev 4 (reservations) merge.

## Deadline
📅 **Day 5, End of Day** — Operations module merged into `develop`.

## Estimated Hours Needed
~20 hours total

## Git Branch
```
feature/housekeeping
```

## Files You Own
- `app/models/HousekeepingTask.php`
- `app/models/MaintenanceOrder.php`
- `app/models/LostAndFound.php`
- `app/controllers/HousekeepingController.php`
- `app/controllers/MaintenanceController.php`
- `app/views/housekeeping/index.php`
- `app/views/housekeeping/show.php`
- `app/views/housekeeping/create.php`
- `app/views/maintenance/index.php`
- `app/views/maintenance/show.php`
- `app/views/maintenance/create.php`

**Total: 11 files**

---

## Functions You Must Implement

### `app/models/HousekeepingTask.php` (13 functions):
| Function | What It Does |
|---|---|
| `all()` | SELECT all tasks JOIN rooms + users for room_number and staff name |
| `find($id)` | SELECT one task with room + staff info |
| `create($data)` | INSERT task (room_id, assigned_to, task_type, status, notes) |
| `update($id, $data)` | UPDATE task fields |
| `delete($id)` | DELETE task |
| `room()` | Return parent room |
| `assignedStaff()` | Return the housekeeper user |
| `findByRoom($roomId)` | Tasks for a specific room |
| `findByStatus($status)` | Tasks with given status (pending, in_progress, done) |
| `findByAssignee($userId)` | Tasks assigned to specific housekeeper |
| `markComplete($id, $qualityScore)` | Set status='done', quality_score, update room→'available' |
| `createTurndownTask($roomId)` | INSERT evening turndown task |
| `logMinibarConsumption($roomId, $items)` | Record items + post charge to guest's folio |

### `app/models/MaintenanceOrder.php` (10 functions):
| Function | What It Does |
|---|---|
| `all()` | SELECT all orders JOIN rooms + users |
| `find($id)` | SELECT one order with details |
| `create($data)` | INSERT order (room_id, reported_by, description, priority) |
| `update($id, $data)` | UPDATE order fields |
| `delete($id)` | DELETE order |
| `room()` | Return parent room |
| `reporter()` | Return reporting user |
| `assignedStaff()` | Return assigned user |
| `escalate($id)` | SET status='escalated', room→'out_of_order' |
| `resolve($id)` | SET status='resolved', resolved_at=NOW(), room→'available' |

### `app/models/LostAndFound.php` (7 functions):
| Function | What It Does |
|---|---|
| `all()` | SELECT all items JOIN rooms + guests |
| `find($id)` | SELECT one item |
| `create($data)` | INSERT item (room_id, found_by, description) |
| `update($id, $data)` | UPDATE item |
| `delete($id)` | DELETE item |
| `claim($id, $guestId)` | SET status='claimed', link to guest |
| `guest()` | Return linked guest |

### `app/controllers/HousekeepingController.php` (6 functions):
| Function | What It Does |
|---|---|
| `index()` | Load tasks grouped by status → housekeeping/index |
| `show($id)` | Load task details → housekeeping/show |
| `create()` | Load rooms + housekeepers for dropdowns → housekeeping/create |
| `store()` | Validate → HousekeepingTask::create() → redirect |
| `complete($id)` | Read quality score → markComplete() → update room → redirect |
| `minibar($id)` | Log consumption → post charge to folio → redirect |

### `app/controllers/MaintenanceController.php` (6 functions):
| Function | What It Does |
|---|---|
| `index()` | Load all orders → maintenance/index |
| `show($id)` | Load order details → maintenance/show |
| `create()` | Load rooms → maintenance/create |
| `store()` | Validate → MaintenanceOrder::create() → redirect |
| `resolve($id)` | Call resolve() → update room status → redirect |
| `escalate($id)` | Call escalate() → room→'out_of_order' → redirect |

## Requirements Covered
- (23) Real-Time Room Status State Machine (HK updates room status)
- (24) Maintenance Work-Order Escalation
- (25) Linen & Consumable Inventory Sync (deduct on cleaning)
- (26) Lost and Found Management
- (27) Preventative Maintenance Scheduler
- (28) Inspection Quality Scoring
- (29) Minibar Consumption Logger
- (30) Turn-Down Service Coordinator
- (31) HK-to-Front-Desk Instant Alert

## Dependencies
| What | From | When |
|---|---|---|
| Room model with updateStatus() | Dev 2 | Day 3 |
| Check-out creating HK tasks | Dev 4 | Day 4 |
| postToRoom() for minibar billing | Dev 5 | Day 4-5 |
| Auth system | Dev 1 | Day 2 |

## Developers Waiting For You
| Dev | Needs | When |
|---|---|---|
| Dev 7 | Pending HK task counts for dashboard | Day 5 |
| Dev 2 | Room status updates after HK completion | Day 5 |

---

## Step By Step Work Plan

**Step 1** (Day 4, 30 min): Pull develop, create branch, verify rooms + reservations work.

**Step 2** (Day 4, 4 hrs): Implement HousekeepingTask model CRUD.
- `all()`: JOIN rooms for room_number + users for assigned housekeeper name
- `markComplete($id, $qualityScore)`: UPDATE task SET status='done', quality_score=?. Then load the Room model and call `updateStatus('available')` on the room. This is the critical link — when HK finishes, the room becomes available again.

**Step 3** (Day 4, 3 hrs): Implement MaintenanceOrder model CRUD.
- `escalate($id)`: UPDATE status='escalated'. Load Room model, call `updateStatus('out_of_order')`.
- `resolve($id)`: UPDATE status='resolved', resolved_at=NOW(). Load Room model, call `updateStatus('available')`.

**Step 4** (Day 4 evening, 2 hrs): Implement LostAndFound model. Simple CRUD + `claim()`.

**Step 5** (Day 5, 6 hrs): Build both controllers + all 6 views.
- HK index: task board/table showing Room, Type, Status, Assigned, Quality Score. Status badges: pending=yellow, in_progress=blue, done=green, skipped=gray.
- Maintenance index: table with Priority badges: low=info, medium=warning, high=orange, critical=danger.
- Action buttons: Start, Complete (with quality score input), Resolve, Escalate.

**Step 6** (Day 5, 2 hrs): Implement minibar + turndown.
- `logMinibarConsumption()`: For each item, call Dev 5's `FolioCharge::postToRoom()` to charge the guest's folio.
- `createTurndownTask()`: INSERT a new task with type='turndown'.

## Day By Day Schedule
| Day | Task |
|---|---|
| Day 1-2 | Study HK/Maintenance models. Draft SQL. Plan room status interactions. |
| Day 3 | Wait for Dev 2+4. Prepare queries. |
| Day 4 | Pull develop. Implement all 3 models. Start controllers. |
| Day 5 | Build controllers + views. Implement minibar/turndown. **Merge to develop.** |
| Day 6-7 | Integration testing. Full checkout→HK→room available flow. |

## Implementation Guidance

**HK Task Lifecycle**:
```
checkout happens (Dev 4)
    → auto-creates HK task (status='pending', type='cleaning')
    → housekeeper sees task in their list
    → starts cleaning (status='in_progress')
    → finishes (status='done', quality_score set)
    → room status updates: dirty → cleaning → inspecting → available
```

**Minibar Flow**: Housekeeper checks minibar → logs consumed items → each item becomes a folio_charge on the guest's active folio. Use `FolioCharge::postToRoom()` from Dev 5.

**Maintenance Escalation**: When a maintenance issue is critical, escalating it sets the room to 'out_of_order', which removes it from availability. Resolving sets it back to 'available'.

**Quality Score**: Supervisors rate HK work 1-5 after inspection. This is stored in the task and can be used for performance reports.

## What To Test Before Merge
- [ ] HK task list shows tasks grouped by status
- [ ] Create HK task works
- [ ] Complete task: quality score saved, room becomes 'available'
- [ ] Maintenance order list with priority badges
- [ ] Create work order works
- [ ] Escalate: room becomes 'out_of_order'
- [ ] Resolve: room becomes 'available'
- [ ] Lost & Found: create, claim works
- [ ] Full flow: checkout → HK task created → complete → room available

## Risks To Avoid
1. **Room status update is CRITICAL** — if markComplete() doesn't update room, rooms stay dirty forever
2. **Don't modify Room.php** — use `$this->model('Room')` in your controller to load and call its methods
3. **Minibar posting requires Dev 5's model** — coordinate timing
4. **Quality score must be 1-5** — validate in controller
5. Always use prepared statements

## Definition of Done
- [ ] All HousekeepingTask, MaintenanceOrder, LostAndFound methods work
- [ ] HK completion updates room status
- [ ] Maintenance escalation/resolution updates room status
- [ ] All 6 views render with Bootstrap
- [ ] Minibar charges post to guest folio
- [ ] Branch merged into develop

## Handover Notes
Tell Dev 7:
1. `SELECT COUNT(*) FROM housekeeping_tasks WHERE status='pending'` for dashboard
2. `SELECT COUNT(*) FROM maintenance_orders WHERE status='open'` for dashboard
3. Quality scores can be averaged for staff performance reports
