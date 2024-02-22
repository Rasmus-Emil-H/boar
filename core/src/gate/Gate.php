<?php

/**
|----------------------------------------------------------------------------
| Application authorization
|----------------------------------------------------------------------------
| From here you can control access between entities and actions
| 
| @author RE_WEB
| @package \app\core\src\gate
|
*/

namespace app\core\src\gate;

use \app\core\src\database\Entity;
use \app\core\src\miscellaneous\CoreFunctions;
use \app\models\UserModel;

class Gate {

    public static function isAuthenticatedUserAllowed(string $method, Entity $entity): bool {
        if (!method_exists(__CLASS__, $method)) return false;
        return self::{$method}($entity);
    }

    public static function isSpecificUserAllowed(string $method, UserModel $user, Entity $entity): bool {
        if (!method_exists(__CLASS__, $method)) return false;
        return self::{$method}();
    }

    public static function isEntityAllowed(string $method, Entity $entityFrom, Entity $entityTo): bool {
        if (!method_exists(__CLASS__, $method)) return false;
        return self::{$method}();
    }

    protected static function testUpdateEntity(Entity $entity): bool {
        return $entity->user()->key() === CoreFunctions::applicationUser()->key();
    }

}