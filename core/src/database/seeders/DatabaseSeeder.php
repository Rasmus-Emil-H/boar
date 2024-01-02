<?php

/**
 * Default seeder
 * @package app\core\database
 * @author RE_WEB
*/

namespace app\core\src\database\seeders;

use \app\core\src\miscellaneous\CoreFunctions;

class DatabaseSeeder {

    public function up(string $model, array $fields, int $amount): void {
        for($i = 0; $i < $amount; $i++) {
            $staticModel = ('\\app\models\\'.$model.'Model');
            CoreFunctions::app()->classCheck($staticModel);
            $entity = new $staticModel();
            $entity->set($fields)->save();
            (new $staticModel($entity->key()))->addMetaData(['event' => 'Database seeder added: ' . $model]);
        }
    }

}