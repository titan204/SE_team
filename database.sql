-- ============================================================
--  Hotel Management System — Database Schema
--  Normalized to 3NF | MySQL + PDO
--  SE Project — University Level
-- ============================================================



-- ────────────────────────────────────────────────────────────
--  USERS & ROLES  (Staff authentication & access control)
-- ────────────────────────────────────────────────────────────
create database hotel_management ; 
use hotel_management ; 

CREATE TABLE roles (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(50)  NOT NULL UNIQUE,   -- 'manager', 'front_desk', 'housekeeper' , 'guest' 
    created_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE users (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role_id      INT UNSIGNED NOT NULL,
    name         VARCHAR(100) NOT NULL,
    email        VARCHAR(150) NOT NULL UNIQUE,
    password     VARCHAR(255) NOT NULL,          -- bcrypt hashed
    is_active    TINYINT(1)   NOT NULL DEFAULT 1,
    created_at   TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles(id)
);

-- ────────────────────────────────────────────────────────────
--  ROOMS  (Inventory of hotel rooms)
-- ────────────────────────────────────────────────────────────

CREATE TABLE room_types (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL UNIQUE,   -- 'Standard', 'Deluxe', 'Suite'
    description TEXT,
    base_price  DECIMAL(10,2) NOT NULL,
    capacity    TINYINT UNSIGNED NOT NULL DEFAULT 2
);

CREATE TABLE rooms (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    room_type_id  INT UNSIGNED NOT NULL,
    room_number   VARCHAR(10)  NOT NULL UNIQUE,
    floor         TINYINT UNSIGNED NOT NULL,
    status        ENUM('available','occupied','dirty','cleaning','inspecting','out_of_order') NOT NULL DEFAULT 'available',
    notes         TEXT,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_rooms_type FOREIGN KEY (room_type_id) REFERENCES room_types(id)
);

-- ────────────────────────────────────────────────────────────
--  GUESTS  (Guest profiles & CRM)
-- ────────────────────────────────────────────────────────────

CREATE TABLE guests (
    id                 INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name               VARCHAR(150) NOT NULL,
    email              VARCHAR(150) NOT NULL UNIQUE,
    phone              VARCHAR(30),
    national_id        VARCHAR(50),
    nationality        VARCHAR(80),
    date_of_birth      DATE,
    loyalty_tier       ENUM('standard','silver','gold','platinum') NOT NULL DEFAULT 'standard',
    lifetime_nights    INT UNSIGNED NOT NULL DEFAULT 0,
    lifetime_value     DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    is_blacklisted     TINYINT(1)   NOT NULL DEFAULT 0,
    blacklist_reason   TEXT,
    is_vip             TINYINT(1)   NOT NULL DEFAULT 0,
    gdpr_anonymized    TINYINT(1)   NOT NULL DEFAULT 0,
    referred_by        INT UNSIGNED,              -- self-referencing FK
    created_at         TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at         TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_guests_referrer FOREIGN KEY (referred_by) REFERENCES guests(id) ON DELETE SET NULL
);

CREATE TABLE guest_preferences (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    guest_id     INT UNSIGNED NOT NULL,
    pref_key     VARCHAR(100) NOT NULL,   -- 'pillow_type', 'dietary', 'floor_preference'
    pref_value   VARCHAR(255) NOT NULL,
    CONSTRAINT fk_guestpref_guest FOREIGN KEY (guest_id) REFERENCES guests(id) ON DELETE CASCADE,
    UNIQUE KEY uq_guest_pref (guest_id, pref_key)
);

-- ────────────────────────────────────────────────────────────
--  CORPORATE ACCOUNTS
-- ────────────────────────────────────────────────────────────

CREATE TABLE corporate_accounts (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_name    VARCHAR(200) NOT NULL,
    contact_email   VARCHAR(150),
    contact_phone   VARCHAR(30),
    contracted_rate DECIMAL(5,2),    -- % discount
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE guest_corporate (
    guest_id     INT UNSIGNED NOT NULL,
    corporate_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (guest_id, corporate_id),
    CONSTRAINT fk_gc_guest     FOREIGN KEY (guest_id)     REFERENCES guests(id) ON DELETE CASCADE,
    CONSTRAINT fk_gc_corporate FOREIGN KEY (corporate_id) REFERENCES corporate_accounts(id) ON DELETE CASCADE
);

-- ────────────────────────────────────────────────────────────
--  RESERVATIONS
-- ────────────────────────────────────────────────────────────

CREATE TABLE reservations (
    id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    guest_id          INT UNSIGNED NOT NULL,
    room_id           INT UNSIGNED NOT NULL,
    assigned_by       INT UNSIGNED,              -- users.id (front-desk staff)
    check_in_date     DATE         NOT NULL,
    check_out_date    DATE         NOT NULL,
    actual_check_in   DATETIME,
    actual_check_out  DATETIME,
    status            ENUM('pending','confirmed','checked_in','checked_out','cancelled','no_show') NOT NULL DEFAULT 'pending',
    adults            TINYINT UNSIGNED NOT NULL DEFAULT 1,
    children          TINYINT UNSIGNED NOT NULL DEFAULT 0,
    special_requests  TEXT,
    deposit_amount    DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    deposit_paid      TINYINT(1)   NOT NULL DEFAULT 0,
    is_group          TINYINT(1)   NOT NULL DEFAULT 0,
    group_id          INT UNSIGNED,              -- links group reservations
    total_price       DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    created_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_res_guest  FOREIGN KEY (guest_id)    REFERENCES guests(id),
    CONSTRAINT fk_res_room   FOREIGN KEY (room_id)     REFERENCES rooms(id),
    CONSTRAINT fk_res_staff  FOREIGN KEY (assigned_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_res_dates    (check_in_date, check_out_date),
    INDEX idx_res_status   (status),
    INDEX idx_res_guest    (guest_id)
);

-- ────────────────────────────────────────────────────────────
--  BILLING / FOLIOS
-- ────────────────────────────────────────────────────────────

CREATE TABLE folios (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    reservation_id INT UNSIGNED NOT NULL UNIQUE,
    total_amount   DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    amount_paid    DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    balance_due    DECIMAL(12,2) GENERATED ALWAYS AS (total_amount - amount_paid) STORED,
    status         ENUM('open','settled','refunded') NOT NULL DEFAULT 'open',
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_folio_res FOREIGN KEY (reservation_id) REFERENCES reservations(id) ON DELETE CASCADE
);

CREATE TABLE folio_charges (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    folio_id     INT UNSIGNED NOT NULL,
    charge_type  ENUM('room_rate','service','minibar','spa','restaurant','penalty','tax','other') NOT NULL,
    description  VARCHAR(255) NOT NULL,
    amount       DECIMAL(10,2) NOT NULL,
    posted_by    INT UNSIGNED,                  -- users.id
    posted_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_charge_folio FOREIGN KEY (folio_id)   REFERENCES folios(id) ON DELETE CASCADE,
    CONSTRAINT fk_charge_staff FOREIGN KEY (posted_by)  REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE payments (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    folio_id       INT UNSIGNED NOT NULL,
    amount         DECIMAL(10,2) NOT NULL,
    method         ENUM('cash','credit_card','debit_card','bank_transfer','online') NOT NULL,
    reference      VARCHAR(100),
    processed_by   INT UNSIGNED,
    processed_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_pay_folio FOREIGN KEY (folio_id)     REFERENCES folios(id) ON DELETE CASCADE,
    CONSTRAINT fk_pay_staff FOREIGN KEY (processed_by) REFERENCES users(id) ON DELETE SET NULL
);

-- ────────────────────────────────────────────────────────────
--  HOUSEKEEPING
-- ────────────────────────────────────────────────────────────

CREATE TABLE housekeeping_tasks (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    room_id       INT UNSIGNED NOT NULL,
    assigned_to   INT UNSIGNED,                 -- users.id (housekeeper)
    task_type     ENUM('cleaning','turndown','inspection','deep_clean','minibar_check') NOT NULL DEFAULT 'cleaning',
    status        ENUM('pending','in_progress','done','skipped') NOT NULL DEFAULT 'pending',
    notes         TEXT,
    quality_score TINYINT UNSIGNED,             -- 1–5, set by supervisor
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_hk_room  FOREIGN KEY (room_id)     REFERENCES rooms(id),
    CONSTRAINT fk_hk_staff FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL
);

-- ────────────────────────────────────────────────────────────
--  MAINTENANCE
-- ────────────────────────────────────────────────────────────

CREATE TABLE maintenance_orders (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    room_id      INT UNSIGNED NOT NULL,
    reported_by  INT UNSIGNED,
    assigned_to  INT UNSIGNED,
    description  TEXT         NOT NULL,
    priority     ENUM('low','medium','high','critical') NOT NULL DEFAULT 'medium',
    status       ENUM('open','in_progress','resolved','escalated') NOT NULL DEFAULT 'open',
    resolved_at  DATETIME,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_maint_room     FOREIGN KEY (room_id)     REFERENCES rooms(id),
    CONSTRAINT fk_maint_reporter FOREIGN KEY (reported_by) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_maint_assigned FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL
);

-- ────────────────────────────────────────────────────────────
--  LOST & FOUND
-- ────────────────────────────────────────────────────────────

CREATE TABLE lost_and_found (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    guest_id     INT UNSIGNED,
    room_id      INT UNSIGNED,
    found_by     INT UNSIGNED,
    description  TEXT NOT NULL,
    status       ENUM('found','claimed','donated','discarded') NOT NULL DEFAULT 'found',
    found_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_laf_guest FOREIGN KEY (guest_id) REFERENCES guests(id) ON DELETE SET NULL,
    CONSTRAINT fk_laf_room  FOREIGN KEY (room_id)  REFERENCES rooms(id)  ON DELETE SET NULL,
    CONSTRAINT fk_laf_staff FOREIGN KEY (found_by) REFERENCES users(id)  ON DELETE SET NULL
);

-- ────────────────────────────────────────────────────────────
--  FEEDBACK
-- ────────────────────────────────────────────────────────────

CREATE TABLE feedback (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    reservation_id INT UNSIGNED NOT NULL,
    guest_id       INT UNSIGNED NOT NULL,
    rating         TINYINT UNSIGNED NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comments       TEXT,
    submitted_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_feedback_res   FOREIGN KEY (reservation_id) REFERENCES reservations(id) ON DELETE CASCADE,
    CONSTRAINT fk_feedback_guest FOREIGN KEY (guest_id)       REFERENCES guests(id)
);

-- ────────────────────────────────────────────────────────────
--  AUDIT LOG
-- ────────────────────────────────────────────────────────────

CREATE TABLE audit_log (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED,
    action      VARCHAR(100) NOT NULL,   -- 'price_override', 'refund', 'blacklist'
    target_type VARCHAR(50),             -- 'reservation', 'folio', 'guest'
    target_id   INT UNSIGNED,
    old_value   TEXT,
    new_value   TEXT,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- ────────────────────────────────────────────────────────────
--  SEED DATA — Roles
-- ────────────────────────────────────────────────────────────

INSERT INTO roles (name) VALUES ('manager'), ('front_desk'), ('housekeeper') , ('guest');

-- ────────────────────────────────────────────────────────────
--  SEED DATA — Room Types
-- ────────────────────────────────────────────────────────────

INSERT INTO room_types (name, description, base_price, capacity) VALUES
('Standard', 'Comfortable room with essential amenities for a pleasant stay.',        500.00, 2),
('Deluxe',   'Spacious room with premium amenities and city view.',                   800.00, 3),
('Suite',    'Luxury suite with separate living area, premium furnishings and minibar.', 1500.00, 4);

-- ────────────────────────────────────────────────────────────
--  SEED DATA — Rooms  (at least one of each status)
-- ────────────────────────────────────────────────────────────

INSERT INTO rooms (room_type_id, room_number, floor, status, notes) VALUES
(1, '101', 1, 'available',    'Ground floor, near lobby'),
(1, '102', 1, 'occupied',     'Guest currently checked in'),
(2, '201', 2, 'dirty',        'Checkout completed, awaiting housekeeping'),
(2, '202', 2, 'cleaning',     'Housekeeping in progress'),
(2, '203', 2, 'inspecting',   'Cleaning done, supervisor review pending'),
(3, '301', 3, 'available',    'Premium corner suite'),
(3, '302', 3, 'out_of_order', 'AC unit under repair'),
(1, '103', 1, 'available',    'Near elevator');

CREATE TABLE external_services (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name         VARCHAR(150) NOT NULL,
    service_type VARCHAR(50)  NOT NULL,
    description  TEXT,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE service_bookings (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    guest_id     INT UNSIGNED NOT NULL,
    service_id   INT UNSIGNED NOT NULL,
    booking_date DATE         NOT NULL,
    booking_time TIME         NOT NULL,
    status       ENUM('pending','confirmed','cancelled') NOT NULL DEFAULT 'pending',
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_service_booking_guest FOREIGN KEY (guest_id) REFERENCES guests(id) ON DELETE CASCADE,
    CONSTRAINT fk_service_booking_service FOREIGN KEY (service_id) REFERENCES external_services(id) ON DELETE CASCADE
);

USE hotel_management;


-- ============================================================
--  Hotel Management System — Comprehensive Seed Data
--  All guests are also linked to user accounts (same email)
--  Today's reference date: 2026-04-27
-- ============================================================

USE hotel_management;

-- ════════════════════════════════════════════════════════════
--  1. USERS  (7 Staff + 8 Guest Accounts)
--     All passwords = 'Password123!'  (bcrypt, cost 10)
-- ════════════════════════════════════════════════════════════

INSERT INTO users (role_id, name, email, password, is_active) VALUES
-- ── Managers ─────────────────────────────────────────────────
(1, 'Ahmed Hassan',      'ahmed.hassan@grandhotel.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),
(1, 'Sara Mohamed',      'sara.mohamed@grandhotel.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),
-- ── Front Desk ───────────────────────────────────────────────
(2, 'Omar Ali',          'omar.ali@grandhotel.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),
(2, 'Nour Ibrahim',      'nour.ibrahim@grandhotel.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),
-- ── Housekeepers ─────────────────────────────────────────────
(3, 'Fatma Khaled',      'fatma.khaled@grandhotel.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),
(3, 'Mohamed Samir',     'mohamed.samir@grandhotel.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),
(3, 'Layla Ahmed',       'layla.ahmed@grandhotel.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),
-- ── Guest Accounts  (email = guests.email → the link) ────────
(4, 'John Smith',        'john.smith@gmail.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),  -- user_id 8
(4, 'Emma Wilson',       'emma.wilson@gmail.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),  -- user_id 9
(4, 'Carlos Rodriguez',  'carlos.rodriguez@gmail.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),  -- user_id 10
(4, 'Yuki Tanaka',       'yuki.tanaka@gmail.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),  -- user_id 11
(4, 'Aisha Al-Rashid',   'aisha.alrashid@gmail.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),  -- user_id 12
(4, 'Pierre Dubois',     'pierre.dubois@gmail.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),  -- user_id 13
(4, 'Priya Sharma',      'priya.sharma@gmail.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),  -- user_id 14
(4, 'David Chen',        'david.chen@gmail.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1); -- user_id 15

-- ════════════════════════════════════════════════════════════
--  2. GUESTS  (Mirror of guest user accounts — same email)
--     guest_id 1-8 map to user_id 8-15 via shared email
-- ════════════════════════════════════════════════════════════

INSERT INTO guests
    (name, email, phone, national_id, nationality, date_of_birth,
     loyalty_tier, lifetime_nights, lifetime_value, is_vip, is_blacklisted)
VALUES
-- id=1
('John Smith',       'john.smith@gmail.com',       '+1-212-555-0101',  'US123456',
 'American',  '1985-03-14', 'gold',     42,  35000.00, 1, 0),
-- id=2
('Emma Wilson',      'emma.wilson@gmail.com',       '+44-207-946-0102', 'UK789012',
 'British',   '1990-07-22', 'silver',   18,   9500.00, 0, 0),
-- id=3
('Carlos Rodriguez', 'carlos.rodriguez@gmail.com',  '+34-91-555-0103',  'ES345678',
 'Spanish',   '1978-11-05', 'standard',  5,   2500.00, 0, 0),
-- id=4
('Yuki Tanaka',      'yuki.tanaka@gmail.com',       '+81-3-5555-0104',  'JP901234',
 'Japanese',  '1992-01-30', 'platinum', 95,  95000.00, 1, 0),
-- id=5
('Aisha Al-Rashid',  'aisha.alrashid@gmail.com',    '+971-4-555-0105',  'AE567890',
 'Emirati',   '1988-09-18', 'silver',   22,  12000.00, 0, 0),
-- id=6
('Pierre Dubois',    'pierre.dubois@gmail.com',     '+33-1-5555-0106',  'FR123789',
 'French',    '1975-05-25', 'gold',     55,  48000.00, 0, 0),
-- id=7
('Priya Sharma',     'priya.sharma@gmail.com',      '+91-98-5555-0107', 'IN456012',
 'Indian',    '1995-12-10', 'standard',  8,   3200.00, 0, 0),
-- id=8
('David Chen',       'david.chen@gmail.com',        '+1-650-555-0108',  'US789345',
 'American',  '1982-06-03', 'gold',     38,  30000.00, 0, 0);

-- Referral links (done after insert to satisfy FK)
UPDATE guests SET referred_by = 1 WHERE id = 3;  -- Carlos referred by John
UPDATE guests SET referred_by = 2 WHERE id = 7;  -- Priya referred by Emma

-- ════════════════════════════════════════════════════════════
--  3. GUEST PREFERENCES
-- ════════════════════════════════════════════════════════════

INSERT INTO guest_preferences (guest_id, pref_key, pref_value) VALUES
(1, 'pillow_type',      'firm'),
(1, 'floor_preference', 'high'),
(1, 'dietary',          'no pork'),
(2, 'pillow_type',      'soft'),
(2, 'room_temperature', 'cool'),
(3, 'dietary',          'vegetarian'),
(3, 'floor_preference', 'low'),
(4, 'pillow_type',      'memory_foam'),
(4, 'room_temperature', 'warm'),
(4, 'newspaper',        'Financial Times'),
(4, 'amenities',        'extra towels,fruit basket'),
(5, 'dietary',          'halal'),
(5, 'pillow_type',      'soft'),
(6, 'newspaper',        'Le Monde'),
(6, 'dietary',          'no shellfish'),
(7, 'dietary',          'vegan'),
(7, 'room_temperature', 'cool'),
(8, 'pillow_type',      'firm'),
(8, 'floor_preference', 'high');

-- ════════════════════════════════════════════════════════════
--  4. CORPORATE ACCOUNTS
-- ════════════════════════════════════════════════════════════

INSERT INTO corporate_accounts
    (company_name, contact_email, contact_phone, contracted_rate)
VALUES
('TechCorp International',  'travel@techcorp.com',       '+1-800-555-0201', 15.00),
('Global Consulting Group', 'bookings@gcg.com',           '+44-800-555-0202', 10.00),
('Emirates Business Hub',   'hotels@emirateshub.ae',      '+971-4-800-0203',  20.00);

-- Guest ↔ Corporate links
INSERT INTO guest_corporate (guest_id, corporate_id) VALUES
(1, 1),   -- John Smith      → TechCorp
(4, 1),   -- Yuki Tanaka     → TechCorp
(6, 2),   -- Pierre Dubois   → Global Consulting
(5, 3);   -- Aisha Al-Rashid → Emirates Business Hub

-- ════════════════════════════════════════════════════════════
--  5. RESERVATIONS
--
--  Room map (from schema seed):
--    id=1  101 Standard   available
--    id=2  102 Standard   occupied   ← John Smith checked in
--    id=3  201 Standard   dirty      ← Emma Wilson just checked out
--    id=4  202 Deluxe     cleaning   ← Priya Sharma just checked out
--    id=5  203 Deluxe     inspecting ← Pierre Dubois just checked out
--    id=6  301 Suite      available  ← Yuki Tanaka arrives May 1
--    id=7  302 Suite      out_of_order
--    id=8  103 Standard   available
-- ════════════════════════════════════════════════════════════

INSERT INTO reservations
    (guest_id, room_id, assigned_by,
     check_in_date, check_out_date,
     actual_check_in, actual_check_out,
     status, adults, children,
     special_requests,
     deposit_amount, deposit_paid,
     is_group, group_id,
     total_price)
VALUES
-- ── 1. John Smith — currently checked in, room 102 (4 nights × 500 = 2000) ──
(1, 2, 3,
 '2026-04-25', '2026-04-29',
 '2026-04-25 14:30:00', NULL,
 'checked_in', 2, 0,
 'Extra pillows, high floor preference',
 500.00, 1, 0, NULL, 2000.00),

-- ── 2. Emma Wilson — checked out, room 201 (4 nights × 500 = 2000) ──────────
(2, 3, 4,
 '2026-04-22', '2026-04-26',
 '2026-04-22 15:10:00', '2026-04-26 11:45:00',
 'checked_out', 1, 0,
 NULL,
 500.00, 1, 0, NULL, 2000.00),

-- ── 3. Priya Sharma — checked out, room 202 (4 nights × 800 = 3200) ─────────
(7, 4, 3,
 '2026-04-23', '2026-04-27',
 '2026-04-23 13:00:00', '2026-04-27 10:30:00',
 'checked_out', 2, 1,
 'Baby cot needed, vegan breakfast',
 800.00, 1, 0, NULL, 3200.00),

-- ── 4. Pierre Dubois — checked out, room 203 (3 nights × 800 = 2400) ────────
(6, 5, 4,
 '2026-04-24', '2026-04-27',
 '2026-04-24 16:00:00', '2026-04-27 12:30:00',
 'checked_out', 1, 0,
 'Late check-out requested',
 800.00, 1, 0, NULL, 2400.00),

-- ── 5. Carlos Rodriguez — confirmed, arrives tomorrow (4 nights × 500 = 2000) ─
(3, 1, 3,
 '2026-04-28', '2026-05-02',
 NULL, NULL,
 'confirmed', 2, 0,
 'Vegetarian welcome plate',
 500.00, 1, 0, NULL, 2000.00),

-- ── 6. Yuki Tanaka — confirmed VIP, Suite 301 (6 nights × 1500 = 9000) ───────
(4, 6, 4,
 '2026-05-01', '2026-05-07',
 NULL, NULL,
 'confirmed', 2, 0,
 'Champagne on arrival, butler service, Financial Times daily',
 2000.00, 1, 1, 1, 9000.00),

-- ── 7. Aisha Al-Rashid — pending, room 103 (2 nights × 500 = 1000) ───────────
(5, 8, NULL,
 '2026-05-10', '2026-05-12',
 NULL, NULL,
 'pending', 1, 0,
 'Halal dining options, prayer mat in room',
 0.00, 0, 0, NULL, 1000.00),

-- ── 8. David Chen — cancelled, room 101 ──────────────────────────────────────
(8, 1, 3,
 '2026-05-03', '2026-05-06',
 NULL, NULL,
 'cancelled', 2, 0,
 NULL,
 500.00, 0, 0, NULL, 1500.00),

-- ── 9. John Smith — old checked-out stay, Suite 301 (5 nights × 1500 = 7500) ─
(1, 6, 3,
 '2026-03-10', '2026-03-15',
 '2026-03-10 14:00:00', '2026-03-15 12:00:00',
 'checked_out', 2, 0,
 'Anniversary celebration – roses and champagne in room',
 2000.00, 1, 0, NULL, 7500.00),

-- ── 10. Yuki Tanaka — no-show, room 103 (old booking) ─────────────────────
(4, 8, 4,
 '2026-02-20', '2026-02-22',
 NULL, NULL,
 'no_show', 2, 0,
 NULL,
 500.00, 1, 0, NULL, 1000.00);

-- ════════════════════════════════════════════════════════════
--  6. FOLIOS  (One per active/completed reservation)
--     Cancelled & no-show get folios to track deposit status
-- ════════════════════════════════════════════════════════════

INSERT INTO folios (reservation_id, total_amount, amount_paid, status) VALUES
(1,  2085.00,   500.00, 'open'),      -- John Smith in-house (minibar added)
(2,  2150.00,  2150.00, 'settled'),   -- Emma Wilson (room service added)
(3,  3350.00,  3350.00, 'settled'),   -- Priya Sharma (spa added)
(4,  2550.00,  2550.00, 'settled'),   -- Pierre Dubois (late checkout penalty)
(5,  2000.00,   500.00, 'open'),      -- Carlos Rodriguez (deposit only, not arrived)
(6,  9000.00,  2000.00, 'open'),      -- Yuki Tanaka (deposit only, not arrived)
(8,  1500.00,     0.00, 'open'),      -- David Chen (deposit not paid, cancelled)
(9,  7850.00,  7850.00, 'settled'),   -- John Smith old stay (spa + dinner added)
(10, 1000.00,   500.00, 'open');      -- Yuki no-show (deposit held)

-- ════════════════════════════════════════════════════════════
--  7. FOLIO CHARGES
--     folio IDs: 1=JohnActive, 2=Emma, 3=Priya, 4=Pierre,
--                5=CarlosFuture, 6=YukiFuture, 7=DavidCancelled,
--                8=JohnOld, 9=YukiNoShow
-- ════════════════════════════════════════════════════════════

INSERT INTO folio_charges
    (folio_id, charge_type, description, amount, posted_by)
VALUES
-- ── Folio 1: John Smith (currently in-house) ──────────────────────────────
(1, 'room_rate',  'Room 102 – Standard × 4 nights',            2000.00, 3),
(1, 'minibar',    'Minibar consumption – 2026-04-26',             65.00, 5),
(1, 'service',    'Extra pillow set + turndown service',           20.00, 6),

-- ── Folio 2: Emma Wilson (settled) ────────────────────────────────────────
(2, 'room_rate',  'Room 201 – Standard × 4 nights',            2000.00, 4),
(2, 'restaurant', 'In-room dining – 2026-04-24',                 150.00, 4),

-- ── Folio 3: Priya Sharma (settled) ───────────────────────────────────────
(3, 'room_rate',  'Room 202 – Deluxe × 4 nights',              3200.00, 3),
(3, 'spa',        'Aromatherapy massage – 2026-04-25',            150.00, 3),

-- ── Folio 4: Pierre Dubois (settled) ──────────────────────────────────────
(4, 'room_rate',  'Room 203 – Deluxe × 3 nights',              2400.00, 4),
(4, 'restaurant', 'Restaurant dinner – 2026-04-25',               100.00, 4),
(4, 'penalty',    'Late check-out fee (2 hrs past policy)',         50.00, 1),

-- ── Folio 5: Carlos Rodriguez (future – room rate pre-posted) ─────────────
(5, 'room_rate',  'Room 101 – Standard × 4 nights (pre-auth)', 2000.00, 3),

-- ── Folio 6: Yuki Tanaka (future – room rate pre-posted) ──────────────────
(6, 'room_rate',  'Room 301 – Suite × 6 nights (pre-auth)',    9000.00, 4),

-- ── Folio 8: John Smith old stay (settled) ────────────────────────────────
(8, 'room_rate',  'Room 301 – Suite × 5 nights',               7500.00, 3),
(8, 'spa',        'Couples massage – 2026-03-12',                 200.00, 3),
(8, 'restaurant', 'Anniversary dinner – 2026-03-14',              150.00, 3),

-- ── Folio 9: Yuki no-show (deposit held as penalty) ───────────────────────
(9, 'room_rate',  'No-show fee – deposit forfeited',             500.00, 1);

-- ════════════════════════════════════════════════════════════
--  8. PAYMENTS
-- ════════════════════════════════════════════════════════════

INSERT INTO payments
    (folio_id, amount, method, reference, processed_by)
VALUES
-- Folio 1: John Smith deposit
(1,   500.00, 'credit_card',   'TXN-APR25-001', 3),
-- Folio 2: Emma Wilson — deposit + balance at checkout
(2,   500.00, 'credit_card',   'TXN-APR22-001', 4),
(2,  1650.00, 'credit_card',   'TXN-APR26-002', 4),
-- Folio 3: Priya Sharma — deposit + balance
(3,   800.00, 'credit_card',   'TXN-APR23-001', 3),
(3,  2550.00, 'credit_card',   'TXN-APR27-001', 3),
-- Folio 4: Pierre Dubois — deposit + balance (mix of methods)
(4,   800.00, 'credit_card',   'TXN-APR24-001', 4),
(4,  1750.00, 'cash',           NULL,             4),
-- Folio 5: Carlos Rodriguez deposit via online portal
(5,   500.00, 'online',        'WEB-APR20-001', 3),
-- Folio 6: Yuki Tanaka deposit via bank transfer
(6,  2000.00, 'bank_transfer', 'BT-APR15-001',  4),
-- Folio 8: John Smith old stay — deposit then full settlement
(8,  2000.00, 'credit_card',   'TXN-MAR10-001', 3),
(8,  5850.00, 'credit_card',   'TXN-MAR15-002', 3),
-- Folio 9: Yuki no-show — deposit was paid (now forfeited)
(9,   500.00, 'credit_card',   'TXN-FEB18-001', 4);

-- ════════════════════════════════════════════════════════════
--  9. HOUSEKEEPING TASKS
-- ════════════════════════════════════════════════════════════

INSERT INTO housekeeping_tasks
    (room_id, assigned_to, task_type, status, notes, quality_score)
VALUES
-- Room 201 (dirty) → full clean assigned to Fatma
(3, 5, 'cleaning',      'pending',     'Post-checkout full clean, change all linen', NULL),
-- Room 202 (cleaning) → Samir in progress
(4, 6, 'cleaning',      'in_progress', 'Deep clean + restock minibar and amenities', NULL),
-- Room 203 (inspecting) → clean done by Fatma, inspection pending for Layla
(5, 5, 'cleaning',      'done',        'Clean completed after checkout',              4),
(5, 7, 'inspection',    'pending',     'Supervisor sign-off required before re-let',  NULL),
-- Room 102 (occupied, VIP) → evening turndown by Samir
(2, 6, 'turndown',      'pending',     'VIP guest – chocolates and extra towels',      NULL),
-- Room 301 (Suite available) → minibar stocked for VIP arrival May 1
(6, 7, 'minibar_check', 'done',        'Fully stocked, champagne chilled for Yuki',   5),
-- Room 101 (available) → inspection cleared for Carlos arriving tomorrow
(1, 5, 'inspection',    'done',        'Room cleared and ready for check-in',         5),
-- Room 103 (available) → routine clean completed
(8, 6, 'cleaning',      'done',        'Routine clean, ready for next guest',         4),
-- Room 201 deep-clean scheduled (scheduled for later today)
(3, 7, 'deep_clean',    'pending',     'Full mattress flip and carpet steam clean',   NULL);

-- ════════════════════════════════════════════════════════════
--  10. MAINTENANCE ORDERS
-- ════════════════════════════════════════════════════════════

INSERT INTO maintenance_orders
    (room_id, reported_by, assigned_to, description, priority, status, resolved_at)
VALUES
-- Room 302 — AC failure (reason it's out_of_order)
(7, 3, NULL,
 'AC unit not cooling and making grinding noise. Room taken out of service pending repair.',
 'high', 'in_progress', NULL),
-- Room 202 — dripping tap (minor, reported during stay)
(4, 5, NULL,
 'Bathroom basin tap dripping. Needs washer replacement.',
 'low', 'open', NULL),
-- Room 102 — TV remote resolved
(2, 3, NULL,
 'Guest reported TV remote unresponsive. Batteries replaced, issue resolved.',
 'low', 'resolved', '2026-04-26 10:00:00'),
-- Room 301 — balcony door stiff (before VIP arrival)
(6, 4, NULL,
 'Suite balcony sliding door lock is stiff. Lubrication and adjustment required before May 1.',
 'medium', 'open', NULL),
-- Room 201 — light flickering
(3, 6, NULL,
 'Bathroom ceiling light flickering. Likely loose fitting or blown bulb.',
 'low', 'open', NULL);

-- ════════════════════════════════════════════════════════════
--  11. EXTERNAL SERVICES & BOOKINGS
-- ════════════════════════════════════════════════════════════

INSERT INTO external_services (name, service_type, description) VALUES
('Grand Spa & Wellness',      'spa',        'Full-service spa: massages, facials, hydrotherapy'),
('The Gourmet Kitchen',       'restaurant', 'Fine dining with international and local cuisine'),
('Airport Luxury Transfers',  'transport',  'Premium car service to/from all major airports'),
('City Cultural Tours',       'tour',       'Guided half-day and full-day city sightseeing tours'),
('Business Centre Services',  'business',  'Printing, scanning, secretarial, and meeting room hire');

INSERT INTO service_bookings
    (guest_id, service_id, booking_date, booking_time, status)
VALUES
(1, 1, '2026-04-28', '10:00:00', 'confirmed'),   -- John  → Spa (tomorrow)
(1, 3, '2026-04-29', '12:00:00', 'confirmed'),   -- John  → Airport transfer on checkout
(4, 1, '2026-05-03', '11:00:00', 'confirmed'),   -- Yuki  → Spa mid-stay
(4, 2, '2026-05-02', '19:30:00', 'confirmed'),   -- Yuki  → Fine dining
(5, 3, '2026-05-10', '08:00:00', 'pending'),     -- Aisha → Airport transfer on arrival
(2, 4, '2026-04-23', '14:00:00', 'confirmed'),   -- Emma  → City tour during stay
(3, 5, '2026-04-29', '09:00:00', 'pending'),     -- Carlos → Business centre day after arrival
(6, 2, '2026-04-25', '20:00:00', 'confirmed');   -- Pierre → Restaurant (past stay)

-- ════════════════════════════════════════════════════════════
--  12. LOST & FOUND
-- ════════════════════════════════════════════════════════════

INSERT INTO lost_and_found
    (guest_id, room_id, found_by, description, status)
VALUES
(2, 3, 5,
 'Black leather wallet found under bed after checkout. Contains credit cards and cash.',
 'found'),
(1, 6, 6,
 'Gold wristwatch left on bathroom shelf after March stay. Guest notified.',
 'claimed'),
(NULL, 4, 5,
 'Blue umbrella found in wardrobe. No guest could be identified.',
 'donated'),
(7, 4, 6,
 'USB-C phone charger on bedside table after checkout.',
 'found'),
(6, 5, 7,
 'Designer sunglasses case (Gucci) left in room safe.',
 'found');

-- ════════════════════════════════════════════════════════════
--  13. FEEDBACK  (only from completed stays)
-- ════════════════════════════════════════════════════════════

INSERT INTO feedback (reservation_id, guest_id, rating, comments) VALUES
(2, 2, 4,
 'Great stay overall. Room was clean and the front desk staff were very helpful. WiFi could be faster.'),
(3, 7, 5,
 'Absolutely wonderful experience. The spa was exceptional and the room was spotless. Will return!'),
(4, 6, 3,
 'Room and food were good, but the late checkout fee was unexpected and not communicated upfront.'),
(9, 1, 5,
 'The anniversary surprise exceeded all expectations. Suite was magnificent. Cannot wait to come back.');

-- ════════════════════════════════════════════════════════════
--  14. AUDIT LOG  (key actions trail)
-- ════════════════════════════════════════════════════════════

INSERT INTO audit_log
    (user_id, action, target_type, target_id, old_value, new_value)
VALUES
-- Manager added penalty charge to Pierre's folio
(1, 'price_override',   'folio',       4,  '2500.00',    '2550.00'),
-- David Chen cancelled his reservation
(1, 'status_change',    'reservation', 8,  'confirmed',  'cancelled'),
-- Check-in events
(3, 'check_in',         'reservation', 1,  'confirmed',  'checked_in'),
(3, 'check_in',         'reservation', 9,  'confirmed',  'checked_in'),
-- Check-out events
(4, 'check_out',        'reservation', 2,  'checked_in', 'checked_out'),
(3, 'check_out',        'reservation', 3,  'checked_in', 'checked_out'),
(4, 'check_out',        'reservation', 4,  'checked_in', 'checked_out'),
(3, 'check_out',        'reservation', 9,  'checked_in', 'checked_out'),
-- Room 302 taken out of service
(1, 'room_status',      'room',        7,  'available',  'out_of_order'),
-- Yuki Tanaka upgraded to Platinum
(2, 'loyalty_upgrade',  'guest',       4,  'gold',       'platinum'),
-- VIP flag set for John Smith
(2, 'vip_flag',         'guest',       1,  '0',          '1'),
-- No-show marked
(4, 'no_show',          'reservation', 10, 'confirmed',  'no_show');
