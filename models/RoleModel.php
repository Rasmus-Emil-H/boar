<?php

namespace app\models;

use \app\core\src\database\Entity;

class RoleModel extends Entity {
		
	public function getTableName(): string {
		return 'Roles';
	}
	
	public function getKeyField(): string {
		return 'RoleID';
	}
	public function getPivot(): string {
		return 'role_user';
	}
	
}