-- ============================================================
--  AUDIT LOG SEED — 30-day coverage (Apr 6 → May 5, 2026)
--  Columns: user_id, action, target_type, target_id,
--            old_value, new_value, ip_address, user_agent, created_at
-- ============================================================

INSERT INTO `audit_log`
 (user_id,action,target_type,target_id,old_value,new_value,ip_address,user_agent,created_at)
VALUES
-- April 6-10
(1,'login','user',1,NULL,NULL,'197.32.14.5','Mozilla/5.0 (Windows NT 10.0) Chrome/123.0','2026-04-06 08:01:22'),
(3,'reservation_created','reservation',1,NULL,'{\"room\":\"102\",\"guest\":\"John Smith\"}','197.32.14.5','Mozilla/5.0 (Windows NT 10.0) Chrome/123.0','2026-04-06 08:15:44'),
(4,'reservation_created','reservation',2,NULL,'{\"room\":\"201\",\"guest\":\"Emma Wilson\"}','197.32.14.8','Mozilla/5.0 (Windows NT 10.0) Chrome/123.0','2026-04-07 09:22:10'),
(2,'room_status_changed','room',3,'available','occupied','197.32.14.5','Mozilla/5.0 (Windows NT 10.0) Chrome/123.0','2026-04-08 14:05:33'),
(3,'check_in','reservation',2,NULL,'checked_in','197.32.14.5','Mozilla/5.0 (Windows NT 10.0) Chrome/123.0','2026-04-09 13:15:00'),
(1,'user_role_updated','user',6,'front_desk','revenue_manager','10.0.0.1','Mozilla/5.0 (Macintosh) Chrome/123.0','2026-04-10 09:00:00'),
-- April 11-15
(2,'payment_recorded','payment',40,NULL,'{\"amount\":2150,\"method\":\"credit_card\"}','197.32.14.8','Mozilla/5.0 Chrome/123.0','2026-04-11 08:20:00'),
(3,'check_out','reservation',2,'checked_in','checked_out','197.32.14.5','Mozilla/5.0 Chrome/123.0','2026-04-12 11:45:00'),
(4,'reservation_cancelled','reservation',8,'confirmed','cancelled','197.32.14.8','Mozilla/5.0 Chrome/123.0','2026-04-13 10:30:00'),
(1,'login','user',1,NULL,NULL,'197.32.14.5','Mozilla/5.0 Chrome/123.0','2026-04-14 07:58:11'),
(2,'maintenance_order_created','maintenance_orders',1,NULL,'{\"room\":\"302\",\"issue\":\"AC unit\",\"priority\":\"high\"}','197.32.14.5','Mozilla/5.0 Chrome/123.0','2026-04-15 09:00:00'),
-- April 16-20
(3,'payment_recorded','payment',41,NULL,'{\"amount\":1500,\"method\":\"credit_card\"}','197.32.14.8','Mozilla/5.0 Chrome/123.0','2026-04-16 08:10:00'),
(5,'housekeeping_task_updated','housekeeping_tasks',3,'pending','in_progress','192.168.1.10','Mozilla/5.0 Chrome/123.0','2026-04-16 09:30:00'),
(5,'housekeeping_task_updated','housekeeping_tasks',3,'in_progress','done','192.168.1.10','Mozilla/5.0 Chrome/123.0','2026-04-16 11:00:00'),
(4,'reservation_created','reservation',3,NULL,'{\"room\":\"202\",\"guest\":\"Priya Sharma\"}','197.32.14.8','Mozilla/5.0 Chrome/123.0','2026-04-17 10:00:00'),
(3,'check_in','reservation',3,NULL,'checked_in','197.32.14.5','Mozilla/5.0 Chrome/123.0','2026-04-17 13:00:00'),
(2,'payment_recorded','payment',43,NULL,'{\"amount\":1200,\"method\":\"cash\"}','197.32.14.5','Mozilla/5.0 Chrome/123.0','2026-04-18 08:00:00'),
(1,'login','user',1,NULL,NULL,'197.32.14.5','Mozilla/5.0 Chrome/123.0','2026-04-19 07:55:00'),
(3,'reservation_created','reservation',4,NULL,'{\"room\":\"203\",\"guest\":\"Pierre Dubois\"}','197.32.14.5','Mozilla/5.0 Chrome/123.0','2026-04-19 09:00:00'),
(6,'report_viewed','report',NULL,NULL,'{\"type\":\"revenue\",\"period\":\"weekly\"}','10.0.0.1','Mozilla/5.0 Chrome/123.0','2026-04-19 10:30:00'),
(4,'check_in','reservation',4,NULL,'checked_in','197.32.14.8','Mozilla/5.0 Chrome/123.0','2026-04-20 16:00:00'),
-- April 21-25
(5,'housekeeping_task_updated','housekeeping_tasks',5,'pending','done','192.168.1.10','Mozilla/5.0 Chrome/123.0','2026-04-21 07:10:00'),
(3,'payment_recorded','payment',44,NULL,'{\"amount\":1350,\"method\":\"credit_card\"}','197.32.14.5','Mozilla/5.0 Chrome/123.0','2026-04-22 08:15:00'),
(3,'check_out','reservation',3,'checked_in','checked_out','197.32.14.5','Mozilla/5.0 Chrome/123.0','2026-04-23 10:30:00'),
(2,'payment_recorded','payment',45,NULL,'{\"amount\":3000,\"method\":\"credit_card\"}','197.32.14.8','Mozilla/5.0 Chrome/123.0','2026-04-24 07:00:00'),
(4,'check_out','reservation',4,'checked_in','checked_out','197.32.14.8','Mozilla/5.0 Chrome/123.0','2026-04-25 12:30:00'),
-- April 26-30
(1,'login','user',1,NULL,NULL,'197.32.14.5','Mozilla/5.0 Chrome/123.0','2026-04-26 08:02:00'),
(6,'report_viewed','report',NULL,NULL,'{\"type\":\"occupancy\",\"period\":\"monthly\"}','10.0.0.1','Mozilla/5.0 Chrome/123.0','2026-04-26 10:00:00'),
(3,'reservation_created','reservation',30,NULL,'{\"room\":\"104\",\"guest\":\"Carlos Rodriguez\"}','197.32.14.5','Mozilla/5.0 Chrome/123.0','2026-04-27 09:00:00'),
(3,'reservation_created','reservation',31,NULL,'{\"room\":\"105\",\"guest\":\"Aisha Al-Rashid\"}','197.32.14.5','Mozilla/5.0 Chrome/123.0','2026-04-27 09:15:00'),
(2,'found_item_logged','found_items',1,NULL,'{\"item\":\"Brown leather wallet\",\"location\":\"room 201\"}','197.32.14.5','Mozilla/5.0 Chrome/123.0','2026-04-28 14:00:00'),
(3,'payment_recorded','payment',48,NULL,'{\"amount\":2000,\"method\":\"bank_transfer\"}','197.32.14.8','Mozilla/5.0 Chrome/123.0','2026-04-29 08:45:00'),
(4,'check_in','reservation',24,NULL,'checked_in','197.32.14.8','Mozilla/5.0 Chrome/123.0','2026-04-29 14:00:00'),
(5,'housekeeping_task_updated','housekeeping_tasks',7,'pending','done','192.168.1.10','Mozilla/5.0 Chrome/123.0','2026-04-30 04:30:00'),
-- May 1-4
(3,'check_in','reservation',25,NULL,'checked_in','197.32.14.5','Mozilla/5.0 Chrome/123.0','2026-05-01 13:30:00'),
(2,'payment_recorded','payment',50,NULL,'{\"amount\":500,\"method\":\"credit_card\"}','197.32.14.8','Mozilla/5.0 Chrome/123.0','2026-05-01 06:00:00'),
(1,'login','user',1,NULL,NULL,'197.32.14.5','Mozilla/5.0 Chrome/123.0','2026-05-02 07:58:00'),
(6,'report_viewed','report',NULL,NULL,'{\"type\":\"audit_log\",\"period\":\"last_30_days\"}','10.0.0.1','Mozilla/5.0 Chrome/123.0','2026-05-02 09:00:00'),
(3,'work_order_created','work_orders',3,NULL,'{\"room\":\"202\",\"issue\":\"dripping tap\"}','197.32.14.5','Mozilla/5.0 Chrome/123.0','2026-05-02 10:00:00'),
(5,'housekeeping_task_updated','housekeeping_tasks',2,'pending','in_progress','192.168.1.10','Mozilla/5.0 Chrome/123.0','2026-05-02 06:30:00'),
(2,'found_item_logged','found_items',2,NULL,'{\"item\":\"iPhone 15 Pro\",\"location\":\"room 102\"}','197.32.14.5','Mozilla/5.0 Chrome/123.0','2026-05-03 11:00:00'),
(3,'reservation_created','reservation',32,NULL,'{\"room\":\"204\",\"guest\":\"David Chen\"}','197.32.14.5','Mozilla/5.0 Chrome/123.0','2026-05-03 09:00:00'),
(3,'reservation_created','reservation',33,NULL,'{\"room\":\"106\",\"guest\":\"Emma Wilson\"}','197.32.14.5','Mozilla/5.0 Chrome/123.0','2026-05-03 09:30:00'),
(4,'reservation_created','reservation',34,NULL,'{\"room\":\"205\",\"guest\":\"Pierre Dubois\"}','197.32.14.8','Mozilla/5.0 Chrome/123.0','2026-05-03 10:00:00'),
(1,'work_order_status_changed','work_orders',1,'open','in_progress','10.0.0.1','Mozilla/5.0 Chrome/123.0','2026-05-03 09:00:00'),
(2,'payment_recorded','payment',56,NULL,'{\"amount\":500,\"method\":\"credit_card\"}','197.32.14.8','Mozilla/5.0 Chrome/123.0','2026-05-04 07:00:00'),
(3,'billing_dispute_raised','billing_disputes',1,NULL,'{\"reservation\":4,\"amount\":50,\"reason\":\"late checkout not communicated\"}','197.32.14.5','Mozilla/5.0 Chrome/123.0','2026-05-04 14:00:00'),
(4,'lost_item_report_created','lost_item_reports',3,NULL,'{\"guest_id\":1,\"item\":\"iPhone black Pro\"}','197.32.14.8','Mozilla/5.0 Chrome/123.0','2026-05-04 15:30:00'),
-- May 5 (today)
(1,'login','user',1,NULL,NULL,'197.32.14.5','Mozilla/5.0 Chrome/123.0','2026-05-05 07:55:00'),
(3,'check_in','reservation',30,NULL,'checked_in','197.32.14.5','Mozilla/5.0 Chrome/123.0','2026-05-05 14:00:00'),
(3,'check_in','reservation',31,NULL,'checked_in','197.32.14.5','Mozilla/5.0 Chrome/123.0','2026-05-05 15:30:00'),
(4,'payment_recorded','payment',70,NULL,'{\"amount\":300,\"method\":\"credit_card\",\"folio\":20}','197.32.14.8','Mozilla/5.0 Chrome/123.0','2026-05-05 14:05:00'),
(4,'payment_recorded','payment',71,NULL,'{\"amount\":400,\"method\":\"credit_card\",\"folio\":21}','197.32.14.8','Mozilla/5.0 Chrome/123.0','2026-05-05 15:35:00'),
(2,'work_order_created','work_orders',9,NULL,'{\"room\":\"201\",\"issue\":\"ceiling light flickering\"}','197.32.14.5','Mozilla/5.0 Chrome/123.0','2026-05-05 09:30:00'),
(6,'report_viewed','report',NULL,NULL,'{\"type\":\"revenue\",\"period\":\"daily\"}','10.0.0.1','Mozilla/5.0 Chrome/123.0','2026-05-05 08:30:00'),
(3,'low_stock_alert_escalated','low_stock_alerts',1,NULL,'{\"item\":\"All-Purpose Cleaner\",\"stock\":3,\"threshold\":8}','197.32.14.5','Mozilla/5.0 Chrome/123.0','2026-05-05 10:00:00'),
(2,'reservation_created','reservation',36,NULL,'{\"room\":\"203\",\"guest\":\"Yuki Tanaka\",\"vip\":true}','197.32.14.5','Mozilla/5.0 Chrome/123.0','2026-05-05 13:00:00'),
(1,'login','user',2,NULL,NULL,'197.32.14.9','Mozilla/5.0 (Windows NT 10.0) Firefox/124.0','2026-05-05 08:10:00'),
(6,'report_viewed','report',NULL,NULL,'{\"type\":\"audit_log\",\"period\":\"today\"}','10.0.0.1','Mozilla/5.0 Chrome/123.0','2026-05-05 16:00:00');
