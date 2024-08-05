<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__, 1) . '/yard.php';

try {
    $app = new \app\core\Application();
    if (!IS_CLI) $app->bootstrap();
} catch (\Throwable $e) {
    die;
}

if (isset($argv)) {
    $task = $argv[1] ??= 'none';
    \app\core\src\CLI::checkTask($task);
}