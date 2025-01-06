<?php

namespace app\core\src\console;

use \app\core\src\console\cmds\CreateEntity;
use \app\core\src\console\cmds\SeedDatabase;

use \app\core\src\contracts\Console;

class ConsoleCommand {

    private string $command;
    private array $arguments;

    private array $commands = [
        'create-entity' => CreateEntity::class,
        'seed-database' => SeedDatabase::class
    ];

    private Console $cmd;

    public function __construct(array $arguments) {
        $this->arguments = $arguments;
        $this->setCommand();
        $this->removeRedundantArgs();
    }

    private function setCommand() {
        $this->command = $this->arguments[1];
    }

    private function removeRedundantArgs() {
        unset($this->arguments[0], $this->arguments[1]);
    }

    public function setCmd(): self|string {
        if (!isset($this->commands[$this->command])) return $this->printUsage();

        $this->cmd = $this->commands[$this->command]();

        return $this;
    }

    public function run(): void {
        $this->cmd->run($this->arguments);
    }

    protected function printUsage() {
        echo "Unknow command was provided. Usage: php boar {{command}} {{args}";
    }

    
}
