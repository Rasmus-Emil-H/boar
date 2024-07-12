<?php

namespace app\core\src\console;

use \app\core\src\console\cmds\CreateEntity;

class ConsoleCommand {

    public function run($argv) {
        if (!$this->checkArguments($argv)) return;

        $command = $argv[1];
        $entityName = $argv[2];

        switch ($command) {
            case 'create-entity':
                (new CreateEntity())->createEntity($entityName);
                break;
            default:
                echo "Unknown command: $command\n";
                $this->printUsage();
        }
    }

    private function checkArguments($argv): bool {
        $argc = count($argv) < 3;
        if (!$argc) $this->printUsage();
        
        return $argc;
    }

    protected function printUsage() {
        echo "Usage: php boar create-entity <entity-type> <entity-name>\n";
    }

    
}