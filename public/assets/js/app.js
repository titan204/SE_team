// ── Form Validation ──────────────────────────────────────────

/** validateLoginForm() — email format + password min-length check. */
function validateLoginForm() {
    var email = document.getElementById('email');
    var pass  = document.getElementById('password');
    if (!email || !pass) return true;
    var emailOk = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim());
    if (!emailOk) { alert('Please enter a valid email address.'); email.focus(); return false; }
    if (pass.value.length < 6) { alert('Password must be at least 6 characters.'); pass.focus(); return false; }
    return true;
}

function validateReservationForm() {
    // TODO: Validate check-in/out dates (check-out > check-in)
    // TODO: Validate guest selection + room selection
}

function validateGuestForm() {
    // TODO: Validate required fields (name, email, phone format)
}

function validateRoomForm() {
    // TODO: Validate room number uniqueness (via AJAX) + room type selection
}

function validatePaymentForm() {
    // TODO: Validate amount > 0 + payment method selection
}

// ── Dynamic UI Helpers ───────────────────────────────────────

function filterAvailableRooms(checkIn, checkOut, roomTypeId) {
    // TODO: AJAX call to fetch available rooms; update room dropdown
}

function loadGuestHistory(guestId) {
    // TODO: AJAX call to load guest reservation history; display in modal
}

function updateRoomStatus(roomId, newStatus) {
    // TODO: AJAX call to update room status; refresh badge on page
}

function loadFolioCharges(folioId) {
    // TODO: AJAX call to fetch charges for a folio; update charges table
}

// ── Confirmation Dialogs ─────────────────────────────────────

/** confirmDelete(entity, id) → "Are you sure you want to delete this {entity}?" */
function confirmDelete(entityName, id) {
    return confirm('Are you sure you want to delete this ' + entityName + '?');
}

function confirmCheckOut(reservationId) {
    return confirm('Confirm check-out for reservation #' + reservationId + '?');
}

function confirmNoShow(reservationId) {
    return confirm('Mark as No-Show? This will trigger a penalty charge.');
}

// ── Utility Functions ────────────────────────────────────────

/** formatCurrency(amount) → "$1,234.56" */
function formatCurrency(amount) {
    return '$' + parseFloat(amount).toLocaleString('en-US', {
        minimumFractionDigits: 2, maximumFractionDigits: 2
    });
}

/** formatDate(str) → "Jan 15, 2025" */
function formatDate(dateString) {
    var d = new Date(dateString);
    return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
}

// ── Page Initialization ──────────────────────────────────────

document.addEventListener('DOMContentLoaded', function () {
    // Bootstrap tooltip init (used by audit log value truncation)
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
        new bootstrap.Tooltip(el);
    });
    console.log('Hotel Management System loaded.');
});
