<?php
  global $TEST_STATUS_DISABLED;
  global $TEST_STATUS_ACTIVE;
  global $TEST_STATUSES;
  global $TEST_STATUSES_ACTIONS;

  $TEST_STATUS_DISABLED = 0;
  $TEST_STATUS_ACTIVE = 1;

  $TEST_STATUSES = array(
    $TEST_STATUS_DISABLED => 'Disabled',
    $TEST_STATUS_ACTIVE => 'Active',
  );

  $TEST_STATUSES_ACTIONS = array(
    $TEST_STATUS_DISABLED => 'Disable',
    $TEST_STATUS_ACTIVE => 'Activate',
  );
