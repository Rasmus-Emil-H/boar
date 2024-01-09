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

	public function getForeignKeys(): array {
		return [];
	}
	
	public function rules(): array {
		return [];
	}

	public function getRelationObjects() {
		return [];
	}

}