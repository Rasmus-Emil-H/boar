<?php

namespace app\controllers;

use \app\core\src\Controller;
use \app\models\UserModel;
use \app\models\Authenticator;

class AuthController extends Controller {

    public function login() {
        if (app()->getSession()->get('user')) app()->getResponse()->redirect('/home');
        if (app()->getRequest()->isPost()) new Authenticator(app()->getRequest()->getBody(), 'applicationLogin');
        $this->setLayout('auth');
        $this->setView('login');
    }

    public function logout() {
        (new UserModel())->logout();
        app()->getSession()->unset(['user', 'SessionID']);
		app()->getResponse()->redirect('/');
    }

    public function signup() {
        if (app()->getRequest()->isGet()) return $this->setView('signup');
        if (!validateCSRF()) app()->getResponse()->badToken();
        
        $body = app()->getRequest()->getBody();
        $emailExists = UserModel::query()->select()->where(['Email' => $body->email])->run();
        if ($emailExists) app()->getResponse()->dataConflict();

        $userID = (new UserModel())
            ->set(['Email' => $body->email, 'Name' => $body->name, 'Password' => password_hash($body->password, PASSWORD_DEFAULT)])
            ->save();

        $user = new UserModel($userID);
        $user->addMetaData(['event' => 'user signed up'])->setRole('User');

        app()->getSession()->set('userID', $userID);
        app()->getResponse()->redirect('/home');
    }

}
