<?php

/**
 * Default seeder
 * @package app\core\database
 * @author RE_WEB
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