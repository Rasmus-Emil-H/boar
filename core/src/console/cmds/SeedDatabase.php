<?php

namespace app\core\src\console\cmds;

use \app\core\src\database\seeders\DatabaseSeeder;

class SeedDatabase {

    public function run(array $arguments) {
        (new DatabaseSeeder())->up(...$arguments);
    }

}