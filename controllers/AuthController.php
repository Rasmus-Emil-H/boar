<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\Request;
use app\models\User;

class AuthController extends Controller {

    public function login() {
        $this->setLayout('auth');
        return $this->render('login');
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