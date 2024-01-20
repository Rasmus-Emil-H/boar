<?php

namespace app\controllers;

use \app\core\src\Controller;
use \app\core\src\miscellaneous\CoreFunctions;
use \app\models\UserModel;
use \app\models\AuthenticationModel;

class AuthController extends Controller {

    public function login(): void {
        if ((new UserModel())->hasActiveSession()) $this->response->redirect('/home');
        if ($this->request->isPost()) new AuthenticationModel($this->requestBody, 'applicationLogin');
        $this->setLayout('auth');
        $this->setView('login');
    }

    public function logout(): void {
        (new UserModel())->logout();
        $this->session->unset(['user', 'SessionID']);
		$this->response->redirect('/');
    }

    public function twofactor() {
        if ($this->request->isGet()) return $this->setView('twofactor');
        $this->response->setResponse(200, ['SMS SEND']);
    }

    public function requestNewPassword() {
        if ($this->request->isGet()) return $this->setView('requestNewPassword');
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
            return $this->setView('resetPassword');
        }

        if (!CoreFunctions::validateCSRF()) $this->response->badToken();
        $newPassword = $this->requestBody->body->password;
        if ($newPassword !== $this->requestBody->body->passwordRepeat) $this->response->setResponse(409, ['Passwords do not match']);

        $userToResetPasswordOn = (new UserModel())->checkPasswordResetToken($this->requestBody->body->resetToken);
        $userID = CoreFunctions::first($userToResetPasswordOn)->get('EntityID');
        $user = new UserModel($userID);
        $user->validatePassword($newPassword);
        $user
            ->set(['Password' => password_hash($newPassword, PASSWORD_DEFAULT)])
            ->save();

    }

    public function signup() {
        if ($this->request->isGet()) return $this->setView('signup');
        if (!CoreFunctions::validateCSRF()) $this->response->badToken();
        
        $request = $this->requestBody;
        $emailExists = (new UserModel())->find('Email', $request->body->email);
        if ($emailExists) $this->response->dataConflict();

        $user = (new UserModel())
            ->set(['Email' => $request->body->email, 'Name' => $request->body->name, 'Password' => password_hash($request->body->password, PASSWORD_DEFAULT)])
            ->save()
            ->setRole('User')
            ->addMetaData(['event' => 'user signed up']);

        $this->session->set('userID', $user->key());
        $this->response->redirect('/home');
    }
    
}
