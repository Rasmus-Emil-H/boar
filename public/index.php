<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once 'resources/scss_compiler/compiler.inc.php';
require_once dirname(__DIR__, 1) . '/yard.php';

try {
    $autologikGO = new \app\core\Application();
    if (!IS_CLI) $autologikGO->bootstrap();
} catch (\Throwable $e) {
    \app\core\src\miscellaneous\CoreFunctions::dd($e->getMessage());
}

if (isset($argv)) {
    $task = $argv[1] ??= 'none';
    if ($task === CRONJOB_CLI_CHECK) exit((new \app\core\src\scheduling\Cron())->checkAndIterate());
    if ($task === DATABASE_MIGRATION_CLI_CHECK) exit((new \app\core\src\database\Migration())->applyMigrations());
    exit(CLI_TOOL_NOT_FOUND_MESSAGE . PHP_EOL);
}