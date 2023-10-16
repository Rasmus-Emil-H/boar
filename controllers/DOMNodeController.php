<?php

/**
 * Home Controller 
 * AUTHOR: RE_WEB
 * @package app\controllers
*/

namespace app\controllers;

use app\core\Application;
use app\core\Controller;

class DOMNodeController extends Controller {

    public function navbar() {
      $this->setView('partials/', 'navbar');
      $this->setData(['navbar' => $this->getView(), 'navigationItems' => ['Dashboard' => '/', 'Profile' => '/user/profile', 'Logout' => '/auth/logout']]);
    }

}
