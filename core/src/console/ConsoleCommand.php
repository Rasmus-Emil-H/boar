<?php

namespace app\core\src\console;

use \app\core\src\console\cmds\CreateEntity;
use \app\core\src\console\cmds\CreateTest;
use \app\core\src\console\cmds\SeedDatabase;
use \app\core\src\console\cmds\UnitTest;

use \app\core\src\contracts\Console;

class ConsoleCommand {

    private string $command;

    private array $commands = [
        'create-entity' => CreateEntity::class,
        'seed-database' => SeedDatabase::class,
        'create-test' => CreateTest::class,
        'unit-test' => UnitTest::class,
    ];

    private Console $cmd;

    public function __construct(
        private array $arguments
    ) {
        $this->setCommand();
        $this->removeRedundantArgs();
    }

    private function setCommand() {
        if (!isset($this->arguments[1])) return $this->help();

        $this->command = $this->arguments[1];
    }

    private function removeRedundantArgs() {
        unset($this->arguments[0], $this->arguments[1]);
    }

    public function setCmd(): self|string {
        if (!isset($this->commands[$this->command])) exit($this->printUsage());

        $this->cmd = new $this->commands[$this->command]();

        return $this;
    }

    public function run(): void {
        $this->setCmd();
        $this->cmd->run($this->arguments);
    }

    public function help() {
        $cmds = implode(PHP_EOL, array_keys($this->commands));

        $help = <<<EOT
        Usage: php boar [options...]

        Current commands:
        $cmds
        EOT;

        exit(echoCLI($help));
    }

    protected function printUsage(): string {
        return "Unknow command was provided. Usage: php boar {{command}} {{args} \n";
    }

    
}