<?php

namespace app\controllers;

use \app\core\src\Controller;
use \app\core\src\miscellaneous\CoreFunctions;
use \app\models\UserModel;
use \app\models\Authenticator;

class AuthController extends Controller {

    public function login() {
        if ($this->session->get('user')) $this->response->redirect('/home');
        if ($this->request->isPost()) new Authenticator($this->request->getBody(), 'applicationLogin');
        $this->setLayout('auth');
        $this->setView('login');
    }

    public function logout() {
        (new UserModel())->logout();
        $this->session->unset(['user', 'SessionID']);
		$this->response->redirect('/');
    }

    public function signup() {
        if ($this->request->isGet()) return $this->setView('signup');
        if (!CoreFunctions::validateCSRF()) $this->response->badToken();
        
        $body = $this->request->getBody();
        $emailExists = UserModel::query()->select()->where(['Email' => $body->email])->run();
        if ($emailExists) $this->response->dataConflict();

        $userID = (new UserModel())
            ->set(['Email' => $body->email, 'Name' => $body->name, 'Password' => password_hash($body->password, PASSWORD_DEFAULT)])
            ->save()
            ->setRole('User')
            ->addMetaData(['event' => 'user signed up']);

        $this->session->set('userID', $userID);
        $this->response->redirect('/home');
    }

}
