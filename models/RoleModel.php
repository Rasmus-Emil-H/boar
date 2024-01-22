<?php

namespace app\models;

use \app\core\src\database\Entity;

final class RoleModel extends Entity {

	private const ROLE_USER_RELATION = 'role_user';
		
	public function getTableName(): string {
		return 'Roles';
	}
	
	public function getKeyField(): string {
		return 'RoleID';
	}
	
	public function getPivot(): string {
		return self::ROLE_USER_RELATION;
	}
	
}