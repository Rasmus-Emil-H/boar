<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\Request;
use app\models\User;
use app\models\LoginForm;
use app\core\Response;
use app\core\middlewares\AuthMiddleware;
use app\models\Authenticator;

class AuthController extends Controller {

    public string $defaultRoute = 'login';

    public function __construct() {
        $this->registerMiddleware(new AuthMiddleware(['profile']));
    }

    public function login(Request $request, Response $response) {
        if(Application::$app->session->get('user')) $response->redirect('/home');
        if($request->isPost()) new Authenticator($request->getBody(), 'login');
        $this->setLayout('auth');
        return $this->render('login', [
            
        ]);
    }

    public function register(Request $request) {
        $user = new User();
        if ($request->isPost()) {
            $user->loadData($request->getBody());
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

    public function logout(Request $request, Response $response) {
        Application::$app->authentication->logout();
        $response->redirect('/');
    }

    public function profile() {
        return $this->render('profile');
    }

}