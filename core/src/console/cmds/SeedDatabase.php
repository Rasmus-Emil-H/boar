<?php

namespace app\core\src\console\cmds;

use \app\core\src\contracts\Console;
use \app\core\src\database\seeders\DatabaseSeeder;

class SeedDatabase extends BaseCommand implements Console {

    public function run(array $args): void {
        if (count($args) !== 2) exit($this->stdin(__CLASS__ . ' usage: php boar seed-database handler amount', 'red'));

        (new DatabaseSeeder())->up(...$args);
    }

}