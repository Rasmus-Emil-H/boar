<?php

namespace app\controllers;

use \app\core\src\Controller;
use \app\core\src\database\table\Table;
use \app\core\src\miscellaneous\CoreFunctions;
use \app\models\UserModel;
use \app\models\AuthenticationModel;

class AuthController extends Controller {

    public function login(): void {
        if ((new UserModel())->hasActiveSession()) $this->response->redirect(env('routes')->defaults->redirectTo);

        if ($this->request->isPost()) new AuthenticationModel($this->requestBody, 'applicationLogin');
        
        $this->setClientLayoutStructure('auth', 'login');
    }

    public function logout(): void {
        (new UserModel())->logout();
        app()->getSession()->unset(['user', 'SessionID']);
		$this->response->redirect('/');
    }

    public function twofactor() {
        if ($this->request->isGet()) return $this->setClientLayoutStructure('auth', 'twoFactor');
        $this->response->setResponse(200, ['SMS SEND']);
    }

    public function requestNewPassword() {
        if ($this->request->isGet()) return $this->setClientLayoutStructure('auth', 'requestNewPassword');
        (new UserModel())->requestPasswordReset($this->requestBody->body->email);
    }

    public function validatePasswordResetToken(): void {
        $resetToken = $this->requestBody->body->resetPassword ?? false;
        $resetTokenExists = (new UserModel())->checkPasswordResetToken($resetToken);
        if (!$resetToken || empty($resetTokenExists)) $this->response->redirect('/auth/login');
    }

    public function resetPassword() {
        if ($this->request->isGet()) {
            $this->validatePasswordResetToken();
            return $this->setClientLayoutStructure('auth', 'resetPassword');
        }
        
        $newPassword = $this->requestBody->body->password;
        $resetToken = $this->requestBody->body->resetToken;
        if ($newPassword !== $this->requestBody->body->passwordRepeat) $this->response->setResponse(409, ['Passwords do not match']);

        $userToResetPasswordOn = (new UserModel())->checkPasswordResetToken($resetToken);
        $userID = CoreFunctions::first($userToResetPasswordOn)->get(Table::ENTITY_TYPE_COLUMN);
        $user = new UserModel($userID);
        $user->resetPassword($newPassword, $resetToken);
    }

    public function signup() {
        if ($this->request->isGet()) return $this->setView('signup');
        
        $request = $this->requestBody;
        $emailExists = (new UserModel())->find('Email', $request->body->email);
        if ($emailExists->exists()) $this->response->dataConflict(ths('Email already exists'));

        (new UserModel())
            ->set(['Email' => $request->body->email, 'Name' => $request->body->name, 'Password' => password_hash($request->body->password, PASSWORD_DEFAULT)])
            ->save()
            ->setRole('User')
            ->addMetaData(['event' => 'user signed up']);

        $this->response->setResponse(201, ['redirect' => '/auth/login']);
    }
    
}
