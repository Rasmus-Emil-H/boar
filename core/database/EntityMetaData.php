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
        return 'MetaID';
    }

    protected function getTableName(): string {
        return 'Meta';
    }

    

}