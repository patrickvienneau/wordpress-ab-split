<?php
  function deleteTestById($id) {
    $test = getTestById($id);

    if (empty($test)) return false;

    $test->delete();

    return true;
  }
