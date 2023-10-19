<?php

/**
 * Default seeder
 * @package app\core\database
 * @author RE_WEB
*/

namespace app\core\database\seeders;

use \app\core\database\Entity;

class DatabaseSeeder {

    public function up(string $model, array $fields, int $amount): void {
        for($i = 0; $i < $amount; $i++) {
            $staticModel = '\\app\models\\'.$model.'Model';
            app()->classCheck($staticModel);
            $static = new $staticModel();
            $static
                ->set($fields)
                ->save();
            (new $staticModel($static->key()))->addMetaData(['event' => 'Database seeder added this ' . $model]);
        }
    }

}