<?php

namespace app\models;

use \app\core\src\database\Entity;

final class TestModel extends Entity {

    public function getTableName(): string {
        return 'TestTable';
    }
        
    public function getKeyField(): string {
        return 'TestID';
    }
    
}