# 💰 Developer 5 — Mission File --> marwan mohamed (from 2 to 3 )
# Billing, Payments & Financial Operations

---

## Your Main Responsibility

You own all **financial operations**: folios (guest bills), charges (room rate, minibar, spa, etc.), payments (cash, card, transfer), refunds, pro-forma invoices, and split-bill functionality.

## Why Your Work Is Important

Billing is how the hotel **makes money**. Every charge, payment, and balance must be accurate. If the numbers don't add up, the system fails. Your folio is the financial backbone connecting reservations to money.

## Start Day / Time
📅 **Day 4, Morning** — After Dev 4 merges reservations (which auto-creates folios).

## Deadline
📅 **Day 5, End of Day** — Billing module merged into `develop`.

## Estimated Hours Needed
~20 hours total

## Git Branch
```
feature/billing
```

## Files You Own
- `app/models/Folio.php`
- `app/models/FolioCharge.php`
- `app/models/Payment.php`
- `app/controllers/BillingController.php`
- `app/views/billing/index.php`
- `app/views/billing/show.php`
- `app/views/billing/invoice.php`
- `app/views/billing/split.php`

**Total: 8 files**

---

## Functions You Must Implement

### `app/models/Folio.php` (12 functions):
| Function | What It Does |
|---|---|
| `all()` | SELECT folios JOIN reservations + guests for guest_name |
| `find($id)` | SELECT one folio by ID |
| `findByReservation($resId)` | SELECT folio WHERE reservation_id = ? |
| `create($data)` | INSERT new folio (Dev 4 also creates folios — yours is backup) |
| `update($id, $data)` | UPDATE folio fields |
| `delete($id)` | DELETE folio |
| `reservation()` | Return parent reservation |
| `charges()` | SELECT all folio_charges WHERE folio_id = ? |
| `payments()` | SELECT all payments WHERE folio_id = ? |
| `recalculateTotal()` | SUM all charges → UPDATE total_amount |
| `settle()` | If balance_due=0, SET status='settled' |
| `generateProFormaInvoice($id)` | Return formatted folio data for invoice view |
| `splitBill($chargeIds, $splitRatio)` | Split specific charges between guests |

### `app/models/FolioCharge.php` (8 functions):
| Function | What It Does |
|---|---|
| `all()` | SELECT all charges |
| `find($id)` | SELECT one charge |
| `findByFolio($folioId)` | SELECT charges WHERE folio_id = ? |
| `create($data)` | INSERT charge (folio_id, charge_type, description, amount, posted_by) |
| `update($id, $data)` | UPDATE charge |
| `delete($id)` | DELETE charge |
| `folio()` | Return parent folio |
| `postToRoom($guestId, $chargeType, $amount, $desc)` | Find guest's active folio → add charge |

### `app/models/Payment.php` (8 functions):
| Function | What It Does |
|---|---|
| `all()` | SELECT all payments |
| `find($id)` | SELECT one payment |
| `findByFolio($folioId)` | SELECT payments WHERE folio_id = ? |
| `create($data)` | INSERT payment (folio_id, amount, method, reference, processed_by) |
| `update($id, $data)` | UPDATE payment |
| `delete($id)` | DELETE payment |
| `folio()` | Return parent folio |
| `processRefund($paymentId, $amount)` | Create negative payment + update folio + log audit |

### `app/controllers/BillingController.php` (7 functions):
| Function | What It Does |
|---|---|
| `index()` | Load all open folios → billing/index view |
| `show($id)` | Load folio + charges + payments → billing/show view |
| `addCharge($id)` | Validate POST → FolioCharge::create() → recalculate → redirect |
| `payment($id)` | Validate POST → Payment::create() → update folio → check settle → redirect |
| `invoice($id)` | Load folio data → billing/invoice view (printable) |
| `refund($id)` | Payment::processRefund() → log audit → redirect |
| `splitBill($id)` | Show split form or process split → billing/split view |

## Requirements Covered
- (33) Folio Consolidation Engine
- (34) External Service POS Bridge (postToRoom)
- (35) Service Cancellation Fee Logic
- (36) Shared-Expense Split-Bill Logic
- (37) Pro-Forma Invoice Generator
- (7) Deposit & Pre-Authorization Manager

## Dependencies
| What | From | When |
|---|---|---|
| Reservations creating folios automatically | Dev 4 | Day 4 morning |
| Guest + Room data for invoice display | Dev 2, Dev 3 | Day 3 |
| Auth system | Dev 1 | Day 2 |

## Developers Waiting For You
| Dev | Needs | When |
|---|---|---|
| Dev 7 | Revenue data for reports | Day 5 |
| Dev 6 | Minibar charges posted to folio (via postToRoom) | Day 5 |

---

## Step By Step Work Plan

**Step 1** (Day 4, 30 min): Pull develop, create branch, verify folios exist (created by Dev 4).

**Step 2** (Day 4, 3 hrs): Implement Folio model CRUD.
- `all()`: JOIN reservations + guests to show guest_name and reservation dates
- `find($id)`: Same JOIN for single folio
- `findByReservation()`: Simple WHERE query
- `charges()` and `payments()`: Simple WHERE queries

**Step 3** (Day 4, 3 hrs): Implement FolioCharge model.
- `create($data)`: INSERT charge. After inserting, call `Folio::recalculateTotal()`
- `postToRoom($guestId, ...)`: Find the guest's active reservation (status='checked_in'), get its folio_id, then INSERT the charge
- Charge types: room_rate, service, minibar, spa, restaurant, penalty, tax, other

**Step 4** (Day 4, 2 hrs): Implement Payment model.
- `create($data)`: INSERT payment. After inserting, UPDATE folio's amount_paid. Check if folio should be settled.
- `processRefund()`: INSERT a payment with negative amount. UPDATE folio. INSERT audit_log entry.

**Step 5** (Day 4 evening, 2 hrs): Implement `recalculateTotal()` and `settle()`.
- `recalculateTotal()`: SELECT SUM(amount) FROM folio_charges WHERE folio_id = ?. Then UPDATE folios SET total_amount = ? WHERE id = ?
- `settle()`: Check if balance_due <= 0. If yes, UPDATE status='settled'

**Step 6** (Day 5, 6 hrs): Build BillingController + all 4 views.
- `index` view: table with Guest, Reservation#, Total, Paid, Balance, Status badge
- `show` view: folio summary card + charges table + payments table + "Add Charge" form + "Record Payment" form
- `invoice` view: printable layout with hotel header, guest details, itemized charges, tax, total
- `split` view: charge checkboxes + split ratio inputs

**Step 7** (Day 5, 2 hrs): Implement `splitBill()` and `generateProFormaInvoice()`.

## Day By Day Schedule
| Day | Task |
|---|---|
| Day 1-2 | Study billing flow. Draft SQL queries. Understand folio lifecycle. |
| Day 3 | Wait for Dev 4. Prepare all queries. Study invoice formats. |
| Day 4 | Pull develop. Implement all 3 models (Folio, FolioCharge, Payment). |
| Day 5 | Build controller + views. Implement split bill + invoice. **Merge to develop.** |
| Day 6-7 | Integration testing. Verify billing accuracy. |

## Implementation Guidance

**Folio Lifecycle**:
```
Reservation created → Folio created (status='open', total=room_rate×nights)
    ↓
Charges added (minibar, spa, etc.) → total_amount recalculated
    ↓
Payments recorded → amount_paid updated
    ↓
When balance_due = 0 → status='settled'
```

**CRITICAL: Money Precision**: Always use `DECIMAL(10,2)` in DB and `number_format()` in PHP. NEVER use `float` for money — it causes rounding errors.

**recalculateTotal()**: SUM all charges, not just add the new one. This prevents drift from rounding.

**balance_due**: This is a GENERATED COLUMN in MySQL (`total_amount - amount_paid`). You don't need to update it — it auto-calculates. But verify it works.

**Refund**: A refund is a negative payment. Create a payment record with negative amount. This makes the accounting trail clear.

**postToRoom() flow**: External service (spa, restaurant) calls postToRoom(guestId, 'spa', 50.00, 'Full body massage'). Your code: find active reservation for this guest → get folio_id → INSERT charge → recalculate total.

## What To Test Before Merge
- [ ] Folio list shows all folios with guest names
- [ ] Folio detail shows charges + payments correctly
- [ ] Add charge → total_amount increases
- [ ] Record payment → amount_paid increases, balance_due decreases
- [ ] When fully paid → folio status becomes 'settled'
- [ ] Invoice view renders correctly
- [ ] Refund creates negative payment + updates folio
- [ ] Numbers always add up correctly (test with known values)

## Risks To Avoid
1. **NEVER use float for money** — always DECIMAL
2. **Always recalculate totals from SUM** — don't add incrementally
3. **Refunds must be logged in audit_log** — otherwise no accountability
4. **Don't modify Reservation model** — that's Dev 4's file
5. **Test with exact numbers** — e.g., room $100 × 3 nights = $300 total

## Definition of Done
- [ ] All Folio, FolioCharge, Payment methods work
- [ ] Charges update folio total correctly
- [ ] Payments update folio balance correctly
- [ ] Folio settles when fully paid
- [ ] Invoice renders as printable page
- [ ] Refund works with audit trail
- [ ] All 4 views render with Bootstrap
- [ ] Branch merged into develop

## Handover Notes
Tell Dev 6 and Dev 7:
1. To post a charge from external service: `$charge->postToRoom($guestId, 'minibar', 15.00, 'Cola + Snacks')`
2. Folio status: 'open', 'settled', 'refunded'
3. `$folio->charges()` returns all charges, `$folio->payments()` returns all payments
4. Revenue for reports: `SELECT SUM(total_amount) FROM folios WHERE status='settled'`
