<?php
// ============================================================
//  ServiceBooking Model — Guest bookings for external services
//  Table: service_bookings
// ============================================================

class ServiceBooking extends Model
{
    public function create($data)
    {
        $guestId = (int) ($data['guest_id'] ?? 0);
        $serviceId = (int) ($data['service_id'] ?? 0);
        $bookingDate = mysqli_real_escape_string($this->db, $data['booking_date'] ?? '');
        $bookingTime = mysqli_real_escape_string($this->db, $data['booking_time'] ?? '');
        $status = mysqli_real_escape_string($this->db, $data['status'] ?? 'pending');

        $query = "
            INSERT INTO service_bookings (guest_id, service_id, booking_date, booking_time, status)
            VALUES ($guestId, $serviceId, '$bookingDate', '$bookingTime', '$status')
        ";

        $result = mysqli_query($this->db, $query);

        if (!$result) {
            die('Insert Failed: ' . mysqli_error($this->db));
        }

        return mysqli_insert_id($this->db);
    }

    public function findByGuest($guestId)
    {
        $guestId = (int) $guestId;
        $query = "
            SELECT sb.*, es.name AS service_name, es.service_type, es.description AS service_description
            FROM service_bookings sb
            JOIN external_services es ON es.id = sb.service_id
            WHERE sb.guest_id = $guestId
            ORDER BY sb.booking_date DESC, sb.booking_time DESC, sb.created_at DESC
        ";

        $result = mysqli_query($this->db, $query);

        if (!$result) {
            die('Query Failed: ' . mysqli_error($this->db));
        }

        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}
