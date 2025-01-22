<?php

namespace app\core\src\console;

use \app\core\src\console\cmds\CreateEntity;
use \app\core\src\console\cmds\CreateTest;
use \app\core\src\console\cmds\Migrate;
use \app\core\src\console\cmds\NewMigration;
use \app\core\src\console\cmds\SeedDatabase;
use \app\core\src\console\cmds\UnitTest;

use \app\core\src\contracts\Console;

use \app\core\src\ServiceContainer;

class ConsoleCommand {

    private string $command;

    private array $commands = [
        'create-entity' => CreateEntity::class,
        'migrate'       => Migrate::class,
        'new-migration' => NewMigration::class,
        'seed-database' => SeedDatabase::class,
        'create-test'   => CreateTest::class,
        'unit-test'     => UnitTest::class,
    ];

    private Console $cmd;

    private const USAGE_TEXT = 'Unknow command was provided. Usage: php boar {{command}} {{args}' . PHP_EOL;

    public function __construct(
        private array $arguments,
        private ServiceContainer $container
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

        $this->cmd = new $this->commands[$this->command]($this->container->get('cli'));

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

        exit($this->container->get('cli')->printWithColor($help, 'red'));
    }

    protected function printUsage(): string {
        return self::USAGE_TEXT;
    }

}