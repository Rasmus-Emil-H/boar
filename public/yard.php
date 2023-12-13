<?php

/**
|----------------------------------------------------------------------------
| Convenience for those methods that's used frequently throughout the system
|----------------------------------------------------------------------------
|
| @author RE_WEB
| @package none
|
*/

function displayDD($input, $title = 'Debugging'): void {
  echo '<pre style="padding: 2rem; background-color: #588157; color: white; border-radius: 4px;" class="debug"><h2 class="text-center">' . $title . '</h2> ';
  var_dump($input);
  echo ' <h2 style="margin:0;padding:0;" class="text-center">END OF ' . $title . '</h2> </pre>';
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

function app(): object {
  return \app\core\Application::$app;
}

function validateCSRF(): bool {
  return (new \app\core\tokens\CsrfToken())->validate();
}

function ths(string $input): string {
  return hs(app()->i18n->translate($input));
}

function last(array|object $iterable): object {
  return (object)$iterable[array_key_last($iterable)];
}

function first(array|object $iterable): object {
  return (object)$iterable[array_key_first($iterable)];
}

function loopAndEcho(array|object $iterable, bool $echoKey = false): void {
  foreach ($iterable as $key => $value) echo $echoKey ? $key : $value;
}

function applicationUser(): ?\app\models\UserModel {
  $tryUser = app()->getSessionUser();
  return empty($tryUser) ? null : first($tryUser);
}