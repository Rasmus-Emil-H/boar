<?php

/**
|----------------------------------------------------------------------------
| Database seeder
|----------------------------------------------------------------------------
| Object used to create entities instead of manual labor
| 
| @author RE_WEB
| @package core
|
*/

namespace app\core\src\database\seeders;

use app\core\src\factories\ModelFactory;

class DatabaseSeeder {

    public function up(string $model, array $fields, int $amount): void {
        for($i = 0; $i < $amount; $i++) {
            $entity = (new ModelFactory(['handler' => $model, 'data' => $fields]))->create();
            $entity->set($fields)->save(addMetaData: false);
        }
    }

}