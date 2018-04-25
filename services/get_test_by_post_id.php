<?php
  function get_test_by_post_id($post_id) {
    global $wpdb;
    global $PLUGIN_TABLE_NAME_PAGE;

    $results = $wpdb->get_results("
      SELECT p.test_id
      FROM $PLUGIN_TABLE_NAME_PAGE AS p
      WHERE p.post_id = $post_id
    ");
    $test_id = $results[0]->test_id;

    return getTestById($test_id);
  }
