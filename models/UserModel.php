<?php

namespace app\models;

use \app\core\src\database\Entity;
use \app\core\src\miscellaneous\CoreFunctions;
use \app\core\src\miscellaneous\Hash;

final class UserModel extends Entity {

	public function getTableName(): string {
		return 'Users';
	}
		
	public function getKeyField(): string {
		return 'UserID';
	}

	public function setRole(string $role): self {
		$this->allowSave();
		$roleID = (new RoleModel())->query()->select(['RoleID'])->where(['Name' => $role])->run();
		(new RoleModel())->pivot(['UserID' => $this->key(), 'RoleID' => CoreFunctions::first($roleID)->key()]);
		return $this;
	}

	public function initializeTwofactorProgress(): void {
		$this->app->getSession()->set('user', $this->key());
        $this->app->getResponse()->setResponse(200, ['redirect' => '/auth/twofactor']);
    }

	public function login() {
		$sessionID = Hash::uuid();
        $this->app->getSession()->set('SessionID', $sessionID);
        (new SessionModel())->set(['Value' => $sessionID, 'UserID' => $this->key()])->save();
		$this->app->getResponse()->setResponse(200, ['redirect' => '/home']);
	}

	public function logout() {
		$sessions = (new SessionModel())->find($this->getKeyField(), CoreFunctions::applicationUser()->key());
		foreach ($sessions as $session) $session->delete();
	}

	public function requestPasswordReset(string $email) {
		$user = $this->find('Email', $email);
		if (empty($user) || !CoreFunctions::first($user)) $this->app->getResponse()->notFound('User not found');
		$resetLink = $this->app->getRequest()->getServerInformation()['HTTP_HOST'] . '/auth/resetPassword?resetPassword='.Hash::create(50);
		CoreFunctions::first($user)->addMetaData([$resetLink]);
		mail($email, 'Reset password link', $resetLink);
		$this->app->getResponse()->setResponse(200, ['redirect' => '/auth/login']);
	}

	public function resetPassword(string $newPassword, string $resetToken) {
		$token = $this->getMetaData()->select()->like(['Data' => 'resetPassword='.$resetToken])->run();
        $this->validatePassword($newPassword);
        $this->set(['Password' => password_hash($newPassword, PASSWORD_DEFAULT)])->save();
        CoreFunctions::first($token)->delete();
        $this->app->getResponse()->setResponse(201, ['redirect' => '/auth/login']);
	}

	public function hasActiveSession() {
		$session = (new SessionModel())->query()->select()->where(['Value' => $this->app->getSession()->get('SessionID'), 'UserID' => $this->app->getSession()->get('user')])->run();
        return !empty($session) && CoreFunctions::first($session)->exists();
	}

	public function checkPasswordResetToken(string|bool $resetToken): array {
		return $this->getMetaData()->select()->like(['Data' => 'resetPassword='.$resetToken])->run();
	}

	public function validatePassword(string $password) {
		if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%#*?&])[A-Za-z\d@$!%*?#&]{8,40}$/', $password)) 
            CoreFunctions::app()->getResponse()->setResponse(409, ['Passwords must contains atleast: 1 uppercase letter, 1 lowercase letter, 1 digits, one special characters (@$!%*?&#) and be atleast 8 characters long']);
	}
	
}