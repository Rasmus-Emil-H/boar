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

    public function twofactor() {
        if ($this->request->isGet()) return $this->setView('twofactor');
        $this->response->setResponse(200, ['SMS SEND']);
    }

    public function logout(): void {
        (new UserModel())->logout();
        $this->session->unset(['user', 'SessionID']);
		$this->response->redirect('/');
    }

    public function requestNewPassword() {
        if ($this->request->isGet()) return $this->setView('requestNewPassword');
    }

    public function validatePasswordResetToken() {
        $resetToken = $this->requestBody->body->resetPassword ?? false;
        $resetTokenExists = (new UserModel())->getMetaData()->select()->like(['Data' => 'resetPassword='.$resetToken])->run();
        if (!$resetToken || empty($resetTokenExists)) $this->response->redirect('/auth/login');
    }

    public function resetPassword() {
        if ($this->request->isGet()) {
            $this->validatePasswordResetToken();
            return $this->setView('resetPassword');
        }
        (new UserModel())->resetPassword($this->requestBody->body->email);
    }

    public function signup() {
        if ($this->request->isGet()) return $this->setView('signup');
        if (!CoreFunctions::validateCSRF()) $this->response->badToken();
        
        $request = $this->requestBody;
        $emailExists = (new UserModel())->query()->select()->where(['Email' => $request->body->email])->run();
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
