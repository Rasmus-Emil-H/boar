<?php

namespace app\core\src;

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

    private static function checkValidity(string $task) {
        if (!array_key_exists($task, self::getJobs()))
            throw new \app\core\src\exceptions\NotFoundException('CLI TOOL NOT FOUND' . PHP_EOL);
    }

    public static function checkTask(string $task): void {
        self::checkValidity($task);

        exit(self::getJobs()[$task]());
    }
}
