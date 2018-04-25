<?php
  $data = array();

  if ($_REQUEST['id']) {
    $data = getTestById($_REQUEST['id'])->toArray();
  }

  if (!empty($_POST)) {
    $result = save_test();

    if($result instanceof WP_Veem_AB_Test) {
      $data = $result->toArray();
      $success = true;
    } else {
      $data = array_merge($data, $_POST);
      $errors = $result;
    }
  }

  if (isset($errors)):
    ?>
      <div class="updated notice notice-error is-dismissible">
        <p>Could not save information. Verify the information you inputted below.</p>

        <button type="button" class="notice-dismiss">
          <span class="screen-reader-text">Dismiss this notice.</span>
        </button>
      </div>
    <?php

  elseif($success):
    ?>
      <div class="updated notice notice-success is-dismissible">
        <p>Test <strong><?php echo $data['name']; ?></strong> <?php echo empty($_POST['id']) ? 'created' : 'saved'; ?>.</p>

        <button type="button" class="notice-dismiss">
          <span class="screen-reader-text">Dismiss this notice.</span>
        </button>
      </div>
    <?php
  endif;
?>


<div class="wrap test-form">
  <h1>Edit Split Test</h1>

  <form method="post">
    <?php wp_nonce_field('test_info_nonce', 'test_info_nonce_edit_test', true, true); ?>

    <?php if(!empty($_REQUEST['id'])): ?>
      <input type="hidden" name="id" value="<?php echo $_REQUEST['id']; ?>" />
    <?php endif; ?>

    <table class="form-table">
      <tbody>
        <tr class="field-group <?php echo isset($errors['name']) ? 'validation-error': '' ; ?>">
          <th scope="row">
            Name
          </th>

          <td>
            <input type="text" name="name" value="<?php echo $data['name']; ?>" />

            <?php if(isset($errors['name'])): ?>
              <p class="validation-error"><?php echo $errors['name']->message; ?></p>
            <?php endif; ?>
          </td>
        </tr>

        <tr class="field-group <?php echo isset($errors['status']) ? 'validation-error': '' ; ?>">
          <th scope="row">Status</th>

          <td>
            <div class="radio-group">
              <label>
                <input
                  type="radio"
                  name="status"
                  value="1"
                  <?php if(!is_numeric($data['status']) || $data['status']+0 === 1): ?>
                  checked
                  <?php endif; ?>
                />

                Active
              </label>

              <label>
                <input
                  type="radio"
                  name="status"
                  value="0"
                  class="<?php echo isset($errors['status']) ? 'validation-error': '' ; ?>"
                  <?php if(is_numeric($data['status']) && $data['status']+0 === 0): ?>
                  checked
                  <?php endif; ?>
                />

                Inactive
              </label>
            </div>

            <?php if(isset($errors['status'])): ?>
              <p class="validation-error"><?php echo $errors['status']->message; ?></p>
            <?php endif; ?>
          </td>
        </tr>

        <tr class="field-group">
          <th>
            Pages
          </th>

          <?php
            $page_errors = array();

            if (isset($errors['page'])) $page_errors['page'] = $errors['page']->message;
            if (isset($errors['pages'])) $page_errors['pages'] = array_map(function($error) { return $error->message; }, $errors['pages']);
          ?>

          <td>
            <div
              data-repeatable
              data-repeatable-errors='<?php echo json_encode($page_errors); ?>'
              data-repeatable-limit="2"
              data-repeatable-data='<?php echo json_encode($data['pages']); ?>'
            >
              <h4 data-repeatable-attribute-text="Page {{LETTER}}">Page A</h4>

              <div data-toggle-type>
                <div class="radio-group">
                  <label>
                    <input
                      type="radio"
                      name="pages[0][type]"
                      value="internal"
                      data-repeatable-attribute-name="pages[{{index}}][type]"
                    /> Wordpress Post
                  </label>

                  <label>
                    <input
                      type="radio"
                      name="pages[0][type]"
                      value="external"
                      data-repeatable-attribute-name="pages[{{index}}][type]"
                    /> External URL
                  </label>
                </div>

                <div data-show-toggle-type="internal">
                  <strong class="block">Wordpress Page</strong>

                  <input
                    type="text"
                    name="pages[0][post_id]"
                    data-repeatable-attribute-name="pages[{{index}}][post_id]"
                    data-post-autocomplete
                    value="<?php echo $data['post_id']; ?>"
                  />
                </div>

                <div data-show-toggle-type="external">
                  <strong class="block">External URL</strong>

                  <input
                    type="text"
                    name="pages[0][url]"
                    data-repeatable-attribute-name="pages[{{index}}][url]"
                    data-validate="url"
                    autocomplete="off"
                    value="<?php echo $data['url']; ?>"
                  />
                </div>
              </div>
            </div>
          </td>
        </tr>
      </tbody>
    </table>

    <?php submit_button(); ?>
  </form>
</div>
