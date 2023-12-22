<?php

/**
 * Bootstrap UserModel 
 * AUTHOR: RE_WEB
 * @package app\core\UserModel
 */

namespace app\models;

use \app\core\src\database\Entity;

class SessionModel extends Entity {

    const keyID     = 'SessionID';
	const tableName = 'Sessions';

	public function getTableName(): string {
		return 'Sessions';
	}
	
	public function getKeyField(): string {
		return 'SessionID';
	}

	public function getForeignKeys(): array {
		return [];
	}
	
}