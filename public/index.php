<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__, 1) . '/yard.php';

try {
    if (!IS_CLI) {
        $boar = new \app\core\Application();
        $boar->bootstrap();
    }
} catch (\Throwable $e) {
    \app\core\src\miscellaneous\CoreFunctions::dd($e->getMessage());
}

if (isset($argv)) {
    $task = $argv[1] ??= 'none';
    \app\core\src\CLI::checkTask($task);
}