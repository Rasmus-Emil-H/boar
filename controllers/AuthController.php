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
        if(!validateCSRF()) return false;
        $body = app()->request->getBody();
        $emailExists = UserModel::search(['Email' => $body['email']]);
        if ($emailExists) app()->response->setResponse(409, ['errors' => 'Email exists']);
        $static = new UserModel();
        $static
            ->set(['Email' => $body['email'], 'Name' => $body['name'], 'Password' => password_hash($body['password'], PASSWORD_DEFAULT)])
            ->save();
        app()->response->setResponse(201, ['data' => 'User created']);
    }

}
