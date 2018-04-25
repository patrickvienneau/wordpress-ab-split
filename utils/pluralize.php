<?php

  function pluralize($word = '', $flag, $plural_word = null) {
    if (!$flag) return $word;

    return isset($plural_word) ? $plural_word : $word.'s';
  }
