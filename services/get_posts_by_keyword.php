<?php
  add_action('wp_ajax_get_posts_by_keyword' , 'get_posts_by_keyword');

  function get_posts_by_keyword() {
    $keyword = $_GET['keyword'];
    $query = array(
      'posts_per_page' => 10,
      's' => esc_attr($keyword),
      'post_type' => array('post', 'page'),
    );

    $wp_query = new WP_Query($query);
    $posts = $wp_query->posts;

    wp_send_json($posts);
  }
