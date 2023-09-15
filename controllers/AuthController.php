<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\models\UserModel;
use app\models\Authenticator;

class AuthController extends Controller {

    public string $defaultRoute = 'login';

    public function __construct() {
        
    }

    public function login() {
        if (Application::$app->session->get('user')) Application::$app->response->redirect('/home');
        if (Application::$app->request->isPost()) new Authenticator(Application::$app->request->getBody(), 'login');
        $this->setLayout('auth');
        return $this->render('login');
    }

    public function register() {
        Application::$app->session->unset('user');
        Application::$app->response->redirect('/');
    }

    public function profile() {
        return $this->render('profile');
    }

}
