<?php

function array_some($collection = [], $predicate) {
  if (!is_array($collection)) throw new Exception ('$collection must be an array');
  if (!is_callable($predicate)) throw new Exception ('$predicate must be a function');

  foreach ($collection as $key => $value) {
    if ($predicate($value, $key)) return true;
  }

  return false;
}
