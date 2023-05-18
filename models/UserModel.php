<?php

/*******************************
 * Bootstrap UserModel 
 * AUTHOR: RE_WEB
 * @package app\core\UserModel
*/

namespace app\models;

use \app\core\database\Entity;
use \app\core\Application;

class UserModel extends Entity {

    const keyID     = 'user_id';
	const tableName = 'users';
	
	/*
	 * Tablename
	 * @return string
	*/
	
	public function getTableName(): string {
		return 'users';
	}
	
	/*
	 * Primary key
	 * @return string
	*/
	
	public function getKeyField(): string {
		return 'user_id';
	}

	public function getForeignKeys(): array {
		return [];
	}

	public static function search(array $criterias, array $values = ['*'], array $additionalQueryBuilding = []): array {
        $rows = Application::$app->connection->select('sessions', $values)->whereClause($criterias);
        $rows = $rows->execute();
        return self::load(array_column($rows, static::keyID));
    }

}