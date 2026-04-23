# 👤 Developer 3 — Mission File --> nourhan ezzat (from 27 to 28)
# Guest Management & CRM

---

## Your Main Responsibility

You own everything related to **hotel guests**: guest profiles, preferences (pillow types, dietary needs), CRM features (VIP flagging, loyalty tiers, blacklisting), corporate accounts, GDPR anonymization, and referral tracking.

## Why Your Work Is Important

Guests are the **customers** of the hotel. Dev 4 needs your Guest model to create reservations. Dev 5 needs guest data for billing. Dev 7 needs guest counts for the dashboard. Without working guests, the hotel has no business.

## Start Day / Time
📅 **Day 2, Morning** — After Dev 1 merges core auth.

## Deadline
📅 **Day 3, End of Day** — Guest module merged into `develop`.

## Estimated Hours Needed
~22 hours total

## Git Branch
```
feature/guests
```

## Files You Own
- `app/models/Guest.php`
- `app/models/GuestPreference.php`
- `app/models/CorporateAccount.php`
- `app/controllers/GuestsController.php`
- `app/views/guests/index.php`
- `app/views/guests/show.php`
- `app/views/guests/create.php`
- `app/views/guests/edit.php`

**Total: 8 files**

---

## Functions You Must Implement

### `app/models/Guest.php` (17 functions):
| Function | What It Does |
|---|---|
| `all()` | SELECT all guests ORDER BY name |
| `find($id)` | SELECT one guest by ID |
| `create($data)` | INSERT new guest (name, email, phone, national_id, nationality, dob) |
| `update($id, $data)` | UPDATE guest fields |
| `delete($id)` | DELETE a guest (only if no active reservations) |
| `reservations()` | SELECT all reservations for this guest |
| `preferences()` | SELECT guest_preferences WHERE guest_id = ? |
| `corporateAccount()` | SELECT corporate account via guest_corporate junction |
| `feedback()` | SELECT all feedback WHERE guest_id = ? |
| `calculateLifetimeValue()` | SUM all folio totals across this guest's reservations |
| `updateLoyaltyTier()` | Based on lifetime_nights: 0-9=standard, 10-24=silver, 25-49=gold, 50+=platinum |
| `flagAsVip()` | SET is_vip=1, log to audit_log |
| `blacklist($reason)` | SET is_blacklisted=1, blacklist_reason=$reason |
| `anonymize()` | Replace PII with anonymized data (GDPR) |
| `referrals()` | SELECT guests WHERE referred_by = this guest's ID |

### `app/models/GuestPreference.php` (7 functions):
| Function | What It Does |
|---|---|
| `all()` | SELECT all preferences |
| `find($id)` | SELECT one preference by ID |
| `findByGuest($guestId)` | SELECT preferences WHERE guest_id = ? |
| `create($data)` | INSERT (guest_id, pref_key, pref_value) |
| `update($id, $data)` | UPDATE preference |
| `delete($id)` | DELETE preference |
| `guest()` | Return the guest who owns this preference |

### `app/models/CorporateAccount.php` (6 functions):
| Function | What It Does |
|---|---|
| `all()` | SELECT all corporate accounts |
| `find($id)` | SELECT one by ID |
| `create($data)` | INSERT new corporate account |
| `update($id, $data)` | UPDATE corporate account |
| `delete($id)` | DELETE corporate account |
| `guests()` | SELECT guests linked via guest_corporate junction |

### `app/controllers/GuestsController.php` (10 functions):
| Function | What It Does |
|---|---|
| `index()` | Load all guests → guests/index view. Support search by name/email. |
| `show($id)` | Load guest with preferences, reservations, feedback → guests/show |
| `create()` | Render empty form → guests/create |
| `store()` | Validate POST → Guest::create() → redirect |
| `edit($id)` | Load guest → guests/edit |
| `update($id)` | Validate POST → Guest::update() → redirect |
| `delete($id)` | Guest::delete() → redirect |
| `blacklist($id)` | Read reason from POST → Guest::blacklist() → redirect |
| `anonymize($id)` | Guest::anonymize() → redirect |
| `flagVip($id)` | Guest::flagAsVip() → redirect |

## Requirements Covered
- (12) Guest Preference Sentiment Logger
- (13) Stay-History Aggregator (LTV + loyalty tier)
- (14) Automated VIP Flagging
- (17) Blacklist & Security Flagging
- (19) Privacy & GDPR Right to be Forgotten
- (20) Referral Reward Tracking
- (22) Corporate Account Manager

## Dependencies
| What | From | When |
|---|---|---|
| Database + core framework | Dev 1 | Day 2 morning |
| Login system | Dev 1 | Day 2 morning |

## Developers Waiting For You
| Dev | Needs | When |
|---|---|---|
| Dev 4 | Guest model to assign guests to reservations | Day 3 |
| Dev 5 | Guest data for billing/invoices | Day 4 |
| Dev 7 | Guest counts for dashboard | Day 5 |

---

## Step By Step Work Plan

**Step 1** (Day 2, 30 min): Pull develop, create branch, verify DB tables.

**Step 2** (Day 2, 4 hrs): Implement Guest model CRUD — all(), find(), create(), update(), delete(). The `all()` should support optional search: `WHERE name LIKE ? OR email LIKE ?`.

**Step 3** (Day 2, 2 hrs): Implement GuestPreference model — all methods. Key-value pairs: pref_key='pillow_type', pref_value='firm'.

**Step 4** (Day 2 evening, 2 hrs): Implement CorporateAccount model — all methods. The `guests()` method needs a JOIN through the `guest_corporate` junction table.

**Step 5** (Day 3, 6 hrs): Build GuestsController + all 4 views.
- `index` view: table with Name, Email, Phone, Loyalty badge, VIP badge, Actions
- `show` view: full profile card + preferences list + reservation history + action buttons
- `create`/`edit` views: Bootstrap forms

**Step 6** (Day 3, 3 hrs): Implement business logic methods:
- `calculateLifetimeValue()`: SUM folios.total_amount for all this guest's reservations
- `updateLoyaltyTier()`: Check lifetime_nights → update loyalty_tier column
- `flagAsVip()`: UPDATE is_vip=1 + INSERT into audit_log
- `blacklist($reason)`: UPDATE is_blacklisted=1, blacklist_reason

**Step 7** (Day 4, 2 hrs): Implement `anonymize()`:
- Replace name with 'ANONYMIZED'
- Replace email with 'anon_[id]@gdpr.removed'
- Set phone, national_id, nationality, date_of_birth to NULL
- Set gdpr_anonymized = 1
- Delete all guest_preferences for this guest

**Step 8** (Day 4, 1 hr): Implement `referrals()` — SELECT guests WHERE referred_by = ?.

**Step 9**: Add seed data — at least 5-6 guests with varied loyalty tiers, one VIP, one blacklisted.

## Day By Day Schedule
| Day | Task |
|---|---|
| Day 1 | Study models. Draft SQL. Set up environment. |
| Day 2 | Implement Guest CRUD, GuestPreference CRUD, CorporateAccount CRUD. Seed data. |
| Day 3 | Build controller + views. Implement business logic. **Merge to develop.** |
| Day 4 | Implement anonymize(), referrals(). Help Dev 4. Fix bugs. |
| Day 5-6 | Integration testing. Verify guest data flows to reservations + billing. |
| Day 7 | Bug fixes. |

## Implementation Guidance

**Guest Search**: In `all()`, accept optional `$search` parameter. If provided, add `WHERE name LIKE '%search%' OR email LIKE '%search%'`. Use prepared statements with wildcards in the bound value, not in the SQL string.

**Loyalty Tier Logic**:
- 0-9 nights → 'standard'
- 10-24 nights → 'silver'
- 25-49 nights → 'gold'
- 50+ nights → 'platinum'

**Lifetime Value**: JOIN reservations → folios, SUM(folios.total_amount) WHERE reservations.guest_id = ?

**GDPR Anonymize**: Replace all personally identifiable information. Keep the record (for billing history integrity) but strip all personal data.

**VIP/Blacklist badges in views**: Use Bootstrap badges — `<span class="badge bg-warning">VIP</span>`, `<span class="badge bg-danger">Blacklisted</span>`.

## What To Test Before Merge
- [ ] Guest list shows all guests with search working
- [ ] Guest create/edit/delete works
- [ ] Guest show page displays preferences and history
- [ ] VIP flagging updates the guest record
- [ ] Blacklist function works with reason
- [ ] Loyalty tier displays correctly
- [ ] Corporate account linking works
- [ ] Seed data present

## Risks To Avoid
1. Don't forget search functionality in guest list — it's a key feature
2. GDPR anonymize must be thorough — don't leave any PII
3. Don't allow deleting a guest who has active reservations
4. Don't modify layouts/main.php — Dev 7's file
5. Always use prepared statements for search queries

## Definition of Done
- [ ] All Guest, GuestPreference, CorporateAccount methods implemented
- [ ] All business logic (VIP, blacklist, loyalty, LTV) works
- [ ] GDPR anonymize works completely
- [ ] All 4 views render with Bootstrap
- [ ] Search by name/email works
- [ ] Seed data exists
- [ ] Branch merged into develop

## Handover Notes
Tell Dev 4, Dev 5, Dev 7:
1. `$guest->find($id)` returns full guest profile
2. `$guest->all($search)` supports optional search parameter
3. Guest has `is_vip` and `is_blacklisted` boolean flags — check before check-in
4. `$guest->calculateLifetimeValue()` returns the total spend
