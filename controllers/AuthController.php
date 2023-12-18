<?php

namespace app\controllers;

use \app\core\Controller;
use \app\models\UserModel;
use \app\models\Authenticator;

class AuthController extends Controller {

    public function login() {
        if (app()->session->get('user')) app()->response->redirect('/home');
        if (app()->request->isPost()) new Authenticator(app()->request->getBody(), 'applicationLogin');
        
        $this->setLayout('auth');
        $this->setView('', 'login');
    }

    public function logout() {
        (new UserModel())->logout();
        app()->session->unset('user');
		app()->session->unset('SessionID');
		app()->response->redirect('/');
    }

    public function signup() {
        if (app()->request->isGet()) return $this->setView('', 'signup');
        if (!validateCSRF()) app()->response->badToken();
        
        $body = app()->request->getBody();
        $emailExists = UserModel::query()->select()->where(['Email' => $body->email])->run();
        if ($emailExists) app()->response->dataConflict();

        $userID = (new UserModel())
            ->set(['Email' => $body->email, 'Name' => $body->name, 'Password' => password_hash($body->password, PASSWORD_DEFAULT)])
            ->save();

        $user = new UserModel($userID);
        $user
            ->addMetaData(['event' => 'user signed up'])
            ->setRole('User');

        app()->session->set('userID', $userID);
        app()->response->redirect('/home');
    }

}
