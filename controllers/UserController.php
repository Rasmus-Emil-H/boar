<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\Request;
use app\core\Response;
use app\core\middlewares\AuthMiddleware;


class UserController extends Controller {

    public string $defaultRoute = 'profile';

    public function __construct() { 
        
    }

    public function index(Request $request, Response $response) {

        return $this->render($this->defaultRoute, [

        ]);
    }

}