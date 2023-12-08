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
          '<a class="dropdown-item" href="/user/profile">Profile</a>',
          '<a class="dropdown-item" href="/auth/logout">Logout</a>'
        ]
      ]);
    }

}
