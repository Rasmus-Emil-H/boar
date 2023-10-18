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

  public function index() {
    $this->setView('', 'home');
    (new \app\core\database\seeders\DatabaseSeeder())->up('User', ['Name' => 'qwd', 'Email' => rand(1,10000).'@live.dk'], 10);
  }
    
}
