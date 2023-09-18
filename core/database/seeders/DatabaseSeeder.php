<?php

/**
 * Default seeder
 * @package app\core\database
 * @author RE_WEB
*/

namespace app\core\database\seeders;

use \app\core\Application;
use \app\core\database\relations\Relations;
use \app\core\database\Entity;
use \app\models;

class DatabaseSeeder extends Entity {

    public function up(Model $model, array $fields): void {
        $model->set($fields);
        $model->save();
    }

}