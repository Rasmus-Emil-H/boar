<?php

/*******************************
 * Home Controller 
 * AUTHOR: RE_WEB
 * @package app\controllers
*/

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\Request;
use app\core\Response;
use app\core\middlewares\AuthMiddleware;


class HomeController extends Controller {

    public function __construct() { 
        $this->registerMiddleware(new AuthMiddleware(['profile', 'home']));
    }

    public function index() {
        $this->setChildData(['Product:edit'], $this);
        return $this->render('home');
    }

}