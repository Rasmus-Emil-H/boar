<?php

namespace app\controllers;

use \app\core\src\Controller;
use \app\core\src\middlewares\AuthMiddleware;
use app\models\UserModel;

class UserController extends Controller {

    public function index() {
        $this->setFrontendTemplateAndData('users');
    }

    public function profile() {
        $this->setFrontendTemplateAndData('profile');
    }

}
