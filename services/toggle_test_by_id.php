<?php
  function toggleTestById($id) {
    $test = getTestById($id);

    if ($test->isDisabled()) {
      $test->enable();
    } else {
      $test->disable();
    }

    $test->save(false);

    return true;
  }
