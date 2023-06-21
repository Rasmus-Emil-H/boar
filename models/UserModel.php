<?php

/*******************************
 * Bootstrap UserModel 
 * AUTHOR: RE_WEB
 * @package app\core\UserModel
*/

namespace app\models;

use \app\core\database\Entity;
use \app\core\Application;

class UserModel extends Entity {

    const keyID     = 'UserID';
	const tableName = 'Users';
	
	/*
	 * Tablename
	 * @return string
	*/
	
	public function getTableName(): string {
		return 'Users';
	}
	
	/*
	 * Primary key
	 * @return string
	*/
	
	public function getKeyField(): string {
		return 'UserID';
	}

	public function getForeignKeys(): array {
		return [];
	}
	
}