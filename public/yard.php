<?php

define('PASSWORD_PASSWORD_DEFAULT', 'qwd');
define('DEBUG_COLOR', '#e63946');

function displayDD($input): void {
  echo '<pre style="background:'.DEBUG_COLOR.';padding:1rem;opacity:0.'.rand(7, 9).';"> <h2 style="text-align:center;">DEBUGGING</h2> ';
  var_dump($input);
  echo ' <h2 style="text-align:center;">END OF DEBUGGING</h2> </pre>';
}

function dd($input) {
  displayDD($input);
  exit;
}

function d($input) {
  displayDD($input);
  echo '<hr />';
}