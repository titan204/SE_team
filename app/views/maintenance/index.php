<?php $pageTitle = 'Maintenance Orders'; ?>
<!--
============================================================
  Used by: MaintenanceController@index

  This page should display:
  - Maintenance orders table: Room, Description, Priority, Status, Assigned To, Actions
  - Priority badges (color-coded: low=info, medium=warning, high=orange, critical=danger)
  - Filter by priority, status
  - "New Work Order" button
  - Action buttons: Resolve, Escalate
============================================================
-->

<?php require VIEW_PATH . '/layouts/main.php'; ?>
