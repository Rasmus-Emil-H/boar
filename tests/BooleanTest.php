<?php

namespace app\tests;

use \app\core\src\contracts\UnitTest;

use \app\core\src\unittest\TestCase;

final class BooleanTest extends TestCase implements UnitTest {

    public function run(): mixed {
        return $this->assertIsBool(true);
    }

}