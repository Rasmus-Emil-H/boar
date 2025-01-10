<?php

namespace app\core\src\console\cmds;

use \app\core\src\contracts\Console;

use \app\core\src\File;

use \app\core\src\factories\TestCaseFactory;

class UnitTest implements Console {

    private array $testFiles = [];
    private int $nestedDirDepth = 4;

    public function __construct() {
        $this->testFiles = File::getFilesWithoutDots(dirname(__DIR__, $this->nestedDirDepth) . '/tests');
        
        echoCLI('Ready to run tests: ' . count($this->testFiles));
    }

    public function run(array $args): void {
        array_map(function($file) {
            $handler = preg_replace('/' . File::PHP_EXTENSION . '/', '', $file);

            echoCLI('Running: ' . $handler);

            $test = (new TestCaseFactory(compact('handler')))->create();
            $result = $test->run();
            
            echoCLI('Result: ' . $result);
        }, $this->testFiles);
    }
    
}