<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\Request;
use app\core\Response;
use app\core\middlewares\AuthMiddleware;
use app\models\UserModel;

class UserController extends Controller {

    protected const DEFAULT_VIEW = 'profile';

    public string $defaultRoute = 'index';

    public function __construct() { 
        
    }

    public function index() {

        return $this->render(self::DEFAULT_VIEW, [
            'users' => UserModel::all()
        ]);
    }

}
