<?php

/**
 * Bootstrap UserModel 
 * AUTHOR: RE_WEB
 * @package app\core\UserModel
 */

namespace app\models;

use \app\core\database\Entity;

class RoleModel extends Entity {

    const keyID     = 'RoleID';
	const tableName = 'Roles';
		
	public function getTableName(): string {
		return 'Roles';
	}
	
	public function getKeyField(): string {
		return 'RoleID';
	}

	public function getForeignKeys(): array {
		return [];
	}
	
}