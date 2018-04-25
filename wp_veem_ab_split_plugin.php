<?php
/*
 Plugin Name: Veem A/B Split
 Plugin URI: https://github.com/patrickvienneau/wordpress-ab-split
 Version: 1.0
 Description: Our Veem in-house plugin, it attempts to offer a means to create A/B split user flows with external URLs.
 Author: Veem
 Author URI: https://github.com/aligncommerce
 License: GPL2
 */

defined( 'ABSPATH' ) or die('Nothing to see here');

require_once($PLUGIN_DIR_PATH.'utils/index.php');
require_once($PLUGIN_DIR_PATH.'constants/index.php');
require_once($PLUGIN_DIR_PATH.'services/index.php');
require_once($PLUGIN_DIR_PATH.'classes/index.php');
require_once($PLUGIN_DIR_PATH.'admin_panel.php');

global $PLUGIN_TABLE_NAME_TEST;
global $PLUGIN_TABLE_NAME_PAGE;
global $wpdb;
global $PLUGIN_DIR_PATH;

$PLUGIN_TABLE_NAME_TEST = $wpdb->prefix.'veem_ab_split_test';
$PLUGIN_TABLE_NAME_PAGE = $wpdb->prefix.'veem_ab_split_page';
$PLUGIN_DIR_PATH = plugin_dir_path(__FILE__);

register_activation_hook(__FILE__, 'plugin_activate');
register_uninstall_hook(__FILE__, 'plugin_deactivate');

add_action('admin_enqueue_scripts', 'enqueue_scripts');
add_action('admin_enqueue_scripts', 'enqueue_styles');
add_action('wp', 'wp_veem_ab_split_init');

function plugin_activate() {
  global $wpdb;
  global $PLUGIN_TABLE_NAME_TEST;
  global $PLUGIN_TABLE_NAME_PAGE;

  $charset_collate = $wpdb->get_charset_collate();

  $sql = "
  CREATE TABLE ".$PLUGIN_TABLE_NAME_TEST." (
    `id` bigint(20) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `status` tinyint(1) NOT NULL,
    `created_at` datetime NOT NULL,
    `created_by` bigint(20) NOT NULL,
    PRIMARY KEY (`id`)
  ) $charset_collate;

  CREATE TABLE ".$PLUGIN_TABLE_NAME_PAGE." (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `test_id` bigint(20) NOT NULL,
  `post_id` bigint(20) DEFAULT NULL,
  `url` varchar(350) COLLATE utf8_unicode_ci DEFAULT NULL,
  `visit_count` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `created_by` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) $charset_collate;
  ";

  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
  dbDelta($sql);
}

function plugin_deactivate() {
  global $wpdb;
  global $PLUGIN_TABLE_NAME_TEST;
  global $PLUGIN_TABLE_NAME_PAGE;

  $sql = "DROP TABLE IF EXISTS $PLUGIN_TABLE_NAME_TEST; DROP TABLE IF EXISTS $PLUGIN_TABLE_NAME_PAGE";
  $wpdb->query($sql);
}

function enqueue_scripts() {
  $veem_base = array( 'BASE_URL' => get_site_url() );

  wp_enqueue_script('veem_ab_split_js_veem', plugins_url('/assets/js/base.js', __FILE__));
  wp_enqueue_script('veem_ab_split_js_jquery_extend', plugins_url('/assets/js/extend.jquery.js', __FILE__), array('jquery'));
  wp_enqueue_script('veem_ab_split_js_toggle_type', plugins_url('/assets/js/toggle-type.js', __FILE__), array('jquery'));
  wp_enqueue_script('veem_ab_split_js_post_autocomplete', plugins_url('/assets/js/post-autocomplete.js', __FILE__), array('jquery'));
  wp_enqueue_script('veem_ab_split_js_validation', plugins_url('/assets/js/validation.js', __FILE__), array('jquery'));
  wp_enqueue_script('veem_ab_split_js_repeatable', plugins_url('/assets/js/repeatable.js', __FILE__), array('jquery'));

  wp_localize_script( 'veem_ab_split_js_post_autocomplete', 'veem_base', $veem_base );
}

function enqueue_styles() {
  wp_register_style('veem_ab_split_css_base', plugins_url('/assets/css/base.css', __FILE__));
  wp_register_style('veem_ab_split_css_autocomplete', plugins_url('/assets/css/autocomplete.css', __FILE__));
  wp_register_style('veem_ab_split_css_validation', plugins_url('/assets/css/validation.css', __FILE__));
  wp_register_style('veem_ab_split_css_repeatable', plugins_url('/assets/css/repeatable.css', __FILE__));
  wp_register_style('veem_ab_split_css_form', plugins_url('/assets/css/form.css', __FILE__));

  wp_enqueue_style('veem_ab_split_css_base');
  wp_enqueue_style('veem_ab_split_css_autocomplete');
  wp_enqueue_style('veem_ab_split_css_validation');
  wp_enqueue_style('veem_ab_split_css_repeatable');
  wp_enqueue_style('veem_ab_split_css_form');
}

function wp_veem_ab_split_init() {
  if (isset($_GET['noredirect'])) return false;
  if (!is_single() && !is_page()) return false;

  $post_ID = get_the_ID();
  $test = get_active_test_by_post_id($post_ID);

  if (empty($test)) return false;

  $page_count = count($test->pages);
  $rand_index = rand(0, $page_count-1);
  $chosen_page = $test->pages[$rand_index];

  $chosen_page->visit_count++;
  $chosen_page->save();

  if ($chosen_page->isInternal()) {
    if ($post_ID !== $chosen_page->post_id) $url = get_permalink($chosen_page->post_id);
  } else {
    $url = $chosen_page->url;
  }

  if (!empty($url)) {
    header("Location: $url", 302);
    die();
  }
}
