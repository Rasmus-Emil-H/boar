<?php

function displayDD($input): void {
  echo '<pre class="debug"><h2 class="text-center">DEBUGGING</h2> ';
  var_dump($input);
  echo ' <h2 class="text-center">END OF DEBUGGING</h2> </pre>';
}

function dd($input) {
  displayDD($input);
  exit;
}

function d($input) {
  displayDD($input);
  echo '<hr />';
}