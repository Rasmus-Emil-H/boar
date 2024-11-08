<?php

declare(strict_types=1);

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

function hs(?string $string): string {
    if (!$string) return '';

    return htmlspecialchars($string);
}

function app() {
    return \app\core\src\miscellaneous\CoreFunctions::app();
}

function getIterableJsonEncodedData(array|object $iterable): array {
    $result = [];

    foreach ($iterable as $iteration) {
        if (!method_exists($iteration, 'getData')) 
            throw new \app\core\src\exceptions\NotFoundException("getData method was not found");
        
        foreach ($iteration->getData() as $dataKey => $dataValue) {
            if (is_iterable($dataValue)) $result[$iteration->key()][$dataKey] = getIterableJsonEncodedData($dataValue);
            else $result[$iteration->key()][$dataKey] = json_encode($dataValue);
        }
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

function panic(string $reason = ''): void {
    exit($reason);
}