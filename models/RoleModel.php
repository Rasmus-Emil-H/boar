<?php

/*******************************
 * Bootstrap UserModel 
 * AUTHOR: RE_WEB
 * @package app\core\UserModel
*/

namespace app\models;

use \app\core\database\Entity;

class RoleModel extends Entity {

    const keyID     = 'RoleID';
	const tableName = 'Roles';
	
	/*
	 * Tablename
	 * @return string
	*/
	
	public function getTableName(): string {
		return 'Roles';
	}
	
	/*
	 * Primary key
	 * @return string
	*/
	
	public function getKeyField(): string {
		return 'RoleID';
	}

	public function getForeignKeys(): array {
		return [];
	}
	
}