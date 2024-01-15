<?php

namespace app\models;

use \app\core\src\database\Entity;
use \app\core\src\miscellaneous\CoreFunctions;
use \app\core\src\miscellaneous\Hash;;

final class UserModel extends Entity {
	
	public function getTableName(): string {
		return 'Users';
	}
		
	public function getKeyField(): string {
		return 'UserID';
	}

	public function getForeignKeys(): array {
		return [];
	}

	public function setRole(string $role): self {
		$this->allowSave();
		$roleID = (new RoleModel())->query()->select(['RoleID'])->where(['Name' => $role])->run();
		(new RoleModel())->pivot(['UserID' => $this->key(), 'RoleID' => CoreFunctions::first($roleID)->key()]);
		return $this;
	}

	public function orders() {
		return $this->hasMany(OrderModel::class)->run();
	}

	public function authenticate(UserModel $user): void {
        $this->app->getSession()->set('user', $user->key());
		$sessionID = Hash::uuid();
        $this->app->getSession()->set('SessionID', $sessionID);
        (new SessionModel())->set(['Value' => $sessionID, 'UserID' => $user->key()])->save();
        $this->app->getResponse()->redirect('/home');
    }

	public function logout() {
		$sessions = (new SessionModel())->query()->select()->where([$this->getKeyField() => CoreFunctions::applicationUser()->key()])->run();
		foreach ($sessions as $session) $session->delete();
	}
	
}