<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\Request;
use app\models\UserModel;
use app\models\LoginForm;
use app\core\Response;
use app\core\middlewares\AuthMiddleware;
use app\models\Authenticator;

class AuthController extends Controller {

    public string $defaultRoute = 'login';

    public function __construct() {
        $this->registerMiddleware(new AuthMiddleware(['profile', 'home']));
    }

    public function login() {
        if(Application::$app->session->get('user')) Application::$app->response->redirect('/home');
        if(Application::$app->request->isPost()) new Authenticator(Application::$app->request->getBody(), 'login');
        $this->setLayout('auth');
        return $this->render('login', [
            
        ]);
    }

    public function register() {
        $user = new UserModel();
        if (Application::$app->request->isPost()) {
            $user->set(Application::$app->request->getBody());
            if ($user->validate() && $user->save()) {
                Application::$app->session->setFlashMessage('success', 'User created');
                Application::$app->response->redirect('/');
                return;
            }
            return $this->render('register', [
                'model' => $user,
            ]);
        }
        $this->setLayout('auth');
        return $this->render('register', [
            'model' => $user,
        ]);
    }

    public function logout() {
        Application::$app->session->unset('user');
        Application::$app->response->redirect('/');
    }

    public function profile() {
        return $this->render('profile');
    }

}