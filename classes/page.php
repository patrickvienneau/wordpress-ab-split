<?php

class WP_Veem_AB_Page {
  public $id;
  public $post_id;
  public $url;
  public $visit_count = 0;
  private $type = null;

  public function __construct($data) {
    $data = (array) $data;

    if (isset($data['id'])) $this->id = (int) $data['id'];
    if (isset($data['test_id'])) $this->test_id = (int) $data['test_id'];
    if (isset($data['post_id'])) $this->post_id = (int) $data['post_id'];
    if (isset($data['url'])) $this->url = $data['url'];
    if (isset($data['visit_count'])) $this->visit_count = (int) $data['visit_count'];
    if (isset($data['created_by'])) $this->created_by = (int) $data['created_by'];
    if (isset($data['created_at'])) $this->created_at = strtotime($data['created_at']);

    if (isset($data['type'])) {
      $this->type = $data['type'];
    } else if (!empty($this->post_id)) {
      $this->type = 'internal';
    } else {
      $this->type = 'external';
    }
  }

  public function validate() {
    if ($this->isInternal()) {
      if (!get_post_status($this->post_id)) return new WP_Veem_AB_Error('A Wordpress page must be specified.');
    } else {
      if (empty($this->url)) return new WP_Veem_AB_Error('An external URL must be specified.');
      if (!filter_var($this->url, FILTER_VALIDATE_URL)) return new WP_Veem_AB_Error('A valid URL must be specified.');
    }

    return true;
  }

  public function save() {
    global $wpdb;
    global $PLUGIN_TABLE_NAME_PAGE;

    $newData = array(
      'test_id' => $this->test_id,
      'post_id' => $this->post_id,
      'url' => $this->url,
      'visit_count' => $this->visit_count,
    );

    if (isset($this->id)) {
      $affectedRows = $wpdb->update(
        $PLUGIN_TABLE_NAME_PAGE,
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
        $PLUGIN_TABLE_NAME_PAGE,
        $newData
      );
    }

    if ($affectedRows < 1) return false;

    $this->id = $wpdb->insert_id;
    return true;
  }

  public function delete() {
    global $wpdb;
    global $PLUGIN_TABLE_NAME_PAGE;

    $wpdb->delete(
      $PLUGIN_TABLE_NAME_PAGE,
      array(
        'id' => $this->id
      )
    );
  }

  public function isInternal() {
    return $this->type === 'internal';
  }

  public function isExternal() {
    return !$this->isInternal();
  }

  public function toArray() {
    return get_object_vars($this);
  }
}
