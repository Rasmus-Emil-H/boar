<?php

namespace app\core\src\console;

use \app\core\src\console\cmds\CreateEntity;

class ConsoleCommand {

    public function run($argv) {
        if (count($argv) < 3) {
            $this->printUsage();
            return;
        }

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

    protected function printUsage() {
        echo "Usage: php autologik create-entity <entity-type> <entity-name>\n";
    }

    
}
