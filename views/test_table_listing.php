<?php
  global $PLUGIN_DIR_PATH;
  global $TEST_STATUSES_ACTIONS;

  switch($_REQUEST['action']) {
    case 'delete':
      $result = deleteTestById($_REQUEST['id']);
      break;
    case 'disable':
    case 'enable':
      $result = toggleTestById($_REQUEST['id']);
      break;
  }

  if ($result):
  ?>
    <div class="updated notice notice-success is-dismissible">
      <p>
        <?php
          switch ($_REQUEST['action']):
            case 'disable':
            case 'enable':
              $test = getTestById($_REQUEST['id']);
              ?>
                You have successfully <?php echo strtolower($TEST_STATUSES_ACTIONS[$test->status]); ?>d the test “<?php echo $test->name; ?>”.
              <?php
            break;
            case 'delete':
              ?>
                You have successfully deleted the test.
              <?php
            break;
          endswitch;
        ?>
      </p>

      <button type="button" class="notice-dismiss">
        <span class="screen-reader-text">Dismiss this notice.</span>
      </button>
    </div>
  <?php
    endif;
  ?>

  <div class="wrap">
    <h1>
      <?php echo esc_html( get_admin_page_title() ); ?>
    </h1>

    <a href="<?php echo add_query_arg('page', 'veem-ab-split-edit', admin_url('admin.php')); ?>" class="page-title-action">Add New</a>

    <?php
      $test_list_table = new WP_Veem_AB_List_Table();
      $test_list_table->prepare_items();
      $test_list_table->display();
    ?>
  </div>
<?php
