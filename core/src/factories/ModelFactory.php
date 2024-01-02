<?php

namespace app\core\src\factories;

use \app\core\src\miscellaneous\CoreFunctions;

class ModelFactory extends AbstractFactory {

    protected const MODEL = 'Model';

    public function create(): \app\core\src\database\Entity {
        $model = ('\\app\models\\' . $this->getHandler() . self::MODEL);
        $this->validateObject($model);
        return new $model();
    }

}