<?php

declare(strict_types=1);

use \app\core\src\exceptions\InvalidTypeException;

use \app\core\src\miscellaneous\CoreFunctions;

/**
|----------------------------------------------------------------------------
| Session
|----------------------------------------------------------------------------
|
*/

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
    return \app\core\src\miscellaneous\CoreFunctions::ths($string);
}

function hs(string|int $string): string {
    if (!$string) return '';

    return htmlspecialchars((string)$string);
}

function app() {
    return \app\core\src\miscellaneous\CoreFunctions::app();
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

function dumpAndDie(mixed $input) {
    return \app\core\src\miscellaneous\CoreFunctions::dd($input);
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

function type(mixed $mixed): mixed {
    return get_debug_type($mixed);
}