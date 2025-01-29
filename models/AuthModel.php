<?php

namespace app\models;

use \app\core\src\database\Entity;

final class AuthModel extends Entity {

	public function getTableName(): string {
		return 'Auth';
	}
		
	public function getKeyField(): string {
		return 'AuthID';
	}

}