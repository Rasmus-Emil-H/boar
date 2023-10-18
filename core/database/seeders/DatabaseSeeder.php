<?php

/**
 * Default seeder
 * @package app\core\database
 * @author RE_WEB
*/

namespace app\core\database\seeders;

use \app\core\database\Entity;
use \app\models;

class DatabaseSeeder {

    public function up(string $model, array $fields, int $amount): void {
        for($i = 0; $i < $amount; $i++) {
            $staticModel = $model.'Model';
            $static = new $staticModel();
            $static
                ->set($fields)
                ->save();
            (new $model($static))->addMetaData(['event' => 'Database seeder added this user']);
        }
    }

}