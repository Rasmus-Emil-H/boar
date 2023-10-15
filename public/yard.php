<?php

function displayDD($input, $title = null): void {
  echo '<pre class="debug"><h2 class="text-center">'.($title ?? 'Debugging').'</h2> ';
  var_dump($input);
  echo ' <h2 class="text-center">END OF '.($title ?? 'Debugging').'</h2> </pre>';
}

function dd($input) {
  displayDD($input);
  exit;
}

function d($input) {
  displayDD($input);
  echo '<hr />';
}

function hs($input) {
  return \app\core\miscellaneous\Html::escape($input);
}