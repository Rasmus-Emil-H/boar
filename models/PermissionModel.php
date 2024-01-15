<?php

namespace app\models;

use \app\core\src\database\Entity;

final class PermissionModel extends Entity {
	
	public function getTableName(): string {
		return 'Permission';
	}
	
	public function getKeyField(): string {
		return 'PermissionID';
	}

}