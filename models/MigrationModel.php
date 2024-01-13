<?php

namespace app\models;

use \app\core\src\database\Entity;

class MigrationModel extends Entity {

	const keyID     = 'MigrationID';
	const tableName = 'Migrations';

	public function getAttributes(): array {
		return [];
	}
	
	public function getTableName(): string {
		return 'Migrations';
	}
	
	public function getKeyField(): string {
		return 'MigrationID';
	}
	
}