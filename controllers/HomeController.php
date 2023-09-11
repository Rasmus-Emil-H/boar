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
    
    }

    public function index() {
      $this->render('home');
    }
}
