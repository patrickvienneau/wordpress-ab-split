<?php
  function get_active_test_by_post_id($post_id) {
    global $wpdb;
    global $PLUGIN_TABLE_NAME_PAGE;
    global $PLUGIN_TABLE_NAME_TEST;
    global $TEST_STATUS_ACTIVE;

    $results = $wpdb->get_results("
      SELECT p.test_id
      FROM $PLUGIN_TABLE_NAME_PAGE AS p
      INNER JOIN $PLUGIN_TABLE_NAME_TEST AS t
        ON t.id = p.test_id
      WHERE
        p.post_id = $post_id AND
        t.status = $TEST_STATUS_ACTIVE
    ");
    $test_id = $results[0]->test_id;

    return getTestById($test_id);
  }
