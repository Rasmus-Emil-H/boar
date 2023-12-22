<?php

/**
 * Bootstrap Query 
 * AUTHOR: RE_WEB
 * @package app\models\Query
 */

namespace app\models;

use \app\core\src\database\Entity;

class PermissionModel extends Entity {

	const keyID     = 'PermissionID';
	const tableName = 'Permission';
	
	public function getTableName(): string {
		return 'Permission';
	}
	
	public function getKeyField(): string {
		return 'PermissionID';
	}

	public function getForeignKeys(): array {
		return [];
	}
	
	public function rules(): array {
		return [];
	}

}