<?php

namespace app\controllers;

use \app\core\src\Controller;
use \app\core\src\miscellaneous\CoreFunctions;

class HeaderController extends Controller {

    public function index() {
		$this->setView('header', 'partials/');
		$this->setChildren(['navbar' => 'DOMNode:navbar']);
		$assets = app()->getParentController()->getClientAssets();
		$this->setData([
			'header' => $this->getView(),
			'appName' => app()->getConfig()->get('appName'),
			'stylesheets' => $assets->get('css'),
			'metaTags' => $assets->get('metaTags')
		]);
    }

}