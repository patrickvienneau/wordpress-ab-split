<?php

class WP_Veem_AB_Test {
  public $id;
  public $name;
  public $status;
  public $pages = array();

  public function __construct($data) {
    $data = (array) $data;

    if (isset($data['id'])) $this->id = (int) $data['id'];
    if (isset($data['name'])) $this->name = $data['name'];
    if (isset($data['status'])) $this->status = (int) $data['status'];
    if (isset($data['created_by'])) $this->created_by = (int) $data['created_by'];
    if (isset($data['created_at'])) $this->created_at = strtotime($data['created_at']);

    if (isset($data['pages'])) {
      $this->pages = array_map(
        function($page){
          return new WP_Veem_AB_Page($page);
        },
        $data['pages']
      );
    }
  }

  public function validate() {
    $ii = 0;

    $page_errors = array_reduce($this->pages, function($carry, $page) use (&$ii) {
      $error = $page->validate();

      if ($error instanceof WP_Veem_AB_Error) $carry[$ii++] = $error;

      return $carry;
    }, array());

    if (!empty($page_errors)) $errors['pages'] = $page_errors;
    if (empty($this->name)) $errors['name'] = new WP_Veem_AB_Error('Name is required.');
    if (!is_numeric($this->status)) $errors['status'] = new WP_Veem_AB_Error('Status is required.');

    if (count($this->pages) < 2) {
      $errors['page'] = new WP_Veem_AB_Error('You need 2 pages.');
    } elseif ($this->hasRepeatingValues()) {
      $errors['page'] = new WP_Veem_AB_Error('Cannot define 2 pages with the same values');
    }

    return $errors;
  }

  public function hasErrors() {
    $errors = $this->validate();

    return !empty($errors);
  }

  public function hasRepeatingValues () {
    $pages = $this->pages;
    $values = array();

    return array_some($pages, function($page) use ($values) {
      $value = $page->isInternal() ? $page->post_id : $page->url;

      if (empty($value)) return false;
      if (in_array($value, $values)) return true;

      $values[] = $value;
    });
  }

  public function getPageCount() {
    return count($pages);
  }

  public function add(Page $page) {
    $this->pages[$page->id];
  }

  public function remove(integer $id) {
    unset($this->pages[$id]);
  }

  public function save($deep = true) {
    global $wpdb;
    global $PLUGIN_TABLE_NAME_TEST;

    $newData = array(
      'name' => $this->name,
      'status' => $this->status,
    );

    if (isset($this->id)) {
      $affectedRows = $wpdb->update(
        $PLUGIN_TABLE_NAME_TEST,
        $newData,
        array('id' => $this->id)
      );
    } else {
      $this->created_by = get_current_user_id();
      $this->created_at = date("Y-m-d H:i:s");

      $newData = array_merge($newData, array(
        'created_by' => $this->created_by,
        'created_at' => $this->created_at,
      ));

      $affectedRows = $wpdb->insert(
        $PLUGIN_TABLE_NAME_TEST,
        $newData
      );
    }

    if ($affectedRows < 1) return false;

    $this->id = $wpdb->insert_id;

    if ($deep) {
      foreach ($this->pages as $key => $page) {
        $page->test_id = $this->id;

        if ($page->save()) $this->page[$key] = $page;
      }
    }

    return true;
  }

  public function delete() {
    global $wpdb;
    global $PLUGIN_TABLE_NAME_TEST;

    array_map(
      function($page) {
        $page->delete();
      },
      $this->pages
    );

    $wpdb->delete(
      $PLUGIN_TABLE_NAME_TEST,
      array(
        'id' => $this->id
      )
    );
  }

  public function isEnabled() {
    global $TEST_STATUS_ACTIVE;

    return $this->status === $TEST_STATUS_ACTIVE;
  }

  public function isDisabled() {
    return !$this->isEnabled();
  }

  public function enable() {
    global $TEST_STATUS_ACTIVE;

    $this->status = $TEST_STATUS_ACTIVE;

    return $this;
  }

  public function disable() {
    global $TEST_STATUS_DISABLED;

    $this->status = $TEST_STATUS_DISABLED;

    return $this;
  }

  public function toArray() {
    $test = get_object_vars($this);
    $test['pages'] = array_map(
      function($page) {
        return $page->toArray();
      },
      $this->pages
    );

    return $test;
  }
}
