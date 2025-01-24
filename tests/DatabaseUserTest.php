<?php

namespace app\tests;

use \app\core\src\contracts\UnitTest;

use \app\core\src\unittest\TestCase;

use \app\models\UserModel;

final class DatabaseUserTest extends TestCase implements UnitTest {

    public function run(): mixed {
        try {
            $cUser = (new UserModel())->tableHasValidEntry();
            return is_object($cUser) && $cUser->exists();
        } catch (\Throwable $e) {
            return false;
        }
    }

}