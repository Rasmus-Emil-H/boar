<?php

define('PASSWORD_PASSWORD_DEFAULT', 'qwd');

function dd($input) {
  echo '<pre> DEBUGGING';
  var_dump($input);
  echo ' END OF DEBUGGING </pre>';
  exit;
}

function d($input) {
  echo '<pre> DEBUGGING ';
  var_dump($input);
  echo ' END OF DEBUGGING </pre>';
  echo '<hr />';
}