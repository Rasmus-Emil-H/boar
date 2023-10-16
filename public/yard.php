<?php

/**
 * Minor convenience for those methods that's used throughout the system
 * @package none
 * @author REWEB
 */

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

function hs($input): string {
  return \app\core\miscellaneous\Html::escape($input);
}

/**
 * Getter
 * @return object
 */

function app(): object {
  return \app\core\Application::$app;
}

/**
 * Eval current CSRF token
 */

function validateCSRF(): bool {
  return (new \app\core\tokens\CsrfToken())->validate();
}

/**
 * Escape
 * But with translation incoporeated
 * 
 * @param string input
 * @return string
 */

function hts(string $input): string {
  return hs(app()->i18n->translate($input));
}