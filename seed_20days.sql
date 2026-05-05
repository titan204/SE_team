-- ============================================================
--  20-DAY FORWARD COVERAGE  (2026-05-05 → 2026-05-24)
--  Rooms: 10,11,12,13,15,16,17,18,19,5 (confirmed available)
--  Guests: 1-8 (read-only references)
-- ============================================================

-- ── RESERVATIONS ─────────────────────────────────────────
INSERT IGNORE INTO `reservations`
 (id,guest_id,room_id,assigned_by,check_in_date,check_out_date,
  actual_check_in,actual_check_out,status,adults,children,
  special_requests,deposit_amount,deposit_paid,is_group,group_id,
  total_price,created_at,updated_at)
VALUES
(30,3,10,3,'2026-05-05','2026-05-08','2026-05-05 14:00:00',NULL,'checked_in',1,0,'Vegetarian meals, quiet room',300.00,1,0,NULL,1500.00,'2026-05-03 10:00:00','2026-05-05 14:00:00'),
(31,5,11,4,'2026-05-05','2026-05-09','2026-05-05 15:30:00',NULL,'checked_in',2,0,'Halal dining, prayer mat',400.00,1,0,NULL,2000.00,'2026-05-03 11:00:00','2026-05-05 15:30:00'),
(32,8,15,3,'2026-05-06','2026-05-10',NULL,NULL,'confirmed',1,0,'Business stay, early check-in',640.00,1,0,NULL,3200.00,'2026-05-04 09:00:00','2026-05-04 09:00:00'),
(33,2,12,4,'2026-05-06','2026-05-09',NULL,NULL,'confirmed',2,0,'Anniversary dinner arrangement',300.00,1,0,NULL,1500.00,'2026-05-04 10:00:00','2026-05-04 10:00:00'),
(34,6,16,3,'2026-05-07','2026-05-11',NULL,NULL,'confirmed',2,0,'Champagne and flowers on arrival',640.00,1,0,NULL,3200.00,'2026-05-04 11:00:00','2026-05-04 11:00:00'),
(35,7,17,4,'2026-05-07','2026-05-10',NULL,NULL,'confirmed',1,0,'Vegan breakfast, yoga mat',480.00,1,0,NULL,2400.00,'2026-05-04 12:00:00','2026-05-04 12:00:00'),
(36,4,5, 3,'2026-05-08','2026-05-12',NULL,NULL,'confirmed',2,0,'VIP platinum - fruit basket, champagne',640.00,1,0,NULL,3200.00,'2026-05-04 13:00:00','2026-05-04 13:00:00'),
(37,1,13,4,'2026-05-08','2026-05-11',NULL,NULL,'confirmed',2,0,'High floor, extra pillows',300.00,1,0,NULL,1500.00,'2026-05-04 14:00:00','2026-05-04 14:00:00'),
(38,3,18,3,'2026-05-09','2026-05-13',NULL,NULL,'confirmed',1,0,'Quiet room, no early housekeeping',640.00,1,0,NULL,3200.00,'2026-05-04 15:00:00','2026-05-04 15:00:00'),
(39,8,19,4,'2026-05-10','2026-05-13',NULL,NULL,'confirmed',2,1,'Extra bed for child, baby cot',480.00,1,0,NULL,2400.00,'2026-05-04 16:00:00','2026-05-04 16:00:00'),
(40,5,10,3,'2026-05-11','2026-05-15',NULL,NULL,'confirmed',1,0,'Halal meals throughout stay',400.00,1,0,NULL,2000.00,'2026-05-04 17:00:00','2026-05-04 17:00:00'),
(41,2,15,4,'2026-05-12','2026-05-16',NULL,NULL,'confirmed',2,0,'Garden view preferred',640.00,1,0,NULL,3200.00,'2026-05-05 08:00:00','2026-05-05 08:00:00'),
(42,6,11,3,'2026-05-12','2026-05-15',NULL,NULL,'confirmed',1,0,'Late checkout requested',300.00,1,0,NULL,1500.00,'2026-05-05 08:30:00','2026-05-05 08:30:00'),
(43,7,16,4,'2026-05-13','2026-05-17',NULL,NULL,'confirmed',2,0,'Vegan breakfast, afternoon tea',640.00,1,0,NULL,3200.00,'2026-05-05 09:00:00','2026-05-05 09:00:00'),
(44,4,12,3,'2026-05-14','2026-05-18',NULL,NULL,'confirmed',2,0,'VIP platinum - premium amenities',400.00,1,0,NULL,2000.00,'2026-05-05 09:30:00','2026-05-05 09:30:00'),
(45,1,17,4,'2026-05-15','2026-05-19',NULL,NULL,'confirmed',2,0,'High floor, late arrival 11pm',640.00,1,0,NULL,3200.00,'2026-05-05 10:00:00','2026-05-05 10:00:00'),
(46,3,18,3,'2026-05-16','2026-05-20',NULL,NULL,'confirmed',1,0,'Business trip, daily newspaper',640.00,1,0,NULL,3200.00,'2026-05-05 10:30:00','2026-05-05 10:30:00'),
(47,8,13,4,'2026-05-17','2026-05-21',NULL,NULL,'confirmed',2,0,'Airport pickup requested',400.00,1,0,NULL,2000.00,'2026-05-05 11:00:00','2026-05-05 11:00:00'),
(48,5,19,3,'2026-05-18','2026-05-22',NULL,NULL,'confirmed',2,0,'Halal meals, prayer direction card',640.00,1,0,NULL,3200.00,'2026-05-05 11:30:00','2026-05-05 11:30:00'),
(49,2,5, 4,'2026-05-19','2026-05-23',NULL,NULL,'confirmed',1,0,'Blackout curtains, no disturbance',640.00,1,0,NULL,3200.00,'2026-05-05 12:00:00','2026-05-05 12:00:00');

-- ── FOLIOS for reservations 30-49 ─────────────────────────
-- Real schema: (id, reservation_id, total_amount, amount_paid, status, created_at, updated_at)
-- balance_due is GENERATED (total_amount - amount_paid)
INSERT IGNORE INTO `folios`
 (id,reservation_id,total_amount,amount_paid,status,created_at,updated_at)
VALUES
(20,30,1500.00, 300.00,'open','2026-05-05 14:00:00','2026-05-05 14:00:00'),
(21,31,2000.00, 400.00,'open','2026-05-05 15:30:00','2026-05-05 15:30:00'),
(22,32,3200.00, 640.00,'open','2026-05-04 09:00:00','2026-05-04 09:00:00'),
(23,33,1500.00, 300.00,'open','2026-05-04 10:00:00','2026-05-04 10:00:00'),
(24,34,3200.00, 640.00,'open','2026-05-04 11:00:00','2026-05-04 11:00:00'),
(25,35,2400.00, 480.00,'open','2026-05-04 12:00:00','2026-05-04 12:00:00'),
(26,36,3200.00, 640.00,'open','2026-05-04 13:00:00','2026-05-04 13:00:00'),
(27,37,1500.00, 300.00,'open','2026-05-04 14:00:00','2026-05-04 14:00:00'),
(28,38,3200.00, 640.00,'open','2026-05-04 15:00:00','2026-05-04 15:00:00'),
(29,39,2400.00, 480.00,'open','2026-05-04 16:00:00','2026-05-04 16:00:00'),
(30,40,2000.00, 400.00,'open','2026-05-04 17:00:00','2026-05-04 17:00:00'),
(31,41,3200.00, 640.00,'open','2026-05-05 08:00:00','2026-05-05 08:00:00'),
(32,42,1500.00, 300.00,'open','2026-05-05 08:30:00','2026-05-05 08:30:00'),
(33,43,3200.00, 640.00,'open','2026-05-05 09:00:00','2026-05-05 09:00:00'),
(34,44,2000.00, 400.00,'open','2026-05-05 09:30:00','2026-05-05 09:30:00'),
(35,45,3200.00, 640.00,'open','2026-05-05 10:00:00','2026-05-05 10:00:00'),
(36,46,3200.00, 640.00,'open','2026-05-05 10:30:00','2026-05-05 10:30:00'),
(37,47,2000.00, 400.00,'open','2026-05-05 11:00:00','2026-05-05 11:00:00'),
(38,48,3200.00, 640.00,'open','2026-05-05 11:30:00','2026-05-05 11:30:00'),
(39,49,3200.00, 640.00,'open','2026-05-05 12:00:00','2026-05-05 12:00:00');

-- ── PAYMENTS — daily coverage 2026-05-05 to 2026-05-24 ───
-- (folio_id, amount, method, reference, processed_by, processed_at)
INSERT IGNORE INTO `payments`
 (id,folio_id,amount,method,reference,processed_by,processed_at)
VALUES
-- May 5: deposits for res 30+31 (already checked in today)
(70,20, 300.00,'credit_card','DEP-30-0505',3,'2026-05-05 14:05:00'),
(71,21, 400.00,'credit_card','DEP-31-0505',4,'2026-05-05 15:35:00'),
-- May 6: res 32+33 deposits
(72,22, 640.00,'credit_card','DEP-32-0506',3,'2026-05-06 13:00:00'),
(73,23, 300.00,'cash',       'DEP-33-0506',4,'2026-05-06 14:30:00'),
-- May 7: res 34+35 deposits
(74,24, 640.00,'credit_card','DEP-34-0507',3,'2026-05-07 13:00:00'),
(75,25, 480.00,'debit_card', 'DEP-35-0507',4,'2026-05-07 14:00:00'),
-- May 8: res 36+37 deposits + res 30 settlement (checkout)
(76,26, 640.00,'credit_card','DEP-36-0508',3,'2026-05-08 13:30:00'),
(77,27, 300.00,'cash',       'DEP-37-0508',4,'2026-05-08 14:00:00'),
(78,20,1200.00,'credit_card','SET-30-0508',3,'2026-05-08 11:30:00'),
-- May 9: res 38 deposit + res 33 settlement
(79,28, 640.00,'credit_card','DEP-38-0509',3,'2026-05-09 13:00:00'),
(80,23,1200.00,'cash',       'SET-33-0509',4,'2026-05-09 11:00:00'),
-- May 10: res 39 deposit + res 32+35 settlements
(81,29, 480.00,'bank_transfer','DEP-39-0510',4,'2026-05-10 12:00:00'),
(82,22,2560.00,'credit_card','SET-32-0510',3,'2026-05-10 11:30:00'),
(83,25,1920.00,'credit_card','SET-35-0510',4,'2026-05-10 12:30:00'),
-- May 11: res 40 deposit + res 31+37 settlements
(84,30, 400.00,'credit_card','DEP-40-0511',3,'2026-05-11 13:00:00'),
(85,21,1600.00,'credit_card','SET-31-0511',4,'2026-05-11 11:00:00'),
(86,27,1200.00,'credit_card','SET-37-0511',3,'2026-05-11 12:00:00'),
-- May 12: res 41+42 deposits + res 34+38 partial
(87,31, 640.00,'credit_card','DEP-41-0512',4,'2026-05-12 13:00:00'),
(88,32, 300.00,'cash',       'DEP-42-0512',3,'2026-05-12 13:30:00'),
(89,24,1500.00,'credit_card','PAR-34-0512',4,'2026-05-12 10:00:00'),
-- May 13: res 43 deposit + res 38+39 settlements
(90,33, 640.00,'debit_card', 'DEP-43-0513',4,'2026-05-13 13:00:00'),
(91,28,2560.00,'credit_card','SET-38-0513',3,'2026-05-13 11:00:00'),
(92,29,1920.00,'credit_card','SET-39-0513',4,'2026-05-13 12:00:00'),
-- May 14: res 44 deposit + res 34 balance
(93,34, 400.00,'credit_card','DEP-44-0514',3,'2026-05-14 13:00:00'),
(94,24,1060.00,'credit_card','SET-34-0514',4,'2026-05-14 11:00:00'),
-- May 15: res 45 deposit + res 40+42 settlements
(95,35, 640.00,'credit_card','DEP-45-0515',3,'2026-05-15 13:00:00'),
(96,30,1600.00,'bank_transfer','SET-40-0515',4,'2026-05-15 11:00:00'),
(97,32,1200.00,'credit_card','SET-42-0515',3,'2026-05-15 12:00:00'),
-- May 16: res 46 deposit + res 36+41 partial
(98, 36, 640.00,'credit_card','DEP-46-0516',4,'2026-05-16 13:00:00'),
(99, 26,1500.00,'credit_card','PAR-36-0516',3,'2026-05-16 10:00:00'),
-- May 17: res 47 deposit + res 36 balance + res 43 partial
(100,37, 400.00,'credit_card','DEP-47-0517',4,'2026-05-17 13:00:00'),
(101,26,1060.00,'credit_card','SET-36-0517',3,'2026-05-17 11:00:00'),
-- May 18: res 48 deposit + res 41+43 settlements
(102,38, 640.00,'credit_card','DEP-48-0518',3,'2026-05-18 13:00:00'),
(103,31,2560.00,'credit_card','SET-41-0518',4,'2026-05-18 11:00:00'),
(104,33,2560.00,'credit_card','SET-43-0518',3,'2026-05-18 12:00:00'),
-- May 19: res 49 deposit + res 44 settlement
(105,39, 640.00,'debit_card', 'DEP-49-0519',4,'2026-05-19 13:00:00'),
(106,34,1600.00,'credit_card','SET-44-0519',3,'2026-05-19 11:00:00'),
-- May 20: res 45+46 settlements
(107,35,2560.00,'credit_card','SET-45-0520',4,'2026-05-20 11:00:00'),
(108,36, 640.00,'credit_card','BAL-46-0520',3,'2026-05-20 12:00:00'),
-- May 21: res 47 settlement
(109,37,1600.00,'cash',       'SET-47-0521',4,'2026-05-21 11:00:00'),
-- May 22: res 48 settlement
(110,38,2560.00,'credit_card','SET-48-0522',3,'2026-05-22 11:00:00'),
-- May 23: res 49 settlement
(111,39,2560.00,'credit_card','SET-49-0523',4,'2026-05-23 11:00:00');

-- ── WORK ORDERS (fills the empty base work_orders table) ─
INSERT IGNORE INTO `work_orders`
 (id,type,room_id,asset_id,description,priority,status,
  assigned_to_user_id,created_by_user_id,work_performed,
  parts_used,time_spent_minutes,supervisor_id,
  created_at,completed_at,closed_at)
VALUES
(1,'emergency',7,NULL,'AC unit grinding noise — Room 302 OOO','high','in_progress',3,2,NULL,NULL,NULL,1,'2026-05-03 09:00:00',NULL,NULL),
(2,'preventative',NULL,1,'Quarterly HVAC filter replacement — all floors','normal','completed',3,1,'All filters replaced. Units operational.','[]',180,1,'2026-05-01 08:00:00','2026-05-01 11:00:00',NULL),
(3,'emergency',4,NULL,'Bathroom tap dripping — Room 202','low','open',3,2,NULL,NULL,NULL,NULL,'2026-05-04 10:00:00',NULL,NULL),
(4,'preventative',NULL,8,'Monthly elevator inspection — Elevator B','normal','in_progress',3,1,NULL,NULL,NULL,1,'2026-05-03 07:00:00',NULL,NULL),
(5,'emergency',NULL,8,'Elevator B door sensor malfunction','high','open',3,2,NULL,NULL,NULL,1,'2026-05-04 14:00:00',NULL,NULL),
(6,'preventative',5,NULL,'Deep clean Room 203 — post inspection','normal','completed',5,6,'Full deep clean. Carpet steamed. Room cleared.','[]',120,1,'2026-05-04 08:00:00','2026-05-04 10:00:00',NULL),
(7,'emergency',NULL,5,'Pool pump pressure warning','normal','completed',3,2,'Pressure valve adjusted. System stable.','[]',45,1,'2026-05-03 06:00:00','2026-05-03 06:45:00',NULL),
(8,'preventative',NULL,7,'Boiler descaling — quarterly','normal','pending_parts',3,1,NULL,'[]',NULL,1,'2026-05-05 08:00:00',NULL,NULL),
(9,'emergency',3,NULL,'Ceiling light flickering — Room 201','low','open',3,2,NULL,NULL,NULL,NULL,'2026-05-05 09:30:00',NULL,NULL),
(10,'preventative',NULL,NULL,'Annual fire suppression inspection — all zones','high','open',3,1,NULL,NULL,NULL,1,'2026-05-05 10:00:00',NULL,NULL);

-- ── HOUSEKEEPING TASKS for turnover coverage ──────────────
INSERT IGNORE INTO `housekeeping_tasks`
 (room_id,assigned_to,task_type,status,notes,quality_score,
  created_at,updated_at,completed_at)
VALUES
(10,5,'cleaning','done','Check-in clean res 30. Vegetarian welcome plate placed.',88,'2026-05-05 07:00:00','2026-05-05 09:30:00','2026-05-05 09:30:00'),
(11,6,'cleaning','done','Check-in clean res 31. Prayer mat positioned.',91,'2026-05-05 07:30:00','2026-05-05 09:45:00','2026-05-05 09:45:00'),
(15,6,'cleaning','done','Pre-arrival clean res 32. Business setup.',90,'2026-05-06 10:00:00','2026-05-06 12:00:00','2026-05-06 12:00:00'),
(12,7,'cleaning','done','Pre-arrival clean res 33. Anniversary rose petals.',92,'2026-05-06 10:30:00','2026-05-06 12:30:00','2026-05-06 12:30:00'),
(16,5,'cleaning','done','Pre-arrival clean res 34. Champagne chilled.',89,'2026-05-07 10:00:00','2026-05-07 12:00:00','2026-05-07 12:00:00'),
(17,6,'cleaning','done','Pre-arrival clean res 35. Yoga mat placed.',87,'2026-05-07 10:30:00','2026-05-07 12:30:00','2026-05-07 12:30:00'),
(10,7,'deep_clean','done','Post-checkout res 30. Deep clean completed.',84,'2026-05-08 12:00:00','2026-05-08 14:30:00','2026-05-08 14:30:00'),
(5, 5,'cleaning','done','VIP prep res 36. Platinum amenities set.',95,'2026-05-08 10:00:00','2026-05-08 12:30:00','2026-05-08 12:30:00'),
(13,6,'cleaning','done','Pre-arrival clean res 37. Extra pillows.',88,'2026-05-08 10:30:00','2026-05-08 12:00:00','2026-05-08 12:00:00'),
(18,7,'cleaning','done','Pre-arrival clean res 38. DND sign placed.',86,'2026-05-09 10:00:00','2026-05-09 12:00:00','2026-05-09 12:00:00'),
(19,6,'cleaning','in_progress','Pre-arrival clean res 39. Baby cot setup.',NULL,'2026-05-10 10:00:00','2026-05-10 10:00:00',NULL),
(10,7,'cleaning','pending','Turnover res 30 checkout — prep for res 40.',NULL,'2026-05-11 07:00:00','2026-05-11 07:00:00',NULL),
(15,5,'cleaning','pending','Post-checkout res 32 — prep for res 41.',NULL,'2026-05-12 07:00:00','2026-05-12 07:00:00',NULL),
(11,6,'cleaning','pending','Post-checkout res 31 — prep for res 42.',NULL,'2026-05-12 07:30:00','2026-05-12 07:30:00',NULL),
(16,5,'cleaning','pending','Post-checkout res 34 — prep for res 43.',NULL,'2026-05-13 07:00:00','2026-05-13 07:00:00',NULL),
(12,6,'cleaning','pending','Post-checkout res 33 — prep for res 44.',NULL,'2026-05-14 07:00:00','2026-05-14 07:00:00',NULL),
(17,7,'cleaning','pending','Post-checkout res 35 — prep for res 45.',NULL,'2026-05-15 07:00:00','2026-05-15 07:00:00',NULL),
(18,5,'cleaning','pending','Post-checkout res 38 — prep for res 46.',NULL,'2026-05-16 07:00:00','2026-05-16 07:00:00',NULL),
(13,6,'cleaning','pending','Post-checkout res 37 — prep for res 47.',NULL,'2026-05-17 07:00:00','2026-05-17 07:00:00',NULL),
(19,7,'cleaning','pending','Post-checkout res 39 — prep for res 48.',NULL,'2026-05-18 07:00:00','2026-05-18 07:00:00',NULL),
(5, 5,'cleaning','pending','Post-checkout res 36 — prep for res 49.',NULL,'2026-05-19 07:00:00','2026-05-19 07:00:00',NULL);
