<?php

/**
 * Home Controller 
 * AUTHOR: RE_WEB
 * @package app\controllers
 */

namespace app\controllers;

use \app\core\src\Controller;
use \app\core\src\middlewares\AuthMiddleware;

class HomeController extends Controller {

    public function index() {
        $this->setFrontendTemplateAndData(templateFile: 'home');
    }

}
