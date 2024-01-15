<?php

namespace app\models;

use \app\core\src\database\Entity;

final class MigrationModel extends Entity {
	
	public function getTableName(): string {
		return 'Migrations';
	}
	
	public function getKeyField(): string {
		return 'MigrationID';
	}
	
}