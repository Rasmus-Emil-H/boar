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

use \app\core\src\factories\ModelFactory;

class DatabaseSeeder {

    public function up(string $handler, int $amount): void {
        try {
            for ($i = 0; $i < $amount; $i++) {
                $entity = (new ModelFactory(compact('handler')))->create();
                $fields = $entity->getEntityTableFields();
                $entity->set(...$fields->getData())->save();
            }
        } catch (\Exception $e) {
            var_dump($e->getMessage(), $e->getFile(), $e->getLine());
        }
    }

}