<?php
  function save_test() {
    check_admin_referer('test_info_nonce', 'test_info_nonce_edit_test');
    $test = new WP_Veem_AB_Test($_POST);
    $errors = $test->validate();

    if (!empty($errors)) return $errors;

    $test->save();

    return $test;
  }
