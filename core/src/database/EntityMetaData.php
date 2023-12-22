<?php

/**
 * Meta data for entities
 * @package app\core\database
 * @author RE_WEB
 */

namespace app\core\src\database;

class EntityMetaData extends Entity {

    const keyID     = 'MetaID';
	const tableName = 'Meta';

    protected function getKeyField(): string {
        return 'MetaID';
    }

    protected function getTableName(): string {
        return 'Meta';
    } 

}