<?php
  function getTestById($id) {
    global $wpdb;
    global $PLUGIN_TABLE_NAME_TEST;
    global $PLUGIN_TABLE_NAME_PAGE;

    if (empty($id)) return null;

    $pages = $wpdb->get_results(
      "SELECT t.*, p.*
      FROM $PLUGIN_TABLE_NAME_PAGE AS p
      LEFT JOIN $PLUGIN_TABLE_NAME_TEST AS t
        ON p.test_id = t.id
      WHERE t.id = $id"
    );

    $tests = array_reduce(
      $pages,
      function($carry, $page) {
        $test_id = $page->test_id;

        if (!$carry[$test_id] instanceof WP_Veem_AB_Test) {
          $carry[$test_id] = new WP_Veem_AB_Test($page);
          $carry[$test_id]->id = $page->test_id;
        }

        $carry[$test_id]->pages[] = new WP_Veem_AB_Page($page);

        return $carry;
      },
      array()
    );

    return array_values($tests)[0];
  }
