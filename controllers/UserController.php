<?php

namespace app\controllers;

use \app\core\src\Controller;
use \app\core\src\middlewares\AuthMiddleware;
use app\models\UserModel;

class UserController extends Controller {

    public function __construct() {
        $this->registerMiddleware(new AuthMiddleware());
    }

    public function index() {
        $this->setView('users');
    }

    public function profile() {
        $this->setView('profile');
    }

}
