<?php
  class WP_Veem_AB_Error {
    public $message;
    public $type;

    public function __construct($message = 'Something went wrong...', $type = 'error') {
      $this->message = $message;
      $this->type = $type;
    }
  }
