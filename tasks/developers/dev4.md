# 📋 Developer 4 — Mission File
# Reservation System & Front-Desk Workflow

---

## Your Main Responsibility

You own the **reservation lifecycle**: creating bookings, confirming them, checking guests in/out, handling cancellations, no-shows, group bookings, and early check-in eligibility. This is the central module that connects guests to rooms.

## Why Your Work Is Important

Reservations are the **heart of the system**. Without bookings, there's no billing (Dev 5), no housekeeping triggers (Dev 6), and no data for reports (Dev 7). Your check-in/check-out workflow drives the entire hotel operation.

## Start Day / Time
📅 **Day 3, Morning** — After Dev 2 (rooms) and Dev 3 (guests) merge.

## Deadline
📅 **Day 4, End of Day** — Reservations merged into `develop`.

## Estimated Hours Needed
~24 hours total

## Git Branch
```
feature/reservations
```

## Files You Own
- `app/models/Reservation.php`
- `app/controllers/ReservationsController.php`
- `app/views/reservations/index.php`
- `app/views/reservations/show.php`
- `app/views/reservations/create.php`
- `app/views/reservations/edit.php`

**Total: 6 files**

---

## Functions You Must Implement

### `app/models/Reservation.php` (17 functions):
| Function | What It Does |
|---|---|
| `all()` | SELECT reservations JOIN guests + rooms for names |
| `find($id)` | SELECT one reservation with guest + room info |
| `create($data)` | INSERT new reservation |
| `update($id, $data)` | UPDATE reservation fields |
| `delete($id)` | DELETE reservation |
| `guest()` | Return guest for this reservation |
| `room()` | Return room for this reservation |
| `folio()` | Return folio for this reservation |
| `confirm($id)` | SET status='confirmed' |
| `checkIn($id)` | SET status='checked_in', actual_check_in=NOW(), room→'occupied' |
| `checkOut($id)` | SET status='checked_out', actual_check_out=NOW(), room→'dirty', create HK task |
| `cancel($id)` | SET status='cancelled' |
| `markNoShow($id)` | SET status='no_show', release room |
| `findByDateRange($from, $to)` | Filter reservations by date range |
| `findByStatus($status)` | Filter by status |
| `findByGuest($guestId)` | All reservations for one guest |
| `findGroupReservations($groupId)` | All reservations with same group_id |
| `checkEarlyCheckInEligibility($id)` | Check if room is clean + available early |

### `app/controllers/ReservationsController.php` (10 functions):
| Function | What It Does |
|---|---|
| `index()` | Load reservations with filters (date, status, guest) → view |
| `show($id)` | Load reservation + guest + room + folio → view |
| `create()` | Load guests + available rooms → create form |
| `store()` | Validate → check room available → create reservation → create folio → redirect |
| `edit($id)` | Load reservation → edit form |
| `update($id)` | Validate → update → redirect |
| `delete($id)` | Cancel reservation → redirect |
| `checkin($id)` | Call confirm/checkIn → update room status → redirect |
| `checkout($id)` | Call checkOut → update room → create HK task → redirect |
| `noshow($id)` | Call markNoShow → release room → redirect |

## Requirements Covered
- (1) Dynamic Room-Allocation Engine (uses Dev 2's findAvailable)
- (2) Multi-State Reservation Workflow (pending→confirmed→checked_in→checked_out)
- (3) Automatic Room-Upgrade Logic (suggest at check-in)
- (4) Group Booking Coordinator (group_id linking)
- (5) Early Check-In Eligibility Checker
- (6) No-Show Penalty Trigger
- (7) Deposit & Pre-Authorization Manager
- (11) Check-Out Queue Optimizer (staggered windows)

## Dependencies
| What | From | When |
|---|---|---|
| Room model with findAvailable() + updateStatus() | Dev 2 | Day 3 morning |
| Guest model with all() + find() | Dev 3 | Day 3 morning |
| Auth system | Dev 1 | Day 2 |

## Developers Waiting For You
| Dev | Needs | When |
|---|---|---|
| Dev 5 | Reservation + folio created together | Day 4 |
| Dev 6 | checkOut() creates HK task | Day 4 |
| Dev 7 | Reservation counts for dashboard | Day 5 |

---

## Step By Step Work Plan

**Step 1** (Day 3, 30 min): Pull develop (now has auth + rooms + guests), create branch, verify you can load rooms and guests.

**Step 2** (Day 3, 4 hrs): Implement Reservation model CRUD.
- `all()` needs triple JOIN: reservations + guests (for name) + rooms (for room_number)
- `find($id)` needs same JOIN
- `create($data)` inserts all fields from the form
- For `create()`, also calculate total_price: room's base_price × number of nights

**Step 3** (Day 3, 4 hrs): Implement workflow methods.
- `confirm($id)`: UPDATE status='confirmed' WHERE id=?
- `checkIn($id)`: UPDATE status='checked_in', actual_check_in=NOW(). Then call Room::updateStatus('occupied')
- `checkOut($id)`: UPDATE status='checked_out', actual_check_out=NOW(). Then call Room::updateStatus('dirty'). Then INSERT a new housekeeping_task for that room with type='cleaning', status='pending'
- `cancel($id)`: UPDATE status='cancelled'. Free the room if needed.
- `markNoShow($id)`: UPDATE status='no_show'. Free the room.

**Step 4** (Day 3 evening, 3 hrs): Implement search/filter methods.
- `findByDateRange($from, $to)`: WHERE check_in_date >= ? AND check_out_date <= ?
- `findByStatus($status)`: WHERE status = ?
- `findByGuest($guestId)`: WHERE guest_id = ?
- `findGroupReservations($groupId)`: WHERE group_id = ? AND group_id IS NOT NULL

**Step 5** (Day 4, 6 hrs): Build ReservationsController + all 4 views.
- `create` view: guest dropdown, date pickers, room type filter, available rooms dropdown, adults/children inputs, special requests textarea, group booking toggle
- `index` view: table with status badges (pending=yellow, confirmed=blue, checked_in=green, checked_out=gray, cancelled=red, no_show=dark)
- `show` view: full details + action buttons (Check-In, Check-Out, Cancel, No-Show)

**Step 6** (Day 4, 2 hrs): Implement `store()` in controller.
- When creating a reservation, ALSO create a folio:
  ```
  INSERT INTO folios (reservation_id, total_amount) VALUES (?, ?)
  ```
- This ensures Dev 5 always has a folio to work with

**Step 7** (Day 5, 2 hrs): Implement group booking.
- When is_group=1, generate a group_id (can be reservation ID of first booking)
- findGroupReservations() returns all bookings in the group

**Step 8** (Day 5, 1 hr): Implement `checkEarlyCheckInEligibility($id)`:
- Get the reservation's room_id
- Check if room status is 'available' (meaning HK is done)
- If available AND current time is before standard check-in time → eligible

## Day By Day Schedule
| Day | Task |
|---|---|
| Day 1 | Study Reservation model. Map workflow on paper. |
| Day 2 | Wait for Dev 2+3. Draft SQL queries. Study folio auto-creation. |
| Day 3 | Pull develop. Implement model CRUD + workflow methods + filters. |
| Day 4 | Build controller + views. Implement store() with folio creation. **Merge to develop.** |
| Day 5 | Group bookings. Early check-in. Polish. |
| Day 6-7 | Integration testing. Full workflow testing. |

## Implementation Guidance

**Reservation Status Workflow**:
```
pending → confirmed → checked_in → checked_out
    ↓         ↓
cancelled   cancelled
    
pending → no_show (if guest doesn't arrive)
```

**Calculating total_price**: Get room's base_price from room_types. Calculate nights = check_out_date - check_in_date. total_price = base_price × nights.

**Check-in side effects**: When checking in, you must update TWO tables: reservations (status) AND rooms (status→occupied). Use `$this->model('Room')` inside the controller.

**Check-out side effects**: Three things happen: (1) reservation status→checked_out, (2) room status→dirty, (3) new housekeeping_task created. You need to touch the housekeeping_tasks table directly with an INSERT — Dev 6 will implement the full HK model, but you create the basic task.

**Folio auto-creation**: When `store()` creates a reservation, immediately INSERT a folio with reservation_id and total_amount. This is critical for Dev 5.

## What To Test Before Merge
- [ ] Create reservation with guest + room works
- [ ] Reservation list shows correct data with JOINs
- [ ] Status badges display correctly
- [ ] Check-in: reservation→checked_in, room→occupied
- [ ] Check-out: reservation→checked_out, room→dirty, HK task created
- [ ] Cancel works
- [ ] No-show works
- [ ] Date range filter works
- [ ] Status filter works
- [ ] Folio is auto-created on reservation creation
- [ ] Total price calculated correctly

## Risks To Avoid
1. **Folio auto-creation is CRITICAL** — if you forget, Dev 5 has nothing to work with
2. **Check-out must create HK task** — if you forget, Dev 6's module has no data
3. **Always update room status on check-in/check-out** — otherwise room state machine breaks
4. **Date calculations**: use PHP's DateTime class for night calculation, not string math
5. Don't modify Room.php or Guest.php — those belong to Dev 2 and Dev 3

## Definition of Done
- [ ] Full reservation CRUD works
- [ ] All 6 status transitions work correctly
- [ ] Check-in updates room status
- [ ] Check-out updates room status + creates HK task
- [ ] Folio auto-created with correct total
- [ ] Search filters work
- [ ] All 4 views render with Bootstrap
- [ ] Branch merged into develop

## Handover Notes
Tell Dev 5, Dev 6, Dev 7:
1. Every reservation automatically creates a folio record
2. `$res->folio()` returns the folio for a reservation
3. Check-out automatically creates a housekeeping_task with type='cleaning'
4. Reservation statuses: pending, confirmed, checked_in, checked_out, cancelled, no_show
5. `$res->all()` returns guest_name and room_number in the result
