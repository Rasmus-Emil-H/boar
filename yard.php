<?php

/**
|----------------------------------------------------------------------------
| Yard for various easy getters
|----------------------------------------------------------------------------
|
| @author RE_WEB
|
*/

declare(strict_types=1);

use \app\core\src\exceptions\InvalidTypeException;

use \app\core\src\miscellaneous\CoreFunctions;

/**
|----------------------------------------------------------------------------
| Session
|----------------------------------------------------------------------------
|
*/

$timeout = 2629800;

session_set_cookie_params([
    'lifetime' => $timeout
]);

ini_set("session.gc_probability", 1);
ini_set("session.gc_divisor", 100);
ini_set("session.gc_maxlifetime", $timeout );
ini_set("session.cookie_lifetime", $timeout );

session_start();

/**
|----------------------------------------------------------------------------
| Error reporting
|----------------------------------------------------------------------------
|
*/

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/**
|----------------------------------------------------------------------------
| Define
|----------------------------------------------------------------------------
|
*/

define('IS_CLI', isset($argv));

/**
|----------------------------------------------------------------------------
| Functions
|----------------------------------------------------------------------------
|
*/

function ths(string $string): string {
    return CoreFunctions::ths($string);
}

function hs(string|int $string): string {
    if (!$string) return '';

    return htmlspecialchars((string)$string);
}

function app() {
    return CoreFunctions::app();
}

function getIterableJsonEncodedData(array|object $iterable): array {
    $result = [];

    foreach ($iterable as $iteration) {
        if (!method_exists($iteration, 'getData')) 
            throw new \app\core\src\exceptions\NotFoundException("getData method was not found");
        
        foreach ($iteration->getData() as $dataKey => $dataValue)
            $result[$iteration->key()][$dataKey] = is_iterable($dataValue) ? getIterableJsonEncodedData($dataValue) : json_encode($dataValue);
    }

    return $result;
}

function debug($data) {
    app()->getLogger()->log($data);
}

function renderComponent($method, $arguments = []) {
    return \app\core\src\html\Html::$method(...$arguments);
}

function CSRFTokenInput(): string {
    return (new \app\core\src\tokens\CsrfToken())->insertHiddenToken();
}

/**
 * Dump and die
 */

function dd(mixed $input) {
    return CoreFunctions::dd($input);
}

function printme($input): void {
    var_dump($input).PHP_EOL;
    space();
}

function space(): void {
    echo PHP_EOL.PHP_EOL.PHP_EOL;
}

function panic(string $reason = ''): void {
    exit($reason);
}

function first(array|object $data): mixed {
    return CoreFunctions::first($data)?->scalar;
}

function last(array|object $data): mixed {
    return CoreFunctions::last($data)?->scalar;
}

function index(array|object $data, string|int $expectedIndex): mixed {
    return CoreFunctions::getIndex($data, $expectedIndex)?->scalar; 
}

function env(string $key): object|string {
    $obj = app()->getConfig()->get($key);

    if ($obj === 'invalidEnvKey') throw new InvalidTypeException('Invalid env key');

    return $obj;
}

function echoCLI(string $string): void {
    echo $string . PHP_EOL;
}

function type(mixed $mixed): string {
    return get_debug_type($mixed);
}

function appUser(): ?\app\models\UserModel {
    return CoreFunctions::applicationUser();
}

/**
 * Gat path
 */
function routePath(): string {
    return app()->getRequest()->getPath();
}

/**
 * Get current entity, if valid
 */
function getEntity() {
    return app()->getParentController()->returnValidEntityIfExists();
}

function rootDir(): string {
    return app()::$ROOT_DIR;
}