<?php

namespace app\controllers;

use \app\core\src\Controller;

class PartialController extends Controller {

    public function navbar() {
      	$this->setView('navbar', 'partials/');
		$this->setData([
			'navbar' => $this->getView(),
			'navigationItems' => app()->getConfig()->get('frontend')->menus->user
		]);
    }

	public function oink(array ...$data) {
		$this->setView('oink', 'partials/');
        $this->setData(...$data)->setAsPartialViewFile();
	}

	public function farm(array ...$data) {
		$this->setView('farm', 'partials/');
        $this->setData(...$data)->setAsPartialViewFile();
	}

}
