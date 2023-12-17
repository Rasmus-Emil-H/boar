<?php

/*
|----------------------------------------------------------------------------
| Convenience for those getters that are used frequently throughout the system
|----------------------------------------------------------------------------
|
| @author RE_WEB
| @package none
|
*/

function displayDD($input, $title = 'Debugging'): void {
  echo '<pre style="padding: 2rem; background-color: #3a6b39; color: white; border-radius: 4px;margin-top: 10px;" class="debug"><h2 class="text-center">' . $title . '</h2><hr>';
  print_r($input);
  echo '<hr>';
  echo '<h2 class="text-center">End of ' . $title . '</h2></pre>';
}

function dd(mixed $input): void {
  displayDD($input);
  exit;
}

function d(mixed $input): void {
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

function first(array|object $iterable): object {
  return (object)$iterable[array_key_first($iterable)];
}

function last(array|object $iterable): object {
  return (object)$iterable[array_key_last($iterable)];
}

function loopAndEcho(array|object $iterable, bool $echoKey = false): void {
  foreach ($iterable as $key => $value) echo $echoKey ? $key : $value;
}

function applicationUser(): ?\app\models\UserModel {
  $tryUser = app()->getSessionUser();
  return empty($tryUser) ? null : first($tryUser);
}