<?php

add_action('admin_menu', 'veem_ab_admin_menu_init_setup');

add_filter('admin_body_class', 'veem_ab_admin_add_body_class');

function veem_ab_admin_menu_init_setup() {
  add_menu_page('Veem A/B Splitter', 'A/B Split', 'manage_options', 'veem-ab-split', 'veem_ab_admin_menu_listing');
  add_submenu_page('veem-ab-split', 'Veem A/B Splitter', 'Add New', 'manage_options', 'veem-ab-split-edit', 'veem_ab_admin_menu_form');
}

function veem_ab_admin_menu_listing() {
  include($PLUGIN_DIR_PATH.'views/test_table_listing.php');
}

function veem_ab_admin_menu_form() {
  include($PLUGIN_DIR_PATH.'views/test_form.php');
}


function veem_ab_admin_add_body_class ($classes) {
  $screen = get_current_screen();

  if (strpos($screen->base, 'veem-ab-split') !== false) $classes .= 'veem-ab-split';

  return $classes;
}
