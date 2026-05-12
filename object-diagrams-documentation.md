# Object Diagrams Documentation

## Guest Active Reservation Scenario
```mermaid
classDiagram
class guest3 {
  id = 3
  name = "Carlos Rodriguez"
  loyalty_tier = "standard"
}
class reservation5 {
  id = 5
  status = "confirmed"
  check_in_date = "2026-04-28"
  check_out_date = "2026-05-02"
}
guest3 --> reservation5 : guest_id
```

## Room Assignment Checked-In Scenario
```mermaid
classDiagram
class room102 {
  id = 2
  room_number = "102"
  status = "occupied"
}
class reservation24 {
  id = 24
  status = "checked_in"
  actual_check_in = "2026-04-28 14:00:00"
}
room102 --> reservation24 : room_id
```

## Reservation to Folio Scenario
```mermaid
classDiagram
class reservation31 {
  id = 31
  guest_id = 5
  status = "checked_in"
  total_price = 2000.00
}
class folio21 {
  id = 21
  reservation_id = 31
  total_amount = 2000.00
  amount_paid = 400.00
  status = "open"
}
reservation31 --> folio21 : reservation_id
```

## Folio Charge Posting Scenario
```mermaid
classDiagram
class folio1 {
  id = 1
  reservation_id = 1
  status = "open"
}
class charge2 {
  id = 2
  charge_type = "minibar"
  amount = 65.00
}
class staff5 {
  id = 5
  name = "Fatma Khaled"
  role_id = 3
}
folio1 --> charge2 : folio_id
staff5 --> charge2 : posted_by
```

## Payment Processing Scenario
```mermaid
classDiagram
class folio20 {
  id = 20
  reservation_id = 30
  total_amount = 4500.00
  amount_paid = 900.00
}
class payment78 {
  id = 78
  amount = 1200.00
  method = "credit_card"
  reference = "SET-30-0508"
}
class frontDesk3 {
  id = 3
  name = "Omar Ali"
  role_id = 2
}
folio20 --> payment78 : folio_id
frontDesk3 --> payment78 : processed_by
```

## Group Reservation Billing Scenario
```mermaid
classDiagram
class groupReservation1 {
  id = 1
  group_name = "TechCorp Conference Group"
  discount_percentage = 10.00
}
class reservation6 {
  id = 6
  is_group = 1
  group_id = 1
  status = "confirmed"
}
class invoice1 {
  id = 1
  group_id = 1
  invoice_type = "group"
  total_amount = 16200.00
  status = "finalized"
}
groupReservation1 --> reservation6 : group_id
groupReservation1 --> invoice1 : group_id
```

## Corporate Guest Mapping Scenario
```mermaid
classDiagram
class guest5 {
  id = 5
  name = "Aisha Al-Rashid"
  loyalty_tier = "silver"
}
class corporate3 {
  id = 3
  company_name = "Emirates Business Hub"
  contracted_rate = 20.00
}
class guestCorporateLink {
  guest_id = 5
  corporate_id = 3
}
guest5 --> guestCorporateLink
corporate3 --> guestCorporateLink
```

## Guest Preference Personalization Scenario
```mermaid
classDiagram
class guest4 {
  id = 4
  name = "Yuki Tanaka"
  is_vip = 1
}
class preference11 {
  id = 11
  pref_key = "amenities"
  pref_value = "extra towels,fruit basket"
}
guest4 --> preference11 : guest_id
```

## External Service Booking Scenario
```mermaid
classDiagram
class guest1 {
  id = 1
  name = "John Smith"
  loyalty_tier = "gold"
}
class service1 {
  id = 1
  name = "Grand Spa & Wellness"
  service_type = "spa"
}
class booking1 {
  id = 1
  booking_date = "2026-04-28"
  booking_time = "10:00:00"
  status = "confirmed"
}
guest1 --> booking1 : guest_id
service1 --> booking1 : service_id
```

## Housekeeping Task Assignment Scenario
```mermaid
classDiagram
class room203 {
  id = 5
  room_number = "203"
  status = "inspecting"
}
class task28 {
  id = 28
  task_type = "cleaning"
  status = "done"
  quality_score = 95
}
class housekeeper5 {
  id = 5
  name = "Fatma Khaled"
  role_id = 3
}
room203 --> task28 : room_id
housekeeper5 --> task28 : assigned_to
```

## Maintenance Emergency Work Order Scenario
```mermaid
classDiagram
class room302 {
  id = 7
  room_number = "302"
  status = "out_of_order"
}
class workOrder1 {
  id = 1
  type = "emergency"
  priority = "high"
  status = "in_progress"
}
class technician3 {
  id = 3
  name = "Omar Ali"
  role_id = 2
}
class supervisor1 {
  id = 1
  name = "Ahmed Hassan"
  role_id = 1
}
room302 --> workOrder1 : room_id
technician3 --> workOrder1 : assigned_to_user_id
supervisor1 --> workOrder1 : supervisor_id
```

## Lost and Found Claim Scenario
```mermaid
classDiagram
class guest1 {
  id = 1
  name = "John Smith"
}
class room301 {
  id = 6
  room_number = "301"
}
class foundRecord2 {
  id = 2
  status = "claimed"
  description = "Gold wristwatch left on bathroom shelf"
}
class finder6 {
  id = 6
  name = "Mohamed Samir"
  role_id = 3
}
guest1 --> foundRecord2 : guest_id
room301 --> foundRecord2 : room_id
finder6 --> foundRecord2 : found_by
```

## Role Based User Assignment Scenario
```mermaid
classDiagram
class roleFrontDesk {
  id = 2
  name = "front_desk"
}
class roleHousekeeper {
  id = 3
  name = "housekeeper"
}
class user3 {
  id = 3
  name = "Omar Ali"
  email = "omar.ali@grandhotel.com"
}
class user5 {
  id = 5
  name = "Fatma Khaled"
  email = "fatma.khaled@grandhotel.com"
}
roleFrontDesk --> user3 : role_id
roleHousekeeper --> user5 : role_id
```

## Virtual Inventory Control Scenario
```mermaid
classDiagram
class roomTypeStandard {
  id = 1
  name = "Standard"
  base_price = 500.00
}
class inventory3May {
  id = 2
  date = "2026-05-03"
  physical_rooms = 3
  virtual_max = 3
  confirmed_count = 2
}
class revenueManager1 {
  id = 1
  name = "Ahmed Hassan"
  role_id = 1
}
roomTypeStandard --> inventory3May : room_type_id
revenueManager1 --> inventory3May : updated_by_user_id
```
