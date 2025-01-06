<?php

/**
|----------------------------------------------------------------------------
| Base for CLI behaviour
|----------------------------------------------------------------------------
|
| @author RE_WEB
| @package core\src
|
*/

namespace app\core\src;

use \app\core\src\exceptions\NotFoundException;

class CLI {

    private static function getJobs(): array {
        return [
            'CronjobScheduler' => function() {
                (new \app\core\src\scheduling\Cron())->run();
            },
            'DatabaseMigration' => function() {
                (new \app\core\src\database\Migration())->applyMigrations();
            },
            'WebsocketInit' => function() {
                \app\core\src\websocket\Websocket::getInstance();
            } 
        ];
    }

    private static function checkValidity(string $task): void {
        if (!array_key_exists($task, self::getJobs()))
            throw new NotFoundException('CLI TOOL NOT FOUND' . PHP_EOL);
    }

    public static function checkTask(string $task): void {
        self::checkValidity($task);

        exit(self::getJobs()[$task]());
    }
}
