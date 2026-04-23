<?php $pageTitle = 'New Reservation'; ?>
<!--
============================================================
  Used by: ReservationsController@create
  Form submits to: ReservationsController@store

  This page should display:
  - Guest dropdown (searchable)
  - Room type and room selection (filtered by availability + dates)
  - Check-in / Check-out date pickers
  - Adults / Children number inputs
  - Special requests textarea
  - Group booking toggle + group master account
  - Deposit amount input
  - Submit and Cancel buttons
  - JS: Dynamic room-allocation — filter rooms by type/date availability
============================================================
-->

<?php require VIEW_PATH . '/layouts/main.php'; ?>
