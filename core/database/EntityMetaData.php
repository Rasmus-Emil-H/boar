<?php

/**
 * Entity metadata base for models
 * @package app\core\database
 * @author RE_WEB
*/

namespace app\core\database;

use \app\core\Application;
use \app\core\database\relations\Relations;

class EntityMetaData extends Entity {

    private   $key;
    protected $data = [];

    /**
     * Related object array
     * @format like: 'objectIdentifier' => Model::class
    */
    // protected array $relatedObjects = [];

    protected function getKeyField(): string {
        return '';
    }

    protected function getTableName(): string {
        return '';
    }

    /**
     * Loads a given entity, instantiates a new if none given.
     * @param mixed $data Can be either an array of existing data or an entity ID to load.
     * @return void
    */

    /**
     * @param array $metaData
     * @return string
    */

    public function set(array $metaData): void {
        $metaData['entityID'] = $this->key();
        Application::$app->connection->create('t_meta_data', json_encode($metaData));
    }

}