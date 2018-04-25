<?php
  add_action('wp_ajax_get_post_by_id' , 'get_post_by_id');

  function get_post_by_id() {
    $id = $_GET['id'];
    $post = get_post($id);

    if (empty($post)) $post = array();

    wp_send_json($post);
  }
