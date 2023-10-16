<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\Request;
use app\core\Response;
use app\core\middlewares\AuthMiddleware;
use app\models\UserModel;

class UserController extends Controller {

    public function index() {
        $this->setView('', 'users');
    }

    public function profile() {
        $this->setView('', 'profile');
    }

}
