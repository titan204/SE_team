-- ============================================================
--  UC29 — LOG MINIBAR CONSUMPTION
-- ============================================================

CREATE TABLE minibar_items (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name             VARCHAR(150) NOT NULL,
    sku              VARCHAR(50)  NOT NULL UNIQUE,
    price            DECIMAL(10,2) NOT NULL,
    reorder_threshold INT UNSIGNED NOT NULL DEFAULT 2,
    is_active        TINYINT(1)   NOT NULL DEFAULT 1
);

CREATE TABLE minibar_inventory (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    room_id          INT UNSIGNED NOT NULL,
    item_id          INT UNSIGNED NOT NULL,
    current_stock    INT UNSIGNED NOT NULL DEFAULT 0,
    last_restocked_at DATETIME,
    UNIQUE KEY uq_mi_room_item (room_id, item_id),
    CONSTRAINT fk_mi_room FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
    CONSTRAINT fk_mi_item FOREIGN KEY (item_id) REFERENCES minibar_items(id) ON DELETE CASCADE
);

CREATE TABLE minibar_logs (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    room_id         INT UNSIGNED NOT NULL,
    reservation_id  INT UNSIGNED,
    housekeeper_id  INT UNSIGNED,
    items           JSON NOT NULL,
    total_amount    DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    logged_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_ml_room FOREIGN KEY (room_id)        REFERENCES rooms(id)        ON DELETE CASCADE,
    CONSTRAINT fk_ml_res  FOREIGN KEY (reservation_id) REFERENCES reservations(id) ON DELETE SET NULL,
    CONSTRAINT fk_ml_hk   FOREIGN KEY (housekeeper_id) REFERENCES users(id)        ON DELETE SET NULL
);

-- Billing retry queue for failed minibar charges
CREATE TABLE IF NOT EXISTS billing_retry_queue (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    reservation_id INT UNSIGNED NOT NULL,
    description    VARCHAR(255) NOT NULL,
    amount         DECIMAL(10,2) NOT NULL,
    quantity       INT UNSIGNED NOT NULL DEFAULT 1,
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_brq_res FOREIGN KEY (reservation_id) REFERENCES reservations(id) ON DELETE CASCADE
);

-- Seed minibar items
INSERT INTO minibar_items (name, sku, price, reorder_threshold, is_active) VALUES
('Still Water 500ml',     'MB-WATER-500',   3.00,  3, 1),
('Sparkling Water 500ml', 'MB-SPARK-500',   3.50,  3, 1),
('Orange Juice 330ml',    'MB-OJ-330',      4.50,  2, 1),
('Cola 330ml',            'MB-COLA-330',    4.00,  3, 1),
('Beer (Local) 330ml',    'MB-BEER-330',    8.00,  2, 1),
('Mixed Nuts 50g',        'MB-NUTS-050',    6.00,  2, 1),
('Chocolate Bar',         'MB-CHOC-001',    5.00,  2, 1),
('Chips 40g',             'MB-CHIP-040',    4.50,  2, 1),
('Sparkling Wine 200ml',  'MB-WINE-200',   18.00,  1, 1),
('Mineral Water 1L',      'MB-WATER-1L',    5.00,  2, 1);

-- Seed minibar inventory (for rooms with minibar — Suites + Deluxe)
INSERT INTO minibar_inventory (room_id, item_id, current_stock, last_restocked_at) VALUES
(2,  1, 4, NOW()), (2,  2, 2, NOW()), (2,  3, 3, NOW()), (2,  4, 4, NOW()), (2,  5, 2, NOW()),
(2,  6, 2, NOW()), (2,  7, 3, NOW()), (2,  8, 2, NOW()), (2,  9, 1, NOW()), (2, 10, 2, NOW()),
(4,  1, 4, NOW()), (4,  2, 2, NOW()), (4,  3, 3, NOW()), (4,  4, 4, NOW()), (4,  5, 2, NOW()),
(4,  6, 2, NOW()), (4,  7, 3, NOW()), (4,  8, 2, NOW()), (4,  9, 1, NOW()), (4, 10, 2, NOW()),
(6,  1, 6, NOW()), (6,  2, 4, NOW()), (6,  3, 4, NOW()), (6,  4, 6, NOW()), (6,  5, 4, NOW()),
(6,  6, 3, NOW()), (6,  7, 4, NOW()), (6,  8, 3, NOW()), (6,  9, 2, NOW()), (6, 10, 4, NOW());

-- ============================================================
--  UC30 / UC37 — LOG FOUND ITEM / MANAGE LOST & FOUND
-- ============================================================

-- Extend found_items (UC30 schema)
CREATE TABLE found_items (
    id                    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    lf_reference          VARCHAR(20)  NOT NULL UNIQUE,
    description           TEXT         NOT NULL,
    location_type         ENUM('room','public') NOT NULL DEFAULT 'room',
    room_number           VARCHAR(10),
    public_area           ENUM('lobby','pool','restaurant','elevator','parking','other'),
    `condition`           ENUM('good','damaged','fragile') NOT NULL DEFAULT 'good',
    photo_url             VARCHAR(500),
    is_high_value         TINYINT(1)   NOT NULL DEFAULT 0,
    escalated_to_security TINYINT(1)   NOT NULL DEFAULT 0,
    found_by_user_id      INT UNSIGNED,
    found_at              DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status                ENUM('stored','matched','claimed','shipped','returned','disposed') NOT NULL DEFAULT 'stored',
    CONSTRAINT fk_fi_user FOREIGN KEY (found_by_user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE security_alerts (
    id                    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    alert_type            VARCHAR(100) NOT NULL,
    message               TEXT         NOT NULL,
    related_found_item_id INT UNSIGNED,
    priority              ENUM('normal','urgent') NOT NULL DEFAULT 'normal',
    created_at            DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    resolved_at           DATETIME,
    CONSTRAINT fk_sa_item FOREIGN KEY (related_found_item_id) REFERENCES found_items(id) ON DELETE SET NULL
);

-- UC37 — Guest lost item reports
CREATE TABLE lost_item_reports (
    id                    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    guest_id              INT UNSIGNED NOT NULL,
    description           TEXT         NOT NULL,
    reservation_id        INT UNSIGNED,
    lost_date             DATE,
    matched_found_item_id INT UNSIGNED,
    status                ENUM('open','matched','closed') NOT NULL DEFAULT 'open',
    created_at            DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_lir_guest FOREIGN KEY (guest_id)              REFERENCES guests(id)      ON DELETE CASCADE,
    CONSTRAINT fk_lir_res   FOREIGN KEY (reservation_id)        REFERENCES reservations(id) ON DELETE SET NULL,
    CONSTRAINT fk_lir_fi    FOREIGN KEY (matched_found_item_id) REFERENCES found_items(id)  ON DELETE SET NULL
);

CREATE TABLE item_returns (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    found_item_id  INT UNSIGNED NOT NULL,
    guest_id       INT UNSIGNED NOT NULL,
    return_method  ENUM('pickup','courier') NOT NULL DEFAULT 'pickup',
    return_address TEXT,
    shipping_cost  DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    returned_at    DATETIME,
    CONSTRAINT fk_ir_item  FOREIGN KEY (found_item_id) REFERENCES found_items(id) ON DELETE CASCADE,
    CONSTRAINT fk_ir_guest FOREIGN KEY (guest_id)      REFERENCES guests(id)      ON DELETE CASCADE
);

-- ============================================================
--  UC31 — TRIGGER LOW-STOCK ALERT
-- ============================================================

CREATE TABLE supply_items (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(150) NOT NULL,
    category      ENUM('cleaning','linen','amenity','minibar','other') NOT NULL DEFAULT 'other',
    min_threshold INT UNSIGNED NOT NULL DEFAULT 5,
    unit          VARCHAR(30)  NOT NULL DEFAULT 'units',
    is_active     TINYINT(1)   NOT NULL DEFAULT 1
);

CREATE TABLE supply_inventory (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    item_id       INT UNSIGNED NOT NULL,
    location      ENUM('housekeeping_store','floor1','floor2','floor3','laundry','kitchen','general') NOT NULL DEFAULT 'general',
    current_stock INT UNSIGNED NOT NULL DEFAULT 0,
    last_updated  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_si_item_loc (item_id, location),
    CONSTRAINT fk_si_item FOREIGN KEY (item_id) REFERENCES supply_items(id) ON DELETE CASCADE
);

CREATE TABLE low_stock_alerts (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    item_id         INT UNSIGNED NOT NULL,
    location        VARCHAR(50)  NOT NULL,
    current_stock   INT          NOT NULL,
    min_threshold   INT          NOT NULL,
    status          ENUM('active','acknowledged','resolved') NOT NULL DEFAULT 'active',
    acknowledged_by INT UNSIGNED,
    acknowledged_at DATETIME,
    escalated       TINYINT(1)   NOT NULL DEFAULT 0,
    created_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_lsa_item FOREIGN KEY (item_id)         REFERENCES supply_items(id) ON DELETE CASCADE,
    CONSTRAINT fk_lsa_user FOREIGN KEY (acknowledged_by) REFERENCES users(id)        ON DELETE SET NULL
);

CREATE TABLE restocking_requisitions (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    items               JSON         NOT NULL,
    requested_by_user_id INT UNSIGNED,
    status              ENUM('pending','in_progress','completed') NOT NULL DEFAULT 'pending',
    created_at          DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_rr_user FOREIGN KEY (requested_by_user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Seed supply items
INSERT INTO supply_items (name, category, min_threshold, unit) VALUES
('Bath Towels',           'linen',    20, 'pieces'),
('Hand Towels',           'linen',    30, 'pieces'),
('Bed Sheets (King)',     'linen',    10, 'sets'),
('Bed Sheets (Queen)',    'linen',    15, 'sets'),
('Shampoo Bottles',       'amenity',  50, 'bottles'),
('Conditioner Bottles',   'amenity',  50, 'bottles'),
('Soap Bars',             'amenity', 100, 'bars'),
('Toilet Rolls',          'amenity', 100, 'rolls'),
('All-Purpose Cleaner',   'cleaning',  10, 'litres'),
('Disinfectant Spray',    'cleaning',  15, 'cans');

INSERT INTO supply_inventory (item_id, location, current_stock) VALUES
(1, 'housekeeping_store', 45),
(2, 'housekeeping_store', 60),
(3, 'housekeeping_store', 18),
(4, 'housekeeping_store', 22),
(5, 'housekeeping_store', 80),
(6, 'housekeeping_store', 75),
(7, 'housekeeping_store', 120),
(8, 'housekeeping_store', 95),
(9, 'housekeeping_store', 12),
(10,'housekeeping_store',  3);

-- ============================================================
--  UC32 — MANAGE QUALITY ASSURANCE
-- ============================================================

-- Add flagged_for_qa column to feedback (links UC20 → UC32)
ALTER TABLE feedback
    ADD COLUMN IF NOT EXISTS overall_score TINYINT UNSIGNED,
    ADD COLUMN IF NOT EXISTS flagged_for_qa TINYINT(1) NOT NULL DEFAULT 0;

CREATE TABLE qa_inspections (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    room_id          INT UNSIGNED NOT NULL,
    inspector_id     INT UNSIGNED NOT NULL,
    inspection_date  DATE         NOT NULL,
    overall_result   ENUM('pass','fail','corrective_action') NOT NULL,
    checklist_scores JSON         NOT NULL,
    notes            TEXT,
    created_at       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_qa_room      FOREIGN KEY (room_id)      REFERENCES rooms(id)  ON DELETE CASCADE,
    CONSTRAINT fk_qa_inspector FOREIGN KEY (inspector_id) REFERENCES users(id)  ON DELETE CASCADE
);

CREATE TABLE corrective_tasks (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    qa_inspection_id INT UNSIGNED NOT NULL,
    assigned_to_user_id INT UNSIGNED,
    task_description TEXT         NOT NULL,
    due_by           DATETIME,
    status           ENUM('pending','completed') NOT NULL DEFAULT 'pending',
    CONSTRAINT fk_ct_qa   FOREIGN KEY (qa_inspection_id)   REFERENCES qa_inspections(id) ON DELETE CASCADE,
    CONSTRAINT fk_ct_user FOREIGN KEY (assigned_to_user_id) REFERENCES users(id)          ON DELETE SET NULL
);

-- ============================================================
--  UC33 — SUBMIT QUALITY SCORE
-- ============================================================

CREATE TABLE quality_scores (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    inspection_id       INT UNSIGNED NOT NULL,
    housekeeper_id      INT UNSIGNED NOT NULL,
    room_id             INT UNSIGNED NOT NULL,
    cleanliness         TINYINT UNSIGNED NOT NULL CHECK (cleanliness BETWEEN 0 AND 100),
    presentation        TINYINT UNSIGNED NOT NULL CHECK (presentation BETWEEN 0 AND 100),
    completeness        TINYINT UNSIGNED NOT NULL CHECK (completeness BETWEEN 0 AND 100),
    speed               TINYINT UNSIGNED NOT NULL CHECK (speed BETWEEN 0 AND 100),
    overall_score       DECIMAL(5,2) NOT NULL,
    notes               TEXT,
    photo_urls          JSON,
    submitted_by_user_id INT UNSIGNED,
    is_disputed         TINYINT(1) NOT NULL DEFAULT 0,
    dispute_resolution  TEXT,
    created_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_qs_inspection  FOREIGN KEY (inspection_id)       REFERENCES qa_inspections(id) ON DELETE CASCADE,
    CONSTRAINT fk_qs_housekeeper FOREIGN KEY (housekeeper_id)      REFERENCES users(id)          ON DELETE CASCADE,
    CONSTRAINT fk_qs_room        FOREIGN KEY (room_id)             REFERENCES rooms(id)          ON DELETE CASCADE,
    CONSTRAINT fk_qs_submitter   FOREIGN KEY (submitted_by_user_id) REFERENCES users(id)          ON DELETE SET NULL
);

CREATE TABLE housekeeper_performance (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    housekeeper_id   INT UNSIGNED NOT NULL UNIQUE,
    avg_score        DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    total_inspections INT UNSIGNED NOT NULL DEFAULT 0,
    trend            ENUM('improving','stable','declining') NOT NULL DEFAULT 'stable',
    updated_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_hp_housekeeper FOREIGN KEY (housekeeper_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Seed housekeeper performance for existing housekeepers (user IDs 5,6,7)
INSERT INTO housekeeper_performance (housekeeper_id, avg_score, total_inspections, trend) VALUES
(5, 88.50, 12, 'stable'),
(6, 91.25, 15, 'improving'),
(7, 85.00,  9, 'stable');

-- ============================================================
--  UC34 — MAINTENANCE WORK-ORDER BASE INFRASTRUCTURE
-- ============================================================

CREATE TABLE assets (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(150) NOT NULL,
    location   VARCHAR(150) NOT NULL,
    asset_type ENUM('hvac','elevator','plumbing','electrical','equipment','other') NOT NULL DEFAULT 'other',
    status     ENUM('operational','under_maintenance','decommissioned') NOT NULL DEFAULT 'operational'
);

CREATE TABLE work_orders (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    type                ENUM('emergency','preventative') NOT NULL,
    room_id             INT UNSIGNED,
    asset_id            INT UNSIGNED,
    description         TEXT         NOT NULL,
    priority            ENUM('low','normal','high','emergency') NOT NULL DEFAULT 'normal',
    status              ENUM('open','in_progress','pending_parts','completed','closed','rejected') NOT NULL DEFAULT 'open',
    assigned_to_user_id INT UNSIGNED,
    created_by_user_id  INT UNSIGNED,
    work_performed      TEXT,
    parts_used          JSON,
    time_spent_minutes  INT UNSIGNED,
    supervisor_id       INT UNSIGNED,
    rejection_reason    TEXT,
    created_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    completed_at        DATETIME,
    closed_at           DATETIME,
    CONSTRAINT fk_wo_room       FOREIGN KEY (room_id)             REFERENCES rooms(id)  ON DELETE SET NULL,
    CONSTRAINT fk_wo_asset      FOREIGN KEY (asset_id)            REFERENCES assets(id) ON DELETE SET NULL,
    CONSTRAINT fk_wo_assigned   FOREIGN KEY (assigned_to_user_id) REFERENCES users(id)  ON DELETE SET NULL,
    CONSTRAINT fk_wo_created    FOREIGN KEY (created_by_user_id)  REFERENCES users(id)  ON DELETE SET NULL,
    CONSTRAINT fk_wo_supervisor FOREIGN KEY (supervisor_id)       REFERENCES users(id)  ON DELETE SET NULL
);

CREATE TABLE work_order_logs (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    work_order_id       INT UNSIGNED NOT NULL,
    action              VARCHAR(100) NOT NULL,
    performed_by_user_id INT UNSIGNED,
    notes               TEXT,
    created_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_wol_wo   FOREIGN KEY (work_order_id)        REFERENCES work_orders(id) ON DELETE CASCADE,
    CONSTRAINT fk_wol_user FOREIGN KEY (performed_by_user_id) REFERENCES users(id)       ON DELETE SET NULL
);

-- ============================================================
--  UC35 — LOG EMERGENCY REPAIR (extends UC34)
-- ============================================================

CREATE TABLE emergency_flags (
    id                      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    work_order_id           INT UNSIGNED NOT NULL UNIQUE,
    severity                ENUM('low','medium','high','safety_critical') NOT NULL,
    is_safety_critical      TINYINT(1) NOT NULL DEFAULT 0,
    property_alert_triggered TINYINT(1) NOT NULL DEFAULT 0,
    CONSTRAINT fk_ef_wo FOREIGN KEY (work_order_id) REFERENCES work_orders(id) ON DELETE CASCADE
);

CREATE TABLE property_wide_alerts (
    id                       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    alert_type               VARCHAR(100) NOT NULL,
    message                  TEXT         NOT NULL,
    triggered_by_work_order_id INT UNSIGNED,
    status                   ENUM('active','resolved') NOT NULL DEFAULT 'active',
    created_at               DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_pwa_wo FOREIGN KEY (triggered_by_work_order_id) REFERENCES work_orders(id) ON DELETE SET NULL
);

CREATE TABLE replacement_review_flags (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    room_id         INT UNSIGNED,
    asset_id        INT UNSIGNED,
    emergency_count INT UNSIGNED NOT NULL DEFAULT 0,
    flagged_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    reviewed        TINYINT(1) NOT NULL DEFAULT 0,
    CONSTRAINT fk_rrf_room  FOREIGN KEY (room_id)  REFERENCES rooms(id)  ON DELETE SET NULL,
    CONSTRAINT fk_rrf_asset FOREIGN KEY (asset_id) REFERENCES assets(id) ON DELETE SET NULL
);

-- ============================================================
--  UC36 — SCHEDULE PREVENTATIVE MAINTENANCE (extends UC34)
-- ============================================================

CREATE TABLE preventative_schedules (
    id                   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    work_order_id        INT UNSIGNED NOT NULL,
    asset_id             INT UNSIGNED,
    room_id              INT UNSIGNED,
    maintenance_type     ENUM('hvac','elevator','plumbing','electrical','deep_cleaning','other') NOT NULL,
    scheduled_date       DATE         NOT NULL,
    estimated_minutes    INT UNSIGNED NOT NULL DEFAULT 60,
    is_recurring         TINYINT(1)   NOT NULL DEFAULT 0,
    recurrence_frequency ENUM('weekly','monthly','quarterly','yearly'),
    next_due_date        DATE,
    CONSTRAINT fk_ps_wo    FOREIGN KEY (work_order_id) REFERENCES work_orders(id) ON DELETE CASCADE,
    CONSTRAINT fk_ps_asset FOREIGN KEY (asset_id)      REFERENCES assets(id)      ON DELETE SET NULL,
    CONSTRAINT fk_ps_room  FOREIGN KEY (room_id)       REFERENCES rooms(id)       ON DELETE SET NULL
);

-- Seed assets
INSERT INTO assets (name, location, asset_type, status) VALUES
('Main HVAC Unit — Floor 1',  'Floor 1 Plant Room',  'hvac',       'operational'),
('Main HVAC Unit — Floor 2',  'Floor 2 Plant Room',  'hvac',       'operational'),
('Main HVAC Unit — Floor 3',  'Floor 3 Plant Room',  'hvac',       'under_maintenance'),
('Passenger Elevator 1',       'Lobby',               'elevator',   'operational'),
('Passenger Elevator 2',       'North Wing',          'elevator',   'operational'),
('Boiler — Hot Water System',  'Basement',            'plumbing',   'operational'),
('Main Electrical Panel',      'Basement',            'electrical', 'operational'),
('Pool Pump System',           'Pool Area',           'equipment',  'operational');
