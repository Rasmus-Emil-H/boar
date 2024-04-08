<?php

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

define('CRONJOB_CLI_CHECK', 'CronjobScheduler');
define('DATABASE_MIGRATION_CLI_CHECK', 'DatabaseMigration');
define('CLI_TOOL_NOT_FOUND_MESSAGE', 'CLI TOOL NOT FOUND' . PHP_EOL);
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

function hs(string $string): string {
    return htmlspecialchars($string);
}

function app() {
    return \app\core\src\miscellaneous\CoreFunctions::app();
}

function getIterableJsonEncodedData(array|object $iterable): array {
    $result = [];
    foreach ($iterable as $iteration)
        $result[] = json_encode($iteration->getData());
    return $result;
}