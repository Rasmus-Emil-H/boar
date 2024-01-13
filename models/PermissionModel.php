<?php

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

}