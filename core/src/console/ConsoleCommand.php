<?php

namespace app\core\src\console;

use \app\core\src\console\cmds\CreateEntity;
use \app\core\src\console\cmds\SeedDatabase;

class ConsoleCommand {

    private string $command;
    private array $arguments;

    public function __construct(array $arguments) {
        $this->arguments = $arguments;
        $this->setCommand();
        $this->removeRedundantArgs();
    }

    private function setCommand() {
        $this->command = $this->arguments[1];
    }

    private function removeRedundantArgs() {
        unset($this->arguments[0]);
    }

    public function run() {
        switch ($this->command) {
            case 'create-entity':
                (new CreateEntity())->run($this->arguments);
                break;
            case 'seed-database':
                (new SeedDatabase())->run($this->arguments);
                break;
            default:
                echo "Unknown command: $this->command\n";
                $this->printUsage();
        }
    }

    protected function printUsage() {
        echo "Usage: php boar {{command}} {{args}";
    }

    
}