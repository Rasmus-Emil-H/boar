<?php

namespace app\core\src\console\cmds;

use \app\core\src\contracts\Console;

use \app\core\src\File;

class CreateTest implements Console {

    public function run(array $args): void {
        if (count($args) !== 1) exit(echoCLI(__CLASS__ . ' only takes 1 argument, the name'));
        
        $this->createTest(first($args));
    }

    protected function createTest(string $name): void {
        $class = ucfirst($name) . 'Test';

        $content = <<<EOT
        <?php

        namespace app\\tests;

        use \app\core\src\contracts\UnitTest;

        use \app\core\src\unittest\TestCase;

        final class $class extends TestCase implements UnitTest {

            public function run(): mixed {
                return 'it\'a alive';
            }

        }
        EOT;

        $fileName = "tests/{$class}.php";

        File::putContent($fileName, $content);

        echoCLI('Created test: ' .  $fileName);
    }
    
}