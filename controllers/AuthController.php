<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\Request;
use app\models\User;
use app\models\LoginForm;
use app\core\Response;

class AuthController extends Controller {

    public function login(Request $request, Response $response) {
        $loginForm = new LoginForm();
        if ($request->isPost()) {
            $loginForm->loadData($request->getBody());
            if ($loginForm->validate() && $loginForm->login()) {
                $response->redirect('/');
                return;
            }
        }
        $this->setLayout('auth');
        return $this->render('login', [
            'model' => $loginForm
        ]);
    }

    public function register(Request $request) {
        
        $user = new User();
        
        if ($request->isPost()) {

            $user->loadData($request->getBody());

            if ($user->validate() && $user->save()) {
                var_dump(123);
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

}