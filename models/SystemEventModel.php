<?php

/**
 * Bootstrap Query 
 * AUTHOR: RE_WEB
 * @package app\models\Query
 */

namespace app\models;

use \app\core\database\Entity;

class SystemEventModel extends Entity {

	const keyID     = 'SystemEventID';
	const tableName = 'SystemEvents';

	public function getAttributes(): array {
		return [''];
	}
	
	public function getTableName(): string {
		return 'SystemEvents';
	}
	
	public function getKeyField(): string {
		return 'SystemEventID';
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