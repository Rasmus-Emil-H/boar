<?php

namespace app\controllers;

use app\core\Controller;
use app\models\UserModel;
use app\models\Authenticator;

class AuthController extends Controller {

    public string $defaultRoute = 'login';

    public function login() {
        if (app()->session->get('user')) app()->response->redirect('/home');
        if (app()->request->isPost()) new Authenticator(app()->request->getBody(), 'login');
        $this->setLayout('auth');
        $this->setView('', 'login');
    }

    public function logout() {
        app()->session->unset('user');
        app()->response->redirect('/');
    }

    public function signup() {
        if (app()->request->isGet()) return $this->setView('', 'signup');
        $body = app()->request->getBody();
        UserModel::search(['email' => $body['email']]) ? '' : '' ;
    }

}
