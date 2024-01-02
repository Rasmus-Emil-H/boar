<?php

/**
 * Bootstrap UserModel 
 * AUTHOR: RE_WEB
 * @package app\core\UserModel
 */

namespace app\models;

use \app\core\src\database\Entity;
use app\core\src\miscellaneous\CoreFunctions;

class UserModel extends Entity {

    const keyID     = 'UserID';
	const tableName = 'Users';
	
	public function getTableName(): string {
		return 'Users';
	}
		
	public function getKeyField(): string {
		return 'UserID';
	}

	public function getForeignKeys(): array {
		return [];
	}

	public function setRole(string $role): void {

	}

	public function orders() {
		return $this->hasMany(OrderModel::class);
	}

	public function logout() {
		$sessions = (new SessionModel())::query()
			->select()
			->where([$this->getKeyField() => CoreFunctions::applicationUser()->key()])
			->run();
		foreach ($sessions as $session) $session->delete();
	}

	public function hasPermissions() {
		
	}
	
}