<?php

namespace app\core\src\console\cmds;

use \app\core\src\CLI;

abstract class BaseCommand {

    public function __construct(
        protected CLI $cli = new CLI()
    ) {}

    protected function stdin(string $message, string $color): void {
        $this->cli->printWithColor($message, $color);
    }

    abstract public function run(array $args): void;

}