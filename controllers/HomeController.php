<?php

/**
 * Home Controller 
 * AUTHOR: RE_WEB
 * @package app\controllers
 */

namespace app\controllers;

use \app\core\src\Controller;

class HomeController extends Controller {

    public function index() {
        $this->setFrontendTemplateAndData('home', ['boar' => 'is live and running']);
    }

}