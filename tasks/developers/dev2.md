# 🏨 Developer 2 — Mission File --> mariam sa3ed (from 27 to 28)
# Room Management & Room Types

---

## Your Main Responsibility

You own everything related to **hotel rooms**: room types (Standard, Deluxe, Suite), individual rooms, room status management (the 6-state machine), and room availability searching.

## Why Your Work Is Important

Rooms are the **core product**. Without working rooms, Dev 4 cannot assign rooms to reservations, Dev 6 cannot assign HK tasks, and Dev 5 cannot calculate billing from room rates.

## Start Day / Time
📅 **Day 2, Morning** — After Dev 1 merges core auth.

## Deadline
📅 **Day 3, End of Day** — Rooms module merged into `develop`.

## Estimated Hours Needed
~21 hours total

## Git Branch
```
feature/rooms
```

## Files You Own
- `app/models/Room.php`
- `app/models/RoomType.php`
- `app/controllers/RoomsController.php`
- `app/views/rooms/index.php`
- `app/views/rooms/show.php`
- `app/views/rooms/create.php`
- `app/views/rooms/edit.php`

---

## Functions You Must Implement

### `app/models/RoomType.php` (6 functions):
| Function | What It Does |
|---|---|
| `all()` | SELECT all room types ordered by base_price |
| `find($id)` | SELECT one room type by ID |
| `create($data)` | INSERT new room type |
| `update($id, $data)` | UPDATE room type fields |
| `delete($id)` | DELETE room type (only if no rooms use it) |
| `rooms()` | SELECT all rooms of this type |

### `app/models/Room.php` (13 functions):
| Function | What It Does |
|---|---|
| `all()` | SELECT all rooms JOIN room_types for type_name |
| `find($id)` | SELECT one room by ID with type info |
| `create($data)` | INSERT new room, default status='available' |
| `update($id, $data)` | UPDATE room fields |
| `delete($id)` | DELETE room (only if no active reservations) |
| `roomType()` | Return room_type record |
| `reservations()` | Return all reservations for this room |
| `housekeepingTasks()` | Return HK tasks for this room |
| `maintenanceOrders()` | Return maintenance orders for this room |
| `updateStatus($newStatus)` | Validate + update room status (state machine) |
| `findAvailable($checkIn, $checkOut, $typeId)` | Rooms NOT reserved during given dates |
| `suggestUpgrade($currentRoomId)` | Find higher-tier available room |

### `app/controllers/RoomsController.php` (8 functions):
| Function | What It Does |
|---|---|
| `index()` | Load all rooms → rooms/index view |
| `show($id)` | Load room details → rooms/show view |
| `create()` | Load room types for dropdown → rooms/create view |
| `store()` | Validate POST → `Room::create()` → redirect |
| `edit($id)` | Load room + types → rooms/edit view |
| `update($id)` | Validate POST → `Room::update()` → redirect |
| `delete($id)` | `Room::delete()` → redirect |
| `updateStatus($id)` | Read new status from POST → `Room::updateStatus()` |

## Requirements Covered
- (1) Dynamic Room-Allocation Engine → `findAvailable()`
- (3) Automatic Room-Upgrade Logic → `suggestUpgrade()`
- (23) Real-Time Room Status State Machine → `updateStatus()`

## Dependencies
| What | From | When |
|---|---|---|
| Database + core framework | Dev 1 | Day 2 morning |
| Login system | Dev 1 | Day 2 morning |

## Developers Waiting For You
| Dev | Needs | When |
|---|---|---|
| Dev 4 | `findAvailable()`, `updateStatus()` | Day 3 |
| Dev 6 | Room model for HK tasks | Day 4 |
| Dev 7 | Room counts for dashboard | Day 5 |

---

## Step By Step Work Plan

**Step 1** (Day 2, 30 min): Pull develop, create branch, verify DB tables exist.

**Step 2** (Day 2, 3 hrs): Implement RoomType model — all(), find(), create(), update(), delete().

**Step 3** (Day 2, 4 hrs): Implement Room model CRUD — all() needs JOIN with room_types to show type_name. find() also needs JOIN. create() with default status='available'.

**Step 4** (Day 2 evening, 3 hrs): Implement `updateStatus()` — validate transitions:
- available → occupied (check-in)
- occupied → dirty (check-out)
- dirty → cleaning (HK starts)
- cleaning → inspecting (HK done)
- inspecting → available (approved)
- Any → out_of_order (escalation)
- out_of_order → available (resolved)

**Step 5** (Day 3 morning, 3 hrs): Implement `findAvailable()` — rooms NOT in any overlapping reservation. Date overlap: `existing.check_in < new.check_out AND existing.check_out > new.check_in`. Exclude cancelled/no_show/checked_out reservations.

**Step 6** (Day 3, 6 hrs): Build RoomsController + all 4 views. Use Bootstrap tables, colored status badges (available=green, occupied=blue, dirty=red, cleaning=yellow, out_of_order=gray).

**Step 7** (Day 4, 2 hrs): Implement `suggestUpgrade()` — find higher-price room types with available rooms.

**Step 8**: Add seed data — 3 room types + 6-8 rooms.

## Day By Day Schedule
| Day | Task |
|---|---|
| Day 1 | Study models. Draft SQL. Set up environment. |
| Day 2 | Implement RoomType CRUD, Room CRUD, updateStatus(). Seed data. |
| Day 3 | Implement findAvailable(). Build controller + views. **Merge to develop.** |
| Day 4 | suggestUpgrade(). Help Dev 4. Fix bugs. |
| Day 5-6 | Integration testing. |
| Day 7 | Bug fixes. |

## Implementation Guidance

**State machine**: Before updating status, check if the transition is valid. Keep an array of allowed transitions and verify against it.

**findAvailable() overlap logic**: Two date ranges overlap when `start1 < end2 AND start2 < end1`. Exclude rooms matching this condition.

**suggestUpgrade()**: Get current room's type base_price. Find types with higher price. Check availability for those types during same dates.

## What To Test Before Merge
- [ ] RoomType CRUD works
- [ ] Room CRUD works with type names showing
- [ ] Status badges show correct colors
- [ ] updateStatus() rejects invalid transitions
- [ ] findAvailable() excludes reserved rooms correctly
- [ ] Seed data present

## Risks To Avoid
1. Don't forget JOIN in all() — rooms without type names are useless
2. Don't allow invalid status transitions
3. Date overlap logic is tricky — test edge cases
4. Don't modify layouts/main.php — Dev 7's file
5. Always use prepared statements

## Definition of Done
- [ ] All Room + RoomType methods implemented
- [ ] Status state machine validates transitions
- [ ] findAvailable() works correctly
- [ ] All 4 views render with Bootstrap
- [ ] Seed data exists
- [ ] Branch merged into develop

## Handover Notes
Tell Dev 4, Dev 6, Dev 7:
1. `$room->findAvailable($checkIn, $checkOut, $typeId)` returns available rooms
2. `$room->updateStatus('occupied')` validates transitions internally
3. Status values: available, occupied, dirty, cleaning, inspecting, out_of_order
4. `$room->find($id)` includes type_name and base_price
