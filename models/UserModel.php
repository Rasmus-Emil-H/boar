<?php

namespace app\models;

use \app\core\src\database\Entity;
use \app\core\src\database\EntityMetaData;
use \app\core\src\miscellaneous\CoreFunctions;
use \app\core\src\miscellaneous\Hash;

final class UserModel extends Entity {

	protected const PASSWORD_DEFAULT_RAND_FROM_INT = 10000;
	protected const PASSWORD_DEFAULT_RAND_TO_INT = 1000000000;
	protected const PASSWORD_ERROR_TEXT = 'Passwords must contains atleast: 1 uppercase letter, 1 lowercase letter, 1 digits, one special characters (@$!%*?&#) and be atleast 8 characters long';

	private const ROLE_USER_RELATION_TABLE = 'role_user';

	private const ROLE_ADMIN  = 'Admin';
	private const ROLE_USER   = 'User';
	
	public function getTableName(): string {
		return 'Users';
	}
		
	public function getKeyField(): string {
		return 'UserID';
	}

	public function setRole(string $role): self {
		$this->checkAllowSave();
		$roleID = (new RoleModel())->query()->select(['RoleID'])->where(['Name' => $role])->run();
		(new RoleModel())->createPivot([$this->getKeyField() => $this->key(), 'RoleID' => CoreFunctions::first($roleID)->key()]);
		return $this;
	}

	public function login() {
		$app = app();
		$session = $app->getSession();

		$sessionID = Hash::uuid();

        $session->set('SessionID', $sessionID);
		$session->set('user', $this->key());
        (new SessionModel())->set(['Value' => $sessionID, $this->getKeyField() => $this->key()])->save();

		$checkForDirect = $session->get('redirect');
		$session->unset('redirect');
		$app->addSystemEvent([$this->get('Name') . ' logged in']);
		
		$app->getResponse()->setResponse(200, [
			'redirect' => $checkForDirect ? $checkForDirect : $app->getConfig()->get('routes')->defaults->redirectTo
		]);
	}

	public function logout(): void {
		(new SessionModel())
			->findOne('Value', app()->getSession()->get('SessionID'))
			?->delete();
	}

	public function requestPasswordReset(string $email) {
		$user = $this->find('Email', $email);
		if (empty($user) || !CoreFunctions::first($user)) app()->getResponse()->notFound('User not found');
		$resetLink = app()->getRequest()->getServerInformation()['HTTP_HOST'] . '/auth/resetPassword?resetPassword='.Hash::create(50);
		CoreFunctions::first($user)->addMetaData([$resetLink]);
		mail($email, 'Reset password link', $resetLink);
		app()->getResponse()->setResponse(200, ['redirect' => '/auth/login']);
	}

	public function resetPassword(string $newPassword, string $resetToken) {
		$token = $this->getMetaData()->like(['Data' => 'resetPassword='.$resetToken])->run();
        $this->validatePassword($newPassword);
        $this->set(['Password' => password_hash($newPassword, PASSWORD_DEFAULT)])->save();
        CoreFunctions::first($token)->delete();
        app()->getResponse()->setResponse(201, ['redirect' => '/auth/login']);
	}

	public function hasActiveSession() {
		$appSession = app()->getSession();

		$session = (new SessionModel())
			->query()
			->select()
			->where(['Value' => $appSession->get('SessionID'), $this->getKeyField() => $appSession->get('user')])
		->run();

        return !empty($session) && CoreFunctions::first($session)->exists();
	}

	public function checkPasswordResetToken(string|bool $resetToken): array {
		return (new EntityMetaData())->query()->select()->like(['Data' => 'resetPassword='.$resetToken])->run();
	}

	public function validatePassword(string $password) {
		if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%#*?&])[A-Za-z\d@$!%*?#&]{8,40}$/', $password)) 
            app()->getResponse()->dataConflict(self::PASSWORD_ERROR_TEXT);
	}

	public function generatePassword(?string $password = null): string {
		return password_hash(
			password: $password ?: rand(self::PASSWORD_DEFAULT_RAND_FROM_INT, self::PASSWORD_DEFAULT_RAND_TO_INT), 
			algo: PASSWORD_DEFAULT
		);
	}

	public function role() {
		return CoreFunctions::first($this->attachedTo(RoleModel::class, self::ROLE_USER_RELATION_TABLE, $this->getKeyField(), $this->key()));
	}

	public function isAdmin(): bool {
		return $this->role()->get('Name') === self::ROLE_ADMIN;
	}
	
}