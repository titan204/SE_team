<?php
// ============================================================
//  ReservationsController — Booking & front-desk workflow
//  Routes:
//    /reservations            → index
//    /reservations/show/5     → show details
//    /reservations/create     → new booking form
//    /reservations/store      → save booking
//    /reservations/edit/5     → edit booking
//    /reservations/update/5   → save edits
//    /reservations/delete/5   → cancel/delete
//    /reservations/checkin/5  → check-in action
//    /reservations/checkout/5 → check-out action
// ============================================================

class ReservationsController extends Controller
{
    public function index()
    {
        // TODO: Require login
        // TODO: Load all reservations with guest names, room numbers
        // TODO: Support search filters (date range, status, guest)
        // TODO: Pass to reservations/index view
        $this->view('reservations/index');
    }

    public function show($id)
    {
        // TODO: Load reservation with guest, room, folio details
        // TODO: Pass to reservations/show view
        $this->view('reservations/show');
    }

    public function create()
    {
        // TODO: Load guests and available rooms for dropdowns
        // TODO: Show reservations/create view
        $this->view('reservations/create');
    }

    public function store()
    {
        // TODO: Validate $_POST data
        // TODO: Check room availability (Dynamic Room-Allocation)
        // TODO: Call Reservation model create()
        // TODO: Create Folio for this reservation
        // TODO: Handle group booking if is_group
        // TODO: Redirect to reservations/index
    }

    public function edit($id)
    {
        // TODO: Load reservation by ID
        // TODO: Pass to reservations/edit view
        $this->view('reservations/edit');
    }

    public function update($id)
    {
        // TODO: Validate $_POST data
        // TODO: Call Reservation model update()
        // TODO: Log changes to audit_log
        // TODO: Redirect to reservations/show/$id
    }

    public function delete($id)
    {
        // TODO: Call Reservation model cancel()
        // TODO: Handle cancellation fee logic
        // TODO: Redirect to reservations/index
    }

    public function checkin($id)
    {
        // TODO: Check Early Check-In eligibility
        // TODO: Suggest room upgrade for VIP guests
        // TODO: Call Reservation model checkIn()
        // TODO: Hold deposit / pre-authorization
        // TODO: Redirect to reservations/show/$id
    }

    public function checkout($id)
    {
        // TODO: Generate final folio
        // TODO: Process payment
        // TODO: Call Reservation model checkOut()
        // TODO: Create housekeeping task for the room
        // TODO: Trigger post-stay feedback survey
        // TODO: Update guest lifetime value & loyalty tier
        // TODO: Redirect to reservations/index
    }

    public function noshow($id)
    {
        // TODO: Call Reservation model markNoShow()
        // TODO: Trigger No-Show Penalty (charge card, release room)
    }
}
