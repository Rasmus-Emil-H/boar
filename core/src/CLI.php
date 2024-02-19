<?php

namespace app\core\src;

class CLI {

    public static function checkTask(string $task): void {
        if ($task === CRONJOB_CLI_CHECK) exit((new scheduling\Cron())->run());
        if ($task === DATABASE_MIGRATION_CLI_CHECK) exit((new database\Migration())->applyMigrations());
        exit(CLI_TOOL_NOT_FOUND_MESSAGE);
    }

}