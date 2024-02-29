<?php

/**
|----------------------------------------------------------------------------
| Entity authorization
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
use \app\core\src\traits\GateStaticMethodTrait;

class Gate {

    use GateStaticMethodTrait;

    protected static function canViewProduct(Entity $product): bool {
        return $product->user()->key() === CoreFunctions::applicationUser()->key();
    }

}