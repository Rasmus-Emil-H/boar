<?php

/**
 * Default seeder
 * @package app\core\database
 * @author RE_WEB
*/

namespace app\core\database\seeders;

use \app\core\database\Entity;
use \app\models;

class DatabaseSeeder extends Entity {

    public function up(Model $model, array $fields, int $amount): void {
        for($i = 0; $i < $amount; $i++) {
            $static = $model
                ->set($fields)
                ->save();
            (new $model($static))->addMetaData(['event' => 'Database seeder added this user']);
        }
    }

}