<?php

/**
 * Home Controller 
 * AUTHOR: RE_WEB
 * @package app\controllers
*/

namespace app\controllers;

use app\core\Controller;

class DOMNodeController extends Controller {

    public function navbar() {
      $this->setView('partials/', 'navbar');
      $this->setData([
        'navbar' => $this->getView(), 
        'navigationItems' => [
          '<a class="text-white" href="/home"><i class="bi bi-house"></i></a>' => [],
          '<a class="text-white" href="/user/profile"><i class="bi bi-person"></i></a>' => [],
          '<a class="text-white float-right" href="/auth/logout"><i class="bi bi-box-arrow-left"></i></a>' => ['float-right']
        ]
      ]);
    }

}
