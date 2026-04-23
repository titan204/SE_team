-- ============================================================
--  Hotel Management System — Database Schema
--  Normalized to 3NF | MySQL + PDO
--  SE Project — University Level
-- ============================================================

-- ────────────────────────────────────────────────────────────
--  USERS & ROLES  (Staff authentication & access control)
-- ────────────────────────────────────────────────────────────

CREATE TABLE roles (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(50)  NOT NULL UNIQUE,   -- 'manager', 'front_desk', 'housekeeper'
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

INSERT INTO roles (name) VALUES ('manager'), ('front_desk'), ('housekeeper');
