<?php

namespace app\controllers;

use \app\core\src\Controller;

class DOMNodeController extends Controller {

    public function navbar() {
      	$this->setView('navbar', 'partials/');
		$this->setData([
			'navbar' => $this->getView(), 
			'navigationItems' => [
				'<a class="dropdown-item" href="/user/profile">Profile</a>',
				'<a class="dropdown-item" href="/order">Orders</a>',
				'<a class="dropdown-item" href="/product">Products</a>',
				'<a class="dropdown-item" href="/auth/logout">Logout</a>'
			]
		]);
    }

}