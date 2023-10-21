<?php

/*******************************
 * Bootstrap Query 
 * AUTHOR: RE_WEB
 * @package app\models\Query
*******************************/

namespace app\models;

use \app\core\database\Entity;

class MigrationModel extends Entity {

	const keyID     = 'MigrationID';
	const tableName = 'Migrations';

	public function getAttributes(): array {
		return [];
	}
	
	/*
	 * Tablename
	 * @return string
	*/
	
	public function getTableName(): string {
		return 'Migrations';
	}
	
	/*
	 * Primary key
	 * @return string
	*/
	
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