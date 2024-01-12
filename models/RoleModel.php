<?php

namespace app\models;

use \app\core\src\database\Entity;

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

	public function getPivot(): string {
		return 'role_user';
	}
	
}